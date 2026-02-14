<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Ase;

use Hubzero\Database\Drivers\Base\BaseSqlDriver;
use Hubzero\Database\Exception\ConnectionFailedException;
use Hubzero\Database\Exception\QueryFailedException;

/**
 * SAP ASE (Sybase Adaptive Server Enterprise) database driver
 *
 * SAP ASE is a relational database management system originally developed
 * by Sybase and now owned by SAP. It uses Transact-SQL but differs
 * significantly from Microsoft SQL Server.
 *
 * Key ASE-specific features and limitations:
 * - T-SQL dialect (diverged from SQL Server decades ago)
 * - IDENTITY columns for auto-increment
 * - System catalogs: sysobjects, syscolumns, systypes, sysindexes
 * - No INFORMATION_SCHEMA views
 * - No ROW_NUMBER() or other window functions
 * - No multi-row INSERT VALUES (use INSERT...SELECT...UNION ALL)
 * - No MERGE statement for upserts
 * - No sequences
 * - TOP n for pagination (no OFFSET/FETCH, no TOP...START AT)
 * - ALTER TABLE MODIFY (not ALTER TABLE ALTER COLUMN)
 * - STR_REPLACE() (not REPLACE())
 * - CONVERT() for date formatting (not FORMAT())
 * - BIT type cannot be NULL
 * - SET IDENTITY_INSERT for explicit identity values
 * - sp_rename for renaming objects
 * - SAVE TRANSACTION / ROLLBACK TRANSACTION for savepoints
 *
 * REQUIREMENTS:
 * - PHP pdo_dblib extension (FreeTDS)
 * - SAP ASE 16.0+
 *
 * PDO_DBLIB QUIRKS:
 * - ATTR_EMULATE_PREPARES is always true (cannot be disabled)
 * - ATTR_SERVER_VERSION is not supported
 * - ATTR_AUTOCOMMIT is not supported
 * - execute([val]) sends all values as quoted strings — use bindValue()
 *   with explicit types for non-string columns
 * - rowCount() returns -1 for SELECT statements
 * - Multiple result sets from sp_help etc. must be fully consumed
 */
class AseDriver extends BaseSqlDriver
{
    /**
     * Check if this driver's PDO extension is available
     *
     * @return bool
     */
    public static function test()
    {
        return extension_loaded('pdo_dblib');
    }

    /**
     * The name of the database driver
     *
     * @var string
     */
    protected $name = 'ase';

    /**
     * The character(s) used to quote SQL statement names
     *
     * ASE uses square brackets for identifier quoting.
     *
     * @var  string|array
     */
    protected $nameQuote = ['[', ']'];

    /**
     * The wrapper format for quoting identifiers
     *
     * ASE uses square brackets [name] instead of double quotes "name".
     *
     * @var  string
     */
    protected $wrapper = '[%s]';

    /**
     * Cache of identity column names keyed by table name
     *
     * @var array<string, string|false>
     */
    protected $identityColumns = [];

    /**
     * Whether the _sequences emulation table has been verified/created
     *
     * @var bool
     */
    private $sequenceTableReady = false;

    /**
     * The current transaction depth (for savepoint support)
     *
     * @var int
     */
    protected $transactionDepth = 0;

    /**
     * Timeout in milliseconds for retrying queries blocked by async DDL
     *
     * ASE DDL operations (ALTER TABLE, etc.) can block subsequent queries on
     * the same table with "Retry your query later." This timeout controls
     * how long the exponential backoff retry loop will wait before giving up.
     *
     * @var int
     */
    protected $ddlRetryTimeoutMs = 30000;

    /**
     * Number of rows to skip on the next fetch (client-side OFFSET emulation)
     *
     * ASE has no OFFSET clause. TOP (offset+limit) fetches extra rows,
     * and this property tells loadObjectList() etc. to discard the first N.
     *
     * @var int
     */
    protected $pendingOffset = 0;

    /**
     * Client-side row limit for UNION queries
     *
     * ASE cannot apply TOP/LIMIT to UNION results, so we fetch all rows
     * and truncate client-side.
     *
     * @var int|null
     */
    protected $pendingLimit = null;

    /**
     * @inheritdoc
     */
    protected function resetDriverState(array $options = []): void
    {
        $this->pendingOffset = 0;
        $this->pendingLimit = null;
        $this->sequenceTableReady = false;
        $this->identityColumns = [];
    }

    /**
     * SQL expression for current timestamp
     *
     * @var string
     */
    protected string $nowExpression = 'GETDATE()';

    /**
     * SQL expression for random ordering
     *
     * @var string
     */
    protected string $randExpression = 'RAND()';

    /**
     * SQL function for string length
     *
     * @var string
     */
    protected string $lengthFunction = 'CHAR_LENGTH';

    /**
     * SQL function for IFNULL/COALESCE
     *
     * @var string
     */
    protected string $ifNullFunction = 'ISNULL';

    /**
     * Constructs a new database object based on the given params
     *
     * @param   array  $options  The database connection params
     * @return  void
     * @throws  ConnectionFailedException
     */
    public function __construct($options)
    {
        if (!isset($options['dsn'])) {
            $host = $options['host'] ?? '127.0.0.1';
            if (!empty($options['port'])) {
                $host .= ':' . $options['port'];
            }

            $options['dsn'] = "dblib:host={$host}";
            if (!empty($options['database'])) {
                $options['dsn'] .= ";dbname={$options['database']}";
            }
            if (!empty($options['charset'])) {
                $options['dsn'] .= ";charset={$options['charset']}";
            }
        }

        parent::__construct($options);

        // Override syntax: PDO_DBLIB reports 'dblib' but we need 'ase'
        // to resolve to the correct Syntax\Ase class
        $this->syntax = 'ase';

        // Set session options for compatibility
        try {
            $pdo = $this->getConnection();
            if ($pdo instanceof \Hubzero\Database\Connection\PdoConnection) {
                $pdo = $pdo->getPdo();
            }
            // Switch to silent mode to avoid PDO_DBLIB throwing on info messages
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);

            // Double-quoted identifiers
            $pdo->exec('SET QUOTED_IDENTIFIER ON');
            // ISO date format (YYYY-MM-DD)
            $pdo->exec('SET DATEFORMAT ymd');

            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $e) {
            // Non-fatal
        }
    }

    // =========================================================================
    // SQL Helper Methods
    // =========================================================================

    /**
     * Format a boolean value as a SQL literal
     *
     * ASE uses BIT type with 1/0 values.
     *
     * @param   bool  $value  The boolean value
     * @return  string
     */
    public function formatBooleanLiteral(bool $value): string
    {
        return $value ? '1' : '0';
    }

    /**
     * Returns the SQL expression for current timestamp in DDL DEFAULT
     *
     * ASE does not support CURRENT_TIMESTAMP as a default value;
     * use GETDATE() instead.
     *
     * @return  string
     */
    public function currentTimestampDefault(): string
    {
        return 'GETDATE()';
    }

    /**
     * Returns the SQL keyword for INSERT with ignore duplicates
     *
     * ASE doesn't have INSERT IGNORE.
     *
     * @return  string
     */
    public function sqlInsertIgnore(): string
    {
        return 'INSERT INTO';
    }

    /**
     * Returns the SQL keyword for REPLACE (upsert)
     *
     * ASE doesn't have REPLACE or MERGE.
     *
     * @return  string
     */
    public function sqlReplace(): string
    {
        return 'INSERT INTO';
    }

    /**
     * Returns whether the database supports REGEXP operator
     *
     * ASE does not have native REGEXP support.
     *
     * @return  bool
     */
    public function supportsRegexp(): bool
    {
        return false;
    }

    /**
     * Returns the SQL for a REGEXP-like comparison
     *
     * Uses PATINDEX as a fallback (SQL Server wildcards, not regex).
     *
     * @param   string  $column   The column to match
     * @param   string  $pattern  The pattern
     * @param   bool    $not      Whether to negate
     * @return  string
     */
    public function sqlRegexp(string $column, string $pattern, bool $not = false): string
    {
        $notStr = $not ? ' = 0' : ' > 0';
        return 'PATINDEX(' . $this->quote($pattern) . ', ' . $column . ')' . $notStr;
    }

    /**
     * Returns the SQL for date subtraction
     *
     * @param   string  $date   The date column or value
     * @param   int     $value  The interval value
     * @param   string  $unit   The interval unit
     * @return  string
     */
    public function sqlDateSub(string $date, int $value, string $unit = 'DAY'): string
    {
        $unit = strtolower($unit);
        return 'DATEADD(' . $unit . ', -' . $value . ', ' . $date . ')';
    }

    /**
     * Returns the SQL for date addition
     *
     * @param   string  $date   The date column or value
     * @param   int     $value  The interval value
     * @param   string  $unit   The interval unit
     * @return  string
     */
    public function sqlDateAdd(string $date, int $value, string $unit = 'DAY'): string
    {
        $unit = strtolower($unit);
        return 'DATEADD(' . $unit . ', ' . $value . ', ' . $date . ')';
    }

    /**
     * Returns the SQL for date formatting
     *
     * ASE does not have FORMAT(). Uses CONVERT() with style codes.
     *
     * @param   string  $date    The date column or value
     * @param   string  $format  The format string (MySQL format, converted)
     * @return  string
     */
    public function sqlDateFormat(string $date, string $format): string
    {
        // Map common MySQL format strings to CONVERT style codes
        $styleMap = [
            '%Y-%m-%d'          => 140,  // ISO date: 2026-02-14
            '%Y-%m-%d %H:%i:%s' => 140,  // ISO datetime
            '%m/%d/%Y'          => 101,  // US: 02/14/2026
            '%Y.%m.%d'          => 102,  // ANSI: 2026.02.14
            '%H:%i:%s'          => 108,  // Time: 18:43:36
            '%Y%m%d'            => 112,  // Compact: 20260214
        ];

        if (isset($styleMap[$format])) {
            return 'CONVERT(VARCHAR(30), ' . $date . ', ' . $styleMap[$format] . ')';
        }

        // For complex formats, extract parts individually with DATEPART
        // and concatenate. This handles the most common case: %Y-%m-%d
        return "CONVERT(VARCHAR(30), " . $date . ", 140)";
    }

    /**
     * Returns the SQL for extracting year from a date
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    public function sqlYear(string $date): string
    {
        return 'DATEPART(yy, ' . $date . ')';
    }

    /**
     * Returns the SQL for extracting month from a date
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    public function sqlMonth(string $date): string
    {
        return 'DATEPART(mm, ' . $date . ')';
    }

    /**
     * Returns the SQL for converting a date to Unix timestamp
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    public function sqlUnixTimestamp(string $date): string
    {
        return "DATEDIFF(second, '1970-01-01', " . $date . ')';
    }

    /**
     * Returns the SQL for extracting a substring based on a delimiter
     *
     * @param   string  $str    The string expression
     * @param   string  $delim  The delimiter
     * @param   int     $count  The occurrence count
     * @return  string
     */
    public function sqlSubstringIndex(string $str, string $delim, int $count): string
    {
        $quotedDelim = $this->quote($delim);

        if ($count === 1) {
            return 'LEFT(' . $str . ', CHARINDEX(' . $quotedDelim . ', ' . $str . ') - 1)';
        } elseif ($count === -1) {
            return 'RIGHT(' . $str . ', CHAR_LENGTH(' . $str . ') - CHAR_LENGTH(' . $quotedDelim
                . ') - CHARINDEX(' . $quotedDelim . ', REVERSE('
                . $str . ')) + 1)';
        }

        return 'LEFT(' . $str . ', CHARINDEX(' . $quotedDelim . ', ' . $str . ') - 1)';
    }

    /**
     * Returns the SQL for concatenating strings
     *
     * ASE uses + operator for string concatenation.
     *
     * @param   array  $strings  Array of expressions to concatenate
     * @return  string
     */
    public function sqlConcat(array $strings): string
    {
        $wrapped = array_map(fn($s) => "ISNULL($s, '')", $strings);
        return '(' . implode(' + ', $wrapped) . ')';
    }

    /**
     * Returns the SQL for concatenating strings with a separator
     *
     * @param   string  $separator  The separator string
     * @param   array   $strings    Array of strings to concatenate
     * @return  string
     */
    public function sqlConcatWs(string $separator, array $strings): string
    {
        $quotedSep = $this->quote($separator);
        $parts = [];
        foreach ($strings as $i => $str) {
            if ($i > 0) {
                $parts[] = $quotedSep;
            }
            $parts[] = "ISNULL($str, '')";
        }
        return '(' . implode(' + ', $parts) . ')';
    }

    /**
     * Quote a value for use in SQL
     *
     * ASE does not allow implicit VARCHAR→INT/DECIMAL conversion. When a
     * PHP int or float is passed, return the raw numeric string without
     * single quotes so ASE treats it as a numeric literal.
     *
     * @param   mixed  $text    The value to quote
     * @param   bool   $escape  True to escape
     * @return  string
     */
    public function quote($text, $escape = true)
    {
        if (is_int($text) || is_float($text)) {
            return (string) $text;
        }

        if ($text === null) {
            return 'NULL';
        }

        return parent::quote($text, $escape);
    }

    // =========================================================================
    // Schema Building Methods
    // =========================================================================

    /**
     * Quote a single identifier using square brackets
     *
     * @param   string  $identifier  The identifier to quote
     * @return  string
     */
    public function quoteIdentifier(string $identifier): string
    {
        return '[' . str_replace(']', ']]', $identifier) . ']';
    }

    /**
     * ASE does not support IF NOT EXISTS for CREATE TABLE
     *
     * @return  bool
     */
    public function supportsIfNotExists(): bool
    {
        return false;
    }

    /**
     * ASE does not support IF NOT EXISTS for CREATE INDEX
     *
     * @return  bool
     */
    public function supportsIfNotExistsForIndex(): bool
    {
        return false;
    }

    /**
     * Normalize a column type for ASE
     *
     * @param   string  $type       Column type
     * @param   array   $modifiers  Column modifiers
     * @return  string
     */
    public function normalizeColumnType(
        string $type,
        array $modifiers = []
    ): string {
        $grammar = $this->getSchemaGrammar();
        if ($grammar->getTypeMapping($type) !== null) {
            return parent::normalizeColumnType($type, $modifiers);
        }
        $lower = strtolower($type);
        if ($lower !== $type && $grammar->getTypeMapping($lower) !== null) {
            return parent::normalizeColumnType($lower, $modifiers);
        }

        // Strip UNSIGNED (ASE supports unsigned types but our type mapping doesn't use them)
        $cleaned = trim(str_ireplace(' UNSIGNED', '', $type));

        // Strip integer display widths
        if (preg_match('/^BIGINT(\(\d+\))?$/i', $cleaned)) {
            return 'BIGINT';
        }
        if (preg_match('/^INT(EGER)?(\(\d+\))?$/i', $cleaned)) {
            return 'INT';
        }
        if (preg_match('/^MEDIUMINT(\(\d+\))?$/i', $cleaned)) {
            return 'INT';
        }
        if (preg_match('/^SMALLINT(\(\d+\))?$/i', $cleaned)) {
            return 'SMALLINT';
        }
        if (preg_match('/^TINYINT(\(\d+\))?$/i', $cleaned)) {
            return 'TINYINT';
        }

        // BOOLEAN -> BIT
        if (
            strcasecmp($cleaned, 'BOOLEAN') === 0
            || strcasecmp($cleaned, 'BOOL') === 0
            || strcasecmp($cleaned, 'TINYINT(1)') === 0
        ) {
            return 'BIT';
        }

        // DATETIME / TIMESTAMP
        if (
            strcasecmp($cleaned, 'DATETIME') === 0
            || strcasecmp($cleaned, 'TIMESTAMP') === 0
        ) {
            return 'DATETIME';
        }

        // TEXT variants
        if (preg_match('/^(TINY|MEDIUM|LONG)?TEXT$/i', $cleaned)) {
            return 'TEXT';
        }

        // BLOB variants → IMAGE
        if (preg_match('/^(TINY|MEDIUM|LONG)?BLOB$/i', $cleaned)) {
            return 'IMAGE';
        }

        // SET/ENUM → VARCHAR(255)
        if (
            preg_match('/^SET\s*\(/i', $cleaned)
            || preg_match('/^ENUM\s*\(/i', $cleaned)
        ) {
            return 'VARCHAR(255)';
        }

        // DOUBLE → FLOAT
        if (preg_match('/^DOUBLE(\s*\([^)]+\))?$/i', $cleaned)) {
            return 'FLOAT';
        }

        // NVARCHAR(MAX) → TEXT (ASE doesn't have MAX)
        if (preg_match('/^NVARCHAR\s*\(\s*MAX\s*\)$/i', $cleaned)) {
            return 'TEXT';
        }

        return $cleaned;
    }

    /**
     * Map a MySQL-style column type to ASE's type
     *
     * @param   string  $type  The MySQL column type
     * @return  string
     */
    public function mapColumnType(string $type): string
    {
        $upper = strtoupper($type);
        $upper = preg_replace('/\s+UNSIGNED$/i', '', $upper);

        if ($upper === 'DOUBLE' || preg_match('/^DOUBLE\s*\([^)]+\)$/i', $upper)) {
            return 'FLOAT';
        }

        if ($upper === 'DATETIME' || $upper === 'TIMESTAMP') {
            return 'DATETIME';
        }

        if (preg_match('/^(TINY|MEDIUM|LONG)?TEXT$/i', $upper)) {
            return 'TEXT';
        }

        if (preg_match('/^(TINY|MEDIUM|LONG)?BLOB$/i', $upper)) {
            return 'IMAGE';
        }

        if ($upper === 'BOOLEAN' || $upper === 'BOOL' || $upper === 'TINYINT(1)') {
            return 'BIT';
        }

        if (preg_match('/^ENUM\s*\(/i', $upper)) {
            return 'VARCHAR(255)';
        }

        return $type;
    }

    /**
     * Build an auto-increment primary key column definition
     *
     * @param   string  $quotedName  The quoted column name
     * @param   string  $type        The column type
     * @return  string
     */
    public function buildAutoIncrementColumn(string $quotedName, string $type): string
    {
        return "$quotedName $type IDENTITY PRIMARY KEY";
    }

    /**
     * Build a UNIQUE constraint definition
     *
     * @param   string  $quotedName  The quoted constraint name
     * @param   string  $columnList  The column list SQL
     * @return  string
     */
    public function buildUniqueConstraint(string $quotedName, string $columnList): string
    {
        return "CONSTRAINT $quotedName UNIQUE ($columnList)";
    }

    // =========================================================================
    // Schema Introspection Methods
    // =========================================================================

    /**
     * Check if a table exists
     *
     * @param   string  $table  The table name
     * @return  string
     */
    protected function getTableExistsQuery(string $table): string
    {
        return "SELECT 1 FROM sysobjects WHERE name = "
            . $this->quote($table) . " AND type = 'U'";
    }

    /**
     * Retrieves field information about the given table
     *
     * @param   string  $table     The table name
     * @param   bool    $typeOnly  True to only return field types
     * @return  array
     */
    public function getTableColumns($table, $typeOnly = true)
    {
        $table = $this->replacePrefix($table);
        $this->setQuery(
            "SELECT c.name AS Field, t.name AS Type, c.length AS Length, "
            . "c.prec AS Prec, c.scale AS Scale, "
            . "CASE WHEN c.status & 8 = 8 THEN 'YES' ELSE 'NO' END AS [Null], "
            . "CASE WHEN c.status & 128 = 128 THEN 'YES' ELSE 'NO' END AS [Identity] "
            . "FROM syscolumns c "
            . "JOIN systypes t ON c.usertype = t.usertype "
            . "WHERE c.id = object_id(" . $this->quote($table) . ") "
            . "ORDER BY c.colid"
        );

        $columns = [];
        $results = $this->loadObjectList();

        foreach ($results as $column) {
            if ($typeOnly) {
                $columns[$column->Field] = $column->Type;
            } else {
                $columns[$column->Field] = (object) [
                    'Field'    => $column->Field,
                    'Type'     => $column->Type,
                    'Length'   => $column->Length,
                    'Prec'     => $column->Prec,
                    'Scale'    => $column->Scale,
                    'Null'     => $column->Null,
                    'Identity' => $column->Identity,
                ];
            }
        }

        return $columns;
    }

    /**
     * Retrieves key information about the given table
     *
     * @param   string  $table  A table name
     * @return  array
     */
    public function getTableKeys($table)
    {
        $table = $this->replacePrefix($table);

        // ASE sysindexes: indid > 0 AND indid < 255 = real indexes
        // status & 2 = UNIQUE, status & 2048 = PRIMARY KEY
        $this->setQuery(
            "SELECT i.name AS index_name, "
            . "index_col(" . $this->quote($table) . ", i.indid, 1) AS column_name, "
            . "CASE WHEN i.status & 2048 = 2048 THEN 1 ELSE 0 END AS is_primary_key, "
            . "CASE WHEN i.status & 2 = 2 THEN 1 ELSE 0 END AS is_unique, "
            . "1 AS key_ordinal "
            . "FROM sysindexes i "
            . "WHERE i.id = object_id(" . $this->quote($table) . ") "
            . "AND i.indid > 0 AND i.indid < 255 "
            . "AND i.name IS NOT NULL "
            . "ORDER BY i.name"
        );
        $indexes = $this->loadObjectList();

        $keys = [];
        foreach ($indexes as $index) {
            $keyName = $index->is_primary_key
                ? 'PRIMARY'
                : $index->index_name;

            $keys[$keyName] = (object) [
                'Key_name'     => $keyName,
                'Column_name'  => $index->column_name,
                'Non_unique'   => $index->is_unique ? 0 : 1,
                'Seq_in_index' => $index->key_ordinal,
            ];
        }

        return $keys;
    }

    /**
     * Gets foreign key information for a table
     *
     * @param   string  $table  The table name
     * @return  array
     */
    public function getForeignKeys($table)
    {
        $table = $this->replacePrefix($table);

        // ASE uses sysreferences for FK metadata
        // fokey1..16 and refkey1..16 are column IDs
        $sql = "SELECT "
            . "o.name AS constraint_name, "
            . "col_name(r.tableid, r.fokey1) AS column_name, "
            . "object_name(r.reftabid) AS referenced_table, "
            . "col_name(r.reftabid, r.refkey1) AS referenced_column "
            . "FROM sysreferences r "
            . "JOIN sysobjects o ON r.constrid = o.id "
            . "WHERE r.tableid = object_id(" . $this->quote($table) . ")";

        $this->setQuery($sql);

        return $this->groupForeignKeyRows($this->loadObjectList(), [
            'constraint_name' => 'constraint_name',
            'column_name'     => 'column_name',
            'foreign_table'   => 'referenced_table',
            'foreign_column'  => 'referenced_column',
            'on_update'       => fn() => 'NO ACTION',
            'on_delete'       => fn() => 'NO ACTION',
        ]);
    }

    /**
     * Gets index information for a table (normalized format)
     *
     * @param   string  $table  The table name
     * @return  array
     */
    public function getIndexes($table)
    {
        $table = $this->replacePrefix($table);
        $indexes = [];
        $qt = $this->quote($table);

        // Query sysindexes for all real indexes
        $this->setQuery(
            "SELECT i.name AS index_name, "
            . "CASE WHEN i.status & 2 = 2 THEN 1 ELSE 0 END AS is_unique, "
            . "CASE WHEN i.status & 2048 = 2048 THEN 1 ELSE 0 END AS is_primary_key, "
            . "i.indid "
            . "FROM sysindexes i "
            . "WHERE i.id = object_id($qt) "
            . "AND i.indid > 0 AND i.indid < 255 "
            . "AND i.name IS NOT NULL "
            . "ORDER BY i.name"
        );
        $results = $this->loadObjectList();

        foreach ($results as $row) {
            $indexName = $row->index_name;

            if (!isset($indexes[$indexName])) {
                $indexes[$indexName] = (object) [
                    'name'    => $indexName,
                    'columns' => [],
                    'unique'  => (bool) $row->is_unique,
                    'primary' => (bool) $row->is_primary_key,
                ];
            }

            // Get columns for this index using index_col()
            for ($pos = 1; $pos <= 16; $pos++) {
                $this->setQuery(
                    "SELECT index_col($qt, " . (int) $row->indid . ", $pos) AS col_name"
                );
                $colResult = $this->loadObject();
                if ($colResult && $colResult->col_name !== null) {
                    $indexes[$indexName]->columns[] = $colResult->col_name;
                } else {
                    break;
                }
            }
        }

        return array_values($indexes);
    }

    /**
     * Gets an array of all tables in the database
     *
     * @return  array
     */
    public function getTableList()
    {
        $this->setQuery("SELECT name FROM sysobjects WHERE type = 'U' ORDER BY name");
        return $this->loadColumn();
    }

    /**
     * Shows the table CREATE statement
     *
     * @param   string|array  $tables  A table name or list of names
     * @return  array
     */
    public function getTableCreate($tables)
    {
        if (!is_array($tables)) {
            $tables = [$tables];
        }

        $result = [];
        foreach ($tables as $table) {
            $result[$table] = $this->generateCreateTableSql($table);
        }

        return $result;
    }

    /**
     * Generate CREATE TABLE SQL from system catalogs
     *
     * @param   string  $table  Table name
     * @return  string
     */
    protected function generateCreateTableSql($table)
    {
        $table = $this->replacePrefix($table);
        $columns = $this->getTableColumns($table, false);

        if (empty($columns)) {
            return "-- Table '$table' not found";
        }

        // Get column default values from system tables
        $defaults = $this->getColumnDefaults($table);

        $columnDefs = [];
        foreach ($columns as $col) {
            $def = '[' . $col->Field . '] ' . strtoupper($col->Type);

            // Add length/precision for applicable types
            $type = strtolower($col->Type);
            if (in_array($type, ['varchar', 'char', 'nvarchar', 'nchar', 'varbinary'])) {
                $def .= '(' . $col->Length . ')';
            } elseif (in_array($type, ['decimal', 'numeric']) && $col->Prec) {
                $def .= '(' . $col->Prec . ',' . $col->Scale . ')';
            }

            if ($col->Identity === 'YES') {
                $def .= ' IDENTITY';
            }

            // Add DEFAULT if present
            if (isset($defaults[$col->Field])) {
                $def .= ' DEFAULT ' . $defaults[$col->Field];
            }

            $def .= ($col->Null === 'YES') ? ' NULL' : ' NOT NULL';

            $columnDefs[] = $def;
        }

        // Get primary key
        $pkCols = $this->getPrimaryKeyColumns($table);
        if (!empty($pkCols)) {
            $columnDefs[] = 'PRIMARY KEY ([' . implode('], [', $pkCols) . '])';
        }

        $createSql = "CREATE TABLE [" . $table . "] (\n    ";
        $createSql .= implode(",\n    ", $columnDefs);
        $createSql .= "\n)";

        return $createSql;
    }

    /**
     * Get column default values from ASE system tables
     *
     * @param   string  $table  The table name
     * @return  array   Column name => default value expression
     */
    protected function getColumnDefaults(string $table): array
    {
        $this->setQuery(
            "SELECT c.name AS col_name, cm.text AS default_text "
            . "FROM syscolumns c "
            . "JOIN sysobjects d ON c.cdefault = d.id AND d.type = 'D' "
            . "JOIN syscomments cm ON d.id = cm.id "
            . "WHERE c.id = object_id(" . $this->quote($table) . ")"
        );

        $rows = $this->loadObjectList();
        $defaults = [];

        if ($rows) {
            foreach ($rows as $row) {
                // syscomments stores "DEFAULT  value" — extract just the value
                $text = trim($row->default_text);
                if (preg_match('/^DEFAULT\s+(.+)$/i', $text, $m)) {
                    $defaults[$row->col_name] = trim($m[1]);
                }
            }
        }

        return $defaults;
    }

    /**
     * Gets the database collation
     *
     * @return  string|bool
     */
    public function getCollation()
    {
        $this->setQuery("SELECT @@sortorder AS collation");
        $result = $this->loadObject();
        return $result ? $result->collation : false;
    }

    /**
     * Gets the primary key of a table
     *
     * @param   string  $table  The table name
     * @return  string|bool
     */
    public function getPrimaryKey($table)
    {
        $table = $this->replacePrefix($table);

        // Find PK index (status & 2048) and get first column
        $this->setQuery(
            "SELECT index_col(" . $this->quote($table) . ", i.indid, 1) AS pk_col "
            . "FROM sysindexes i "
            . "WHERE i.id = object_id(" . $this->quote($table) . ") "
            . "AND i.status & 2048 = 2048"
        );
        return $this->loadResult();
    }

    /**
     * Get primary key column names
     *
     * @param   string  $table  The table name
     * @return  array
     */
    public function getPrimaryKeyColumns($table): array
    {
        $table = $this->replacePrefix($table);
        $qt = $this->quote($table);

        // Find PK index
        $this->setQuery(
            "SELECT i.indid FROM sysindexes i "
            . "WHERE i.id = object_id($qt) AND i.status & 2048 = 2048"
        );
        $indid = $this->loadResult();

        if (!$indid) {
            return [];
        }

        // Get all columns for this index
        $cols = [];
        for ($pos = 1; $pos <= 16; $pos++) {
            $this->setQuery("SELECT index_col($qt, $indid, $pos) AS col_name");
            $result = $this->loadResult();
            if ($result !== null && $result !== false) {
                $cols[] = $result;
            } else {
                break;
            }
        }

        return $cols;
    }

    /**
     * Gets the database engine
     *
     * @param   string  $table  The table name
     * @return  string
     */
    public function getEngine($table)
    {
        return 'SAP ASE';
    }

    /**
     * Gets the character set
     *
     * @param   string  $table  The table name
     * @param   string  $field  Optional field name
     * @return  string|bool
     */
    public function getCharacterSet($table, $field = null)
    {
        $this->setQuery("SELECT @@client_csname AS charset");
        $result = $this->loadObject();
        return $result ? $result->charset : false;
    }

    /**
     * Gets the auto-increment value for the given table
     *
     * @param   string  $table  The table name
     * @return  int|bool
     */
    public function getAutoIncrement($table)
    {
        $table = $this->replacePrefix($table);

        // ASE doesn't have IDENT_CURRENT(). Query the identity column's
        // max value from the table and add 1.
        $identityCol = $this->getIdentityColumn($table);
        if (!$identityCol) {
            return false;
        }

        $this->setQuery(
            "SELECT MAX(" . $this->quoteName($identityCol) . ") AS max_id FROM "
            . $this->quoteName($table)
        );
        $result = $this->loadObject();

        if ($result && $result->max_id !== null) {
            return (int) $result->max_id + 1;
        }

        return 1;
    }

    /**
     * Sets the auto-increment starting value
     *
     * ASE doesn't have DBCC CHECKIDENT. Use SET IDENTITY_INSERT
     * with an explicit insert, then delete.
     *
     * @param   string  $table  The table name
     * @param   int     $value  The auto-increment starting value
     * @return  bool
     */
    public function setAutoIncrement($table, $value): bool
    {
        $table = $this->replacePrefix($table);
        $identityCol = $this->getIdentityColumn($table);

        if (!$identityCol) {
            return false;
        }

        $qt = $this->quoteName($table);
        $qc = $this->quoteName($identityCol);
        $seedId = (int) $value - 1;

        if ($seedId < 1) {
            return true;
        }

        // Check current max identity — if already >= seedId, no action needed.
        // ASE identity counter = max ever inserted, next value = max + 1.
        $this->setQuery("SELECT MAX({$qc}) FROM {$qt}");
        $currentMax = (int) $this->loadResult();
        if ($currentMax >= $seedId) {
            return true;
        }

        // Insert a row with the target ID - 1, then delete it.
        // The next auto-generated ID will be $value.
        $this->execRawSilent("SET IDENTITY_INSERT {$qt} ON");
        try {
            $this->setQuery("INSERT INTO {$qt} ({$qc}) VALUES ({$seedId})");
            $this->execute();
            $this->setQuery("DELETE FROM {$qt} WHERE {$qc} = {$seedId}");
            $this->execute();
        } finally {
            $this->execRawSilent("SET IDENTITY_INSERT {$qt} OFF");
        }

        return true;
    }

    /**
     * Locks a table
     *
     * @param   string  $table  The table name
     * @return  $this
     */
    public function lockTable($table)
    {
        // ASE uses table hints for locking, this is a no-op
        return $this;
    }

    /**
     * Unlocks all tables
     *
     * @return  $this
     */
    public function unlockTables()
    {
        return $this;
    }

    /**
     * Renames a table
     *
     * @param   string  $oldTable  Old table name
     * @param   string  $newTable  New table name
     * @param   string  $backup    Table prefix
     * @param   string  $prefix    For the table
     * @return  $this
     */
    public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
    {
        $oldTable = $this->replacePrefix($oldTable);
        $newTable = $this->replacePrefix($newTable);

        $this->setQuery("EXEC sp_rename " . $this->quote($oldTable) . ", " . $this->quote($newTable));
        $this->execute();

        // sp_rename returns result sets that must be consumed
        $this->consumeResultSets();

        return $this;
    }

    // =========================================================================
    // Transaction Methods
    // =========================================================================

    /**
     * Commits a transaction
     *
     * Supports nested transactions via savepoints.
     *
     * @return  void
     */
    public function transactionCommit()
    {
        if ($this->transactionDepth <= 0) {
            return;
        }

        $this->transactionDepth--;

        if ($this->transactionDepth == 0) {
            $this->getConnection()->commit();
        }
    }

    /**
     * Rolls back a transaction
     *
     * Supports nested transactions via savepoints.
     *
     * @return  void
     */
    public function transactionRollback()
    {
        if ($this->transactionDepth <= 0) {
            return;
        }

        $this->transactionDepth--;

        if ($this->transactionDepth == 0) {
            $this->getConnection()->rollBack();
        } else {
            $this->setQuery('ROLLBACK TRANSACTION SP_' . $this->transactionDepth);
            $this->execute();
        }
    }

    /**
     * Initializes a transaction
     *
     * Supports nested transactions via savepoints.
     *
     * @return  void
     */
    public function transactionStart()
    {
        if ($this->transactionDepth == 0) {
            $this->getConnection()->beginTransaction();
        } else {
            $this->setQuery('SAVE TRANSACTION SP_' . $this->transactionDepth);
            $this->execute();
        }

        $this->transactionDepth++;
    }

    // =========================================================================
    // Execution Methods
    // =========================================================================

    /**
     * Bind parameters to the prepared statement
     *
     * PDO_DBLIB with emulated prepares quotes ALL bound values as strings,
     * ignoring PDO::PARAM_INT hints. ASE does not allow implicit conversion
     * from VARCHAR to INT, DECIMAL, NUMERIC, or other numeric types.
     *
     * Fix: inline all numeric values (PHP int, float, and numeric strings)
     * directly into the SQL, replacing their ? placeholders with the raw
     * numeric values. Numeric strings (e.g., "1", "3.14") are also inlined
     * because the ORM often returns IDs and counts as strings from previous
     * database queries.
     *
     * @param   array  $bindings  Parameter bindings
     * @param   array  $type      Optional explicit types
     * @return  $this
     */
    public function bind($bindings, $type = [])
    {
        if (!$this->statement || empty($bindings)) {
            return parent::bind($bindings, $type);
        }

        // Check if any numeric bindings exist (int, float, or numeric string)
        $hasNumeric = false;
        foreach ($bindings as $binding) {
            if ($this->isNumericBinding($binding)) {
                $hasNumeric = true;
                break;
            }
        }

        if (!$hasNumeric) {
            return parent::bind($bindings, $type);
        }

        $sql = $this->statement->queryString;
        $newBindings = [];
        $newTypes = [];
        $offset = 0;
        $typeIdx = 1;
        $newTypeIdx = 1;

        foreach ($bindings as $binding) {
            $pos = strpos($sql, '?', $offset);
            if ($pos === false) {
                break;
            }

            if ($this->isNumericBinding($binding)) {
                // Inline the numeric value directly into SQL
                $replacement = (string) $binding;
                $sql = substr_replace($sql, $replacement, $pos, 1);
                $offset = $pos + strlen($replacement);
            } else {
                // Keep this ? placeholder
                $newBindings[] = $binding;
                if (isset($type[$typeIdx])) {
                    $newTypes[$newTypeIdx] = $type[$typeIdx];
                }
                $newTypeIdx++;
                $offset = $pos + 1;
            }
            $typeIdx++;
        }

        // Re-prepare with the modified SQL
        $this->prepare($sql);
        return parent::bind($newBindings, $newTypes);
    }

    /**
     * Check if a binding value should be inlined as a numeric literal
     *
     * PHP int and float are always inlined. Numeric strings (e.g., "1",
     * "19.99") are inlined only for non-INSERT queries to avoid
     * NUMERIC→VARCHAR conversion errors when inserting into VARCHAR columns.
     * In WHERE/UPDATE/DELETE contexts, numeric strings must be inlined
     * because ASE rejects implicit VARCHAR→INT conversion.
     *
     * @param   mixed  $value  The binding value
     * @return  bool
     */
    protected function isNumericBinding($value): bool
    {
        if (is_int($value) || is_float($value)) {
            return true;
        }

        // For non-INSERT statements, also inline numeric strings
        if (is_string($value) && $value !== '' && $this->statement) {
            $sql = ltrim($this->statement->queryString);
            if (stripos($sql, 'INSERT') !== 0) {
                return (bool) preg_match('/^-?\d+(\.\d+)?$/', $value);
            }
        }

        return false;
    }

    /**
     * Execute a SQL statement directly
     *
     * PDO_DBLIB with ERRMODE_EXCEPTION throws on ASE informational
     * messages (severity < 10), like "Changed database context". These
     * are not real errors but prevent PDO::exec() from completing.
     *
     * Instead of using PDO::exec() directly, we route through the
     * setQuery()/execute() path which uses prepared statements and
     * handles these informational messages correctly.
     *
     * @param   string  $statement  The SQL statement
     * @return  int     Number of affected rows
     */
    public function exec($statement)
    {
        $this->hasConnectionOrFail();

        $sql = $this->replacePrefix($statement);
        $start = microtime(true);

        $this->setQuery($sql);
        $this->execute();

        $affected = $this->statement ? $this->statement->rowCount() : 0;

        if ($this->debug || $this->slowQueryThreshold > 0) {
            $this->log(microtime(true) - $start);
        }

        return $affected < 0 ? 0 : $affected;
    }

    /**
     * Get the last auto-increment ID
     *
     * PDO::lastInsertId() returns '0' for PDO_DBLIB.
     * Use ASE's @@identity global variable instead.
     *
     * @return  int
     */
    public function insertid()
    {
        try {
            $stmt = $this->getConnection()->query('SELECT @@identity');
            $result = $stmt->fetchColumn();
            return $result !== false ? (int) $result : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Set the number of rows to skip on the next fetch
     *
     * Called by Syntax\Ase::buildSelect() when offset pagination is needed.
     *
     * @param   int  $offset  Number of rows to skip
     * @return  void
     */
    public function setPendingOffset(int $offset): void
    {
        $this->pendingOffset = $offset;
    }

    /**
     * Set a client-side row limit for the next fetch
     *
     * Called by Syntax\Ase::buildSelect() when UNION + LIMIT is needed
     * (ASE cannot apply TOP to UNION results).
     *
     * @param   int|null  $limit  Max rows to return, or null for no limit
     * @return  void
     */
    public function setPendingLimit(?int $limit): void
    {
        $this->pendingLimit = $limit;
    }

    /**
     * Consume and reset pending offset and limit
     *
     * @return  array{int, int|null}  [offset, limit]
     */
    public function consumePendingPagination(): array
    {
        $offset = $this->pendingOffset;
        $limit = $this->pendingLimit;
        $this->pendingOffset = 0;
        $this->pendingLimit = null;
        return [$offset, $limit];
    }

    /**
     * Materialize a subquery with OFFSET into a temp table
     *
     * ASE has no OFFSET clause and forbids ORDER BY in derived tables.
     * This method executes the subquery into a temp table, deletes the
     * first $offset rows using SET ROWCOUNT, and returns a simple
     * SELECT from the temp table.
     *
     * @param   string  $sql       The subquery SQL (with TOP and ORDER BY)
     * @param   array   $bindings  Parameter bindings for the subquery
     * @param   int     $offset    Number of rows to skip
     * @param   int     $limit     Number of rows to return
     * @return  string  SQL referencing the temp table
     */
    public function materializeOffsetSubquery(
        string $sql,
        array $bindings,
        int $offset,
        int $limit
    ): string {
        // Generate unique temp table name (# prefix = ASE session temp table).
        // Temp table names must NOT be bracket-quoted — ASE rejects # as the
        // first character inside [quoted identifiers].
        $tempName = '#ase_sub_' . substr(md5(uniqid('', true)), 0, 8);

        // Insert INTO #temp by placing INTO before FROM
        $intoSql = preg_replace('/\bFROM\b/i', "INTO $tempName FROM", $sql, 1);

        // Execute the SELECT INTO with any bindings
        $this->setQuery($intoSql);
        if (!empty($bindings)) {
            foreach ($bindings as $binding) {
                $this->bind($binding);
            }
        }
        $this->execute();

        // Delete the first $offset rows using SET ROWCOUNT
        $this->setQuery("SET ROWCOUNT $offset DELETE FROM $tempName SET ROWCOUNT 0");
        $this->execute();

        // Return a simple SELECT from the temp table
        return "SELECT * FROM $tempName";
    }

    /**
     * Apply client-side offset and limit to a result array
     *
     * @param   array     $result      The result array
     * @param   int       $offset      Rows to skip
     * @param   int|null  $limit       Max rows, or null
     * @param   bool      $preserveKeys  Whether to preserve array keys
     * @return  array
     */
    protected function applyClientPagination(array $result, int $offset, ?int $limit, bool $preserveKeys = false): array
    {
        if ($offset > 0 || $limit !== null) {
            $result = array_slice($result, $offset, $limit, $preserveKeys);
        }
        return $result;
    }

    /**
     * Load a list of result objects
     *
     * Overrides parent to apply client-side offset/limit for ASE
     * pagination (ASE has no OFFSET clause).
     *
     * @param   string  $key    Column to key results by
     * @param   string  $class  Class name for result objects
     * @return  array|null
     */
    public function loadObjectList($key = '', $class = 'stdClass')
    {
        [$offset, $limit] = $this->consumePendingPagination();

        $result = parent::loadObjectList($key, $class);

        if ($result !== null && ($offset > 0 || $limit !== null)) {
            $result = $this->applyClientPagination($result, $offset, $limit, !empty($key));
        }

        return $result;
    }

    /**
     * Load a list of associative arrays
     *
     * @param   string|null  $key     Column to key results by
     * @param   string|null  $column  Column to extract
     * @return  array|null
     */
    public function loadAssocList($key = null, $column = null)
    {
        [$offset, $limit] = $this->consumePendingPagination();

        $result = parent::loadAssocList($key, $column);

        if ($result !== null && ($offset > 0 || $limit !== null)) {
            $result = $this->applyClientPagination($result, $offset, $limit, !empty($key));
        }

        return $result;
    }

    /**
     * Load a single column from result set
     *
     * @param   int  $offset  Column index (NOT row offset)
     * @return  array|null
     */
    public function loadColumn($offset = 0)
    {
        [$rowOffset, $limit] = $this->consumePendingPagination();

        $result = parent::loadColumn($offset);

        if ($result !== null && ($rowOffset > 0 || $limit !== null)) {
            $result = $this->applyClientPagination($result, $rowOffset, $limit);
        }

        return $result;
    }

    /**
     * Execute the current query, auto-toggling IDENTITY_INSERT when needed
     *
     * @return  $this|int
     * @throws  QueryFailedException
     */
    public function execute()
    {
        // Transform ASE-incompatible SQL constructs before execution
        $this->transformSql();

        // PDO_DBLIB emulated prepares quote ALL bound values as strings,
        // including integers and floats. ASE rejects implicit VARCHAR→INT
        // conversion in INSERT...VALUES context, causing silent data loss.
        // Manually substitute bindings with proper typing before PDO sees them.
        $this->substituteBindings();

        $identityTable = $this->detectIdentityInsert();

        if ($identityTable) {
            $quoted = $this->quoteName($identityTable);
            $this->execRawSilent("SET IDENTITY_INSERT {$quoted} ON");
        }

        try {
            $result = $this->executeWithRetry();
        } finally {
            // PDO_DBLIB leaves pending result sets after every statement
            // execution. We must close the cursor for non-SELECT queries
            // to consume these pending results. Without this, subsequent
            // operations silently fail with "results pending" errors.
            if ($this->statement) {
                $sql = $this->statement->queryString ?? '';
                $firstWord = strtoupper(substr(ltrim($sql), 0, 6));
                if ($firstWord !== 'SELECT') {
                    $this->statement->closeCursor();
                }
            }

            if ($identityTable) {
                $this->execRawSilent("SET IDENTITY_INSERT {$quoted} OFF");
            }
        }

        return $result;
    }

    /**
     * Execute with retry for ASE async DDL conflicts
     *
     * ASE DDL operations can be asynchronous. Subsequent queries on the
     * same table may fail with "ALTER TABLE operation is in progress...
     * Retry your query later." This method retries with exponential backoff
     * (50ms, 100ms, 200ms, 400ms, ...) up to a 10-second timeout.
     *
     * @return  mixed
     * @throws  QueryFailedException
     */
    protected function executeWithRetry()
    {
        $timeoutMs = $this->ddlRetryTimeoutMs;
        $delayMs   = 50;  // initial delay: 50ms
        $startTime = microtime(true);

        while (true) {
            $result = parent::execute();

            // PDO_DBLIB bug: PDOStatement::execute() returns true even on
            // SQL errors. Check errorInfo() for real errors.
            if ($this->statement) {
                $errorInfo = $this->statement->errorInfo();
                $severity = $errorInfo[4] ?? 0;
                if (isset($errorInfo[1]) && $errorInfo[1] !== 0 && $errorInfo[1] !== null && $severity >= 16) {
                    $sql = $this->statement->queryString ?? '';
                    $msg = ($errorInfo[2] ?? 'Unknown error') . " [{$sql}]";

                    // Retryable: "ALTER TABLE operation is in progress... Retry your query later."
                    $elapsedMs = (microtime(true) - $startTime) * 1000;
                    if (stripos($msg, 'Retry your query later') !== false && $elapsedMs < $timeoutMs) {
                        usleep($delayMs * 1000);
                        $delayMs = min($delayMs * 2, 2000); // double, cap at 2s
                        // Re-prepare the statement for retry
                        $this->statement->closeCursor();
                        $this->prepare($sql);
                        continue;
                    }

                    $pdoEx = new \PDOException($msg, 0);
                    $pdoEx->errorInfo = $errorInfo;
                    throw new \Hubzero\Database\Exception\QueryFailedException($msg, 0, $pdoEx);
                }
            }

            // Success
            return $result;
        }
    }

    /**
     * Transform SQL to replace ASE-incompatible constructs
     *
     * Handles:
     * - REPLACE() → STR_REPLACE() (ASE uses STR_REPLACE for string replacement)
     * - ORDER BY in derived tables (subqueries) — stripped because ASE forbids it
     * - ON DELETE/ON UPDATE referential actions — stripped because ASE 16.x
     *   does not support them
     *
     * @return  void
     */
    protected function transformSql(): void
    {
        if (!$this->statement || !isset($this->statement->queryString)) {
            return;
        }

        $sql = $this->statement->queryString;
        $changed = false;

        // Replace REPLACE( with STR_REPLACE( — but only the function call,
        // not the REPLACE keyword used in INSERT REPLACE context
        if (stripos($sql, 'REPLACE(') !== false) {
            $transformed = preg_replace(
                '/\bREPLACE\s*\(/i',
                'STR_REPLACE(',
                $sql
            );
            if ($transformed !== $sql) {
                $sql = $transformed;
                $changed = true;
            }
        }

        // Remove ORDER BY from derived tables (subqueries used as FROM source).
        // ASE forbids ORDER BY in derived tables. We strip the ORDER BY from
        // inside parenthesized subqueries that end with ") AS alias".
        if (preg_match('/ORDER\s+BY\b.*?\)\s*AS\s/is', $sql)) {
            $sql = $this->stripOrderByFromDerivedTables($sql);
            $changed = true;
        }

        // FK referential actions — ASE 16.x does not support them.
        // Throw for non-NO ACTION actions; silently strip NO ACTION (default behavior).
        if (preg_match('/\bON\s+(DELETE|UPDATE)\s/i', $sql)) {
            if (preg_match('/\bON\s+(DELETE|UPDATE)\s+(CASCADE|SET\s+NULL|SET\s+DEFAULT|RESTRICT)\b/i', $sql, $m)) {
                throw new \RuntimeException(
                    'ASE does not support FK referential actions '
                    . "(ON {$m[1]} {$m[2]}). "
                    . 'Use NO ACTION or implement cascade logic in application code.'
                );
            }
            // Strip harmless ON DELETE NO ACTION / ON UPDATE NO ACTION
            $transformed = preg_replace(
                '/\s+ON\s+(DELETE|UPDATE)\s+NO\s+ACTION\b/i',
                '',
                $sql
            );
            if ($transformed !== $sql) {
                $sql = $transformed;
                $changed = true;
            }
        }

        if ($changed) {
            $this->prepare($sql);
        }
    }

    /**
     * Remove ORDER BY clauses from derived tables in SQL
     *
     * Walks parentheses depth to find subqueries used as derived tables
     * and strips their ORDER BY clauses.
     *
     * @param   string  $sql  The SQL string
     * @return  string
     */
    protected function stripOrderByFromDerivedTables(string $sql): string
    {
        // Find parenthesized subqueries followed by ) AS alias
        // Strategy: find the closing ) AS pattern and work backwards to find
        // the matching opening ( and strip ORDER BY within
        $result = preg_replace_callback(
            '/\((\s*SELECT\b.+?)\)\s*(AS\s)/is',
            function ($match) {
                // Remove ORDER BY ... from inside the subquery
                $inner = preg_replace('/\s+ORDER\s+BY\s+[^)]+$/is', '', $match[1]);
                return '(' . $inner . ') ' . $match[2];
            },
            $sql
        );

        return $result ?? $sql;
    }

    /**
     * Manually substitute PDO bindings with properly typed inline values
     *
     * PDO_DBLIB's emulated prepares quote ALL bound values as strings,
     * even integers. ASE rejects implicit VARCHAR→INT conversion in
     * INSERT...VALUES context (and sometimes elsewhere), causing silent
     * data loss. This method bypasses PDO's broken quoting by replacing
     * ? placeholders with properly typed inline values.
     *
     * @return  void
     */
    protected function substituteBindings(): void
    {
        if (empty($this->bindings) || !$this->statement) {
            return;
        }

        $sql = $this->statement->queryString ?? '';
        if ($sql === '') {
            return;
        }

        // Split SQL on ? placeholders. The count of parts minus 1
        // should equal the number of bindings.
        $parts = explode('?', $sql);
        if (count($parts) - 1 !== count($this->bindings)) {
            // Mismatch — let PDO handle it (or fail)
            return;
        }

        // Rebuild SQL with inline values
        $result = $parts[0];
        foreach ($this->bindings as $i => $value) {
            $result .= $this->inlineValue($value) . $parts[$i + 1];
        }

        // Clear bindings and re-prepare with substituted SQL
        $this->bindings = [];
        $this->prepare($result);
    }

    /**
     * Convert a PHP value to an inline SQL literal with proper typing
     *
     * @param   mixed  $value  The PHP value
     * @return  string  SQL literal
     */
    protected function inlineValue($value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_int($value)) {
            return (string) $value;
        }

        if (is_float($value)) {
            return (string) $value;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        // String value — use PDO::quote for proper escaping
        return $this->quote((string) $value);
    }

    /**
     * Execute a SQL statement directly on raw PDO with ERRMODE_SILENT
     *
     * PDO_DBLIB throws on ASE informational messages in ERRMODE_EXCEPTION.
     * This helper temporarily switches to ERRMODE_SILENT for session SET
     * commands and other statements where we need to bypass that issue.
     *
     * @param   string  $sql  The SQL statement
     * @return  int|false
     */
    protected function execRawSilent(string $sql)
    {
        $pdo = $this->getConnection();
        if ($pdo instanceof \Hubzero\Database\Connection\PdoConnection) {
            $pdo = $pdo->getPdo();
        }

        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        $result = $pdo->exec($sql);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $result;
    }

    /**
     * Detect if the current statement needs IDENTITY_INSERT
     *
     * Handles two forms:
     * 1. INSERT INTO table (col, ...) VALUES (...)
     * 2. INSERT INTO table SELECT col, ... FROM ...
     *    (For this form, also rewrites to add column list per ASE requirement)
     *
     * @return  string|null  Table name if toggle needed, null otherwise
     */
    protected function detectIdentityInsert(): ?string
    {
        if (!$this->statement || !isset($this->statement->queryString)) {
            return null;
        }
        $sql = trim($this->statement->queryString);
        if (stripos($sql, 'INSERT') !== 0) {
            return null;
        }

        $ident = '(?:"([^"]+)"|\[([^\]]+)\]|([\w.]+))';

        // Form 1: INSERT INTO table (columns) VALUES/SELECT
        $pattern = '/INSERT\s+INTO\s+' . $ident . '\s*\(([^)]+)\)/i';
        if (preg_match($pattern, $sql, $matches)) {
            $table = $matches[1] ?: ($matches[2] ?: $matches[3]);
            $columns = array_map(function ($c) {
                return strtolower(trim(trim($c), '"[] '));
            }, explode(',', $matches[4]));

            $identityCol = $this->getIdentityColumn($table);
            if ($identityCol && in_array(strtolower($identityCol), $columns)) {
                return $table;
            }
        }

        // Form 2: INSERT INTO table SELECT col,... FROM ...
        // ASE requires a column list when IDENTITY_INSERT is ON.
        // Without a column list, ASE maps SELECT columns positionally to
        // the destination table's columns. If the identity column is among
        // those positions, we must add the column list and enable IDENTITY_INSERT.
        $patternSelect = '/INSERT\s+INTO\s+' . $ident . '\s+SELECT\s+/i';
        if (preg_match($patternSelect, $sql, $matches)) {
            $table = $matches[1] ?: ($matches[2] ?: $matches[3]);
            $identityCol = $this->getIdentityColumn($table);

            if ($identityCol) {
                // Get destination table's column list for positional mapping
                $destColumns = array_keys($this->getTableColumns($table));
                $numDestCols = count($destColumns);

                // Extract select column expressions (before FROM)
                $selectPart = preg_replace(
                    '/INSERT\s+INTO\s+' . $ident . '\s+SELECT\s+/i',
                    '',
                    $sql
                );
                if (preg_match('/^(.+?)\s+FROM\s+/is', $selectPart, $selMatch)) {
                    $selectExprs = array_map('trim', explode(',', $selMatch[1]));
                    $numSelectCols = count($selectExprs);

                    // Check if identity column is within the positional range
                    $identityLower = strtolower($identityCol);
                    $identityInRange = false;
                    for ($i = 0; $i < min($numSelectCols, $numDestCols); $i++) {
                        if (strtolower($destColumns[$i]) === $identityLower) {
                            $identityInRange = true;
                            break;
                        }
                    }

                    if ($identityInRange) {
                        // Rewrite to add column list using destination table's columns
                        $quotedTable = $this->quoteName($table);
                        $destColList = [];
                        for ($i = 0; $i < $numSelectCols && $i < $numDestCols; $i++) {
                            $destColList[] = $this->quoteName($destColumns[$i]);
                        }
                        $colListStr = implode(', ', $destColList);

                        // Rebuild: INSERT INTO table (dest_cols) SELECT ...
                        $selectPos = stripos($sql, 'SELECT');
                        $newSql = "INSERT INTO {$quotedTable} ({$colListStr}) "
                            . substr($sql, $selectPos);
                        $this->prepare($newSql);
                        return $table;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Get the IDENTITY column name for a table, with caching
     *
     * @param   string       $table  Table name
     * @return  string|null
     */
    public function getIdentityColumn(string $table): ?string
    {
        if (!array_key_exists($table, $this->identityColumns)) {
            $this->identityColumns[$table] = $this->queryIdentityColumn($table);
        }

        $result = $this->identityColumns[$table];
        return $result === false ? null : $result;
    }

    /**
     * Query syscolumns for the IDENTITY column of a table
     *
     * @param   string        $table  Table name
     * @return  string|false
     */
    protected function queryIdentityColumn(string $table)
    {
        try {
            $stmt = $this->getConnection()->prepare(
                "SELECT c.name FROM syscolumns c "
                . "WHERE c.id = object_id(?) "
                . "AND c.status & 128 = 128"
            );
            $stmt->execute([$table]);
            $result = $stmt->fetchColumn();
            return $result !== false ? $result : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Consume any remaining result sets from the current statement
     *
     * ASE sp_rename and other system procs return multiple result sets.
     * If not consumed, the next query will fail with "results pending".
     *
     * @return  void
     */
    protected function consumeResultSets(): void
    {
        if (!$this->statement) {
            return;
        }

        try {
            while ($this->statement->nextRowset()) {
                // Consume
            }
        } catch (\Exception $e) {
            // Ignore - may not have more result sets
        }
    }

    // =========================================================================
    // Version & Server Info
    // =========================================================================

    /**
     * Gets the version of the database
     *
     * @return  string
     */
    public function getVersion()
    {
        $full = $this->getFullVersion();
        if ($full && preg_match('/(\d+\.\d+(\.\d+)?)/', $full, $m)) {
            return $m[1];
        }
        return 'Unknown';
    }

    /**
     * Get the full version string
     *
     * @return  string|null
     */
    public function getFullVersion()
    {
        $this->setQuery("SELECT @@version AS version");
        $result = $this->loadObject();
        return $result ? $result->version : null;
    }

    /**
     * Get database server version information
     *
     * @return  array
     */
    public function getServerInfo()
    {
        $version = $this->getVersion();
        $fullVersion = $this->getFullVersion();

        return [
            'version'        => $version,
            'driver_version' => $version,
            'ase_version'    => $version,
            'full_version'   => $fullVersion,
            'comment'        => 'SAP ASE',
        ];
    }

    /**
     * Selects a database for use
     *
     * @param   string  $database  The database name
     * @return  bool
     */
    public function select($database)
    {
        $this->setQuery('USE ' . $this->quoteName($database));
        $this->execute();
        return true;
    }

    /**
     * Sets the connection to use UTF-8 character encoding
     *
     * @return  bool
     */
    public function setUTF()
    {
        return true;
    }

    /**
     * Truncate a table
     *
     * @param   string  $table   The table to truncate
     * @param   int     $nextId  The next auto-increment value
     * @return  $this
     */
    public function truncateTable($table, int $nextId = 1)
    {
        $table = $this->replacePrefix($table);

        // ASE's TRUNCATE TABLE does NOT reset the identity counter.
        // For tables with identity columns, we must DROP + CREATE to reset it.
        $identityCol = $this->getIdentityColumn($table);

        if (!$identityCol) {
            $this->setQuery('TRUNCATE TABLE ' . $this->quoteName($table));
            $this->execute();
            return $this;
        }

        $quoted = $this->quoteName($table);

        // Turn off IDENTITY_INSERT if it was left on from a previous operation.
        // ASE keeps session-level IDENTITY_INSERT state; if not cleared before
        // DROP, the recreated table inherits the stale ON state.
        $this->execRawSilent("SET IDENTITY_INSERT {$quoted} OFF");

        // Capture indexes before dropping (exclude PK — it's in generateCreateTableSql)
        $indexes = $this->getIndexes($table);
        $nonPkIndexes = array_filter($indexes, fn($idx) => !$idx->primary);

        // Generate CREATE TABLE SQL, then drop and recreate
        $createSql = $this->generateCreateTableSql($table);

        $this->setQuery('DROP TABLE ' . $quoted);
        $this->execute();

        $this->setQuery($createSql);
        $this->execute();

        // Recreate non-PK indexes
        foreach ($nonPkIndexes as $idx) {
            $this->addIndex($table, $idx->name, $idx->columns, $idx->unique);
        }

        // Clear identity column cache
        unset($this->identityColumns[$table]);

        // Seed the identity to start at $nextId
        if ($nextId > 1) {
            $this->setAutoIncrement($table, $nextId);
        }

        return $this;
    }

    /**
     * Convert a table's character set and collation
     *
     * @param   string       $table    The table to convert
     * @param   string       $charset  The character set
     * @param   string|null  $collate  Optional collation
     * @return  bool
     */
    public function convertToCharset($table, $charset, $collate = null)
    {
        return true;
    }

    // =========================================================================
    // DDL Methods
    // =========================================================================

    /**
     * Drops a table from the database
     *
     * ASE does not support DROP TABLE IF EXISTS. When $ifExists is true,
     * we check sysobjects first and skip the DROP if the table doesn't exist.
     *
     * @param   string   $tableName  The name of the database table to drop
     * @param   boolean  $ifExists   Optionally check existence before dropping
     * @return  $this
     */
    public function dropTable($tableName, $ifExists = true)
    {
        $tableName = $this->replacePrefix($tableName);

        if ($ifExists && !$this->tableExists($tableName)) {
            return $this;
        }

        $grammar = $this->getSchemaGrammar();
        $this->setQuery($grammar->compileDrop($tableName, false))
             ->execute();

        $this->cleanupSequencesForTable($tableName);

        return $this;
    }

    protected function cleanupSequencesForTable(string $tableName): void
    {
        if ($this->sequenceTableReady || $this->tableExists('_sequences')) {
            $this->setQuery(
                'DELETE FROM [_sequences] WHERE [table_name] = '
                . $this->quote($tableName)
            );
            $this->execute();
        }
    }

    /**
     * Build SQL for modifying a column
     *
     * ASE uses ALTER TABLE MODIFY.
     *
     * @param   string  $table       Table name
     * @param   string  $column      Column name
     * @param   string  $definition  New column definition
     * @param   string  $comment     Optional column comment
     * @return  string
     */
    protected function buildModifyColumnSql(
        string $table,
        string $column,
        string $definition,
        string $comment
    ): string {
        return "ALTER TABLE " . $this->quoteName($table)
            . " MODIFY " . $this->quoteName($column)
            . " " . $definition;
    }

    /**
     * Modify a column and move after a specific column
     *
     * ASE doesn't support column positioning.
     *
     * @param   string  $table        Table name
     * @param   string  $column       Column name
     * @param   string  $definition   New definition
     * @param   string  $afterColumn  Ignored
     * @param   string  $comment      Optional comment
     * @return  bool
     */
    public function modifyColumnAfter(
        string $table,
        string $column,
        string $definition,
        string $afterColumn,
        string $comment = ''
    ): bool {
        return $this->modifyColumn($table, $column, $definition, $comment);
    }

    /**
     * Modify a column and move before a specific column
     *
     * @param   string  $table         Table name
     * @param   string  $column        Column name
     * @param   string  $definition    New definition
     * @param   string  $beforeColumn  Ignored
     * @param   string  $comment       Optional comment
     * @return  bool
     */
    public function modifyColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool {
        return $this->modifyColumn($table, $column, $definition, $comment);
    }

    /**
     * Modify a column and move to first position
     *
     * @param   string  $table       Table name
     * @param   string  $column      Column name
     * @param   string  $definition  New definition
     * @param   string  $comment     Optional comment
     * @return  bool
     */
    public function modifyColumnFirst(string $table, string $column, string $definition, string $comment = ''): bool
    {
        return $this->modifyColumn($table, $column, $definition, $comment);
    }

    /**
     * Change a column name and/or definition
     *
     * @param   string  $table       Table name
     * @param   string  $oldColumn   Current column name
     * @param   string  $newColumn   New column name
     * @param   string  $definition  New column definition
     * @param   string  $comment     Optional comment
     * @return  bool
     */
    public function changeColumn(
        string $table,
        string $oldColumn,
        string $newColumn,
        string $definition,
        string $comment = ''
    ): bool {
        $table = $this->replacePrefix($table);

        if ($oldColumn !== $newColumn) {
            $this->setQuery(
                "EXEC sp_rename "
                . $this->quote($table . '.' . $oldColumn) . ", "
                . $this->quote($newColumn) . ", 'column'"
            );
            $this->execute();
            $this->consumeResultSets();
        }

        $this->setQuery(
            "ALTER TABLE " . $this->quoteName($table)
            . " MODIFY " . $this->quoteName($newColumn)
            . " " . $definition
        );
        $this->execute();

        return true;
    }

    /**
     * Build SQL for adding a column
     *
     * @param   string  $table       Table name
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional comment
     * @return  string
     */
    protected function buildAddColumnSql(
        string $table,
        string $column,
        string $definition,
        string $comment
    ): string {
        // ASE requires NULL or a DEFAULT clause when adding a column.
        // If the definition doesn't explicitly specify NULL/NOT NULL/DEFAULT,
        // append NULL to allow the column to be added.
        // Exception: BIT type cannot be NULL in ASE.
        $defUpper = strtoupper($definition);
        if (
            strpos($defUpper, ' NULL') === false
            && strpos($defUpper, ' NOT NULL') === false
            && strpos($defUpper, ' DEFAULT ') === false
        ) {
            if (preg_match('/^\s*BIT\b/i', $definition)) {
                $definition .= ' DEFAULT 0 NOT NULL';
            } else {
                $definition .= ' NULL';
            }
        }

        return "ALTER TABLE " . $this->quoteName($table)
            . " ADD " . $this->quoteName($column)
            . " " . $definition;
    }

    /**
     * Add a column after a specific column
     *
     * ASE doesn't support column positioning.
     *
     * @param   string  $table        Table name
     * @param   string  $column       Column name
     * @param   string  $definition   Column definition
     * @param   string  $afterColumn  Ignored
     * @param   string  $comment      Optional comment
     * @return  bool
     */
    public function addColumnAfter(
        string $table,
        string $column,
        string $definition,
        string $afterColumn,
        string $comment = ''
    ): bool {
        return $this->addColumn($table, $column, $definition, $comment);
    }

    /**
     * Add a column before a specific column
     *
     * @param   string  $table         Table name
     * @param   string  $column        Column name
     * @param   string  $definition    Column definition
     * @param   string  $beforeColumn  Ignored
     * @param   string  $comment       Optional comment
     * @return  bool
     */
    public function addColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool {
        return $this->addColumn($table, $column, $definition, $comment);
    }

    /**
     * Add a column at the beginning of a table
     *
     * @param   string  $table       Table name
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional comment
     * @return  bool
     */
    public function addColumnFirst(string $table, string $column, string $definition, string $comment = ''): bool
    {
        return $this->addColumn($table, $column, $definition, $comment);
    }

    /**
     * Add a column at the end of a table
     *
     * @param   string  $table       Table name
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional comment
     * @return  bool
     */
    public function addColumnLast(string $table, string $column, string $definition, string $comment = ''): bool
    {
        return $this->addColumn($table, $column, $definition, $comment);
    }

    /**
     * Build SQL for dropping a column
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name
     * @return  string
     */
    protected function buildDropColumnSql(string $table, string $column): string
    {
        return "ALTER TABLE " . $this->quoteName($table)
            . " DROP " . $this->quoteName($column);
    }

    // =========================================================================
    // ENUM Support (not available on ASE)
    // =========================================================================

    /**
     * @return  array  Always empty
     */
    public function getEnumValues($table, $column)
    {
        return [];
    }

    /**
     * @return  bool  Always true (no-op)
     */
    public function addEnumValue($table, $column, $value)
    {
        return true;
    }

    /**
     * @return  bool  Always true (no-op)
     */
    public function removeEnumValue($table, $column, $value)
    {
        return true;
    }

    // =========================================================================
    // View Methods
    // =========================================================================

    /**
     * Creates or replaces a database view
     *
     * @param   string  $name       The view name
     * @param   string  $selectSql  The SELECT statement
     * @param   array   $options    Options (ignored on ASE)
     * @return  bool
     */
    public function createOrReplaceView($name, $selectSql, array $options = []): bool
    {
        $viewName = $this->replacePrefix($name);
        $selectSql = $this->replacePrefix($selectSql);

        $this->dropView($name, true);

        $sql = 'CREATE VIEW ' . $this->quoteName($viewName) . ' AS ' . $selectSql;
        $this->setQuery($sql);
        $this->execute();

        return true;
    }

    /**
     * Drops a database view
     *
     * @param   string  $name      The view name
     * @param   bool    $ifExists  Whether to check existence first
     * @return  bool
     */
    public function dropView($name, $ifExists = true): bool
    {
        $viewName = $this->replacePrefix($name);

        if ($ifExists) {
            if (!$this->viewExists($name)) {
                return true;
            }
        }

        $this->setQuery('DROP VIEW ' . $this->quoteName($viewName));
        $this->execute();

        return true;
    }

    /**
     * Checks if a view exists
     *
     * @param   string  $name  The view name
     * @return  bool
     */
    public function viewExists($name): bool
    {
        $viewName = $this->replacePrefix($name);
        $this->setQuery(
            "SELECT 1 FROM sysobjects WHERE name = " . $this->quote($viewName)
            . " AND type = 'V'"
        );

        return (bool) $this->loadResult();
    }

    /**
     * Returns a list of all views
     *
     * @return  array
     */
    public function getViews(): array
    {
        $this->setQuery(
            "SELECT name FROM sysobjects WHERE type = 'V' ORDER BY name"
        );

        return $this->loadColumn() ?: [];
    }

    /**
     * Returns a list of all database names
     *
     * @return  array
     */
    public function getDatabaseNames(): array
    {
        $this->setQuery(
            "SELECT name FROM master..sysdatabases ORDER BY name"
        );

        return $this->loadColumn() ?: [];
    }

    // =========================================================================
    // Sequence Methods (table-based emulation)
    // =========================================================================

    /**
     * Ensures the _sequences emulation table exists
     *
     * @return  void
     */
    protected function ensureSequenceTable(): void
    {
        if ($this->sequenceTableReady) {
            return;
        }

        if (!$this->tableExists('_sequences')) {
            $this->setQuery(
                'CREATE TABLE [_sequences] ('
                . '[name] VARCHAR(255) NOT NULL, '
                . '[current_value] NUMERIC(19,0) DEFAULT 0 NOT NULL, '
                . '[increment_value] INT DEFAULT 1 NOT NULL, '
                . '[table_name] VARCHAR(255) NULL, '
                . 'PRIMARY KEY ([name])'
                . ')'
            );
            $this->execute();
        } elseif (!$this->tableHasField('_sequences', 'table_name')) {
            // Migrate existing _sequences table to include table_name
            $this->setQuery(
                'ALTER TABLE [_sequences] ADD [table_name] VARCHAR(255) NULL'
            );
            $this->execute();
        }

        $this->sequenceTableReady = true;
    }

    /**
     * Returns a list of all emulated sequences
     *
     * @return  array  Array of SequenceInfo objects
     */
    public function getSequences(): array
    {
        $this->ensureSequenceTable();
        $this->setQuery('SELECT * FROM [_sequences] ORDER BY [name]');
        $rows = $this->loadObjectList();

        return array_map(function ($row) {
            return new \Hubzero\Database\Schema\SequenceInfo([
                'name'          => rtrim($row->name),
                'current_value' => (int) $row->current_value,
                'increment'     => (int) $row->increment_value,
            ]);
        }, $rows);
    }

    /**
     * Creates a new emulated sequence
     *
     * Seeds current_value as start - increment so the first
     * nextSequenceValue() call returns $start.
     *
     * @param   string  $name       The sequence name
     * @param   int     $start      Starting value (default: 1)
     * @param   int     $increment  Increment value (default: 1)
     * @param   array   $options    Additional options (ignored)
     * @return  bool
     */
    public function createSequence($name, $start = 1, $increment = 1, array $options = []): bool
    {
        $this->ensureSequenceTable();
        $name = $this->replacePrefix($name);
        $seedValue = (int) $start - (int) $increment;
        $tableName = $options['table'] ?? null;
        if ($tableName) {
            $tableName = $this->replacePrefix($tableName);
        }

        $columns = '[name], [current_value], [increment_value], [table_name]';
        $values = $this->quote($name) . ', '
            . $seedValue . ', '
            . (int) $increment . ', '
            . ($tableName ? $this->quote($tableName) : 'NULL');

        $this->setQuery(
            "INSERT INTO [_sequences] ({$columns}) VALUES ({$values})"
        );
        $this->execute();

        return true;
    }

    /**
     * Drops an emulated sequence
     *
     * @param   string  $name      The sequence name
     * @param   bool    $ifExists  Whether to silently ignore missing sequences
     * @return  bool
     */
    public function dropSequence($name, $ifExists = true): bool
    {
        $this->ensureSequenceTable();
        $name = $this->replacePrefix($name);

        $this->setQuery(
            'DELETE FROM [_sequences] WHERE [name] = '
            . $this->quote($name)
        );
        $this->execute();

        return true;
    }

    /**
     * Checks if an emulated sequence exists
     *
     * @param   string  $name  The sequence name
     * @return  bool
     */
    public function sequenceExists($name): bool
    {
        $this->ensureSequenceTable();
        $name = $this->replacePrefix($name);

        $this->setQuery(
            'SELECT COUNT(*) FROM [_sequences] WHERE [name] = '
            . $this->quote($name)
        );

        return (int) $this->loadResult() > 0;
    }

    /**
     * Gets the next value from an emulated sequence
     *
     * Updates the current_value atomically and returns the new value.
     *
     * @param   string  $name  The sequence name
     * @return  int
     */
    public function nextSequenceValue($name): int
    {
        $this->ensureSequenceTable();
        $name = $this->replacePrefix($name);

        $this->setQuery(
            'UPDATE [_sequences] SET [current_value] = '
            . '[current_value] + [increment_value] '
            . 'WHERE [name] = ' . $this->quote($name)
        );
        $this->execute();

        $this->setQuery(
            'SELECT [current_value] FROM [_sequences] WHERE [name] = '
            . $this->quote($name)
        );

        return (int) $this->loadResult();
    }

    /**
     * Gets the current value of an emulated sequence (without incrementing)
     *
     * @param   string  $name  The sequence name
     * @return  int
     */
    public function currentSequenceValue($name): int
    {
        $this->ensureSequenceTable();
        $name = $this->replacePrefix($name);

        $this->setQuery(
            'SELECT [current_value] FROM [_sequences] WHERE [name] = '
            . $this->quote($name)
        );
        $result = $this->loadResult();

        return $result !== null ? (int) $result : 0;
    }

    /**
     * Check if this driver supports sequences
     *
     * ASE provides table-based sequence emulation via a _sequences table.
     *
     * @return  bool
     */
    public function supportsSequences(): bool
    {
        return true;
    }

    /**
     * ASE implements sequences via _sequences table emulation
     *
     * @return  bool
     */
    public function usesSequenceEmulation(): bool
    {
        return true;
    }

    // =========================================================================
    // Table Operation Methods
    // =========================================================================

    /**
     * Set the storage engine for a table
     *
     * @param   string  $table   Table name
     * @param   string  $engine  Engine type (ignored)
     * @return  bool
     */
    public function setTableEngine(string $table, string $engine = 'MYISAM'): bool
    {
        return true;
    }

    /**
     * Set the character set for a table
     *
     * @param   string  $table      Table name
     * @param   string  $charset    Character set
     * @param   string  $collation  Collation
     * @return  bool
     */
    public function setTableCharset(
        string $table,
        string $charset = 'utf8',
        string $collation = 'utf8_general_ci'
    ): bool {
        return true;
    }

    /**
     * Add a FULLTEXT index (not supported - falls back to regular index)
     *
     * @param   string        $table    Table name
     * @param   string        $name     Index name
     * @param   string|array  $columns  Columns
     * @return  bool
     */
    public function addFulltextIndex(string $table, string $name, $columns): bool
    {
        return $this->addIndex($table, $name, $columns);
    }

    /**
     * Drop the primary key from a table
     *
     * @param   string  $table  Table name
     * @return  bool
     */
    public function dropPrimaryKey(string $table): bool
    {
        $table = $this->replacePrefix($table);

        // In ASE, PK constraints are stored as indexes in sysindexes,
        // NOT in sysconstraints. The index name IS the constraint name.
        // status & 2048 = "index on primary key"
        $this->setQuery(
            "SELECT i.name FROM sysindexes i "
            . "WHERE i.id = object_id(" . $this->quote($table) . ") "
            . "AND i.indid > 0 AND i.indid < 255 "
            . "AND i.status & 2048 = 2048"
        );
        $pkName = $this->loadResult();

        if ($pkName) {
            $this->setQuery(
                "ALTER TABLE " . $this->quoteName($table)
                . " DROP CONSTRAINT " . $this->quoteName($pkName)
            );
            $this->execute();
        }

        return true;
    }

    /**
     * Add a primary key to a table
     *
     * @param   string        $table    Table name
     * @param   string|array  $columns  Column name(s)
     * @return  bool
     */
    public function addPrimaryKey(string $table, $columns): bool
    {
        $table = $this->replacePrefix($table);
        $columns = (array) $columns;
        $qt = $this->quoteName($table);

        // ASE requires PK columns to be NOT NULL.
        // Alter any nullable columns before adding the constraint.
        $colInfo = $this->getTableColumns($table, false);
        foreach ($columns as $col) {
            if (isset($colInfo[$col]) && $colInfo[$col]->Null === 'YES') {
                $type = $this->buildFullColumnType($colInfo[$col]);
                $qc = $this->quoteName($col);
                $this->setQuery("ALTER TABLE {$qt} MODIFY {$qc} {$type} NOT NULL");
                $this->execute();
            }
        }

        $columnList = implode(', ', array_map([$this, 'quoteName'], $columns));
        $this->setQuery("ALTER TABLE {$qt} ADD PRIMARY KEY ($columnList)");
        $this->execute();

        return true;
    }

    /**
     * Add an auto-increment primary key column
     *
     * @param   string  $table      Table name
     * @param   string  $column     Column name
     * @param   bool    $first      Add as first column (ignored)
     * @param   bool    $useBigInt  Use BIGINT or INT
     * @return  bool
     */
    public function addAutoIncrementPrimaryKey(
        string $table,
        string $column = 'id',
        bool $first = false,
        bool $useBigInt = true
    ): bool {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        if ($this->tableHasField($table, $column)) {
            return true;
        }

        $type = $useBigInt ? 'BIGINT' : 'INT';
        $qt = $this->quoteName($table);
        $qc = $this->quoteName($column);

        // Check if table has rows — ASE can't add IDENTITY to a populated table
        $this->setQuery("SELECT COUNT(*) FROM $qt");
        $rowCount = (int) $this->loadResult();

        if ($rowCount === 0) {
            // Empty table: simple IDENTITY column
            $this->setQuery(
                "ALTER TABLE $qt ADD $qc $type IDENTITY PRIMARY KEY"
            );
            $this->execute();
        } else {
            // Populated table: multi-step approach
            // 1. Add nullable column
            $this->setQuery("ALTER TABLE $qt ADD $qc $type NULL");
            $this->execute();

            // 2. Populate with sequential values
            $this->populateSequentialValues($table, $column);

            // 3. Set NOT NULL
            $this->setQuery("ALTER TABLE $qt MODIFY $qc $type NOT NULL");
            $this->execute();

            // 4. Add primary key
            $this->setQuery("ALTER TABLE $qt ADD PRIMARY KEY ($qc)");
            $this->execute();
        }

        return true;
    }

    /**
     * Populate a column with sequential values
     *
     * ASE doesn't have ROW_NUMBER() so uses a temp table approach.
     * Creates a temp table with IDENTITY + all non-target columns,
     * then updates via composite join to assign sequential IDs.
     *
     * @param   string       $table    Table name
     * @param   string       $column   Column name
     * @param   string|null  $orderBy  Optional ORDER BY column
     * @return  bool
     */
    public function populateSequentialValues(string $table, string $column, ?string $orderBy = null): bool
    {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table) || !$this->tableHasField($table, $column)) {
            return false;
        }

        $quotedTable = $this->quoteName($table);
        $quotedColumn = $this->quoteName($column);

        // Use ALL non-target columns as composite join key to avoid
        // duplicate matches when any single column has repeated values.
        $allColumns = $this->getTableColumns($table, false);
        $joinCols = [];
        $tempColDefs = [];

        foreach ($allColumns as $name => $info) {
            if ($name === $column) {
                continue;
            }
            $joinCols[] = $name;
            $colType = $this->buildFullColumnType($info);
            $tempColDefs[] = $this->quoteName($name) . ' ' . $colType . ' NULL';
        }

        if (empty($joinCols)) {
            return false;
        }

        $colList = implode(', ', array_map([$this, 'quoteName'], $joinCols));
        $orderClause = $orderBy ? $this->quoteName($orderBy) : $colList;

        // Create temp table with IDENTITY + all non-target columns
        $tempDefs = 'row_num INT IDENTITY NOT NULL, ' . implode(', ', $tempColDefs);
        $this->setQuery("CREATE TABLE #seq_temp ($tempDefs)");
        $this->execute();

        $this->setQuery(
            "INSERT INTO #seq_temp ($colList) SELECT $colList FROM $quotedTable ORDER BY $orderClause"
        );
        $this->execute();

        // Build composite join condition on all non-target columns
        $joinConditions = array_map(function ($c) use ($quotedTable) {
            $qc = $this->quoteName($c);
            return "$quotedTable.$qc = t.$qc";
        }, $joinCols);

        $this->setQuery(
            "UPDATE $quotedTable SET $quotedColumn = t.row_num "
            . "FROM $quotedTable INNER JOIN #seq_temp t "
            . "ON " . implode(' AND ', $joinConditions)
        );
        $this->execute();

        $this->setQuery("DROP TABLE #seq_temp");
        $this->execute();

        return true;
    }

    /**
     * Build SQL for creating an index
     *
     * @param   string  $table    Table name
     * @param   string  $name     Index name
     * @param   array   $columns  Column names
     * @param   bool    $unique   Whether unique
     * @return  string
     */
    protected function buildCreateIndexSql(string $table, string $name, array $columns, bool $unique): string
    {
        $columnList = implode(', ', array_map([$this, 'quoteName'], $columns));
        $uniqueStr = $unique ? 'UNIQUE ' : '';
        return "CREATE " . $uniqueStr . "INDEX " . $this->quoteName($name)
            . " ON " . $this->quoteName($table) . " (" . $columnList . ")";
    }

    /**
     * Drop an index from a table
     *
     * ASE uses table.index syntax for DROP INDEX.
     *
     * @param   string  $table  Table name
     * @param   string  $name   Index name
     * @return  bool
     */
    public function dropIndex(string $table, string $name): bool
    {
        $table = $this->replacePrefix($table);
        // ASE DROP INDEX syntax: DROP INDEX table.index_name
        $this->setQuery(
            "DROP INDEX " . $this->quoteName($table) . "." . $this->quoteName($name)
        );
        $this->execute();
        return true;
    }

    /**
     * Get the schema grammar instance
     *
     * @return  \Hubzero\Database\Drivers\Base\BaseSchemaGrammar
     */
    public function getSchemaGrammar()
    {
        return $this->makeSchemaGrammarFromRegistry();
    }

    /**
     * Build a column definition for ALTER TABLE operations
     *
     * @param   string  $name        The column name
     * @param   array   $definition  The column definition
     * @return  string
     */
    public function buildAlterColumnDefinition(string $name, array $definition): string
    {
        $type = $definition['type'];
        $modifiers = $definition['modifiers'] ?? [];

        $hasAutoIncrement = !empty($modifiers['autoIncrement'])
            || stripos($type, 'AUTO_INCREMENT') !== false;
        if ($hasAutoIncrement) {
            $type = preg_replace('/\s*AUTO_INCREMENT\s*/i', ' ', $type);
            $type = trim($type);
        }

        $type = $this->normalizeColumnType($type, $modifiers);

        if (
            array_key_exists('default', $modifiers)
            && self::isZeroDate($modifiers['default'])
        ) {
            $modifiers['nullable'] = true;
            $modifiers['default'] = null;
        }

        $parts = [$this->quoteIdentifier($name), $type];

        if ($hasAutoIncrement) {
            $parts[] = 'IDENTITY';
            $parts[] = 'NOT NULL';
            $parts[] = 'PRIMARY KEY';
            return implode(' ', $parts);
        }

        // DEFAULT before NOT NULL
        if (array_key_exists('default', $modifiers)) {
            $default = $modifiers['default'];
            if ($default === null) {
                $parts[] = 'DEFAULT NULL';
            } elseif ($default === 'CURRENT_TIMESTAMP') {
                $parts[] = 'DEFAULT GETDATE()';
            } elseif (is_bool($default)) {
                $parts[] = 'DEFAULT ' . ($default ? '1' : '0');
            } elseif (is_numeric($default)) {
                $parts[] = 'DEFAULT ' . $default;
            } else {
                $parts[] = "DEFAULT '" . addslashes($default) . "'";
            }
        }

        // Check if nullability is already specified in the raw type string
        $typeUpper = strtoupper($type);
        $hasNullInType = preg_match('/\bNOT\s+NULL\b|\bNULL\b/i', $type);

        if (isset($modifiers['nullable'])) {
            if (!$hasNullInType) {
                $parts[] = $modifiers['nullable'] ? 'NULL' : 'NOT NULL';
            }
        } elseif (!$hasNullInType) {
            if (strtoupper(trim(preg_replace('/\([^)]*\)/', '', $type))) === 'BIT') {
                // ASE BIT cannot be NULL; default to NOT NULL with DEFAULT 0
                if (!array_key_exists('default', $modifiers)) {
                    $parts[] = 'DEFAULT 0';
                }
                $parts[] = 'NOT NULL';
            } else {
                // ASE requires NULL or DEFAULT when adding a column
                $parts[] = 'NULL';
            }
        }

        return implode(' ', $parts);
    }

    /**
     * Build a full column type string from column info object
     *
     * @param   object  $colInfo  Column info
     * @return  string
     */
    public function buildFullColumnType(object $colInfo): string
    {
        $type = strtoupper($colInfo->Type);

        if (in_array(strtolower($colInfo->Type), ['varchar', 'char', 'nvarchar', 'nchar', 'varbinary'])) {
            $type .= '(' . $colInfo->Length . ')';
        } elseif (in_array(strtolower($colInfo->Type), ['decimal', 'numeric']) && $colInfo->Prec) {
            $type .= '(' . $colInfo->Prec . ',' . $colInfo->Scale . ')';
        }

        return $type;
    }

    // =========================================================================
    // Feature Detection
    // =========================================================================

    /**
     * @return  bool
     */
    public function supportsDropColumn(): bool
    {
        return true;
    }

    /**
     * ASE does not support FK referential actions (ON DELETE CASCADE, etc.)
     *
     * @return  bool
     */
    public function supportsReferentialActions(): bool
    {
        return false;
    }

    // =========================================================================
    // Check Constraints
    // =========================================================================

    /**
     * Get CHECK constraints for table
     *
     * @param   string  $table  Table name
     * @return  array
     */
    public function getCheckConstraints(string $table): array
    {
        $table = str_replace(['[', ']'], '', $this->replacePrefix($table));

        $this->setQuery(
            "SELECT o.name AS name, m.text AS expression "
            . "FROM sysobjects o "
            . "JOIN syscomments m ON o.id = m.id "
            . "WHERE o.type = 'C' "
            . "AND o.id IN ("
            . "  SELECT constrid FROM sysconstraints "
            . "  WHERE tableid = object_id(" . $this->quote($table) . ")"
            . ")"
        );

        $results = $this->loadObjectList();
        $constraints = [];
        foreach ($results as $row) {
            $constraints[] = (object) [
                'name' => $row->name,
                'expression' => $row->expression,
            ];
        }

        return $constraints;
    }

    /**
     * Get global configuration variables
     *
     * @return  array
     */
    public function getGlobalVariables(): array
    {
        try {
            $this->setQuery("SELECT name, value FROM sysconfigures ORDER BY name");
            $rows = $this->loadObjectList();
            $vars = [];
            foreach ($rows as $row) {
                $vars[$row->name] = $row->value;
            }
            return $vars;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get server uptime
     *
     * @return  int|null
     */
    public function getUptime(): ?int
    {
        // ASE doesn't expose startup time easily
        return null;
    }
}
