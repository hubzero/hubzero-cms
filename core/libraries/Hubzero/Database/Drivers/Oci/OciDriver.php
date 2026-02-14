<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Oci;

use Hubzero\Database\Drivers\Base\BaseSqlDriver;
use Hubzero\Database\Exception\ConnectionFailedException;
use Hubzero\Database\Exception\QueryFailedException;

/**
 * Oracle (PDO_OCI) database driver
 *
 * Oracle Database is a multi-model database management system produced and
 * marketed by Oracle Corporation. This driver provides Oracle-specific
 * functionality including:
 *
 * Key Oracle-specific features:
 * - Sequences (Oracle uses sequences instead of AUTO_INCREMENT)
 * - MERGE statements for UPSERT operations
 * - Analytic/Window functions
 * - PL/SQL stored procedures
 * - Partitioned tables
 * - Advanced Compression
 * - Transparent Data Encryption
 * - Real Application Clusters (RAC)
 * - Data Guard for high availability
 *
 * Oracle-specific differences handled:
 * - No native LIMIT/OFFSET (uses ROWNUM or FETCH FIRST in 12c+)
 * - No INSERT IGNORE (uses error handling or MERGE)
 * - Case sensitivity in identifiers (uppercase by default)
 * - Sequences instead of AUTO_INCREMENT
 * - TO_DATE/TO_CHAR for date formatting
 * - NVL instead of IFNULL
 * - DUAL table for SELECT without FROM
 *
 * INHERITANCE:
 * This class extends Sql (the universal SQL base) which extends Pdo
 * (the connection layer). All Oracle-specific SQL syntax is here.
 *
 * REQUIREMENTS:
 * - PHP PDO_OCI extension (php-pdo-oci)
 * - Oracle Instant Client libraries
 * - Oracle Database 11g or later (12c+ recommended for full feature support)
 *
 */
class OciDriver extends BaseSqlDriver
{
    /**
     * The name of the database driver
     *
     * @var string
     */
    protected $name = 'oci';

    /**
     * Abstract type to Oracle SQL type mapping
     *
     * Overrides the base Sql class MySQL defaults with Oracle-specific types.
     * Used by normalizeColumnType() via the parent class lookup.
     *
     * @var array<string, string>
     */

    /**
     * The current transaction depth (for savepoint support)
     *
     * @var int
     */
    protected $transactionDepth = 0;

    /**
     * Oracle version (cached after first query)
     *
     * @var string|null
     */
    protected $oracleVersion = null;

    /**
     * The table name from the last INSERT statement
     *
     * Used by insertid() to find the identity sequence.
     *
     * @var string|null
     */
    protected $lastInsertTable = null;

    /**
     * Temporary LOB stream resources kept alive until execution
     *
     * @var array
     */
    protected $lobStreams = [];

    /**
     * @inheritdoc
     */
    protected function resetDriverState(array $options = []): void
    {
        $this->lastInsertTable = null;
        $this->lobStreams = [];
    }

    /**
     * Oracle's NOW() equivalent expression
     *
     * @var string
     */
    protected string $nowExpression = 'SYSDATE';

    /**
     * Oracle's random number function
     *
     * @var string
     */
    protected string $randExpression = 'DBMS_RANDOM.VALUE';

    /**
     * Oracle's string length function
     *
     * @var string
     */
    protected string $lengthFunction = 'LENGTH';

    /**
     * Oracle's IFNULL equivalent function
     *
     * @var string
     */
    protected string $ifNullFunction = 'NVL';

    /**
     * Constructs a new database object based on the given params
     *
     * @param   array  $options  The database connection params
     * @return  void
     * @throws  ConnectionFailedException  If the DSN is invalid or connection fails
     */
    public function __construct($options)
    {
        // Add "extra" options as needed
        if (!isset($options['extras'])) {
            $options['extras'] = [];
        }

        // Establish connection string (TNS or Easy Connect)
        if (!isset($options['dsn'])) {
            // Support Easy Connect syntax: host:port/service_name
            if (isset($options['host']) && isset($options['database'])) {
                $host = $options['host'];
                $port = isset($options['port']) ? $options['port'] : '1521';
                $service = $options['database'];

                // Oracle Easy Connect format
                $options['dsn'] = "oci:dbname=//{$host}:{$port}/{$service}";

                // Add character set if specified
                if (isset($options['charset'])) {
                    $options['dsn'] .= ';charset=' . $options['charset'];
                }
            } elseif (isset($options['database'])) {
                // TNS name format
                $options['dsn'] = "oci:dbname={$options['database']}";

                if (isset($options['charset'])) {
                    $options['dsn'] .= ';charset=' . $options['charset'];
                }
            }
        }

        if (!isset($options['dsn']) || substr($options['dsn'], 0, 4) != 'oci:') {
            throw new ConnectionFailedException('Oracle DSN for PDO connection does not appear to be valid.', 500);
        }

        // Force column names to lowercase for ORM consistency.
        // Oracle returns unquoted identifiers as uppercase; this normalizes them.
        $options['extras'][\PDO::ATTR_CASE] = \PDO::CASE_LOWER;

        // Call parent construct
        parent::__construct($options);

        // Set NLS session parameters for consistent date/timestamp handling.
        // ISO 8601 format matches what PHP and the test suite expects.
        $this->setQuery(
            "ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'"
        );
        $this->execute();
        $this->setQuery(
            "ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'"
        );
        $this->execute();
        $this->setQuery(
            "ALTER SESSION SET NLS_TIMESTAMP_TZ_FORMAT = 'YYYY-MM-DD HH24:MI:SS TZH:TZM'"
        );
        $this->execute();
    }

    /**
     * Detect the database syntax name
     *
     * PDO_OCI reports driver name as 'oci', and our syntax class
     * is Syntax\Oci.
     *
     * @return  string
     */
    protected function detectSyntax()
    {
        return 'oci';
    }

    /**
     * Binds the given bindings to the prepared statement
     *
     * Oracle's PDO_OCI driver always sends bound parameters as VARCHAR2,
     * even when PDO::PARAM_INT is specified. This causes ORA-00932
     * (inconsistent datatypes) when numeric placeholders are used in
     * contexts requiring NUMBER (e.g., COALESCE(number_col, ?)).
     *
     * This override inlines integer and float values as numeric literals
     * directly in the SQL, replacing the corresponding ? placeholders.
     * String, null, and boolean values continue to use standard binding.
     *
     * @param   array  $bindings  The param bindings
     * @param   array  $type      The param types
     * @return  $this
     */
    public function bind($bindings, $type = [])
    {
        // PDO_OCI: strings > 4000 bytes must use PDO::PARAM_LOB
        // streams to avoid ORA-01461 when inserting into CLOB columns
        if ($this->hasLobBindings($bindings)) {
            return $this->bindWithLobs($bindings, $type);
        }

        // Separate numeric bindings (inline) from non-numeric (bind normally)
        $numericPositions = []; // 0-indexed positions that are numeric
        foreach ($bindings as $i => $binding) {
            if (is_int($binding) || is_float($binding)) {
                $numericPositions[$i] = $binding;
            }
        }

        // If no numeric bindings, use standard bind
        if (empty($numericPositions)) {
            return parent::bind($bindings, $type);
        }

        // Replace ? placeholders for numeric bindings with literal values
        $sql = $this->statement->queryString ?? '';
        if ($sql !== '') {
            $newSql = '';
            $bindingIndex = 0;
            $len = strlen($sql);
            $inSingleQuote = false;
            $inDoubleQuote = false;

            for ($pos = 0; $pos < $len; $pos++) {
                $ch = $sql[$pos];

                // Track string literals to avoid replacing ? inside them
                if ($ch === "'" && !$inDoubleQuote) {
                    // Check for escaped quote ('')
                    if ($inSingleQuote && $pos + 1 < $len && $sql[$pos + 1] === "'") {
                        $newSql .= "''";
                        $pos++;
                        continue;
                    }
                    $inSingleQuote = !$inSingleQuote;
                    $newSql .= $ch;
                    continue;
                }
                if ($ch === '"' && !$inSingleQuote) {
                    $inDoubleQuote = !$inDoubleQuote;
                    $newSql .= $ch;
                    continue;
                }

                if ($ch === '?' && !$inSingleQuote && !$inDoubleQuote) {
                    if (isset($numericPositions[$bindingIndex])) {
                        // Inline the numeric literal
                        $newSql .= (string) $numericPositions[$bindingIndex];
                    } else {
                        $newSql .= '?';
                    }
                    $bindingIndex++;
                } else {
                    $newSql .= $ch;
                }
            }

            // Re-prepare with the modified SQL
            if ($newSql !== $sql) {
                $this->prepare($newSql);

                // Build the remaining (non-numeric) bindings array
                $remainingBindings = [];
                $remainingTypes = [];
                $newIdx = 1;
                foreach ($bindings as $i => $binding) {
                    if (!isset($numericPositions[$i])) {
                        $remainingBindings[] = $binding;
                        if (isset($type[$i + 1])) {
                            $remainingTypes[$newIdx] = $type[$i + 1];
                        }
                        $newIdx++;
                    }
                }

                if (!empty($remainingBindings)) {
                    return parent::bind($remainingBindings, $remainingTypes);
                }

                $this->bindings = $bindings;
                return $this;
            }
        }

        return parent::bind($bindings, $type);
    }

    /**
     * Detect if any bindings need LOB handling (strings > 4000 bytes)
     *
     * PDO_OCI cannot bind strings larger than 4000 bytes as regular
     * PARAM_STR — they must use PARAM_LOB to avoid ORA-01461.
     *
     * @param   array  $bindings  The binding values (0-indexed)
     * @return  bool
     */
    protected function hasLobBindings(array $bindings): bool
    {
        foreach ($bindings as $binding) {
            if (is_string($binding) && strlen($binding) > 4000) {
                return true;
            }
        }
        return false;
    }

    /**
     * Bind values with LOB support for Oracle PDO_OCI
     *
     * Uses bindParam with php://memory streams for strings > 4000
     * bytes, which PDO_OCI requires for CLOB column inserts.
     *
     * @param   array  $bindings  The binding values (0-indexed)
     * @param   array  $type      Existing type hints (1-indexed)
     * @return  $this
     */
    protected function bindWithLobs(array $bindings, array $type = [])
    {
        $idx = 1;
        $this->bindings = $bindings;
        $this->lobStreams = [];

        foreach ($bindings as $binding) {
            if (is_string($binding) && strlen($binding) > 4000) {
                // PDO_OCI: strings > 4000 bytes require explicit
                // length in bindParam to avoid ORA-01461
                $lobIdx = count($this->lobStreams);
                $this->lobStreams[$lobIdx] = $binding;
                $this->statement->bindParam(
                    $idx,
                    $this->lobStreams[$lobIdx],
                    \PDO::PARAM_STR,
                    strlen($binding)
                );
            } else {
                $pdoType = \PDO::PARAM_STR;
                if (is_bool($binding)) {
                    $pdoType = \PDO::PARAM_BOOL;
                } elseif (is_null($binding)) {
                    $pdoType = \PDO::PARAM_NULL;
                } elseif (is_int($binding)) {
                    $pdoType = \PDO::PARAM_INT;
                }
                $this->statement->bindValue($idx, $binding, $pdoType);
            }
            $idx++;
        }

        return $this;
    }

    /**
     * Quote an identifier name (table, column, etc.)
     *
     * Oracle stores unquoted identifiers as uppercase. This method
     * uppercases all identifiers before quoting, matching Oracle's
     * default behavior and ensuring consistency with system catalog
     * queries (user_tables, user_tab_columns, etc.).
     *
     * @param   string|array  $name  The identifier name
     * @param   string|null   $as    An optional alias
     * @return  string|array
     */
    public function quoteName($name, $as = null)
    {
        if (is_array($name)) {
            $quotedArray = [];
            foreach ($name as $k => $v) {
                $quotedArray[$k] = $this->quoteName($v);
            }
            return $quotedArray;
        }

        $parts = (strpos($name, '.') !== false)
            ? explode('.', $name) : (array) $name;
        $bits = [];

        foreach ($parts as $part) {
            if ($part === '*') {
                $bits[] = $part;
            } else {
                $part = strtoupper(trim($part, '`"'));
                $bits[] = sprintf($this->wrapper, $part);
            }
        }

        $string = implode('.', $bits);

        if (isset($as)) {
            $as = strtoupper(trim($as, '`"'));
            $string .= ' AS ' . sprintf($this->wrapper, $as);
        }

        return $string;
    }

    /**
     * Quote a single identifier with uppercase
     *
     * @param   string  $identifier  The identifier to quote
     * @return  string
     */
    public function quoteIdentifier(string $identifier): string
    {
        $identifier = strtoupper(trim($identifier, '`"'));
        return '"' . str_replace('"', '""', $identifier) . '"';
    }

    /**
     * Wraps an identifier in appropriate quotes with uppercasing
     *
     * Oracle stores identifiers as uppercase. This method ensures
     * consistent casing and uses space (not AS) for table aliases,
     * since Oracle <23ai does not support AS for table aliases.
     *
     * @param   string  $value  The identifier to wrap
     * @return  string
     */
    public function wrap($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        // Preserve derived-table expressions
        if (
            $value[0] === '('
            && substr($value, -1) === ')'
        ) {
            return $value;
        }

        // Handle explicit AS aliases — use space not AS
        if (
            preg_match(
                '/^(.+)\s+as\s+([^\s]+)$/is',
                $value,
                $matches
            )
        ) {
            return $this->wrap($matches[1])
                . ' ' . $this->wrap($matches[2]);
        }

        // Handle shorthand alias: "table alias"
        if (
            preg_match(
                '/^(.+)\s+([^\s]+)$/s',
                $value,
                $matches
            )
        ) {
            return $this->wrap($matches[1])
                . ' ' . $this->wrap($matches[2]);
        }

        $quoted = [];
        $parts  = explode('.', $value);

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '*') {
                $quoted[] = $part;
            } else {
                $part = strtoupper(trim($part, '`"'));
                $quoted[] = sprintf($this->wrapper, $part);
            }
        }

        return implode('.', $quoted);
    }

    /**
     * Gets the database collation in use
     *
     * Oracle uses NLS_SORT and NLS_COMP parameters for collation
     *
     * @return  string|bool
     */
    public function getCollation()
    {
        $this->setQuery("SELECT value FROM v\$nls_parameters WHERE parameter = 'NLS_SORT'");
        $result = $this->loadResult();

        return $result ?: false;
    }

    /**
     * Shows the table CREATE statement that creates the given tables
     *
     * Oracle doesn't have SHOW CREATE TABLE; we reconstruct it from metadata
     *
     * @param   string|array  $tables  A table name or a list of table names
     * @return  array
     */
    public function getTableCreate($tables)
    {
        $result = [];

        foreach ((array) $tables as $table) {
            $tableName = $this->replacePrefix($table);
            $result[$table] = $this->generateCreateTableSql(strtoupper($tableName));
        }

        return $result;
    }

    /**
     * Generate a CREATE TABLE statement by introspecting the table structure
     *
     * @param   string  $table  The table name (uppercase, with prefix replaced)
     * @return  string  The CREATE TABLE SQL statement
     */
    protected function generateCreateTableSql(string $table): string
    {
        if (!$this->tableExists($table)) {
            return '';
        }

        $columns = $this->getTableColumns($table, false);
        $pk = $this->getPrimaryKey($table);

        $columnDefs = [];
        foreach ($columns as $name => $col) {
            $def = $this->quoteName($name) . ' ' . $col->Type;

            // NOT NULL
            if (isset($col->Null) && $col->Null === 'NO') {
                $def .= ' NOT NULL';
            }

            // DEFAULT value
            if (isset($col->Default) && $col->Default !== null) {
                $def .= ' DEFAULT ' . $col->Default;
            }

            $columnDefs[] = $def;
        }

        // Add primary key constraint
        if ($pk) {
            $pkColumns = is_array($pk) ? $pk : [$pk];
            $pkList = implode(', ', array_map([$this, 'quoteName'], $pkColumns));
            $columnDefs[] = 'PRIMARY KEY (' . $pkList . ')';
        }

        $sql = 'CREATE TABLE ' . $this->quoteName($table) . " (\n  ";
        $sql .= implode(",\n  ", $columnDefs);
        $sql .= "\n)";

        return $sql;
    }

    /**
     * Retrieves field information about the given table
     *
     * @param   string   $table     The name of the database table
     * @param   boolean  $typeOnly  True to only return field types
     * @return  array    An array of fields for the database table
     */
    public function getTableColumns($table, $typeOnly = true)
    {
        $result = [];
        $tableSub = strtoupper($this->replacePrefix($table));

        $this->setQuery("
            SELECT
                c.column_name,
                c.data_type || CASE
                    WHEN c.data_type IN ('VARCHAR2', 'NVARCHAR2', 'CHAR', 'NCHAR', 'RAW')
                    THEN '(' || c.data_length || ')'
                    WHEN c.data_type = 'NUMBER' AND c.data_precision IS NOT NULL
                    THEN '(' || c.data_precision
                        || CASE WHEN c.data_scale > 0
                            THEN ',' || c.data_scale ELSE '' END || ')'
                    ELSE ''
                END AS data_type_full,
                c.nullable,
                c.data_default,
                CASE WHEN ic.column_name IS NOT NULL
                    THEN 'Y' ELSE 'N' END AS is_identity
            FROM user_tab_columns c
            LEFT JOIN user_tab_identity_cols ic
                ON c.table_name = ic.table_name
                AND c.column_name = ic.column_name
            WHERE c.table_name = " . $this->quote($tableSub) . "
            ORDER BY c.column_id
        ");

        $fields = $this->loadObjectList();

        if ($typeOnly) {
            foreach ($fields as $field) {
                // Lowercase keys for ORM compatibility
                $name = strtolower($field->column_name);
                $result[$name] = strtolower($field->data_type_full);
            }
        } else {
            foreach ($fields as $field) {
                $name = strtolower($field->column_name);
                $isIdentity = ($field->is_identity ?? 'N') === 'Y';
                $result[$name] = (object) [
                    'Default' => $isIdentity
                        ? null
                        : trim($field->data_default ?? ''),
                    'Comment' => '',
                    'Field'   => $name,
                    'Type'    => strtolower($field->data_type_full),
                    'Null'    => $field->nullable === 'Y' ? 'YES' : 'NO',
                    'Extra'   => $isIdentity ? 'auto_increment' : '',
                ];
            }
        }

        return $result;
    }

    /**
     * Get the details list of keys for a table
     *
     * @param   string  $table  The name of the table
     * @return  array   An array of the column specification for the table
     */
    public function getTableKeys($table)
    {
        $tableSub = strtoupper($this->replacePrefix($table));

        $this->setQuery(
            "SELECT"
            . " i.index_name,"
            . " CASE WHEN c.constraint_type = 'P'"
            . " THEN 1 ELSE 0 END AS is_primary,"
            . " CASE WHEN i.uniqueness = 'UNIQUE'"
            . " THEN 0 ELSE 1 END AS non_unique,"
            . " ic.column_name,"
            . " ic.column_position"
            . " FROM user_indexes i"
            . " JOIN user_ind_columns ic"
            . " ON i.index_name = ic.index_name"
            . " LEFT JOIN user_constraints c"
            . " ON i.index_name = c.constraint_name"
            . " AND c.constraint_type = 'P'"
            . " WHERE i.table_name = "
            . $this->quote($tableSub)
            . " ORDER BY i.index_name, ic.column_position"
        );

        $rows = $this->loadObjectList();
        $keys = [];

        foreach ($rows as $row) {
            $isPrimary = (int) $row->is_primary;
            $keyName = $isPrimary
                ? 'PRIMARY'
                : strtolower($row->index_name);

            // Use MySQL-compatible property names for introspection
            $keys[$keyName] = (object) [
                'Key_name'     => $keyName,
                'Column_name'  => strtolower($row->column_name),
                'Non_unique'   => (int) $row->non_unique,
                'Seq_in_index' => (int) $row->column_position,
                'Index_type'   => 'BTREE',
            ];
        }

        return $keys;
    }

    /**
     * Gets all indexes for a table with their columns and properties
     *
     * @param   string  $table  The table name
     * @return  array   Array of index objects
     */
    public function getIndexes($table)
    {
        $tableSub = strtoupper($this->replacePrefix($table));

        $this->setQuery(
            "SELECT"
            . " i.index_name,"
            . " CASE WHEN c.constraint_type = 'P'"
            . " THEN 1 ELSE 0 END AS is_primary,"
            . " i.uniqueness,"
            . " ic.column_name,"
            . " ic.column_position"
            . " FROM user_indexes i"
            . " JOIN user_ind_columns ic"
            . " ON i.index_name = ic.index_name"
            . " LEFT JOIN user_constraints c"
            . " ON i.index_name = c.constraint_name"
            . " AND c.constraint_type = 'P'"
            . " WHERE i.table_name = "
            . $this->quote($tableSub)
            . " ORDER BY i.index_name, ic.column_position"
        );

        $rows = $this->loadObjectList();
        $indexes = [];

        foreach ($rows as $row) {
            $isPrimary = (int) $row->is_primary;
            $name = $isPrimary
                ? 'PRIMARY'
                : strtolower($row->index_name);

            if (!isset($indexes[$name])) {
                $indexes[$name] = (object) [
                    'name'    => $name,
                    'columns' => [],
                    'unique'  => $row->uniqueness === 'UNIQUE',
                    'primary' => (bool) $isPrimary,
                ];
            }

            $indexes[$name]->columns[] = strtolower($row->column_name);
        }

        return array_values($indexes);
    }

    /**
     * Checks if a table has a specific key/index
     *
     * Oracle normalizes: PK → 'PRIMARY', others → lowercase.
     *
     * @param   string  $table  The table name
     * @param   string  $key    The key/index name
     * @return  bool
     */
    public function tableHasKey($table, $key)
    {
        $keys = $this->getTableKeys($table);

        if (!is_array($keys)) {
            return false;
        }

        // Check exact match first, then case-insensitive
        return isset($keys[$key]) || isset($keys[strtolower($key)]);
    }

    /**
     * Returns whether or not the given table exists
     *
     * @param   string  $table  A table name (with or without prefix)
     * @return  bool
     */
    public function tableExists($table)
    {
        $table = strtolower($this->replacePrefix($table));
        $tables = $this->getTableList();

        return in_array($table, $tables, true);
    }

    /**
     * Gets an array of all tables in the database
     *
     * Returns lowercase table names for cross-driver consistency.
     *
     * @return  array
     */
    public function getTableList()
    {
        $this->setQuery(
            "SELECT table_name FROM user_tables ORDER BY table_name"
        );
        return array_map('strtolower', $this->loadColumn());
    }

    /**
     * Locks a table in the database
     *
     * @param   string  $tableName  The name of the table to lock
     * @return  $this
     */
    public function lockTable($tableName)
    {
        $this->setQuery('LOCK TABLE ' . $this->quoteName($tableName) . ' IN EXCLUSIVE MODE');
        $this->execute();

        return $this;
    }

    /**
     * Renames a table in the database
     *
     * @param   string  $oldTable  The name of the table to be renamed
     * @param   string  $newTable  The new name for the table
     * @param   string  $backup    Table prefix (unused)
     * @param   string  $prefix    For the table (unused)
     * @return  $this
     * @throws  \RuntimeException  If the source table does not exist
     */
    public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
    {
        $oldTable = strtoupper($this->replacePrefix($oldTable));
        $newTable = strtoupper($this->replacePrefix($newTable));

        if (!$this->tableExists($oldTable)) {
            throw new \RuntimeException('Table not found in Oracle database.');
        }

        $this->setQuery('ALTER TABLE ' . $this->quoteName($oldTable) . ' RENAME TO ' . $this->quoteName($newTable));
        $this->execute();

        return $this;
    }

    /**
     * Selects a database for use
     *
     * Oracle doesn't support database switching; each connection is to a specific schema.
     * You can change the current schema with ALTER SESSION SET CURRENT_SCHEMA.
     *
     * @param   string  $database  The name of the database (schema) to select
     * @return  bool
     */
    public function select($database)
    {
        try {
            $this->setQuery('ALTER SESSION SET CURRENT_SCHEMA = ' . $this->quoteName(strtoupper($database)));
            $this->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Gets the database engine of the given table
     *
     * Oracle doesn't have storage engines like MySQL
     *
     * @param   string       $table  The table for which to retrieve the engine type
     * @return  string|bool
     */
    public function getEngine($table)
    {
        return 'Oracle';
    }


    /**
     * Gets the database character set of the given table
     *
     * Oracle uses NLS_CHARACTERSET parameter
     *
     * @param   string       $table  The table for which to retrieve the character set (ignored)
     * @param   string       $field  The field to check (ignored)
     * @return  string|bool
     */
    public function getCharacterSet($table, $field = null)
    {
        $this->setQuery("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");
        $result = $this->loadResult();

        return $result ?: false;
    }

    /**
     * Converts a table to the specified character set
     *
     * Oracle handles encoding at database level, not per-table
     *
     * @param   string       $table    The table to convert (ignored)
     * @param   string       $charset  The character set (ignored)
     * @param   string|null  $collate  Optional collation (ignored)
     * @return  bool
     */
    public function convertToCharset($table, $charset, $collate = null)
    {
        // Oracle doesn't support per-table character sets
        return true;
    }

    /**
     * Gets the auto-increment value for the given table
     *
     * Oracle uses sequences instead of auto-increment.
     * This attempts to find a sequence named {table}_SEQ or {table}_{pk}_SEQ
     *
     * @param   string    $table  The table for which to retrieve the value
     * @return  int|bool
     */
    public function getAutoIncrement($table)
    {
        $table = strtoupper($this->replacePrefix($table));

        // First check for identity column
        $this->setQuery(
            "SELECT column_name FROM user_tab_identity_cols"
            . " WHERE table_name = " . $this->quote($table)
            . " AND ROWNUM = 1"
        );

        $identityCol = $this->loadResult();

        if ($identityCol) {
            // Identity sequence last_number includes cache (default 20),
            // so query the actual max value from the table instead
            $this->setQuery(
                "SELECT MAX(" . $this->quoteName($identityCol) . ")"
                . " FROM " . $this->quoteName($table)
            );
            $maxVal = $this->loadResult();
            return $maxVal !== null ? (int) $maxVal + 1 : 1;
        }

        // Fall back to conventional sequence names
        $seqNames = [
            $table . '_SEQ',
            $table . '_ID_SEQ',
        ];

        foreach ($seqNames as $name) {
            $this->setQuery(
                "SELECT last_number FROM user_sequences"
                . " WHERE sequence_name = " . $this->quote($name)
            );
            $result = $this->loadResult();
            if ($result !== null) {
                return (int) $result;
            }
        }

        return false;
    }

    /**
     * Sets the auto-increment starting value for the given table
     *
     * Oracle uses sequences; this modifies the sequence if found
     *
     * @param   string  $table  The table name
     * @param   int     $value  The auto-increment starting value
     * @return  bool
     */
    public function setAutoIncrement($table, $value): bool
    {
        $table = strtoupper($this->replacePrefix($table));
        $value = max(1, (int) $value);

        // First, try IDENTITY column — cannot ALTER SEQUENCE on
        // system-generated sequences (ORA-32793), must use
        // ALTER TABLE ... MODIFY ... GENERATED BY DEFAULT AS IDENTITY
        $this->setQuery(
            "SELECT column_name"
            . " FROM user_tab_identity_cols"
            . " WHERE table_name = " . $this->quote($table)
        );
        $identityCol = $this->loadResult();

        if ($identityCol) {
            $this->setQuery(
                "ALTER TABLE " . $this->quoteName($table)
                . " MODIFY " . $this->quoteName($identityCol)
                . " GENERATED BY DEFAULT AS IDENTITY"
                . " (START WITH " . $value . ")"
            );
            $this->execute();
            return true;
        }

        // Fallback to conventional sequence names
        $candidates = [
            $table . '_SEQ',
            $table . '_ID_SEQ',
        ];
        foreach ($candidates as $candidate) {
            if ($this->sequenceExists($candidate)) {
                return $this->resetSequenceValue(
                    $candidate,
                    $value
                );
            }
        }

        return false;
    }

    /**
     * Reset a sequence to produce a specific next value
     *
     * @param   string  $seqName   The sequence name
     * @param   int     $nextVal   The desired next value
     * @return  bool
     */
    protected function resetSequenceValue($seqName, $nextVal)
    {
        // Get current next value from user_sequences
        $this->setQuery(
            "SELECT last_number FROM user_sequences"
            . " WHERE sequence_name = "
            . $this->quote(strtoupper($seqName))
        );
        $current = (int) $this->loadResult();

        $diff = $nextVal - $current;
        if ($diff == 0) {
            return true;
        }

        // Temporarily change increment, advance, restore
        $quotedSeq = $this->quoteName($seqName);
        $this->setQuery(
            "ALTER SEQUENCE " . $quotedSeq
            . " INCREMENT BY " . $diff
        );
        $this->execute();

        $this->setQuery(
            "SELECT " . $quotedSeq . ".NEXTVAL FROM DUAL"
        );
        $this->loadResult();

        $this->setQuery(
            "ALTER SEQUENCE " . $quotedSeq
            . " INCREMENT BY 1"
        );
        $this->execute();

        return true;
    }

    /**
     * Get the allowed values for an ENUM column
     *
     * @note    NO-OP: Oracle doesn't have native ENUM type.
     *          Use CHECK constraints instead.
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @return  array   Always returns empty array
     */
    public function getEnumValues($table, $column)
    {
        return [];
    }

    /**
     * Add a value to an ENUM column
     *
     * @note    NO-OP: Oracle doesn't have native ENUM type
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @param   string  $value   The value to add
     * @return  bool    Always returns true
     */
    public function addEnumValue($table, $column, $value)
    {
        return true;
    }

    /**
     * Remove a value from an ENUM column
     *
     * @note    NO-OP: Oracle doesn't have native ENUM type
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @param   string  $value   The value to remove
     * @return  bool    Always returns true
     */
    public function removeEnumValue($table, $column, $value)
    {
        return true;
    }

    /**
     * Creates or replaces a database view
     *
     * @param   string  $name       The view name (with or without prefix)
     * @param   string  $selectSql  The SELECT statement for the view
     * @param   array   $options    MySQL-specific options (ignored on Oracle)
     * @return  bool
     */
    public function createOrReplaceView($name, $selectSql, array $options = []): bool
    {
        $viewName = strtoupper($this->replacePrefix($name));
        $selectSql = $this->replacePrefix($selectSql);

        $sql = 'CREATE OR REPLACE VIEW ' . $this->quoteName($viewName) . ' AS ' . $selectSql;
        $this->setQuery($sql);
        $this->execute();

        return true;
    }

    /**
     * Drops a database view
     *
     * @param   string  $name      The view name
     * @param   bool    $ifExists  Whether to use IF EXISTS clause (not supported in Oracle)
     * @return  bool
     */
    public function dropView($name, $ifExists = true): bool
    {
        $viewName = strtoupper($this->replacePrefix($name));

        if ($ifExists && !$this->viewExists($name)) {
            return true;
        }

        $sql = 'DROP VIEW ' . $this->quoteName($viewName);
        $this->setQuery($sql);
        $this->execute();

        return true;
    }

    /**
     * Checks if a view exists in the database
     *
     * @param   string  $name  The view name
     * @return  bool
     */
    public function viewExists($name): bool
    {
        $viewName = strtoupper($this->replacePrefix($name));
        $this->setQuery(
            "SELECT COUNT(*) FROM user_views WHERE view_name = " . $this->quote($viewName)
        );

        return (bool) $this->loadResult();
    }

    /**
     * Returns a list of all views in the current schema
     *
     * @return  array  Array of view names
     */
    public function getViews(): array
    {
        $this->setQuery("SELECT LOWER(view_name) AS view_name FROM user_views ORDER BY view_name");
        return $this->loadColumn() ?: [];
    }

    /**
     * Returns a list of all database names (schemas) the user can access
     *
     * @return  array  Array of schema names
     */
    public function getDatabaseNames(): array
    {
        $this->setQuery("SELECT username FROM all_users ORDER BY username");
        return $this->loadColumn() ?: [];
    }

    /**
     * Returns a list of all sequences in the current schema
     *
     * @return  array  Array of SequenceInfo objects
     */
    public function getSequences(): array
    {
        $this->setQuery("
            SELECT LOWER(sequence_name) AS sequence_name
            FROM user_sequences
            ORDER BY sequence_name
        ");

        return $this->loadColumn() ?: [];
    }

    /**
     * Creates a new sequence
     *
     * @param   string  $name       The sequence name
     * @param   int     $start      Starting value (default: 1)
     * @param   int     $increment  Increment value (default: 1)
     * @param   array   $options    Additional options (min, max, cycle, cache)
     * @return  bool
     */
    public function createSequence($name, $start = 1, $increment = 1, array $options = []): bool
    {
        $seqName = strtoupper($name);

        $sql = "CREATE SEQUENCE " . $this->quoteName($seqName);
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
            $sql .= " NOCYCLE";
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
     * @param   bool    $ifExists  Whether to check existence first
     * @return  bool
     */
    public function dropSequence($name, $ifExists = true): bool
    {
        $seqName = strtoupper($name);

        if ($ifExists && !$this->sequenceExists($seqName)) {
            return true;
        }

        $this->setQuery("DROP SEQUENCE " . $this->quoteName($seqName));
        $this->execute();

        return true;
    }

    /**
     * Checks if a sequence exists
     *
     * @param   string  $name  The sequence name
     * @return  bool
     */
    public function sequenceExists($name): bool
    {
        $seqName = strtoupper($name);
        $this->setQuery("SELECT COUNT(*) FROM user_sequences WHERE sequence_name = " . $this->quote($seqName));
        return (bool) $this->loadResult();
    }

    /**
     * Gets the next value from a sequence
     *
     * @param   string  $name  The sequence name
     * @return  int
     */
    public function nextSequenceValue($name): int
    {
        $seqName = strtoupper($name);
        $this->setQuery("SELECT " . $this->quoteName($seqName) . ".NEXTVAL FROM DUAL");
        return (int) $this->loadResult();
    }

    /**
     * Gets the current value of a sequence
     *
     * Note: Oracle's CURRVAL requires NEXTVAL to have been called in the current session
     *
     * @param   string  $name  The sequence name
     * @return  int
     */
    public function currentSequenceValue($name): int
    {
        $seqName = strtoupper($name);
        $this->setQuery("SELECT " . $this->quoteName($seqName) . ".CURRVAL FROM DUAL");
        return (int) $this->loadResult();
    }

    /**
     * Gets the version of the database connector
     *
     * @return  string
     */
    public function getVersion()
    {
        if ($this->oracleVersion === null) {
            // Use PDO attribute — works without DBA privileges
            try {
                $connection = $this->getConnection();
                $this->oracleVersion = $connection
                    ->getAttribute(\PDO::ATTR_SERVER_VERSION);
            } catch (\Exception $e) {
                // Fall back to PRODUCT_COMPONENT_VERSION (no DBA needed)
                $this->setQuery(
                    "SELECT VERSION_FULL FROM PRODUCT_COMPONENT_VERSION"
                    . " WHERE ROWNUM = 1"
                );
                try {
                    $this->oracleVersion = $this->loadResult() ?: 'Unknown';
                } catch (\Exception $e2) {
                    $this->oracleVersion = 'Unknown';
                }
            }
        }

        return $this->oracleVersion;
    }

    /**
     * Gets the full version string
     *
     * @return  string|null
     */
    public function getFullVersion()
    {
        // Use PDO attribute — works without DBA privileges
        try {
            $connection = $this->getConnection();
            $version = $connection
                ->getAttribute(\PDO::ATTR_SERVER_VERSION);
            return 'Oracle Database ' . $version;
        } catch (\Exception $e) {
            return 'Oracle Database';
        }
    }

    /**
     * Truncate a table
     *
     * @param   string  $table   The table to truncate
     * @param   int     $nextId  Ignored by Oracle (sequences are independent objects)
     * @return  $this
     */
    public function truncateTable($table, int $nextId = 1)
    {
        $resolved = strtoupper($this->replacePrefix($table));
        $this->setQuery('TRUNCATE TABLE ' . $this->quoteName($resolved));
        $this->execute();

        // Reset identity sequence to the requested start value
        $this->setAutoIncrement($table, $nextId);

        return $this;
    }

    /**
     * Get database server version information
     *
     * @return  array  Array with standardized keys
     */
    public function getServerInfo()
    {
        $version = $this->getVersion();
        $fullVersion = $this->getFullVersion();

        return [
            'version'         => $version,
            'driver_version'  => $version,
            'oracle_version'  => $version,
            'full_version'    => $fullVersion,
            'comment'         => 'Oracle Database',
        ];
    }


    /**
     * Test to see if the Oracle connector is available
     *
     * @return  bool
     */
    public static function test()
    {
        return class_exists('\PDO') && in_array('oci', \PDO::getAvailableDrivers());
    }

    // =========================================================================
    // Transaction Support with Savepoints
    // =========================================================================

    /**
     * Initializes a transaction
     *
     * Oracle auto-commits each statement by default.
     * Starting a transaction disables auto-commit.
     *
     * @return  void
     */
    public function transactionStart()
    {
        if ($this->transactionDepth == 0) {
            // Commit any implicit transaction before starting a new one
            if ($this->connection && $this->connection->inTransaction()) {
                $this->connection->commit();
            }
            if ($this->connection) {
                $this->connection->beginTransaction();
            }
        } else {
            $this->setQuery('SAVEPOINT SP_' . $this->transactionDepth)
                ->execute();
        }

        $this->transactionDepth++;
    }

    /**
     * Commits a transaction
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
            if ($this->connection) {
                $this->connection->commit();
            }
        }
        // Oracle doesn't have RELEASE SAVEPOINT; savepoints are released on commit
    }

    /**
     * Rolls back a transaction
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
            if ($this->connection) {
                $this->connection->rollBack();
            }
        } else {
            $this->setQuery(
                'ROLLBACK TO SAVEPOINT SP_' . $this->transactionDepth
            )->execute();
        }
    }

    /**
     * Determines if the connection to the server is active
     *
     * @return  bool
     */
    public function connected()
    {
        if (!is_object($this->connection)) {
            return false;
        }

        try {
            $this->setQuery('SELECT 1 FROM DUAL');
            $this->loadResult();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // =========================================================================
    // CLOB Stream → String Conversion
    // =========================================================================

    /**
     * Convert CLOB stream resources to strings in a row
     *
     * Oracle PDO returns CLOB values as PHP stream resources.
     * This converts them to strings for consistency with other drivers.
     *
     * @param   mixed  $row  A row (array, object, or false)
     * @return  mixed
     */
    protected function convertLobs($row)
    {
        if ($row === false || $row === null) {
            return $row;
        }

        if (is_array($row)) {
            foreach ($row as &$value) {
                if (is_resource($value)) {
                    $value = stream_get_contents($value);
                }
            }
            unset($value);
        } elseif (is_object($row)) {
            foreach (get_object_vars($row) as $prop => $value) {
                if (is_resource($value)) {
                    $row->$prop = stream_get_contents($value);
                }
            }
        }

        return $row;
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchObject($class = 'stdClass')
    {
        return $this->convertLobs(parent::fetchObject($class));
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchArray()
    {
        return $this->convertLobs(parent::fetchArray());
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchAssoc()
    {
        return $this->convertLobs(parent::fetchAssoc());
    }

    // =========================================================================
    // SQL Compatibility Helpers - Oracle Implementations
    // =========================================================================

    /**
     * Returns the SQL keyword for INSERT with ignore duplicates
     *
     * Oracle doesn't have INSERT IGNORE; use error handling or MERGE
     *
     * @return  string
     */
    public function sqlInsertIgnore(): string
    {
        // Oracle doesn't have INSERT IGNORE syntax
        // For ignoring duplicates, use MERGE or exception handling
        return 'INSERT INTO';
    }

    /**
     * Returns the SQL keyword for REPLACE (upsert)
     *
     * Oracle uses MERGE statement for upsert operations
     *
     * @return  string
     */
    public function sqlReplace(): string
    {
        // Oracle uses MERGE instead of REPLACE
        // The actual MERGE syntax must be built separately
        return 'INSERT INTO';
    }

    /**
     * Returns the SQL for regular expression matching
     *
     * Oracle uses REGEXP_LIKE() function
     *
     * @param   string  $column   The column to match
     * @param   string  $pattern  The regex pattern
     * @param   bool    $not      Whether to negate the match
     * @return  string
     */
    public function sqlRegexp(string $column, string $pattern, bool $not = false): string
    {
        $negation = $not ? 'NOT ' : '';
        return $negation . 'REGEXP_LIKE(' . $column . ', ' . $this->quote($pattern) . ')';
    }

    /**
     * Returns the SQL for date subtraction
     *
     * Oracle uses date arithmetic: date - INTERVAL
     *
     * @param   string  $date   The date column or value
     * @param   int     $value  The interval value
     * @param   string  $unit   The interval unit
     * @return  string
     */
    public function sqlDateSub(string $date, int $value, string $unit = 'DAY'): string
    {
        $unit = strtoupper($unit);

        // Oracle interval syntax
        switch ($unit) {
            case 'DAY':
            case 'DAYS':
                return "({$date} - INTERVAL '{$value}' DAY)";
            case 'MONTH':
            case 'MONTHS':
                return "ADD_MONTHS({$date}, -{$value})";
            case 'YEAR':
            case 'YEARS':
                return "ADD_MONTHS({$date}, -" . ($value * 12) . ")";
            case 'HOUR':
            case 'HOURS':
                return "({$date} - INTERVAL '{$value}' HOUR)";
            case 'MINUTE':
            case 'MINUTES':
                return "({$date} - INTERVAL '{$value}' MINUTE)";
            case 'SECOND':
            case 'SECONDS':
                return "({$date} - INTERVAL '{$value}' SECOND)";
            default:
                return "({$date} - INTERVAL '{$value}' DAY)";
        }
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
        $unit = strtoupper($unit);

        switch ($unit) {
            case 'DAY':
            case 'DAYS':
                return "({$date} + INTERVAL '{$value}' DAY)";
            case 'MONTH':
            case 'MONTHS':
                return "ADD_MONTHS({$date}, {$value})";
            case 'YEAR':
            case 'YEARS':
                return "ADD_MONTHS({$date}, " . ($value * 12) . ")";
            case 'HOUR':
            case 'HOURS':
                return "({$date} + INTERVAL '{$value}' HOUR)";
            case 'MINUTE':
            case 'MINUTES':
                return "({$date} + INTERVAL '{$value}' MINUTE)";
            case 'SECOND':
            case 'SECONDS':
                return "({$date} + INTERVAL '{$value}' SECOND)";
            default:
                return "({$date} + INTERVAL '{$value}' DAY)";
        }
    }

    /**
     * Returns the SQL for date formatting
     *
     * Oracle uses TO_CHAR(date, format)
     *
     * @param   string  $date    The date column or value
     * @param   string  $format  The format string (MySQL format codes)
     * @return  string
     */
    public function sqlDateFormat(string $date, string $format): string
    {
        // Convert MySQL format codes to Oracle TO_CHAR format
        $conversions = [
            '%Y' => 'YYYY',
            '%y' => 'YY',
            '%m' => 'MM',
            '%c' => 'FM9',
            '%d' => 'DD',
            '%e' => 'FMDD',
            '%H' => 'HH24',
            '%h' => 'HH12',
            '%i' => 'MI',
            '%s' => 'SS',
            '%p' => 'AM',
            '%M' => 'Month',
            '%b' => 'Mon',
            '%W' => 'Day',
            '%a' => 'Dy',
            '%j' => 'DDD',
            '%w' => 'D',
        ];

        $oracleFormat = str_replace(array_keys($conversions), array_values($conversions), $format);

        return 'TO_CHAR(' . $date . ', ' . $this->quote($oracleFormat) . ')';
    }

    /**
     * Returns the SQL for extracting year from a date
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    public function sqlYear(string $date): string
    {
        return 'EXTRACT(YEAR FROM ' . $date . ')';
    }

    /**
     * Returns the SQL for extracting month from a date
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    public function sqlMonth(string $date): string
    {
        return 'EXTRACT(MONTH FROM ' . $date . ')';
    }

    /**
     * Returns the SQL for converting a date to Unix timestamp
     *
     * Oracle uses calculation from epoch
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    public function sqlUnixTimestamp(string $date): string
    {
        return "(({$date} - TO_DATE('1970-01-01', 'YYYY-MM-DD')) * 86400)";
    }

    /**
     * Returns the SQL for extracting a substring based on a delimiter
     *
     * Oracle uses REGEXP_SUBSTR for this functionality
     *
     * @param   string  $str    The string expression
     * @param   string  $delim  The delimiter
     * @param   int     $count  The occurrence count
     * @return  string
     */
    public function sqlSubstringIndex(string $str, string $delim, int $count): string
    {
        $quotedDelim = $this->quote($delim);

        if ($count > 0) {
            // Get everything before the nth delimiter
            return "REGEXP_SUBSTR({$str}, '^([^{$delim}]+{$delim}){" . ($count - 1) . "}[^{$delim}]+')";
        } elseif ($count < 0) {
            // Get everything after the nth delimiter from the right
            $absCount = abs($count);
            return "REGEXP_SUBSTR({$str}, '([^{$delim}]+{$delim}?){" . $absCount . "}\$')";
        }

        return "''";
    }

    /**
     * Returns the SQL for concatenating strings
     *
     * Oracle uses || operator or CONCAT() (2 args only)
     *
     * @param   array  $strings  Array of strings to concatenate
     * @return  string
     */
    public function sqlConcat(array $strings): string
    {
        return '(' . implode(' || ', $strings) . ')';
    }

    /**
     * Returns the SQL for concatenating strings with a separator
     *
     * Oracle doesn't have CONCAT_WS; we build it manually
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
            $parts[] = $str;
        }

        return '(' . implode(' || ', $parts) . ')';
    }

    // =========================================================================
    // Column Operations
    // =========================================================================

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
        $table = strtoupper($table);
        $column = strtoupper($column);
        return 'ALTER TABLE ' . $this->quoteName($table) . ' ADD ' . $this->quoteName($column) . ' ' . $definition;
    }

    protected function applyColumnComment(string $table, string $column, string $comment): void
    {
        $table = strtoupper($table);
        $column = strtoupper($column);
        $this->setQuery(
            'COMMENT ON COLUMN ' . $this->quoteName($table) . '.' . $this->quoteName($column) .
            ' IS ' . $this->quote($comment)
        );
        $this->execute();
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
        $table = strtoupper($table);
        $column = strtoupper($column);
        return 'ALTER TABLE ' . $this->quoteName($table) . ' DROP COLUMN ' . $this->quoteName($column);
    }

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
        $table = strtoupper($table);
        $column = strtoupper($column);
        return 'ALTER TABLE ' . $this->quoteName($table) . ' MODIFY ' . $this->quoteName($column) . ' ' . $definition;
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
        $table = strtoupper($this->replacePrefix($table));
        $oldColumn = strtoupper($oldColumn);
        $newColumn = strtoupper($newColumn);

        if (!$this->tableExists($table)) {
            return false;
        }

        if (!$this->tableHasField($table, $oldColumn)) {
            return false;
        }

        // Rename first if needed
        if ($oldColumn !== $newColumn) {
            $this->setQuery(
                'ALTER TABLE ' . $this->quoteName($table) .
                ' RENAME COLUMN ' . $this->quoteName($oldColumn) . ' TO ' . $this->quoteName($newColumn)
            );
            $this->execute();
        }

        // Then modify
        return $this->modifyColumn($table, $newColumn, $definition, $comment);
    }

    /**
     * Modify a column definition and move it after a specific column
     *
     * Oracle doesn't support column reordering
     *
     * @param   string  $table        Table name
     * @param   string  $column       Column name
     * @param   string  $definition   Column definition
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
        return $this->modifyColumn($table, $column, $definition, $comment);
    }

    /**
     * Modify a column definition and move it before a specific column
     *
     * Oracle doesn't support column reordering
     *
     * @param   string  $table         Table name
     * @param   string  $column        Column name
     * @param   string  $definition    Column definition
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
        return $this->modifyColumn($table, $column, $definition, $comment);
    }

    /**
     * Modify a column definition and move it to the first position
     *
     * Oracle doesn't support column reordering
     *
     * @param   string  $table       Table name
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    public function modifyColumnFirst(string $table, string $column, string $definition, string $comment = ''): bool
    {
        return $this->modifyColumn($table, $column, $definition, $comment);
    }

    /**
     * Add a column after a specific column
     *
     * Oracle doesn't support column positioning
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
        return $this->addColumn($table, $column, $definition, $comment);
    }

    /**
     * Add a column before a specific column
     *
     * Oracle doesn't support column positioning
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
        return $this->addColumn($table, $column, $definition, $comment);
    }

    /**
     * Add a column at the beginning of a table
     *
     * Oracle doesn't support column positioning
     *
     * @param   string  $table       Table name
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional column comment
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
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    public function addColumnLast(string $table, string $column, string $definition, string $comment = ''): bool
    {
        return $this->addColumn($table, $column, $definition, $comment);
    }

    // =========================================================================
    // Index Operations
    // =========================================================================

    /**
     * Add an index to a table
     *
     * @param   string        $table    Table name
     * @param   string        $name     Index name
     * @param   string|array  $columns  Column name(s)
     * @param   bool          $unique   Whether this is a unique index
     * @return  bool
     */
    public function addIndex(
        string $table,
        string $name,
        $columns,
        bool $unique = false
    ): bool {
        $table = strtoupper($this->replacePrefix($table));
        $name = strtoupper($name);

        if (!$this->tableExists($table)) {
            return false;
        }

        // Drop existing index if it already exists (Oracle has no IF NOT EXISTS)
        $this->setQuery(
            "SELECT index_name FROM user_indexes"
            . " WHERE index_name = " . $this->quote($name)
        );
        if ($this->loadResult()) {
            $this->setQuery(
                "DROP INDEX " . $this->quoteName($name)
            );
            $this->execute();
        }

        if (is_string($columns)) {
            $columns = [$columns];
        }

        $columnList = implode(', ', array_map(function ($col) {
            return $this->quoteName(strtoupper($col));
        }, $columns));

        $uniqueStr = $unique ? 'UNIQUE ' : '';

        $query = "CREATE {$uniqueStr}INDEX "
            . $this->quoteName($name)
            . ' ON ' . $this->quoteName($table)
            . " ($columnList)";

        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Drop an index from a table
     *
     * @param   string  $table  Table name (unused in Oracle)
     * @param   string  $name   Index name
     * @return  bool
     */
    public function dropIndex(string $table, string $name): bool
    {
        $name = strtoupper($name);
        $query = 'DROP INDEX ' . $this->quoteName($name);
        $this->setQuery($query);

        try {
            return (bool) $this->execute();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Add a FULLTEXT index to a table
     *
     * Oracle uses Oracle Text for full-text search, which requires
     * creating a context index. This is a simplified implementation.
     *
     * @param   string        $table    Table name
     * @param   string        $name     Index name
     * @param   string|array  $columns  Column name(s)
     * @return  bool
     */
    public function addFulltextIndex(string $table, string $name, $columns): bool
    {
        $table = strtoupper($this->replacePrefix($table));
        $name = strtoupper($name);

        if (!$this->tableExists($table)) {
            return false;
        }

        if (is_string($columns)) {
            $columns = [$columns];
        }

        // Oracle Text index on the first column (Oracle Text doesn't support multi-column easily)
        $column = strtoupper($columns[0]);

        $quotedName = $this->quoteName($name);
        $quotedTable = $this->quoteName($table);
        $quotedColumn = $this->quoteName($column);

        // Try Oracle Text index first
        $query = "CREATE INDEX {$quotedName}"
            . " ON {$quotedTable}({$quotedColumn})"
            . " INDEXTYPE IS CTXSYS.CONTEXT";

        $this->setQuery($query);

        try {
            $this->execute();
            return true;
        } catch (\Exception $e) {
            // Oracle Text not available — fall back to regular index
            return $this->addIndex($table, $name, $columns);
        }
    }

    /**
     * Drop the primary key from a table
     *
     * @param   string  $table  Table name
     * @return  bool
     */
    public function dropPrimaryKey(string $table): bool
    {
        $table = strtoupper($this->replacePrefix($table));

        if (!$this->tableExists($table)) {
            return false;
        }

        // Find the primary key constraint name
        $this->setQuery(
            "SELECT constraint_name FROM user_constraints"
            . " WHERE table_name = " . $this->quote($table)
            . " AND constraint_type = 'P'"
        );
        $constraintName = $this->loadResult();

        if (!$constraintName) {
            return true; // No primary key
        }

        $query = 'ALTER TABLE ' . $this->quoteName($table) . ' DROP CONSTRAINT ' . $this->quoteName($constraintName);
        $this->setQuery($query);
        return (bool) $this->execute();
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
        $table = strtoupper($this->replacePrefix($table));

        if (!$this->tableExists($table)) {
            return false;
        }

        $columns = is_array($columns) ? $columns : [$columns];
        $columnList = implode(', ', array_map(function ($col) {
            return $this->quoteName(strtoupper($col));
        }, $columns));

        $query = 'ALTER TABLE ' . $this->quoteName($table) . ' ADD PRIMARY KEY (' . $columnList . ')';
        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Add an auto-increment primary key column to a table
     *
     * Oracle uses sequences with triggers for auto-increment behavior.
     * In Oracle 12c+, we can use IDENTITY columns.
     *
     * @param   string  $table      Table name
     * @param   string  $column     Column name
     * @param   bool    $first      Add as first column (ignored)
     * @param   bool    $useBigInt  Use NUMBER(19) for large values
     * @return  bool
     */
    public function addAutoIncrementPrimaryKey(
        string $table,
        string $column = 'id',
        bool $first = false,
        bool $useBigInt = true
    ): bool {
        $table = strtoupper($this->replacePrefix($table));
        $column = strtoupper($column);

        if (!$this->tableExists($table)) {
            return false;
        }

        if ($this->tableHasField($table, $column)) {
            return true;
        }

        $type = $useBigInt ? 'NUMBER(19)' : 'NUMBER(10)';
        $quotedTable = $this->quoteName($table);
        $quotedColumn = $this->quoteName($column);

        // Check if table has data — populated tables need multi-step approach
        $this->setQuery(
            "SELECT COUNT(*) FROM {$quotedTable}"
        );
        $rowCount = (int) $this->loadResult();

        if ($rowCount === 0) {
            // Empty table: single-step IDENTITY column
            $this->setQuery(
                "ALTER TABLE {$quotedTable} ADD {$quotedColumn}"
                . " {$type} GENERATED BY DEFAULT AS IDENTITY"
                . " PRIMARY KEY"
            );
            $this->execute();
            return true;
        }

        // Populated table: multi-step approach
        // Step 1: Add nullable column
        $this->setQuery(
            "ALTER TABLE {$quotedTable} ADD {$quotedColumn}"
            . " {$type} NULL"
        );
        $this->execute();

        // Step 2: Populate with sequential values using ROWID
        $this->populateSequentialValues($table, $column);

        // Step 3: Make NOT NULL
        $this->setQuery(
            "ALTER TABLE {$quotedTable} MODIFY {$quotedColumn}"
            . " NOT NULL"
        );
        $this->execute();

        // Step 4: Add primary key constraint
        $this->setQuery(
            "ALTER TABLE {$quotedTable}"
            . " ADD CONSTRAINT "
            . $this->quoteName($table . '_PK')
            . " PRIMARY KEY ({$quotedColumn})"
        );
        $this->execute();

        // Step 5: Create sequence and set next value
        $seqName = $table . '_' . $column . '_SEQ';
        $nextVal = $rowCount + 1;
        $this->dropSequence($seqName, true);
        $this->createSequence($seqName, $nextVal, 1);

        return true;
    }

    /**
     * Populate a column with sequential integer values
     *
     * @param   string       $table    Table name
     * @param   string       $column   Column name
     * @param   string|null  $orderBy  Optional column to order by
     * @return  bool
     */
    public function populateSequentialValues(string $table, string $column, ?string $orderBy = null): bool
    {
        $table = strtoupper($this->replacePrefix($table));
        $column = strtoupper($column);

        if (!$this->tableExists($table) || !$this->tableHasField($table, $column)) {
            return false;
        }

        $quotedTable = $this->quoteName($table);
        $quotedColumn = $this->quoteName($column);
        $orderClause = $orderBy ? $this->quoteName(strtoupper($orderBy)) : 'ROWID';

        // Use MERGE with ROW_NUMBER()
        $query = "MERGE INTO {$quotedTable} t " .
                 "USING (SELECT ROWID AS rid, " .
                 "ROW_NUMBER() OVER (ORDER BY {$orderClause}) AS rn " .
                 "FROM {$quotedTable}) s " .
                 "ON (t.ROWID = s.rid) " .
                 "WHEN MATCHED THEN UPDATE SET t.{$quotedColumn} = s.rn";

        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Gets the primary key of a table
     *
     * @param   string  $table  The table name
     * @return  string|bool
     */
    public function getPrimaryKey($table)
    {
        $table = strtoupper($this->replacePrefix($table));

        $this->setQuery("
            SELECT cols.column_name
            FROM user_constraints cons
            JOIN user_cons_columns cols
                ON cons.constraint_name = cols.constraint_name
            WHERE cons.table_name = " . $this->quote($table) . "
            AND cons.constraint_type = 'P'
            ORDER BY cols.position
        ");

        $result = $this->loadResult();
        // Lowercase for ORM compatibility
        return $result ? strtolower($result) : false;
    }

    /**
     * Get primary key column names
     *
     * @param   string  $table  The table name
     * @return  array   Array of column names in the primary key
     */
    public function getPrimaryKeyColumns($table): array
    {
        $table = strtoupper($this->replacePrefix($table));

        $this->setQuery("
            SELECT cols.column_name
            FROM user_constraints cons
            JOIN user_cons_columns cols ON cons.constraint_name = cols.constraint_name
            WHERE cons.table_name = " . $this->quote($table) . "
            AND cons.constraint_type = 'P'
            ORDER BY cols.position
        ");

        $rows = $this->loadColumn();
        // Lowercase for ORM compatibility
        return $rows ? array_map('strtolower', $rows) : [];
    }

    /**
     * Gets foreign key constraints for a table
     *
     * @param   string  $table  The table name
     * @return  array
     */
    public function getForeignKeys($table)
    {
        $table = strtoupper($this->replacePrefix($table));

        $sql = "SELECT
                    c.constraint_name,
                    cc.column_name,
                    c.r_constraint_name,
                    rc.table_name AS foreign_table,
                    rcc.column_name AS foreign_column,
                    c.delete_rule
                FROM user_constraints c
                JOIN user_cons_columns cc ON c.constraint_name = cc.constraint_name
                JOIN user_constraints rc ON c.r_constraint_name = rc.constraint_name
                JOIN user_cons_columns rcc ON rc.constraint_name = rcc.constraint_name AND cc.position = rcc.position
                WHERE c.constraint_type = 'R'
                AND c.table_name = " . $this->quote($table) . "
                ORDER BY c.constraint_name, cc.position";

        $this->setQuery($sql);

        return $this->groupForeignKeyRows($this->loadObjectList(), [
            'constraint_name' => 'constraint_name',
            'column_name'     => 'column_name',
            'foreign_table'   => 'foreign_table',
            'foreign_column'  => 'foreign_column',
            'on_update'       => fn($row) => 'NO ACTION',
            'on_delete'       => fn($row) => $row->delete_rule ?: 'NO ACTION',
        ]);
    }

    /**
     * Sets the connection to use UTF-8 character encoding
     *
     * Oracle uses NLS_LANG environment or session parameter
     *
     * @return  bool
     */
    public function setUTF()
    {
        // Set session parameters for UTF-8
        $this->setQuery(
            "ALTER SESSION SET NLS_LANGUAGE = 'AMERICAN'"
            . " NLS_TERRITORY = 'AMERICA'"
            . " NLS_CHARACTERSET = 'AL32UTF8'"
        );

        try {
            return (bool) $this->execute();
        } catch (\Exception $e) {
            // Some of these may not be modifiable at session level
            return true;
        }
    }

    /**
     * Unlock tables
     *
     * Oracle releases locks automatically at commit/rollback
     *
     * @return  $this
     */
    public function unlockTables()
    {
        // Oracle releases locks at transaction end
        return $this;
    }

    /**
     * Set the storage engine for a table
     *
     * Oracle doesn't have storage engines
     *
     * @param   string  $table   Table name (ignored)
     * @param   string  $engine  Engine type (ignored)
     * @return  bool
     */
    public function setTableEngine(string $table, string $engine = 'MYISAM'): bool
    {
        return true;
    }

    /**
     * Set the character set and collation for a table
     *
     * Oracle handles encoding at database level
     *
     * @param   string  $table      Table name (ignored)
     * @param   string  $charset    Character set (ignored)
     * @param   string  $collation  Collation (ignored)
     * @return  bool
     */
    public function setTableCharset(
        string $table,
        string $charset = 'utf8',
        string $collation = 'utf8_general_ci'
    ): bool {
        return true;
    }

    // =========================================================================
    // Feature Detection Methods - Oracle Implementations
    // =========================================================================

    /**
     * Check if this database supports sequences
     *
     * @return  bool
     */
    public function supportsSequences(): bool
    {
        return true;
    }

    /**
     * Get the schema grammar instance for this driver
     *
     * @return  \Hubzero\Database\Drivers\Base\BaseSchemaGrammar
     */
    public function getSchemaGrammar()
    {
        return $this->makeSchemaGrammarFromRegistry();
    }

    /**
     * Build a column definition for ALTER TABLE ADD/MODIFY
     *
     * Converts abstract types (string, text, etc.) to Oracle
     * SQL types and appends modifiers (DEFAULT, NOT NULL).
     *
     * @param   string  $name        Column name
     * @param   array   $definition  ['type' => ..., 'modifiers' => [...]]
     * @return  string  SQL column definition
     */
    public function buildAlterColumnDefinition(
        string $name,
        array $definition
    ): string {
        $type = $definition['type'] ?? 'VARCHAR2(255)';
        $modifiers = $definition['modifiers'] ?? [];

        // Normalize abstract types to Oracle SQL types
        $type = $this->normalizeColumnType($type, $modifiers);

        $parts = [$this->quoteName(strtoupper($name)), $type];

        // Translate zero-date default to NULL
        if (
            array_key_exists('default', $modifiers)
            && self::isZeroDate($modifiers['default'])
        ) {
            $modifiers['nullable'] = true;
            $modifiers['default'] = null;
        }

        // DEFAULT before NOT NULL (standard SQL, required)
        if (array_key_exists('default', $modifiers)) {
            $default = $modifiers['default'];
            if ($default === null) {
                $parts[] = 'DEFAULT NULL';
            } elseif (is_bool($default)) {
                $parts[] = 'DEFAULT ' . ($default ? '1' : '0');
            } elseif (is_numeric($default)) {
                $parts[] = 'DEFAULT ' . $default;
            } elseif ($default === 'CURRENT_TIMESTAMP') {
                $parts[] = 'DEFAULT CURRENT_TIMESTAMP';
            } else {
                $parts[] = "DEFAULT '"
                    . str_replace("'", "''", $default) . "'";
            }
        }

        // NOT NULL constraint
        if (
            isset($modifiers['nullable'])
            && $modifiers['nullable'] === false
        ) {
            $parts[] = 'NOT NULL';
        }

        return implode(' ', $parts);
    }

    // =========================================================================
    // Feature Detection Methods
    // =========================================================================

    /**
     * Check if generated/computed columns are supported
     *
     * @return  bool  True - Oracle supports virtual columns
     */
    public function supportsGeneratedColumns(): bool
    {
        return true;
    }

    /**
     * Check if JSON data type is supported
     *
     * @return  bool  True - Oracle 12c+ has JSON support
     */
    public function supportsJson(): bool
    {
        $version = $this->getVersion();
        return version_compare($version, '12.0', '>=');
    }

    /**
     * Check if window functions are supported
     *
     * @return  bool  True - Oracle supports window functions
     */
    public function supportsWindowFunctions(): bool
    {
        return true;
    }

    /**
     * Check if Common Table Expressions (WITH clause) are supported
     *
     * @return  bool  True - Oracle supports CTEs
     */
    public function supportsCTE(): bool
    {
        return true;
    }

    /**
     * Oracle <23c does not support IF NOT EXISTS for CREATE TABLE
     *
     * @return  bool  False - Oracle requires manual existence checks
     */
    public function supportsIfNotExists(): bool
    {
        return false;
    }

    /**
     * Oracle does not support IF NOT EXISTS for CREATE INDEX
     *
     * @return  bool  False
     */
    public function supportsIfNotExistsForIndex(): bool
    {
        return false;
    }

    // =========================================================================
    // Important Overrides
    // =========================================================================

    /**
     * Drops a table from the database
     *
     * Oracle does not support DROP TABLE IF EXISTS (prior to 23c).
     * This method checks for existence before dropping.
     *
     * @param   string  $tableName  The name of the table to drop
     * @param   bool    $ifExists   Check existence before dropping
     * @return  $this
     */
    public function dropTable($tableName, $ifExists = true)
    {
        $tableName = strtoupper($this->replacePrefix($tableName));

        if ($ifExists && !$this->tableExists($tableName)) {
            return $this;
        }

        $this->setQuery(
            $this->getSchemaGrammar()->compileDrop($tableName, false)
        );
        $this->execute();

        return $this;
    }

    /**
     * Gets the auto-generated ID from the last INSERT
     *
     * PDO_OCI's lastInsertId() does not work with IDENTITY columns.
     * We track the last inserted table and query the hidden identity
     * sequence's current value.
     *
     * @return  int|null
     */
    public function insertid()
    {
        // Try PDO::lastInsertId() first
        try {
            $id = $this->connection->lastInsertId();
            if ($id && $id > 0) {
                return (int) $id;
            }
        } catch (\Exception $e) {
            // Fallback below
        }

        // Find the identity sequence for the last inserted table
        if (!$this->lastInsertTable) {
            return null;
        }

        return $this->getLastIdentityValue(
            $this->lastInsertTable
        );
    }

    /**
     * Prepares a query for binding
     *
     * Override to capture the table name from INSERT statements
     * for use by insertid().
     *
     * @param   string  $statement  The statement to prepare
     * @return  $this
     */
    public function prepare($statement)
    {
        // Capture table name from INSERT statements
        if (
            preg_match(
                '/^\s*INSERT\s+(?:ALL\s+)?INTO\s+"?([^"\s(]+)"?/i',
                $statement,
                $matches
            )
        ) {
            $this->lastInsertTable = strtoupper(
                trim($matches[1])
            );
        }

        return parent::prepare($statement);
    }

    /**
     * Override setQuery to capture the table name from INSERT statements
     *
     * @param   string  $sql  The SQL statement
     * @return  $this
     */
    public function setQuery($query)
    {
        // Enforce raw query mode at this level — ensures the
        // backtrace correctly identifies external callers.
        // The flag prevents parent::setQuery() from re-checking.
        $shouldEnforce = $this->rawQueryMode !== 'permissive'
            && !$this->rawQueryModeEnforced;

        if ($shouldEnforce) {
            $this->rawQueryModeEnforced = true;
        }

        try {
            if ($shouldEnforce) {
                $this->enforceRawQueryMode((string) $query);
            }

            if (
                is_string($query) && preg_match(
                    '/^\s*INSERT\s+(?:ALL\s+)?INTO\s+"?([^"\s(]+)"?/i',
                    $query,
                    $matches
                )
            ) {
                $this->lastInsertTable = strtoupper(
                    trim($matches[1])
                );
            }

            return parent::setQuery($query);
        } finally {
            if ($shouldEnforce) {
                $this->rawQueryModeEnforced = false;
            }
        }
    }

    /**
     * Get the last identity value for a table
     *
     * Oracle IDENTITY columns create hidden sequences
     * (ISEQ$$_xxxxx). This queries USER_TAB_IDENTITY_COLS
     * to find the sequence name, then gets its current value.
     *
     * @param   string  $table  The uppercase table name
     * @return  int|null
     */
    protected function getLastIdentityValue($table)
    {
        $table = strtoupper($table);

        // Find the identity sequence
        $this->setQuery(
            "SELECT sequence_name"
            . " FROM user_tab_identity_cols"
            . " WHERE table_name = " . $this->quote($table)
        );
        $seqName = $this->loadResult();

        if (!$seqName) {
            // Try common naming conventions
            $candidates = [
                $table . '_SEQ',
                $table . '_ID_SEQ',
            ];
            foreach ($candidates as $candidate) {
                if ($this->sequenceExists($candidate)) {
                    $seqName = $candidate;
                    break;
                }
            }
        }

        if (!$seqName) {
            return null;
        }

        // Get current value of the sequence
        try {
            $this->setQuery(
                "SELECT " . $this->quoteName($seqName)
                . ".CURRVAL FROM DUAL"
            );
            return (int) $this->loadResult();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Maps MySQL column types to Oracle equivalents
     *
     * Used when converting MySQL DDL to Oracle DDL.
     *
     * @param   string  $type  The MySQL column type
     * @return  string  The Oracle column type
     */
    public function mapColumnType(string $type): string
    {
        $upper = strtoupper(trim($type));

        // Remove UNSIGNED
        $upper = preg_replace('/\s+UNSIGNED$/i', '', $upper);

        // TINYINT(1) or BOOLEAN -> NUMBER(1)
        if (
            $upper === 'TINYINT(1)'
            || $upper === 'BOOLEAN'
            || $upper === 'BOOL'
        ) {
            return 'NUMBER(1)';
        }

        // Integer types -> NUMBER with precision
        if (
            preg_match(
                '/^TINYINT(\s*\(\d+\))?$/i',
                $upper
            )
        ) {
            return 'NUMBER(3)';
        }
        if (
            preg_match(
                '/^SMALLINT(\s*\(\d+\))?$/i',
                $upper
            )
        ) {
            return 'NUMBER(5)';
        }
        if (
            preg_match(
                '/^MEDIUMINT(\s*\(\d+\))?$/i',
                $upper
            )
        ) {
            return 'NUMBER(7)';
        }
        if (
            preg_match(
                '/^INT(EGER)?(\s*\(\d+\))?$/i',
                $upper
            )
        ) {
            return 'NUMBER(10)';
        }
        if (
            preg_match(
                '/^BIGINT(\s*\(\d+\))?$/i',
                $upper
            )
        ) {
            return 'NUMBER(19)';
        }

        // Float/Double
        if (
            preg_match(
                '/^FLOAT(\s*\([^)]+\))?$/i',
                $upper
            )
        ) {
            return 'BINARY_FLOAT';
        }
        if (
            preg_match(
                '/^DOUBLE(\s+PRECISION)?(\s*\([^)]+\))?$/i',
                $upper
            )
        ) {
            return 'BINARY_DOUBLE';
        }

        // Decimal/Numeric - preserve precision
        if (
            preg_match(
                '/^(DECIMAL|NUMERIC)(\s*\([^)]+\))?$/i',
                $upper,
                $m
            )
        ) {
            return 'NUMBER' . ($m[2] ?? '');
        }

        // Text types -> CLOB
        if (
            preg_match(
                '/^(TINY|MEDIUM|LONG)?TEXT$/i',
                $upper
            )
        ) {
            return 'CLOB';
        }

        // VARCHAR -> VARCHAR2
        if (
            preg_match(
                '/^VARCHAR\s*\((\d+)\)$/i',
                $upper,
                $m
            )
        ) {
            return 'VARCHAR2(' . $m[1] . ')';
        }
        if ($upper === 'VARCHAR') {
            return 'VARCHAR2(255)';
        }

        // CHAR - keep as-is
        if (preg_match('/^CHAR\s*\(\d+\)$/i', $upper)) {
            return $type;
        }

        // BLOB types -> BLOB
        if (
            preg_match(
                '/^(TINY|MEDIUM|LONG)?BLOB$/i',
                $upper
            )
        ) {
            return 'BLOB';
        }

        // DateTime types
        if (
            $upper === 'DATETIME'
            || $upper === 'TIMESTAMP'
        ) {
            return 'TIMESTAMP';
        }
        if ($upper === 'DATE') {
            return 'DATE';
        }
        if ($upper === 'TIME') {
            return 'TIMESTAMP';
        }

        // ENUM -> VARCHAR2(255)
        if (preg_match('/^ENUM\s*\(/i', $upper)) {
            return 'VARCHAR2(255)';
        }

        // SET -> VARCHAR2(255)
        if (preg_match('/^SET\s*\(/i', $upper)) {
            return 'VARCHAR2(255)';
        }

        // JSON -> CLOB
        if ($upper === 'JSON') {
            return 'CLOB';
        }

        // YEAR -> NUMBER(4)
        if (
            $upper === 'YEAR'
            || preg_match('/^YEAR\s*\(\d+\)$/i', $upper)
        ) {
            return 'NUMBER(4)';
        }

        return $type;
    }

    /**
     * Format a boolean value as a SQL literal
     *
     * Oracle has no native boolean type; uses NUMBER(1) with 1/0 values.
     *
     * @param   bool  $value  The boolean value
     * @return  string  The SQL literal
     */
    public function formatBooleanLiteral(bool $value): string
    {
        return $value ? '1' : '0';
    }

    /**
     * Apply length/precision modifiers to a mapped column type
     *
     * Oracle override: float and double map to BINARY_FLOAT and
     * BINARY_DOUBLE which don't accept precision parameters.
     * Only decimal (NUMBER) takes precision/scale.
     *
     * @param   string  $abstractType  The original abstract type name
     * @param   string  $nativeType    The mapped database type
     * @param   array   $modifiers     Column modifiers
     * @return  string  The type with modifiers applied
     */
    protected function applyColumnModifiers(
        string $abstractType,
        string $nativeType,
        array $modifiers
    ): string {
        switch ($abstractType) {
            case 'string':
            case 'char':
                $length = $modifiers['length'] ?? 255;
                return "{$nativeType}({$length})";

            case 'decimal':
                if (
                    isset($modifiers['precision'])
                    || isset($modifiers['scale'])
                ) {
                    $p = $modifiers['precision'] ?? 10;
                    $s = $modifiers['scale'] ?? 2;
                    return "{$nativeType}({$p},{$s})";
                }
                return $nativeType;

            default:
                // Oracle: float->BINARY_FLOAT, double->BINARY_DOUBLE
                // These don't take precision parameters
                return $nativeType;
        }
    }

    /**
     * Normalize abstract column types to Oracle DDL types
     *
     * Maps camelCase abstract type names (used by the schema builder)
     * to Oracle-specific SQL types via the $typeMap property and parent
     * class. Also handles concrete MySQL-style types that appear in
     * migrations (e.g., 'BIGINT(20) UNSIGNED').
     *
     * @param   string  $type       The abstract or concrete type name
     * @param   array   $modifiers  Optional modifiers
     * @return  string  The Oracle SQL type
     */
    public function normalizeColumnType(
        string $type,
        array $modifiers = []
    ): string {
        // Abstract types are handled by parent using Grammar's typeMap
        if ($this->getSchemaGrammar()->getTypeMapping($type) !== null) {
            return parent::normalizeColumnType($type, $modifiers);
        }

        // Handle MySQL SET() and ENUM() types
        if (
            preg_match('/^SET\s*\(/i', $type)
            || preg_match('/^ENUM\s*\(/i', $type)
        ) {
            return 'VARCHAR2(255)';
        }

        // Handle concrete MySQL-style types
        $cleaned = trim(
            str_ireplace(' UNSIGNED', '', $type)
        );

        if (
            preg_match(
                '/^BIGINT(\(\d+\))?$/i',
                $cleaned
            )
        ) {
            return 'NUMBER(19)';
        }
        if (
            preg_match(
                '/^INT(EGER)?(\(\d+\))?$/i',
                $cleaned
            )
        ) {
            return 'NUMBER(10)';
        }
        if (
            preg_match(
                '/^MEDIUMINT(\(\d+\))?$/i',
                $cleaned
            )
        ) {
            return 'NUMBER(7)';
        }
        if (
            preg_match(
                '/^SMALLINT(\(\d+\))?$/i',
                $cleaned
            )
        ) {
            return 'NUMBER(5)';
        }
        if (
            preg_match(
                '/^TINYINT(\(\d+\))?$/i',
                $cleaned
            )
        ) {
            return 'NUMBER(3)';
        }
        if (strcasecmp($cleaned, 'DATETIME') === 0) {
            return 'TIMESTAMP';
        }
        if (
            preg_match(
                '/^(TINY|MEDIUM|LONG)?BLOB$/i',
                $cleaned
            )
        ) {
            return 'BLOB';
        }
        if (
            preg_match(
                '/^(TINY|MEDIUM|LONG)?TEXT$/i',
                $cleaned
            )
        ) {
            return 'CLOB';
        }
        if (
            strcasecmp($cleaned, 'BOOLEAN') === 0
            || strcasecmp($cleaned, 'BOOL') === 0
        ) {
            return 'NUMBER(1)';
        }
        if (
            preg_match(
                '/^VARCHAR\((\d+)\)$/i',
                $cleaned,
                $m
            )
        ) {
            return 'VARCHAR2(' . $m[1] . ')';
        }
        if (
            preg_match(
                '/^FLOAT(\(\d+(,\d+)?\))?$/i',
                $cleaned
            )
        ) {
            return 'BINARY_FLOAT';
        }
        if (
            preg_match(
                '/^DOUBLE(\(\d+(,\d+)?\))?$/i',
                $cleaned
            )
        ) {
            return 'BINARY_DOUBLE';
        }
        if (
            preg_match(
                '/^DECIMAL\((\d+),(\d+)\)$/i',
                $cleaned,
                $m
            )
        ) {
            return 'NUMBER(' . $m[1] . ',' . $m[2] . ')';
        }
        if (strcasecmp($cleaned, 'JSON') === 0) {
            return 'CLOB';
        }
        if (strcasecmp($cleaned, 'TIMESTAMP') === 0) {
            return 'TIMESTAMP';
        }
        if (strcasecmp($cleaned, 'DATE') === 0) {
            return 'DATE';
        }
        if (strcasecmp($cleaned, 'TIME') === 0) {
            return 'TIMESTAMP';
        }

        // Already a concrete Oracle type
        return $type;
    }

    /**
     * Get CHECK constraints for table
     *
     * @param   string  $table  Table name
     * @return  array
     */
    public function getCheckConstraints(string $table): array
    {
        $table = strtoupper($this->replacePrefix($table));

        // Note: SEARCH_CONDITION is a LONG column in older Oracle versions
        // which can be tricky to fetch. In 12c+ it's accessible via user_constraints.
        $sql = "SELECT 
                   constraint_name AS name,
                   search_condition
                FROM user_constraints
                WHERE table_name = " . $this->quote($table) . "
                AND constraint_type = 'C'";

        $this->setQuery($sql);
        try {
            $results = $this->loadObjectList();

            $constraints = [];
            foreach ($results as $row) {
                // Handle potential stream resource for LONG type
                $expression = $row->search_condition;
                if (is_resource($expression)) {
                    $expression = stream_get_contents($expression);
                }

                $constraints[] = (object) [
                    'name' => trim($row->name),
                    'expression' => trim($expression)
                ];
            }

            return $constraints;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Add stored (computed) column
     *
     * Oracle uses GENERATED ALWAYS AS (...) VIRTUAL.
     * Oracle doesn't strictly support "STORED" computed columns (they are calculated),
     * but you can index them.
     *
     * @param   string       $table       Table name
     * @param   string       $column      Column name
     * @param   string       $expression  SQL expression
     * @param   string|null  $type        Column type (ignored by Oracle)
     * @return  bool
     */
    public function addStoredColumn(string $table, string $column, string $expression, ?string $type = null): bool
    {
        $table = $this->replacePrefix($table);
        $column = $this->quoteName($column);
        $this->setQuery("ALTER TABLE $table ADD $column GENERATED ALWAYS AS ($expression) VIRTUAL");
        return $this->execute();
    }

    /**
     * Add virtual (computed) column
     *
     * Oracle uses VIRTUAL by default for generated columns.
     *
     * @param   string       $table       Table name
     * @param   string       $column      Column name
     * @param   string       $expression  SQL expression
     * @param   string|null  $type        Column type (ignored by Oracle)
     * @return  bool
     */
    public function addVirtualColumn(string $table, string $column, string $expression, ?string $type = null): bool
    {
        return $this->addStoredColumn($table, $column, $expression, $type);
    }

    /**
     * Get global configuration variables
     *
     * @return  array
     */
    public function getGlobalVariables(): array
    {
        $this->setQuery("SELECT name, value FROM v\$parameter ORDER BY name");
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
        $this->setQuery("SELECT (SYSDATE - startup_time) * 24 * 60 * 60 FROM v\$instance");
        try {
            return (int) $this->loadResult();
        } catch (\Exception $e) {
            return null;
        }
    }


    /**
     * Build an auto-increment primary key column definition
     *
     * Oracle 12c+ uses GENERATED BY DEFAULT AS IDENTITY.
     * BY DEFAULT allows explicit ID inserts (needed for test data and migrations).
     *
     * @param   string  $quotedName  The quoted column name
     * @param   string  $type        The column type
     * @return  string  The column definition SQL
     */
    public function buildAutoIncrementColumn(string $quotedName, string $type): string
    {
        return "$quotedName $type GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY";
    }

    /**
     * Build a regular INDEX definition for CREATE TABLE
     *
     * Oracle does not support inline indexes in CREATE TABLE.
     * Use CREATE INDEX statement instead.
     *
     * @param   string  $quotedName     The quoted index name
     * @param   string  $columnList     The column list SQL
     * @return  string|null  Null as it's not supported inline
     */
    public function buildIndexDefinition(string $quotedName, string $columnList): ?string
    {
        return null;
    }

    /**
     * Build a UNIQUE constraint definition for CREATE TABLE
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
     * Build foreign key constraint definition for inline CREATE TABLE
     *
     * Oracle does not support ON UPDATE in foreign key constraints.
     * Only ON DELETE actions are supported: CASCADE, SET NULL, NO ACTION.
     *
     * @param   array   $fk         Foreign key definition array
     * @param   string  $tableName  The table being created
     * @return  string
     */
    public function buildForeignKeyDefinition(array $fk, string $tableName): string
    {
        $refTable = $this->replacePrefix($fk['referencedTable']);
        $fkName = !empty($fk['name']) ? $fk['name'] : "fk_{$tableName}_{$fk['column']}";
        $constraintName = $this->quoteName($fkName);
        $fkColumn = $this->quoteName($fk['column']);
        $refTableQuoted = $this->quoteName($refTable);
        $refColumn = $this->quoteName($fk['referencedColumn']);

        $sql = "CONSTRAINT $constraintName ";
        $sql .= "FOREIGN KEY ($fkColumn) ";
        $sql .= "REFERENCES $refTableQuoted ($refColumn)";

        // Oracle only supports ON DELETE, not ON UPDATE
        $onDelete = strtoupper($fk['onDelete'] ?? 'NO ACTION');
        if ($onDelete !== 'NO ACTION' && $onDelete !== 'RESTRICT') {
            $sql .= " ON DELETE $onDelete";
        }

        return $sql;
    }
}
