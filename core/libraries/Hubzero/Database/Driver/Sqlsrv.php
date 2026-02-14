<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Driver;

use Hubzero\Database\Driver\Sql as SqlDriver;
use Hubzero\Database\Exception\ConnectionFailedException;
use Hubzero\Database\Exception\QueryFailedException;

/**
 * SQL Server (PDO) database driver
 *
 * Microsoft SQL Server is a relational database management system developed
 * by Microsoft. This driver provides SQL Server-specific functionality.
 *
 * Key SQL Server-specific features:
 * - T-SQL (Transact-SQL) dialect
 * - Clustered and non-clustered indexes
 * - Stored procedures and triggers
 * - Full-text search
 * - Window functions (ROW_NUMBER, RANK, etc.)
 * - Common Table Expressions (CTEs)
 * - IDENTITY columns for auto-increment
 * - SQL Server Agent for job scheduling
 *
 * REQUIREMENTS:
 * - PHP pdo_sqlsrv extension (Microsoft ODBC Driver) or
 * - PHP pdo_dblib extension (FreeTDS)
 *
 * INHERITANCE:
 * This class extends Sql (the universal SQL base) which extends Pdo
 * (the connection layer).
 *
 */
class Sqlsrv extends SqlDriver
{
    /**
     * Check if this driver's PDO extension is available
     *
     * @return bool
     */
    public static function test()
    {
        return extension_loaded('pdo_sqlsrv');
    }

    /**
     * The name of the database driver
     *
     * @var string
     */
    protected $name = 'sqlsrv';

    /**
     * The character(s) used to quote SQL statement names such as tables or columns
     *
     * SQL Server uses square brackets for identifier quoting
     *
     * @var  string|array
     */
    protected $nameQuote = ['[', ']'];

    /**
     * Cache of identity column names keyed by table name
     *
     * Stores the identity column name for each table, or false if the
     * table has no identity column. Used by execute() to auto-toggle
     * IDENTITY_INSERT when inserting explicit values.
     *
     * @var array<string, string|false>
     */
    protected $identityColumns = [];

    /**
     * The current transaction depth (for savepoint support)
     *
     * @var int
     */
    protected $transactionDepth = 0;

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
    protected string $randExpression = 'NEWID()';

    /**
     * SQL function for string length
     *
     * @var string
     */
    protected string $lengthFunction = 'LEN';

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
     * @throws  ConnectionFailedException  If the DSN is invalid or connection fails
     */
    public function __construct($options)
    {
        // Establish connection string
        if (!isset($options['dsn'])) {
            // Build DSN for Microsoft ODBC Driver (pdo_sqlsrv)
            $options['dsn'] = "sqlsrv:Server={$options['host']}";
            if (!empty($options['port'])) {
                $options['dsn'] .= ",{$options['port']}";
            }
            if (!empty($options['database'])) {
                $options['dsn'] .= ";Database={$options['database']}";
            }
            // Trust self-signed certificates (common in dev environments)
            if (!empty($options['trust_server_certificate'])) {
                $options['dsn'] .= ";TrustServerCertificate=1";
            }
            // Enable connection pooling
            $options['dsn'] .= ";ConnectionPooling=1";
        }

        // Call parent construct
        parent::__construct($options);
    }

    // =========================================================================
    // SQL Helper Methods (SQL Server implementations)
    // =========================================================================

    /**
     * Format a boolean value as a SQL literal
     *
     * SQL Server uses BIT type with 1/0 values.
     *
     * @param   bool  $value  The boolean value
     * @return  string  The SQL literal
     */
    public function formatBooleanLiteral(bool $value): string
    {
        return $value ? '1' : '0';
    }

    /**
     * Returns the SQL keyword for INSERT with ignore duplicates
     *
     * SQL Server doesn't have INSERT IGNORE. Use with sqlInsertIgnoreSuffix()
     * to get the proper MERGE or TRY/CATCH pattern.
     *
     * @return  string
     */
    public function sqlInsertIgnore(): string
    {
        // SQL Server doesn't have direct INSERT IGNORE
        // Callers should use MERGE statement or TRY/CATCH for this functionality
        return 'INSERT INTO';
    }

    /**
     * Returns the SQL keyword for REPLACE (upsert)
     *
     * SQL Server uses MERGE statement for upsert operations.
     * This returns INSERT INTO; use sqlReplaceSuffix() for the ON clause.
     *
     * @return  string
     */
    public function sqlReplace(): string
    {
        // SQL Server uses MERGE for upsert, not REPLACE
        return 'INSERT INTO';
    }

    /**
     * Returns the suffix for INSERT IGNORE statements
     *
     * For SQL Server, we need to handle duplicates differently.
     * This could use WHERE NOT EXISTS or be part of a MERGE.
     *
     * @return  string
     */
    public function sqlInsertIgnoreSuffix(): string
    {
        // Note: Full INSERT IGNORE emulation requires knowledge of the key
        // This is a simplified version; complex cases need MERGE
        return '';
    }

    /**
     * Returns whether the database supports REGEXP operator
     *
     * SQL Server does not have native REGEXP support.
     * Pattern matching uses LIKE or PATINDEX.
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
     * SQL Server doesn't support REGEXP. This uses LIKE with wildcards
     * as a fallback, which is NOT regex-compatible.
     *
     * @param   string  $column   The column to match
     * @param   string  $pattern  The pattern (will be treated as LIKE pattern)
     * @param   bool    $not      Whether to negate the match
     * @return  string
     */
    public function sqlRegexp(string $column, string $pattern, bool $not = false): string
    {
        // SQL Server doesn't have REGEXP. Use PATINDEX for basic pattern matching.
        // PATINDEX supports SQL Server wildcards (%, _, []) — not full regex.
        $notStr = $not ? ' = 0' : ' > 0';
        return 'PATINDEX(' . $this->quote($pattern) . ', ' . $column . ')' . $notStr;
    }

    /**
     * Returns the SQL for date subtraction
     *
     * @param   string  $date   The date column or value
     * @param   int     $value  The interval value
     * @param   string  $unit   The interval unit (DAY, MONTH, YEAR, HOUR, MINUTE, SECOND)
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
     * @param   string  $unit   The interval unit (DAY, MONTH, YEAR, HOUR, MINUTE, SECOND)
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
     * SQL Server uses FORMAT() (2012+) or CONVERT() with style codes.
     * This uses FORMAT() which accepts .NET format strings.
     *
     * @param   string  $date    The date column or value
     * @param   string  $format  The format string (MySQL format, will be converted)
     * @return  string
     */
    public function sqlDateFormat(string $date, string $format): string
    {
        // Convert MySQL format codes to SQL Server FORMAT() codes
        $formatMap = [
            '%Y' => 'yyyy',  // 4-digit year
            '%y' => 'yy',    // 2-digit year
            '%m' => 'MM',    // Month (01-12)
            '%d' => 'dd',    // Day (01-31)
            '%H' => 'HH',    // Hour (00-23)
            '%i' => 'mm',    // Minute (00-59)
            '%s' => 'ss',    // Second (00-59)
            '%M' => 'MMMM',  // Month name
            '%b' => 'MMM',   // Abbreviated month name
            '%W' => 'dddd',  // Weekday name
            '%a' => 'ddd',   // Abbreviated weekday name
        ];

        $sqlFormat = str_replace(array_keys($formatMap), array_values($formatMap), $format);
        return 'FORMAT(' . $date . ', ' . $this->quote($sqlFormat) . ')';
    }

    /**
     * Returns the SQL for extracting year from a date
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    public function sqlYear(string $date): string
    {
        return 'YEAR(' . $date . ')';
    }

    /**
     * Returns the SQL for extracting month from a date
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    public function sqlMonth(string $date): string
    {
        return 'MONTH(' . $date . ')';
    }

    /**
     * Returns the SQL for converting a date to Unix timestamp
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    public function sqlUnixTimestamp(string $date): string
    {
        return 'DATEDIFF(SECOND, \'1970-01-01\', ' . $date . ')';
    }

    /**
     * Returns the SQL for extracting a substring based on a delimiter
     *
     * SQL Server doesn't have SUBSTRING_INDEX. This emulates it for count=1.
     * Complex cases with count>1 or count<0 need more elaborate logic.
     *
     * @param   string  $str    The string expression
     * @param   string  $delim  The delimiter to search for
     * @param   int     $count  The occurrence count
     * @return  string
     */
    public function sqlSubstringIndex(string $str, string $delim, int $count): string
    {
        $quotedDelim = $this->quote($delim);

        if ($count === 1) {
            // Return everything before the first delimiter
            return 'LEFT(' . $str . ', CHARINDEX(' . $quotedDelim . ', ' . $str . ') - 1)';
        } elseif ($count === -1) {
            // Return everything after the last delimiter
            return 'RIGHT(' . $str . ', LEN(' . $str . ') - LEN(' . $quotedDelim
                . ') - CHARINDEX(' . $quotedDelim . ', REVERSE('
                . $str . ')) + 1)';
        }

        // For other counts, return a simplified version
        // Full emulation would require a recursive CTE or function
        return 'LEFT(' . $str . ', CHARINDEX(' . $quotedDelim . ', ' . $str . ') - 1)';
    }

    /**
     * Returns the SQL for concatenating strings
     *
     * @param   array  $strings  Array of column names or quoted strings to concatenate
     * @return  string
     */
    public function sqlConcat(array $strings): string
    {
        // SQL Server 2012+ has CONCAT(), older versions use + operator
        return 'CONCAT(' . implode(', ', $strings) . ')';
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
        // SQL Server 2017+ has CONCAT_WS(), older versions need manual handling
        $quotedSep = $this->quote($separator);
        return 'CONCAT_WS(' . $quotedSep . ', ' . implode(', ', $strings) . ')';
    }

    // =========================================================================
    // Schema Building Methods - SQL Server Implementations
    // =========================================================================

    /**
     * Quote a single identifier using SQL Server square brackets
     *
     * @param   string  $identifier  The identifier to quote
     * @return  string  The quoted identifier
     */
    public function quoteIdentifier(string $identifier): string
    {
        return '[' . str_replace(']', ']]', $identifier) . ']';
    }

    /**
     * SQL Server does not support IF NOT EXISTS for CREATE TABLE
     *
     * @return  bool
     */
    public function supportsIfNotExists(): bool
    {
        return false;
    }

    /**
     * SQL Server does not support IF NOT EXISTS for CREATE INDEX
     *
     * @return  bool
     */
    public function supportsIfNotExistsForIndex(): bool
    {
        return false;
    }

    /**
     * Normalize a column type for SQL Server
     *
     * Handles abstract types via the typeMap, and converts MySQL-style
     * raw types (INT(11) UNSIGNED, MEDIUMINT, etc.) to SQL Server types.
     *
     * @param   string  $type       Column type (abstract or raw)
     * @param   array   $modifiers  Column modifiers
     * @return  string  SQL Server column type
     */
    public function normalizeColumnType(
        string $type,
        array $modifiers = []
    ): string {
        // Abstract type handled by base class (uses Grammar's typeMap)
        // Try exact match first, then case-insensitive (e.g. STRING → string)
        $grammar = $this->getSchemaGrammar();
        if ($grammar->getTypeMapping($type) !== null) {
            return parent::normalizeColumnType($type, $modifiers);
        }
        $lower = strtolower($type);
        if ($lower !== $type && $grammar->getTypeMapping($lower) !== null) {
            return parent::normalizeColumnType($lower, $modifiers);
        }

        // Handle concrete MySQL-style types
        // Strip UNSIGNED (SQL Server doesn't support it)
        $cleaned = trim(str_ireplace(' UNSIGNED', '', $type));

        // Strip integer display widths: INT(11) → INT, BIGINT(20) → BIGINT
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

        // BOOLEAN / BOOL / TINYINT(1) → BIT
        if (
            strcasecmp($cleaned, 'BOOLEAN') === 0
            || strcasecmp($cleaned, 'BOOL') === 0
            || strcasecmp($cleaned, 'TINYINT(1)') === 0
        ) {
            return 'BIT';
        }

        // DATETIME / TIMESTAMP → DATETIME2
        if (
            strcasecmp($cleaned, 'DATETIME') === 0
            || strcasecmp($cleaned, 'TIMESTAMP') === 0
        ) {
            return 'DATETIME2';
        }

        // TEXT variants → NVARCHAR(MAX)
        if (preg_match('/^(TINY|MEDIUM|LONG)?TEXT$/i', $cleaned)) {
            return 'NVARCHAR(MAX)';
        }

        // BLOB variants → VARBINARY(MAX)
        if (preg_match('/^(TINY|MEDIUM|LONG)?BLOB$/i', $cleaned)) {
            return 'VARBINARY(MAX)';
        }

        // VARCHAR(n) → NVARCHAR(n)
        if (preg_match('/^VARCHAR\s*\((\d+)\)$/i', $cleaned, $m)) {
            return "NVARCHAR({$m[1]})";
        }

        // CHAR(n) → NCHAR(n)
        if (preg_match('/^CHAR\s*\((\d+)\)$/i', $cleaned, $m)) {
            return "NCHAR({$m[1]})";
        }

        // SET/ENUM → NVARCHAR(255)
        if (
            preg_match('/^SET\s*\(/i', $cleaned)
            || preg_match('/^ENUM\s*\(/i', $cleaned)
        ) {
            return 'NVARCHAR(255)';
        }

        // DOUBLE → FLOAT
        if (preg_match('/^DOUBLE(\s*\([^)]+\))?$/i', $cleaned)) {
            return 'FLOAT';
        }

        return $cleaned;
    }

    /**
     * Map a MySQL-style column type to SQL Server's type
     *
     * @param   string  $type  The MySQL column type
     * @return  string  The SQL Server column type
     */
    public function mapColumnType(string $type): string
    {
        $upper = strtoupper($type);

        // Remove UNSIGNED (SQL Server doesn't support it)
        $upper = preg_replace('/\s+UNSIGNED$/i', '', $upper);

        // TINYINT -> TINYINT (SQL Server has it)
        // INT -> INT (same)
        // BIGINT -> BIGINT (same)

        // DOUBLE/FLOAT -> FLOAT
        if ($upper === 'DOUBLE' || preg_match('/^DOUBLE\s*\([^)]+\)$/i', $upper)) {
            return 'FLOAT';
        }

        // DATETIME -> DATETIME2
        if ($upper === 'DATETIME') {
            return 'DATETIME2';
        }

        // TIMESTAMP -> DATETIME2
        if ($upper === 'TIMESTAMP') {
            return 'DATETIME2';
        }

        // TINYTEXT/TEXT/MEDIUMTEXT/LONGTEXT -> NVARCHAR(MAX)
        if (preg_match('/^(TINY|MEDIUM|LONG)?TEXT$/i', $upper)) {
            return 'NVARCHAR(MAX)';
        }

        // TINYBLOB/BLOB/MEDIUMBLOB/LONGBLOB -> VARBINARY(MAX)
        if (preg_match('/^(TINY|MEDIUM|LONG)?BLOB$/i', $upper)) {
            return 'VARBINARY(MAX)';
        }

        // VARCHAR -> NVARCHAR
        if (preg_match('/^VARCHAR\s*\((\d+)\)$/i', $upper, $matches)) {
            return "NVARCHAR({$matches[1]})";
        }

        // CHAR -> NCHAR
        if (preg_match('/^CHAR\s*\((\d+)\)$/i', $upper, $matches)) {
            return "NCHAR({$matches[1]})";
        }

        // BOOLEAN -> BIT
        if ($upper === 'BOOLEAN' || $upper === 'BOOL' || $upper === 'TINYINT(1)') {
            return 'BIT';
        }

        // ENUM -> NVARCHAR(255)
        if (preg_match('/^ENUM\s*\(/i', $upper)) {
            return 'NVARCHAR(255)';
        }

        return $type;
    }

    /**
     * Build an auto-increment primary key column definition
     *
     * SQL Server uses IDENTITY(1,1).
     *
     * @param   string  $quotedName  The quoted column name
     * @param   string  $type        The column type
     * @return  string  The column definition SQL
     */
    public function buildAutoIncrementColumn(string $quotedName, string $type): string
    {
        return "$quotedName $type IDENTITY(1,1) PRIMARY KEY";
    }

    /**
     * Build a UNIQUE constraint definition for CREATE TABLE
     *
     * SQL Server uses CONSTRAINT ... UNIQUE syntax.
     *
     * @param   string  $quotedName     The quoted constraint name
     * @param   string  $columnList     The column list SQL
     * @return  string  The constraint definition SQL
     */
    public function buildUniqueConstraint(string $quotedName, string $columnList): string
    {
        return "CONSTRAINT $quotedName UNIQUE ($columnList)";
    }

    /**
     * Build a regular INDEX definition for CREATE TABLE
     *
     * SQL Server does not support inline index definitions.
     *
     * @param   string  $quotedName     The quoted index name
     * @param   string  $columnList     The column list SQL
     * @return  string|null  Null - indexes must be created separately
     */
    public function buildIndexDefinition(string $quotedName, string $columnList): ?string
    {
        return null;
    }

    /**
     * Build a FULLTEXT index definition for CREATE TABLE
     *
     * SQL Server uses fulltext catalogs and requires separate setup.
     *
     * @param   string  $quotedName     The quoted index name
     * @param   string  $columnList     The column list SQL
     * @return  string|null  Null - FTS requires CREATE FULLTEXT INDEX
     */
    public function buildFulltextIndexDefinition(string $quotedName, string $columnList): ?string
    {
        return null;
    }

    // =========================================================================
    // Schema Introspection Methods
    // =========================================================================

    /**
     * {@inheritdoc}
     */
    protected function getTableExistsQuery(string $table): string
    {
        return "SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = "
            . $this->quote($table);
    }

    /**
     * Retrieves field information about the given table
     *
     * @param   string  $table     The name of the database table
     * @param   bool    $typeOnly  True (default) to only return field types
     * @return  array
     */
    public function getTableColumns($table, $typeOnly = true)
    {
        $table = $this->replacePrefix($table);
        $this->setQuery(
            "SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, IS_NULLABLE, COLUMN_DEFAULT " .
            "FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = " . $this->quote($table)
        );

        $columns = [];
        $results = $this->loadObjectList();

        foreach ($results as $column) {
            if ($typeOnly) {
                $columns[$column->COLUMN_NAME] = $column->DATA_TYPE;
            } else {
                $columns[$column->COLUMN_NAME] = (object) [
                    'Field'     => $column->COLUMN_NAME,
                    'Type'      => $column->DATA_TYPE,
                    'MaxLength' => $column->CHARACTER_MAXIMUM_LENGTH,
                    'Null'      => $column->IS_NULLABLE,
                    'Default'   => $column->COLUMN_DEFAULT,
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
        $quoted = $this->quote($table);
        $this->setQuery(
            "SELECT i.name AS index_name, c.name AS column_name, "
            . "i.is_unique, i.is_primary_key, ic.key_ordinal "
            . "FROM sys.indexes i "
            . "INNER JOIN sys.index_columns ic "
            . "ON i.object_id = ic.object_id AND i.index_id = ic.index_id "
            . "INNER JOIN sys.columns c "
            . "ON ic.object_id = c.object_id AND ic.column_id = c.column_id "
            . "WHERE i.object_id = OBJECT_ID($quoted) "
            . "ORDER BY i.name, ic.key_ordinal"
        );
        $indexes = $this->loadObjectList();

        $keys = [];
        foreach ($indexes as $index) {
            // Normalize PK name to 'PRIMARY' for cross-DB compatibility
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

        // Include fulltext indexes (stored in sys.fulltext_indexes,
        // not sys.indexes). Use ft_ + column names as the key name
        // so callers can look them up by the name they specified.
        $this->setQuery(
            "SELECT c.name AS column_name "
            . "FROM sys.fulltext_indexes fi "
            . "INNER JOIN sys.fulltext_index_columns fic "
            . "ON fi.object_id = fic.object_id "
            . "INNER JOIN sys.columns c "
            . "ON fic.object_id = c.object_id "
            . "AND fic.column_id = c.column_id "
            . "WHERE fi.object_id = OBJECT_ID($quoted)"
        );
        $ftCols = $this->loadObjectList();
        if (!empty($ftCols)) {
            $colNames = array_map(fn($c) => $c->column_name, $ftCols);
            $ftName = 'ft_' . implode('_', $colNames);
            $keys[$ftName] = (object) [
                'Key_name'     => $ftName,
                'Column_name'  => $ftCols[0]->column_name,
                'Non_unique'   => 1,
                'Seq_in_index' => 1,
                'Index_type'   => 'FULLTEXT',
            ];
        }

        return $keys;
    }

    /**
     * Gets foreign key information for a table
     *
     * @param   string  $table  The table name
     * @return  array   Array of foreign key objects
     */
    public function getForeignKeys($table)
    {
        $table = $this->replacePrefix($table);

        $sql = "SELECT
                    fk.name AS constraint_name,
                    c.name AS column_name,
                    ref_t.name AS referenced_table,
                    ref_c.name AS referenced_column,
                    fk.update_referential_action_desc AS on_update,
                    fk.delete_referential_action_desc AS on_delete
                FROM sys.foreign_keys fk
                INNER JOIN sys.foreign_key_columns fkc
                    ON fk.object_id = fkc.constraint_object_id
                INNER JOIN sys.columns c
                    ON fkc.parent_object_id = c.object_id AND fkc.parent_column_id = c.column_id
                INNER JOIN sys.tables ref_t
                    ON fk.referenced_object_id = ref_t.object_id
                INNER JOIN sys.columns ref_c
                    ON fkc.referenced_object_id = ref_c.object_id AND fkc.referenced_column_id = ref_c.column_id
                WHERE fk.parent_object_id = OBJECT_ID(" . $this->quote($table) . ")
                ORDER BY fk.name, fkc.constraint_column_id";

        $this->setQuery($sql);

        return $this->groupForeignKeyRows($this->loadObjectList(), [
            'constraint_name' => 'constraint_name',
            'column_name'     => 'column_name',
            'foreign_table'   => 'referenced_table',
            'foreign_column'  => 'referenced_column',
            'on_update'       => fn($row) => str_replace('_', ' ', $row->on_update),
            'on_delete'       => fn($row) => str_replace('_', ' ', $row->on_delete),
        ]);
    }

    /**
     * Drop a constraint (foreign key, unique constraint, etc.)
     *
     * @param   string  $table  The table name
     * @param   string  $name   The constraint name
     * @return  bool    True on success
     */
    public function dropConstraint(string $table, string $name): bool
    {
        $table = $this->replacePrefix($table);
        $quotedTable = $this->quoteName($table);

        $this->setQuery("ALTER TABLE $quotedTable DROP CONSTRAINT " . $this->quoteName($name));
        return (bool) $this->execute();
    }

    /**
     * Gets index information for a table (normalized format)
     *
     * Provides a consistent return format across all database drivers.
     *
     * @param   string  $table  The table name
     * @return  array   Array of index objects with 'name', 'columns', 'unique' keys
     */
    public function getIndexes($table)
    {
        $table = $this->replacePrefix($table);
        $indexes = [];

        $this->setQuery(
            "SELECT i.name as index_name, i.is_unique, i.is_primary_key, " .
            "c.name as column_name, ic.key_ordinal " .
            "FROM sys.indexes i " .
            "INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id " .
            "INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id " .
            "WHERE i.object_id = OBJECT_ID(" . $this->quote($table) . ") " .
            "AND i.name IS NOT NULL " .
            "ORDER BY i.name, ic.key_ordinal"
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

            $indexes[$indexName]->columns[] = $row->column_name;
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
        $this->setQuery("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
        return $this->loadColumn();
    }

    /**
     * Shows the table CREATE statement
     *
     * SQL Server doesn't have SHOW CREATE TABLE, so this reconstructs the
     * CREATE TABLE statement from system catalog information.
     *
     * @param   string|array  $tables  A table name or a list of table names
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
     * Generate CREATE TABLE SQL from system catalog
     *
     * @param   string  $table  Table name
     * @return  string
     */
    protected function generateCreateTableSql($table)
    {
        $table = $this->replacePrefix($table);

        // Get columns
        $sql = "SELECT
                    c.COLUMN_NAME,
                    c.DATA_TYPE,
                    c.CHARACTER_MAXIMUM_LENGTH,
                    c.NUMERIC_PRECISION,
                    c.NUMERIC_SCALE,
                    c.IS_NULLABLE,
                    c.COLUMN_DEFAULT,
                    COLUMNPROPERTY(
                        OBJECT_ID(c.TABLE_SCHEMA + '.' + c.TABLE_NAME),
                        c.COLUMN_NAME, 'IsIdentity') AS is_identity
                FROM INFORMATION_SCHEMA.COLUMNS c
                WHERE c.TABLE_NAME = " . $this->quote($table) . "
                ORDER BY c.ORDINAL_POSITION";

        $this->setQuery($sql);
        $columns = $this->loadObjectList();

        if (empty($columns)) {
            return "-- Table '$table' not found";
        }

        // Build column definitions
        $columnDefs = [];
        foreach ($columns as $col) {
            $def = '[' . $col->COLUMN_NAME . '] ' . strtoupper($col->DATA_TYPE);

            // Add length/precision
            if ($col->CHARACTER_MAXIMUM_LENGTH && $col->CHARACTER_MAXIMUM_LENGTH > 0) {
                $def .= '(' . ($col->CHARACTER_MAXIMUM_LENGTH == -1 ? 'MAX' : $col->CHARACTER_MAXIMUM_LENGTH) . ')';
            } elseif ($col->NUMERIC_PRECISION && in_array(strtolower($col->DATA_TYPE), ['decimal', 'numeric'])) {
                $def .= '(' . $col->NUMERIC_PRECISION . ',' . $col->NUMERIC_SCALE . ')';
            }

            // Identity
            if ($col->is_identity) {
                $def .= ' IDENTITY(1,1)';
            }

            // Nullable
            $def .= ($col->IS_NULLABLE === 'YES') ? ' NULL' : ' NOT NULL';

            // Default
            if ($col->COLUMN_DEFAULT !== null) {
                $def .= ' DEFAULT ' . $col->COLUMN_DEFAULT;
            }

            $columnDefs[] = $def;
        }

        // Get primary key
        $sql = "SELECT ku.COLUMN_NAME
                FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc
                JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE ku
                    ON tc.CONSTRAINT_NAME = ku.CONSTRAINT_NAME
                    AND tc.TABLE_NAME = ku.TABLE_NAME
                WHERE tc.TABLE_NAME = " . $this->quote($table) . "
                    AND tc.CONSTRAINT_TYPE = 'PRIMARY KEY'
                ORDER BY ku.ORDINAL_POSITION";

        $this->setQuery($sql);
        $pkColumns = $this->loadColumn();

        if (!empty($pkColumns)) {
            $columnDefs[] = 'PRIMARY KEY ([' . implode('], [', $pkColumns) . '])';
        }

        // Build CREATE TABLE statement
        $createSql = "CREATE TABLE [" . $table . "] (\n    ";
        $createSql .= implode(",\n    ", $columnDefs);
        $createSql .= "\n)";

        return $createSql;
    }

    /**
     * Gets the database collation in use
     *
     * @return  string|bool
     */
    public function getCollation()
    {
        $this->setQuery("SELECT DATABASEPROPERTYEX(DB_NAME(), 'Collation') AS collation");
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
        $this->setQuery(
            "SELECT c.name FROM sys.indexes i " .
            "INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id " .
            "INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id " .
            "WHERE i.is_primary_key = 1 AND i.object_id = OBJECT_ID(" . $this->quote($table) . ")"
        );
        return $this->loadResult();
    }

    /**
     * Get primary key column names
     *
     * @param   string  $table  The table name
     * @return  array   Array of column names in the primary key
     */
    public function getPrimaryKeyColumns($table): array
    {
        $table = $this->replacePrefix($table);
        $this->setQuery(
            "SELECT c.name FROM sys.indexes i " .
            "INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id " .
            "INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id " .
            "WHERE i.is_primary_key = 1 AND i.object_id = OBJECT_ID(" . $this->quote($table) . ") " .
            "ORDER BY ic.key_ordinal"
        );
        $rows = $this->loadColumn();
        return $rows ?: [];
    }

    /**
     * Gets the database engine of the given table
     *
     * SQL Server doesn't have storage engines like MySQL.
     *
     * @param   string  $table  The table name
     * @return  string|bool
     * @note    NO-OP: SQL Server doesn't have storage engines
     */
    public function getEngine($table)
    {
        return 'SQL Server';
    }


    /**
     * Gets the database character set of the given table
     *
     * @param   string  $table  The table name
     * @param   string  $field  Optional field name
     * @return  string|bool
     */
    public function getCharacterSet($table, $field = null)
    {
        // SQL Server uses collation which includes character set info
        return $this->getCollation();
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
        // IDENT_CURRENT returns the last identity value generated.
        // Add 1 to return the *next* value (matching MySQL semantics).
        $this->setQuery(
            "SELECT IDENT_CURRENT(" . $this->quote($table)
            . ") + 1 AS auto_increment"
        );
        $result = $this->loadObject();
        return $result ? (int) $result->auto_increment : false;
    }

    /**
     * Sets the auto-increment starting value for the given table
     *
     * @param   string  $table  The table name
     * @param   int     $value  The auto-increment starting value
     * @return  bool
     */
    public function setAutoIncrement($table, $value): bool
    {
        $table = $this->replacePrefix($table);
        // DBCC CHECKIDENT RESEED sets the *current* identity value,
        // so next insert gets value+1. Subtract 1 so that the next
        // auto-generated value equals $value (matching MySQL semantics).
        $reseedValue = max(0, (int) $value - 1);
        $this->setQuery(
            "DBCC CHECKIDENT (" . $this->quote($table)
            . ", RESEED, " . $reseedValue . ")"
        );
        $this->execute();
        return true;
    }

    /**
     * Locks a table in the database
     *
     * SQL Server uses different locking hints in queries.
     *
     * @param   string  $table  The name of the table to lock
     * @return  $this
     */
    public function lockTable($table)
    {
        // SQL Server uses table hints like WITH (TABLOCKX) in queries
        // This is a no-op; locking is handled differently in SQL Server
        return $this;
    }

    /**
     * Unlocks all tables in the database
     *
     * @return  $this
     */
    public function unlockTables()
    {
        // SQL Server doesn't have UNLOCK TABLES
        return $this;
    }

    /**
     * Renames a table in the database
     *
     * @param   string  $oldTable  The name of the table to be renamed
     * @param   string  $newTable  The new name for the table
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

        return $this;
    }

    /**
     * Commits a transaction
     *
     * Supports nested transactions via savepoints.
     * Note: SQL Server savepoints are released implicitly on commit.
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
        // For nested transactions (savepoints), no explicit release needed
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
            $this->setQuery(
                'ROLLBACK TRANSACTION SP_' . $this->transactionDepth
            );
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
            // Use PDO native method — pdo_sqlsrv tracks transaction state
            // internally and rejects raw BEGIN TRANSACTION via prepare/execute
            $this->getConnection()->beginTransaction();
        } else {
            $this->setQuery('SAVE TRANSACTION SP_' . $this->transactionDepth);
            $this->execute();
        }

        $this->transactionDepth++;
    }

    /**
     * Execute the current query, auto-toggling IDENTITY_INSERT when needed
     *
     * SQL Server requires SET IDENTITY_INSERT table ON before inserting
     * explicit values into an IDENTITY column. This override detects
     * such INSERTs and wraps them transparently.
     *
     * @return  $this|int
     * @throws  QueryFailedException
     */
    public function execute()
    {
        $identityTable = $this->detectIdentityInsert();

        if ($identityTable) {
            $quoted = $this->quoteName($identityTable);
            $this->getConnection()->exec(
                "SET IDENTITY_INSERT {$quoted} ON"
            );
            // INSERT...SELECT without column list needs columns added
            $this->injectColumnListForInsertSelect($identityTable);
        }

        try {
            $result = parent::execute();
        } finally {
            if ($identityTable) {
                $this->getConnection()->exec(
                    "SET IDENTITY_INSERT {$quoted} OFF"
                );
            }
        }

        return $result;
    }

    /**
     * For INSERT INTO table SELECT ... (without column list),
     * SQL Server requires an explicit column list when IDENTITY_INSERT
     * is ON. This rewrites the statement to add one.
     *
     * @param  string  $table  The target table name
     * @return void
     */
    protected function injectColumnListForInsertSelect(string $table): void
    {
        if (!$this->statement) {
            return;
        }

        $sql = $this->statement->queryString ?? '';
        $ident = '(?:"[^"]+"|\\[[^\\]]+\\]|[\\w.]+)';
        $pattern = '/^(INSERT\s+INTO\s+' . $ident . ')\s+(SELECT\b)/i';

        if (!preg_match($pattern, $sql, $m)) {
            return;
        }

        // Query column names directly via raw PDO to avoid clobbering
        // the driver's prepared statement and bindings
        $colStmt = $this->getConnection()->prepare(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS "
            . "WHERE TABLE_NAME = ? ORDER BY ORDINAL_POSITION"
        );
        $colStmt->execute([$table]);
        $colNames = $colStmt->fetchAll(\PDO::FETCH_COLUMN, 0);

        if (empty($colNames)) {
            return;
        }

        $colList = implode(', ', array_map(
            fn($col) => $this->quoteName($col),
            $colNames
        ));

        // Find where SELECT starts after INSERT INTO table
        $selectPos = stripos($sql, 'SELECT', strlen($m[1]));
        $newSql = $m[1] . " ($colList) " . substr($sql, $selectPos);

        // Re-prepare with the rewritten SQL and rebind parameters
        $savedBindings = $this->bindings;
        $this->prepare($newSql);
        if (!empty($savedBindings)) {
            $this->bind($savedBindings);
        }
    }

    /**
     * Detect if the current statement is an INSERT that includes
     * an IDENTITY column, requiring IDENTITY_INSERT to be toggled
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

        // Match table name and column list from INSERT INTO.
        // Identifiers may be quoted with double quotes, square brackets,
        // or unquoted: INSERT INTO "t" ("col") / [t] ([col]) / t (col)
        $ident = '(?:"([^"]+)"|\[([^\]]+)\]|([\w.]+))';

        // Pattern 1: INSERT INTO table (columns) ...
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
            return null;
        }

        // Pattern 2: INSERT INTO table SELECT ... (no column list)
        $pattern2 = '/INSERT\s+INTO\s+' . $ident . '\s+SELECT\b/i';
        if (preg_match($pattern2, $sql, $matches)) {
            $table = $matches[1] ?: ($matches[2] ?: $matches[3]);
            // If the table has an IDENTITY column, toggle is needed
            // since SELECT will include data for that column
            if ($this->getIdentityColumn($table)) {
                return $table;
            }
        }

        return null;
    }

    /**
     * Get the IDENTITY column name for a table, with caching
     *
     * @param   string       $table  Table name
     * @return  string|null  Column name or null if no identity column
     */
    public function getIdentityColumn(string $table): ?string
    {
        if (!array_key_exists($table, $this->identityColumns)) {
            $this->identityColumns[$table] = $this->queryIdentityColumn(
                $table
            );
        }

        $result = $this->identityColumns[$table];
        return $result === false ? null : $result;
    }

    /**
     * Query sys.columns for the IDENTITY column of a table
     *
     * @param   string        $table  Table name
     * @return  string|false  Column name or false if none
     */
    protected function queryIdentityColumn(string $table)
    {
        try {
            $stmt = $this->getConnection()->prepare(
                "SELECT c.name FROM sys.columns c "
                . "WHERE c.object_id = OBJECT_ID(?) "
                . "AND c.is_identity = 1"
            );
            $stmt->execute([$table]);
            $result = $stmt->fetchColumn();
            return $result !== false ? $result : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Gets the version of the database connector
     *
     * @return  string
     */
    public function getVersion()
    {
        $this->setQuery("SELECT SERVERPROPERTY('ProductVersion') AS version");
        $result = $this->loadObject();
        return $result ? $result->version : 'Unknown';
    }

    /**
     * Get the full version string
     *
     * @return  string|null
     */
    public function getFullVersion()
    {
        $this->setQuery("SELECT @@VERSION AS version");
        $result = $this->loadObject();
        return $result ? $result->version : null;
    }


    /**
     * Get database server version information
     *
     * @return  array  Array with standardized keys:
     *                  - 'version': Version number (x.y.z format)
     *                  - 'driver_version': Normalized version - STANDARD KEY
     *                  - 'sqlsrv_version': Alias for driver_version
     *                  - 'full_version': Full version string from server
     *                  - 'comment': Version comment/description
     */
    public function getServerInfo()
    {
        $version = $this->getVersion();
        $fullVersion = $this->getFullVersion();

        return [
            'version'        => $version,
            'driver_version' => $version,        // Standard key for all drivers
            'sqlsrv_version' => $version,        // Driver-specific alias
            'full_version'   => $fullVersion,
            'comment'        => 'SQL Server',
        ];
    }

    /**
     * Selects a database for use
     *
     * @param   string  $database  The name of the database
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
        // SQL Server handles encoding via connection string and collation
        return true;
    }

    /**
     * Truncate a table
     *
     * @param   string  $table   The table to truncate
     * @param   int     $nextId  The next auto-increment value (default 1)
     * @return  $this
     */
    public function truncateTable($table, int $nextId = 1)
    {
        $table = $this->replacePrefix($table);
        $this->setQuery('TRUNCATE TABLE ' . $this->quoteName($table));
        $this->execute();

        if ($nextId !== 1) {
            // After TRUNCATE the table is empty, so RESEED, N makes the
            // next insert get N (not N+1 as it would with rows present)
            $this->setQuery(
                'DBCC CHECKIDENT ('
                . $this->quote($table) . ', RESEED, '
                . $nextId . ')'
            );
            $this->execute();
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
        // SQL Server doesn't support per-table charset changes like MySQL
        // Collation is set at column level
        return true;
    }

    // =========================================================================
    // DDL Helper Methods
    // =========================================================================

    /**
     * Modify a column definition
     *
     * @param   string  $table       Table name
     * @param   string  $column      Column name
     * @param   string  $definition  New column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    protected function buildModifyColumnSql(
        string $table,
        string $column,
        string $definition,
        string $comment
    ): string {
        return "ALTER TABLE " . $this->quoteName($table)
            . " ALTER COLUMN " . $this->quoteName($column)
            . " " . $definition;
    }

    /**
     * Modify a column definition and move it after a specific column
     *
     * SQL Server doesn't support column positioning.
     *
     * @param   string  $table        Table name
     * @param   string  $column       Column name
     * @param   string  $definition   New column definition
     * @param   string  $afterColumn  Column to position after (ignored)
     * @param   string  $comment      Optional column comment
     * @return  bool
     */
    public function modifyColumnAfter(
        string $table,
        string $column,
        string $definition,
        string $afterColumn,
        string $comment = ''
    ): bool {
        // SQL Server doesn't support column positioning
        return $this->modifyColumn($table, $column, $definition, $comment);
    }

    /**
     * Modify a column definition and move it before a specific column
     *
     * SQL Server doesn't support column positioning.
     *
     * @param   string  $table         Table name
     * @param   string  $column        Column name
     * @param   string  $definition    New column definition
     * @param   string  $beforeColumn  Column to position before (ignored)
     * @param   string  $comment       Optional column comment
     * @return  bool
     */
    public function modifyColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool {
        // SQL Server doesn't support column positioning
        return $this->modifyColumn($table, $column, $definition, $comment);
    }

    /**
     * Modify a column definition and move it to the first position
     *
     * SQL Server doesn't support column positioning.
     *
     * @param   string  $table       Table name
     * @param   string  $column      Column name
     * @param   string  $definition  New column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    public function modifyColumnFirst(string $table, string $column, string $definition, string $comment = ''): bool
    {
        // SQL Server doesn't support column positioning
        return $this->modifyColumn($table, $column, $definition, $comment);
    }

    /**
     * Change a column name and/or definition
     *
     * @param   string  $table       Table name
     * @param   string  $oldColumn   Current column name
     * @param   string  $newColumn   New column name
     * @param   string  $definition  New column definition
     * @param   string  $comment     Optional column comment
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

        // First rename the column
        if ($oldColumn !== $newColumn) {
            $this->setQuery(
                "EXEC sp_rename "
                . $this->quote($table . '.' . $oldColumn) . ", "
                . $this->quote($newColumn) . ", 'COLUMN'"
            );
            $this->execute();
        }

        // Then modify the definition
        $this->setQuery(
            "ALTER TABLE " . $this->quoteName($table)
            . " ALTER COLUMN " . $this->quoteName($newColumn)
            . " " . $definition
        );
        $this->execute();

        return true;
    }

    /**
     * Add a column to a table
     *
     * @param   string  $table       Table name
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    protected function buildAddColumnSql(
        string $table,
        string $column,
        string $definition,
        string $comment
    ): string {
        return "ALTER TABLE " . $this->quoteName($table)
            . " ADD " . $this->quoteName($column)
            . " " . $definition;
    }

    /**
     * Add a column after a specific column
     *
     * SQL Server doesn't support column positioning.
     *
     * @param   string  $table        Table name
     * @param   string  $column       Column name
     * @param   string  $definition   Column definition
     * @param   string  $afterColumn  Column to add after (ignored)
     * @param   string  $comment      Optional column comment
     * @return  bool
     */
    public function addColumnAfter(
        string $table,
        string $column,
        string $definition,
        string $afterColumn,
        string $comment = ''
    ): bool {
        // SQL Server doesn't support column positioning
        return $this->addColumn($table, $column, $definition, $comment);
    }

    /**
     * Add a column before a specific column
     *
     * SQL Server doesn't support column positioning.
     *
     * @param   string  $table         Table name
     * @param   string  $column        Column name
     * @param   string  $definition    Column definition
     * @param   string  $beforeColumn  Column to add before (ignored)
     * @param   string  $comment       Optional column comment
     * @return  bool
     */
    public function addColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool {
        // SQL Server doesn't support column positioning
        return $this->addColumn($table, $column, $definition, $comment);
    }

    /**
     * Add a column at the beginning of a table
     *
     * SQL Server doesn't support column positioning.
     *
     * @param   string  $table       Table name
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    public function addColumnFirst(string $table, string $column, string $definition, string $comment = ''): bool
    {
        // SQL Server doesn't support column positioning
        return $this->addColumn($table, $column, $definition, $comment);
    }

    /**
     * Add a column at the end of a table
     *
     * @param   string  $table       Table name
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    public function addColumnLast(string $table, string $column, string $definition, string $comment = ''): bool
    {
        return $this->addColumn($table, $column, $definition, $comment);
    }

    // =========================================================================
    // ENUM Support Methods
    // =========================================================================

    /**
     * Get the values from an ENUM column
     *
     * SQL Server doesn't have native ENUM support. Use CHECK constraints instead.
     *
     * @note    NO-OP: SQL Server doesn't support ENUM types.
     * @see     supportsEnum() to detect if this operation is available
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @return  array   Always empty (SQL Server doesn't have ENUM)
     */
    public function getEnumValues($table, $column)
    {
        // SQL Server doesn't have ENUM type - use CHECK constraints for value validation
        return [];
    }

    /**
     * Add a value to an ENUM column
     *
     * SQL Server doesn't have native ENUM support - this is a no-op.
     *
     * @note    NO-OP: SQL Server doesn't support ENUM types. Use CHECK constraints for value validation.
     * @see     supportsEnum() to detect if this operation is available
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @param   string  $value   The value to add
     * @return  bool    Always true (no-op)
     */
    public function addEnumValue($table, $column, $value)
    {
        // SQL Server doesn't have ENUM type - nothing to do
        return true;
    }

    /**
     * Remove a value from an ENUM column
     *
     * SQL Server doesn't have native ENUM support - this is a no-op.
     *
     * @note    NO-OP: SQL Server doesn't support ENUM types. Use CHECK constraints for value validation.
     * @see     supportsEnum() to detect if this operation is available
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @param   string  $value   The value to remove
     * @return  bool    Always true (no-op)
     */
    public function removeEnumValue($table, $column, $value)
    {
        // SQL Server doesn't have ENUM type - nothing to do
        return true;
    }

    // =========================================================================
    // View Methods
    // =========================================================================

    /**
     * Creates or replaces a database view
     *
     * SQL Server uses ALTER VIEW for existing views or CREATE VIEW for new ones.
     * This method drops and recreates the view if it exists.
     *
     * @note    MySQL-specific options (algorithm, definer, security) are ignored on SQL Server.
     *
     * @param   string  $name       The view name (with or without prefix)
     * @param   string  $selectSql  The SELECT statement for the view (prefixes will be replaced)
     * @param   array   $options    MySQL-specific options (ignored on SQL Server)
     * @return  bool
     */
    public function createOrReplaceView($name, $selectSql, array $options = []): bool
    {
        // Note: $options (algorithm, definer, security) are MySQL-specific and ignored on SQL Server

        $viewName = $this->replacePrefix($name);
        $selectSql = $this->replacePrefix($selectSql);

        // SQL Server doesn't have CREATE OR REPLACE VIEW, so drop first if exists
        $this->dropView($name, true);

        $sql = 'CREATE VIEW ' . $this->quoteName($viewName) . ' AS ' . $selectSql;
        $this->setQuery($sql);
        $this->execute();

        return true;
    }

    /**
     * Drops a database view
     *
     * @param   string  $name      The view name (without prefix)
     * @param   bool    $ifExists  Whether to use IF EXISTS clause (SQL Server 2016+)
     * @return  bool
     */
    public function dropView($name, $ifExists = true): bool
    {
        $tableName = $this->replacePrefix($name);

        if ($ifExists) {
            // SQL Server 2016+ supports DROP VIEW IF EXISTS
            // For older versions, check existence first
            $sql = 'DROP VIEW IF EXISTS ' . $this->quoteName($tableName);
        } else {
            $sql = 'DROP VIEW ' . $this->quoteName($tableName);
        }

        $this->setQuery($sql);
        $this->execute();

        return true;
    }

    /**
     * Checks if a view exists in the database
     *
     * @param   string  $name  The view name (without prefix)
     * @return  bool
     */
    public function viewExists($name): bool
    {
        $tableName = $this->replacePrefix($name);
        $this->setQuery(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_NAME = " . $this->quote($tableName)
        );

        return (bool) $this->loadResult();
    }

    /**
     * Returns a list of all views in the current database
     *
     * @return  array  Array of view names
     **/
    public function getViews(): array
    {
        $this->setQuery(
            "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.VIEWS ORDER BY TABLE_NAME"
        );

        return $this->loadColumn() ?: [];
    }

    /**
     * Returns a list of all database names on the server
     *
     * Excludes system databases (master, tempdb, model, msdb).
     *
     * @return  array  Array of database names
     **/
    public function getDatabaseNames(): array
    {
        $this->setQuery(
            "SELECT name FROM sys.databases WHERE database_id > 4 ORDER BY name"
        );

        return $this->loadColumn() ?: [];
    }

    /**
     * Returns a list of all sequences in the current database
     *
     * SQL Server supports sequences since version 2012.
     *
     * @return  array  Array of SequenceInfo objects
     **/
    public function getSequences(): array
    {
        $this->setQuery(
            "SELECT
                s.name,
                SCHEMA_NAME(s.schema_id) AS schema_name,
                s.start_value,
                s.minimum_value AS min_value,
                s.maximum_value AS max_value,
                s.increment,
                s.is_cycling AS cycle,
                s.cache_size,
                s.current_value,
                TYPE_NAME(s.system_type_id) AS data_type
            FROM sys.sequences s
            ORDER BY SCHEMA_NAME(s.schema_id), s.name"
        );

        $results = $this->loadObjectList();
        $sequences = [];

        if ($results) {
            foreach ($results as $row) {
                $sequences[] = new \Hubzero\Database\Schema\SequenceInfo([
                    'name' => $row->name,
                    'schema' => $row->schema_name,
                    'current_value' => isset($row->current_value) ? (int) $row->current_value : null,
                    'start_value' => (int) $row->start_value,
                    'min_value' => (int) $row->min_value,
                    'max_value' => $row->max_value,
                    'increment' => (int) $row->increment,
                    'cycle' => (bool) $row->cycle,
                    'cache' => (int) ($row->cache_size ?? 1),
                    'data_type' => $row->data_type,
                ]);
            }
        }

        return $sequences;
    }

    /**
     * Creates a new sequence
     *
     * @param   string  $name       The sequence name
     * @param   int     $start      Starting value (default: 1)
     * @param   int     $increment  Increment value (default: 1)
     * @param   array   $options    Additional options (min, max, cycle, cache, schema)
     * @return  bool
     **/
    public function createSequence($name, $start = 1, $increment = 1, array $options = []): bool
    {
        $schema = $options['schema'] ?? 'dbo';
        $fullName = $this->quoteName($schema) . '.' . $this->quoteName($name);

        $sql = "CREATE SEQUENCE {$fullName}";
        $sql .= " START WITH " . (int) $start;
        $sql .= " INCREMENT BY " . (int) $increment;

        if (isset($options['min'])) {
            $sql .= " MINVALUE " . (int) $options['min'];
        }

        if (isset($options['max'])) {
            $sql .= " MAXVALUE " . (int) $options['max'];
        }

        if (isset($options['cycle']) && $options['cycle']) {
            $sql .= " CYCLE";
        } else {
            $sql .= " NO CYCLE";
        }

        if (isset($options['cache'])) {
            $sql .= " CACHE " . (int) $options['cache'];
        }

        $this->setQuery($sql);
        $this->execute();

        return true;
    }

    /**
     * Drops a sequence
     *
     * @param   string  $name      The sequence name
     * @param   bool    $ifExists  Whether to use IF EXISTS clause
     * @return  bool
     **/
    public function dropSequence($name, $ifExists = true): bool
    {
        $sql = "DROP SEQUENCE";
        if ($ifExists) {
            $sql .= " IF EXISTS";
        }
        $sql .= " " . $this->quoteName($name);

        $this->setQuery($sql);
        $this->execute();

        return true;
    }

    /**
     * Checks if a sequence exists
     *
     * @param   string  $name  The sequence name
     * @return  bool
     **/
    public function sequenceExists($name): bool
    {
        $this->setQuery(
            "SELECT COUNT(*) FROM sys.sequences WHERE name = " . $this->quote($name)
        );
        return (bool) $this->loadResult();
    }

    /**
     * Gets the next value from a sequence
     *
     * @param   string  $name  The sequence name
     * @return  int
     **/
    public function nextSequenceValue($name): int
    {
        $this->setQuery("SELECT NEXT VALUE FOR " . $this->quoteName($name));
        return (int) $this->loadResult();
    }

    /**
     * Gets the current value of a sequence (without incrementing)
     *
     * @param   string  $name  The sequence name
     * @return  int
     **/
    public function currentSequenceValue($name): int
    {
        $this->setQuery(
            "SELECT current_value FROM sys.sequences WHERE name = "
            . $this->quote($name)
        );
        return (int) $this->loadResult();
    }

    /**
     * Check if this driver supports sequences
     *
     * @return  bool
     **/
    public function supportsSequences(): bool
    {
        return true;
    }

    /**
     * Set the storage engine for a table
     *
     * @param   string  $table   Table name
     * @param   string  $engine  Engine type (ignored)
     * @return  bool
     * @note    NO-OP: SQL Server doesn't have storage engines
     */
    public function setTableEngine(string $table, string $engine = 'MYISAM'): bool
    {
        return true;
    }

    /**
     * Set the character set and collation for a table
     *
     * @param   string  $table      Table name
     * @param   string  $charset    Character set
     * @param   string  $collation  Collation
     * @return  bool
     * @note    NO-OP: SQL Server handles collation differently
     */
    public function setTableCharset(
        string $table,
        string $charset = 'utf8',
        string $collation = 'utf8_general_ci'
    ): bool {
        return true;
    }

    /**
     * Drop a column from a table
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name
     * @return  bool
     */
    protected function buildDropColumnSql(string $table, string $column): string
    {
        return "ALTER TABLE " . $this->quoteName($table) . " DROP COLUMN " . $this->quoteName($column);
    }

    /**
     * Add a FULLTEXT index to a table
     *
     * SQL Server full-text requires a full-text catalog.
     *
     * @param   string        $table    Table name
     * @param   string        $name     Index name
     * @param   string|array  $columns  Column name(s) to index
     * @return  bool
     */
    public function addFulltextIndex(string $table, string $name, $columns): bool
    {
        // When Full-Text Search is not installed, fall back to a
        // regular index (same approach as Firebird/Informix/Oracle).
        if (!$this->isFullTextInstalled()) {
            return $this->addIndex($table, $name, $columns);
        }

        $table = $this->replacePrefix($table);
        $qt = $this->quoteName($table);
        $columns = (array) $columns;

        // SQL Server fulltext requires a catalog and a unique index.
        // 1. Create a default fulltext catalog if it doesn't exist.
        $catalog = 'ft_catalog_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $table);
        $this->setQuery(
            "IF NOT EXISTS (SELECT 1 FROM sys.fulltext_catalogs "
            . "WHERE name = " . $this->quote($catalog) . ") "
            . "CREATE FULLTEXT CATALOG " . $this->quoteName($catalog)
        );
        $this->execute();

        // 2. Find the unique index on the table (PK or first unique).
        $this->setQuery(
            "SELECT TOP 1 i.name FROM sys.indexes i "
            . "WHERE i.object_id = OBJECT_ID(" . $this->quote($table) . ") "
            . "AND i.is_unique = 1 ORDER BY i.is_primary_key DESC"
        );
        $keyIndex = $this->loadResult();

        if (!$keyIndex) {
            return false;
        }

        // 3. Create the fulltext index.
        $colList = implode(', ', array_map(
            fn($c) => $this->quoteName($c),
            $columns
        ));
        $this->setQuery(
            "CREATE FULLTEXT INDEX ON $qt ($colList) "
            . "KEY INDEX " . $this->quoteName($keyIndex)
            . " ON " . $this->quoteName($catalog)
        );
        $this->execute();

        return true;
    }

    /**
     * Check whether Full-Text Search is installed on this SQL Server instance.
     *
     * @return bool
     */
    public function isFullTextInstalled(): bool
    {
        $this->setQuery("SELECT SERVERPROPERTY('IsFullTextInstalled')");
        return (bool) $this->loadResult();
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

        // Get the primary key constraint name
        $this->setQuery(
            "SELECT name FROM sys.key_constraints " .
            "WHERE type = 'PK' AND parent_object_id = OBJECT_ID(" . $this->quote($table) . ")"
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
     * @param   string|array  $columns  Column name(s) for the primary key
     * @return  bool
     */
    public function addPrimaryKey(string $table, $columns): bool
    {
        $table = $this->replacePrefix($table);
        $columns = (array) $columns;
        $qt = $this->quoteName($table);

        // SQL Server requires PK columns to be NOT NULL
        $tableColumns = $this->getTableColumns($table, false);
        foreach ($columns as $col) {
            if (isset($tableColumns[$col])) {
                $colType = $this->buildFullColumnType($tableColumns[$col]);
                $this->setQuery(
                    "ALTER TABLE $qt ALTER COLUMN "
                    . $this->quoteName($col) . " $colType NOT NULL"
                );
                $this->execute();
            }
        }

        $columnList = implode(', ', array_map([$this, 'quoteName'], $columns));
        $this->setQuery("ALTER TABLE $qt ADD PRIMARY KEY ($columnList)");
        $this->execute();

        return true;
    }

    /**
     * Add an auto-increment primary key column to a table
     *
     * @param   string  $table      Table name
     * @param   string  $column     Column name
     * @param   bool    $first      Add as first column (ignored)
     * @param   bool    $useBigInt  Use BIGINT (true) or INT (false)
     * @return  bool
     */
    public function addAutoIncrementPrimaryKey(
        string $table,
        string $column = 'id',
        bool $first = false,
        bool $useBigInt = true
    ): bool {
        $table = $this->replacePrefix($table);

        // Check table exists
        if (!$this->tableExists($table)) {
            return false;
        }

        // Idempotent: if column already exists, return true
        if ($this->tableHasField($table, $column)) {
            return true;
        }

        $type = $useBigInt ? 'BIGINT' : 'INT';

        $this->setQuery(
            "ALTER TABLE " . $this->quoteName($table)
            . " ADD " . $this->quoteName($column)
            . " $type IDENTITY(1,1) PRIMARY KEY"
        );
        $this->execute();

        return true;
    }

    /**
     * Populate a column with sequential integer values for existing rows
     *
     * Uses SQL Server's CTE with ROW_NUMBER() window function.
     *
     * @param   string       $table    Table name (with or without prefix)
     * @param   string       $column   Column name to populate
     * @param   string|null  $orderBy  Optional column to order by when assigning sequence
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
        // SQL Server requires ORDER BY in ROW_NUMBER(); use (SELECT NULL) for arbitrary order
        $orderClause = $orderBy ? $this->quoteName($orderBy) : '(SELECT NULL)';

        // Use SQL Server's CTE with ROW_NUMBER() to update in place
        $query = "WITH cte AS (" .
                 "SELECT $quotedColumn, ROW_NUMBER() OVER (ORDER BY $orderClause) AS rn " .
                 "FROM $quotedTable) " .
                 "UPDATE cte SET $quotedColumn = rn";

        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Build a full column type string from column info object
     *
     * Reconstructs type with length, e.g. "NVARCHAR(255)" from
     * the column info returned by getTableColumns($table, false).
     *
     * @param   object  $colInfo  Column info with Type and MaxLength
     * @return  string  Full SQL type string
     */
    public function buildFullColumnType(object $colInfo): string
    {
        $type = strtoupper($colInfo->Type);

        // Add length for character types
        if (
            $colInfo->MaxLength !== null
            && in_array($type, ['NVARCHAR', 'VARCHAR', 'NCHAR', 'CHAR', 'VARBINARY'])
        ) {
            $len = ($colInfo->MaxLength == -1) ? 'MAX' : $colInfo->MaxLength;
            $type .= "($len)";
        }

        return $type;
    }

    /**
     * Add an index to a table
     *
     * @param   string        $table    Table name
     * @param   string        $name     Index name
     * @param   string|array  $columns  Column name(s) to index
     * @param   bool          $unique   Whether to create a unique index
     * @return  bool
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
     * @param   string  $table  Table name
     * @param   string  $name   Index name
     * @return  bool
     */
    public function dropIndex(string $table, string $name): bool
    {
        $table = $this->replacePrefix($table);
        $this->setQuery("DROP INDEX " . $this->quoteName($name) . " ON " . $this->quoteName($table));
        $this->execute();
        return true;
    }

    /**
     * Get the schema grammar instance for SQL Server
     *
     * @return  \Hubzero\Database\Schema\Grammar
     */
    public function getSchemaGrammar()
    {
        return new \Hubzero\Database\Schema\Grammars\SqlsrvGrammar($this);
    }


    /**
     * Build a SQL Server column definition string
     *
     * @param   string  $name        Column name
     * @param   array   $definition  Column definition
     * @return  string
     */
    protected function buildSqlsrvColumnDefinition(string $name, array $definition): string
    {
        $type = $definition['type'];
        $modifiers = $definition['modifiers'] ?? [];

        // Detect AUTO_INCREMENT
        $hasAutoIncrement = !empty($modifiers['autoIncrement'])
            || stripos($type, 'AUTO_INCREMENT') !== false;
        if ($hasAutoIncrement) {
            $type = preg_replace('/\s*AUTO_INCREMENT\s*/i', ' ', $type);
            $type = trim($type);
        }

        // Normalize abstract types (string→NVARCHAR(255), boolean→BIT, etc.)
        // and MySQL-style types (INT(11)→INT, VARCHAR→NVARCHAR, etc.)
        $type = $this->normalizeColumnType($type, $modifiers);

        // Translate zero-date default to NULL
        if (
            array_key_exists('default', $modifiers)
            && self::isZeroDate($modifiers['default'])
        ) {
            $modifiers['nullable'] = true;
            $modifiers['default'] = null;
        }

        $parts = [$this->quoteIdentifier($name), $type];

        // Handle auto-increment (SQL Server uses IDENTITY)
        if ($hasAutoIncrement) {
            $parts[] = 'IDENTITY(1,1)';
            $parts[] = 'NOT NULL';
            $parts[] = 'PRIMARY KEY';
            return implode(' ', $parts);
        }

        // DEFAULT before NOT NULL (standard SQL order)
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

        // NULL / NOT NULL
        if (isset($modifiers['nullable'])) {
            $parts[] = $modifiers['nullable'] ? 'NULL' : 'NOT NULL';
        }

        return implode(' ', $parts);
    }

    /**
     * Build a column definition for ALTER TABLE operations
     *
     * Uses normalizeColumnType() to map abstract types to SQL Server types,
     * then applies modifiers (nullable, default, auto-increment).
     *
     * @param   string  $name        The column name
     * @param   array   $definition  The column definition
     * @return  string  The SQL column definition string
     */
    public function buildAlterColumnDefinition(string $name, array $definition): string
    {
        $type = $definition['type'];
        $modifiers = $definition['modifiers'] ?? [];

        // Normalize abstract types to SQL Server types
        $type = $this->normalizeColumnType($type, $modifiers);

        // Detect and normalize AUTO_INCREMENT
        $hasAutoIncrement = !empty($modifiers['autoIncrement'])
            || stripos($type, 'AUTO_INCREMENT') !== false;
        if ($hasAutoIncrement) {
            $type = preg_replace('/\s*AUTO_INCREMENT\s*/i', ' ', $type);
            $type = trim($type);
        }

        // normalizeColumnType handles abstract→native and MySQL→SQL Server
        // mapping. Run it again after stripping AUTO_INCREMENT (if present)
        // to ensure the cleaned type is also properly normalized.
        if ($hasAutoIncrement) {
            $type = $this->normalizeColumnType($type, $modifiers);
        }

        $parts = [$this->quoteIdentifier($name), $type];

        // Translate zero-date default to NULL
        if (
            array_key_exists('default', $modifiers)
            && self::isZeroDate($modifiers['default'])
        ) {
            $modifiers['nullable'] = true;
            $modifiers['default'] = null;
        }

        // Handle auto-increment (SQL Server uses IDENTITY)
        if ($hasAutoIncrement) {
            $parts[] = 'IDENTITY(1,1)';
            $parts[] = 'NOT NULL';
            $parts[] = 'PRIMARY KEY';
            return implode(' ', $parts);
        }

        // DEFAULT before NOT NULL (standard SQL order)
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

        // NULL / NOT NULL
        if (isset($modifiers['nullable'])) {
            $parts[] = $modifiers['nullable'] ? 'NULL' : 'NOT NULL';
        }

        return implode(' ', $parts);
    }

    // =========================================================================
    // Feature Detection Methods
    // =========================================================================

    /**
     * Check if DROP COLUMN is supported
     *
     * @return  bool  True - SQL Server supports DROP COLUMN
     */
    public function supportsDropColumn(): bool
    {
        return true;
    }

    /**
     * Check if generated/computed columns are supported
     *
     * @return  bool  True - SQL Server supports computed columns
     */
    public function supportsGeneratedColumns(): bool
    {
        return true;
    }

    /**
     * Check if JSON data type is supported
     *
     * @return  bool  True - SQL Server 2016+ has JSON support
     */
    public function supportsJson(): bool
    {
        $version = $this->getVersion();
        return version_compare($version, '13.0', '>='); // SQL Server 2016 is version 13
    }

    /**
     * Check if window functions are supported
     *
     * @return  bool  True - SQL Server supports window functions
     */
    public function supportsWindowFunctions(): bool
    {
        return true;
    }

    /**
     * Check if Common Table Expressions (WITH clause) are supported
     *
     * @return  bool  True - SQL Server supports CTEs
     */
    public function supportsCTE(): bool
    {
        return true;
    }

    // =========================================================================
    // Important Overrides
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

        $sql = "SELECT 
                   cc.name AS name,
                   cc.definition AS expression
                FROM sys.check_constraints cc
                JOIN sys.tables t ON cc.parent_object_id = t.object_id
                WHERE t.name = " . $this->quote($table);

        $this->setQuery($sql);
        $results = $this->loadObjectList();

        $constraints = [];
        foreach ($results as $row) {
            $constraints[] = (object) [
                'name' => $row->name,
                'expression' => $row->expression
            ];
        }

        return $constraints;
    }

    /**
     * Add stored (computed) column
     *
     * SQL Server uses AS (...) PERSISTED syntax.
     *
     * @param   string       $table       Table name
     * @param   string       $column      Column name
     * @param   string       $expression  SQL expression
     * @param   string|null  $type        Column type (ignored/inferred by SQL Server)
     * @return  bool
     */
    public function addStoredColumn(string $table, string $column, string $expression, ?string $type = null): bool
    {
        $table = $this->replacePrefix($table);
        $column = $this->quoteName($column);
        $this->setQuery("ALTER TABLE $table ADD $column AS ($expression) PERSISTED");
        return $this->execute();
    }

    /**
     * Add virtual (computed) column
     *
     * SQL Server default computed columns are virtual.
     *
     * @param   string       $table       Table name
     * @param   string       $column      Column name
     * @param   string       $expression  SQL expression
     * @param   string|null  $type        Column type (ignored/inferred by SQL Server)
     * @return  bool
     */
    public function addVirtualColumn(string $table, string $column, string $expression, ?string $type = null): bool
    {
        $table = $this->replacePrefix($table);
        $column = $this->quoteName($column);
        $this->setQuery("ALTER TABLE $table ADD $column AS ($expression)");
        return $this->execute();
    }

    /**
     * Get global configuration variables
     *
     * @return  array
     */
    public function getGlobalVariables(): array
    {
        $this->setQuery("SELECT name, value FROM sys.configurations ORDER BY name");
        try {
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
        $this->setQuery("SELECT DATEDIFF(SECOND, sqlserver_start_time, GETDATE()) as uptime FROM sys.dm_os_sys_info");
        try {
            return (int) $this->loadResult();
        } catch (\Exception $e) {
            return null;
        }
    }
}
