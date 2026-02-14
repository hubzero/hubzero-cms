<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Driver;

use Hubzero\Database\Driver\Pdo as PdoDriver;

/**
 * SQL Base Driver - Universal SQL Operations
 *
 * This abstract class provides the SQL layer that defines the contract
 * for SQL operations and provides shared default implementations.
 * Concrete dialect drivers (Mysql, Pgsql, Sqlite, etc.) extend this class.
 *
 * DESIGN INTENT:
 * This class is a reusable baseline, not a concrete SQL dialect. Some defaults
 * intentionally follow common MySQL-family conventions (types/functions) because
 * they maximize code sharing across the most frequently used drivers. Dialect
 * drivers are expected to override any behavior that is not correct for their
 * database engine.
 *
 * ARCHITECTURE:
 * ```
 * Driver (abstract base - connection management + query execution)
 *   └── Pdo (deprecated BC shell - constructor creates PdoConnection)
 *         └── Sql (this class - universal SQL + abstract methods)
 *               ├── Mysql (MySQL-specific SQL)
 *               │     ├── Mariadb
 *               │     └── Percona
 *               ├── Pgsql (PostgreSQL-specific SQL)
 *               ├── Sqlite (SQLite-specific SQL)
 *               ├── Firebird (Firebird-specific SQL)
 *               └── Informix (Informix-specific SQL)
 * ```
 *
 * RESPONSIBILITIES:
 * - Define abstract methods for dialect-specific SQL operations
 * - Provide default implementations for truly universal SQL
 * - Act as the contract that all SQL dialect drivers must implement
 *
 * WHAT BELONGS HERE:
 * - Abstract methods for schema introspection (tableExists, getTableColumns, etc.)
 * - Abstract methods for SQL helpers (sqlNow, sqlIfNull, etc.)
 * - Universal SQL that works identically on all databases
 *
 * WHAT DOES NOT BELONG HERE:
 * - Connection handling (that's in Driver.php)
 * - Database-specific syntax (that's in Mysql/Pgsql/Sqlite/etc.)
 *
 */
abstract class Sql extends PdoDriver
{
    /**
     * Abstract-to-native type mapping for this driver
     *
     * Maps camelCase abstract type names (e.g., 'integer', 'string', 'boolean')
     * to database-specific SQL types. Override this property in subclasses
     * to provide driver-specific mappings.
     *
     * The default mapping is a pragmatic common baseline and is not intended
     * to represent a standards-pure or engine-specific dialect.
     *
     * @var array<string, string>
     */
    protected array $typeMap = [
        // Integer types
        'tinyInteger'   => 'TINYINT',
        'smallInteger'  => 'SMALLINT',
        'mediumInteger' => 'MEDIUMINT',
        'integer'       => 'INT',
        'bigInteger'    => 'BIGINT',
        'boolean'       => 'TINYINT(1)',

        // String types
        'string'     => 'VARCHAR',
        'char'       => 'CHAR',
        'tinyText'   => 'TINYTEXT',
        'text'       => 'TEXT',
        'mediumText' => 'MEDIUMTEXT',
        'longText'   => 'LONGTEXT',

        // Numeric types
        'float'   => 'FLOAT',
        'double'  => 'DOUBLE',
        'decimal' => 'DECIMAL',

        // Date/time types
        'date'        => 'DATE',
        'time'        => 'TIME',
        'datetime'    => 'DATETIME',
        'timestamp'   => 'TIMESTAMP',
        'timestampTz' => 'TIMESTAMP',
        'year'        => 'YEAR',

        // Binary types
        'binary' => 'BLOB',

        // Special types
        'json'       => 'JSON',
        'uuid'       => 'CHAR(36)',
        'ulid'       => 'CHAR(26)',
        'ipAddress'  => 'VARCHAR(45)',
        'macAddress' => 'VARCHAR(17)',
    ];

    // =========================================================================
    // SQL Expression Properties - Override in subclasses for dialect differences
    // =========================================================================

    /**
     * SQL expression for the current timestamp
     *
     * Baseline default chosen for broad reuse; dialect drivers should override
     * where needed for correctness.
     *
     * @var string
     */
    protected string $nowExpression = 'NOW()';

    /**
     * SQL expression for random ordering
     *
     * Baseline default chosen for broad reuse; dialect drivers should override
     * where needed for correctness.
     *
     * @var string
     */
    protected string $randExpression = 'RAND()';

    /**
     * SQL function name for string length (in characters)
     *
     * Baseline default chosen for broad reuse; dialect drivers should override
     * where needed for correctness.
     *
     * @var string
     */
    protected string $lengthFunction = 'CHAR_LENGTH';

    /**
     * SQL function name for null coalescing
     *
     * Baseline default chosen for broad reuse; dialect drivers should override
     * where needed for correctness.
     *
     * @var string
     */
    protected string $ifNullFunction = 'IFNULL';

    // =========================================================================
    // Abstract Methods - Must be implemented by dialect drivers
    // =========================================================================

    /**
     * Checks for the existence of a table
     *
     * @param   string  $table  The table we're looking for
     * @return  bool
     */
    abstract public function tableExists($table);

    /**
     * Returns whether or not the given table has a given field
     *
     * This method provides a universal implementation that works across all drivers
     * by leveraging the abstract getTableColumns() method.
     *
     * @param   string  $table  A table name
     * @param   string  $field  A field name
     * @return  bool
     */
    public function tableHasField($table, $field)
    {
        $columns = $this->getTableColumns($table);

        if (!is_array($columns)) {
            return false;
        }

        // Check both exact match and lowercase match for case-insensitive databases
        return isset($columns[$field]) || isset($columns[strtolower($field)]);
    }

    /**
     * Normalize SHOW COLUMNS style rows into getTableColumns() output.
     *
     * @param   array  $fields
     * @param   bool   $typeOnly
     * @return  array
     */
    protected function mapShowColumnsResult(array $fields, bool $typeOnly = true): array
    {
        $result = [];

        if ($typeOnly) {
            foreach ($fields as $field) {
                $result[$field->Field] = preg_replace("/[(0-9)]/", '', $field->Type);
            }
        } else {
            foreach ($fields as $field) {
                $result[$field->Field] = $field;
            }
        }

        return $result;
    }

    /**
     * Execute a SHOW COLUMNS-style query and normalize the result.
     *
     * @param   string  $query
     * @param   bool    $typeOnly
     * @return  array
     */
    protected function getTableColumnsFromShowQuery(string $query, bool $typeOnly = true): array
    {
        $this->setQuery($query);
        $fields = $this->loadObjectList();

        if (!is_array($fields)) {
            return [];
        }

        return $this->mapShowColumnsResult($fields, $typeOnly);
    }

    /**
     * Check if a SHOW COLUMNS style result contains a field.
     *
     * Accepts arrays keyed by field name or plain numeric arrays of row objects.
     *
     * @param   mixed   $fields
     * @param   string  $field
     * @return  bool
     */
    protected function hasFieldInShowColumnsResult($fields, string $field): bool
    {
        if (!is_array($fields)) {
            return false;
        }

        // loadObjectList('Field') typically returns an array keyed by field name.
        if (isset($fields[$field]) || isset($fields[strtolower($field)])) {
            return true;
        }

        // Fallback for numeric arrays of objects.
        foreach ($fields as $row) {
            if (is_object($row) && isset($row->Field) && strcasecmp((string) $row->Field, $field) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Execute a SHOW-fields style query and check for a field name.
     *
     * @param   string  $query
     * @param   string  $field
     * @return  bool
     */
    protected function tableHasFieldFromShowQuery(string $query, string $field): bool
    {
        $this->setQuery($query);
        $fields = $this->loadObjectList('Field');

        return $this->hasFieldInShowColumnsResult($fields, $field);
    }

    /**
     * Resolve primary key column from SHOW KEYS style rows.
     *
     * Matches current MySQL behavior: if multiple rows match, the last one wins.
     *
     * @param   mixed   $keys
     * @param   string  $primaryKeyName
     * @return  string|false
     */
    protected function getPrimaryKeyFromTableKeys($keys, string $primaryKeyName = 'PRIMARY')
    {
        $key = false;

        if ($keys && is_array($keys) && count($keys) > 0) {
            foreach ($keys as $k) {
                if (isset($k->Key_name) && $k->Key_name == $primaryKeyName) {
                    $key = $k->Column_name ?? false;
                }
            }
        }

        return $key;
    }

    /**
     * Normalize primary key columns from SHOW KEYS style rows.
     *
     * Rows are ordered by Seq_in_index so multi-column keys are returned
     * in deterministic index order.
     *
     * @param   mixed  $rows
     * @return  array
     */
    protected function extractPrimaryKeyColumnsFromTableKeyRows($rows): array
    {
        if (!is_array($rows) || empty($rows)) {
            return [];
        }

        if (count($rows) > 1) {
            usort($rows, function ($a, $b) {
                return (int) ($a->Seq_in_index ?? 0) <=> (int) ($b->Seq_in_index ?? 0);
            });
        }

        $columns = [];
        foreach ($rows as $row) {
            if (isset($row->Column_name)) {
                $columns[] = $row->Column_name;
            }
        }

        return $columns;
    }

    /**
     * Resolve primary key column from SHOW COLUMNS style rows.
     *
     * Returns the first row where Key equals the given marker.
     *
     * @param   mixed   $columns
     * @param   string  $marker
     * @return  string|false
     */
    protected function getPrimaryKeyFromShowColumns($columns, string $marker = 'PRI')
    {
        if (!is_array($columns)) {
            return false;
        }

        foreach ($columns as $column) {
            if (isset($column->Key) && $column->Key === $marker) {
                return $column->Field ?? false;
            }
        }

        return false;
    }

    /**
     * Normalize SHOW KEYS style output into index objects.
     *
     * @param   mixed  $keys
     * @return  array
     */
    protected function normalizeIndexesFromTableKeys($keys): array
    {
        $indexes = [];

        if (!is_array($keys)) {
            return $indexes;
        }

        foreach ($keys as $keyName => $keyInfo) {
            if (!isset($indexes[$keyName])) {
                $indexes[$keyName] = (object) [
                    'name'    => $keyName,
                    'columns' => [],
                    'unique'  => ($keyInfo->Non_unique ?? 1) == 0,
                    'primary' => $keyName === 'PRIMARY',
                ];
            }
            $indexes[$keyName]->columns[] = $keyInfo->Column_name;
        }

        return array_values($indexes);
    }

    /**
     * Execute SHOW TABLES and return table names.
     *
     * @return  array
     */
    protected function getTableListFromShowTables(): array
    {
        $this->setQuery('SHOW TABLES');
        $tables = $this->loadColumn();

        return is_array($tables) ? $tables : [];
    }

    /**
     * Execute SHOW CREATE TABLE queries for one or more tables.
     *
     * @param   string|array  $tables
     * @param   bool          $escapeTable
     * @param   string        $createKeyword
     * @return  array
     */
    protected function getTableCreateFromShowCreate(
        $tables,
        bool $escapeTable = true,
        string $createKeyword = 'table'
    ): array {
        $result = [];

        foreach ((array) $tables as $table) {
            $tableName = (string) $table;
            if ($escapeTable) {
                $tableName = $this->escape($tableName);
            }

            $this->setQuery('SHOW CREATE ' . $createKeyword . ' ' . $this->quoteName($tableName));
            $row = $this->loadRow();
            $result[$table] = $row[1] ?? '';
        }

        return $result;
    }

    /**
     * Execute SHOW KEYS for a table.
     *
     * @param   string       $table
     * @param   string|null  $indexBy
     * @return  array
     */
    protected function getTableKeysFromShowKeys(string $table, ?string $indexBy = null): array
    {
        $this->setQuery('SHOW KEYS FROM ' . $this->quoteName($table));
        $keys = $indexBy !== null
            ? $this->loadObjectList($indexBy)
            : $this->loadObjectList();

        return is_array($keys) ? $keys : [];
    }

    /**
     * Build SHOW COLUMNS/FIELDS query for a table.
     *
     * @param   string  $table
     * @param   bool    $full
     * @param   bool    $escapeTable
     * @param   string  $keyword
     * @return  string
     */
    protected function buildShowColumnsQueryForDriver(
        string $table,
        bool $full = false,
        bool $escapeTable = true,
        string $keyword = 'COLUMNS'
    ): string {
        $tableName = $escapeTable ? $this->escape($table) : $table;
        $fullPart = $full ? 'FULL ' : '';

        return 'SHOW ' . $fullPart . $keyword . ' FROM ' . $this->quoteName($tableName);
    }

    /**
     * Extract normalized driver version (x.y.z) from a version string.
     *
     * @param   string|null  $version
     * @param   bool         $anchorStart
     * @return  string|null
     */
    protected function extractDriverVersionFromString(?string $version, bool $anchorStart = false): ?string
    {
        if (!$version) {
            return null;
        }

        $pattern = $anchorStart
            ? '/^(\d+\.\d+\.\d+)/'
            : '/(\d+\.\d+\.\d+)/';

        if (!preg_match($pattern, $version, $matches)) {
            return null;
        }

        return $matches[1] ?? null;
    }

    /**
     * Build REGEXP/NOT REGEXP predicate SQL.
     *
     * @param   string  $column
     * @param   string  $pattern
     * @param   bool    $not
     * @return  string
     */
    protected function buildRegexpPredicateSql(string $column, string $pattern, bool $not = false): string
    {
        $notStr = $not ? ' NOT' : '';

        return $column . $notStr . ' REGEXP ' . $this->quote($pattern);
    }

    /**
     * Build DATE_SUB(...) SQL.
     *
     * @param   string  $date
     * @param   int     $value
     * @param   string  $unit
     * @return  string
     */
    protected function buildDateSubExpression(string $date, int $value, string $unit = 'DAY'): string
    {
        return 'DATE_SUB(' . $date . ', INTERVAL ' . $value . ' ' . strtoupper($unit) . ')';
    }

    /**
     * Build DATE_ADD(...) SQL.
     *
     * @param   string  $date
     * @param   int     $value
     * @param   string  $unit
     * @return  string
     */
    protected function buildDateAddExpression(string $date, int $value, string $unit = 'DAY'): string
    {
        return 'DATE_ADD(' . $date . ', INTERVAL ' . $value . ' ' . strtoupper($unit) . ')';
    }

    /**
     * Build DATE_FORMAT(...) SQL.
     *
     * @param   string  $date
     * @param   string  $format
     * @return  string
     */
    protected function buildDateFormatExpression(string $date, string $format): string
    {
        return 'DATE_FORMAT(' . $date . ', ' . $this->quote($format) . ')';
    }

    /**
     * Build YEAR(...) SQL.
     *
     * @param   string  $date
     * @return  string
     */
    protected function buildYearExpression(string $date): string
    {
        return 'YEAR(' . $date . ')';
    }

    /**
     * Build MONTH(...) SQL.
     *
     * @param   string  $date
     * @return  string
     */
    protected function buildMonthExpression(string $date): string
    {
        return 'MONTH(' . $date . ')';
    }

    /**
     * Build UNIX_TIMESTAMP(...) SQL.
     *
     * @param   string  $date
     * @return  string
     */
    protected function buildUnixTimestampExpression(string $date): string
    {
        return 'UNIX_TIMESTAMP(' . $date . ')';
    }

    /**
     * Build SUBSTRING_INDEX(...) SQL.
     *
     * @param   string  $str
     * @param   string  $delim
     * @param   int     $count
     * @return  string
     */
    protected function buildSubstringIndexExpression(string $str, string $delim, int $count): string
    {
        return 'SUBSTRING_INDEX(' . $str . ', ' . $this->quote($delim) . ', ' . $count . ')';
    }

    /**
     * Build CONCAT(...) SQL.
     *
     * @param   array  $strings
     * @return  string
     */
    protected function buildConcatExpression(array $strings): string
    {
        return 'CONCAT(' . implode(', ', $strings) . ')';
    }

    /**
     * Build CONCAT_WS(...) SQL.
     *
     * @param   string  $separator
     * @param   array   $strings
     * @return  string
     */
    protected function buildConcatWithSeparatorExpression(string $separator, array $strings): string
    {
        return 'CONCAT_WS(' . $this->quote($separator) . ', ' . implode(', ', $strings) . ')';
    }

    /**
     * Parse table or column character set from SHOW CREATE TABLE output.
     *
     * @param   array        $create
     * @param   string       $table
     * @param   string|null  $field
     * @return  string|bool
     */
    protected function parseCharacterSetFromCreate(array $create, string $table, ?string $field = null)
    {
        if (isset($field)) {
            preg_match(
                '/' . $this->quoteName($field) . ' [[:alnum:]\(\)]* CHARACTER SET ([[:alnum:]]*)/',
                $create[$table] ?? '',
                $matches
            );
        } else {
            preg_match('/CHARSET=([[:alnum:]]*)/', $create[$table] ?? '', $matches);
        }

        return $matches[1] ?? false;
    }

    /**
     * Convert table charset/collation using ALTER TABLE ... CONVERT TO CHARACTER SET.
     *
     * @param   string       $table
     * @param   string       $charset
     * @param   string|null  $collate
     * @return  bool
     */
    protected function convertToCharsetUsingAlter(string $table, string $charset, ?string $collate = null): bool
    {
        $sql = 'ALTER TABLE ' . $this->quoteName($table) . ' CONVERT TO CHARACTER SET ' . $charset;
        if ($collate !== null) {
            $sql .= ' COLLATE ' . $collate;
        }

        $this->setQuery($sql);
        return (bool) $this->execute();
    }

    /**
     * Check table existence using SHOW TABLES LIKE.
     *
     * @param   string  $table
     * @return  bool
     */
    protected function tableExistsFromShowTablesLike(string $table): bool
    {
        $query = 'SHOW TABLES LIKE '
            . str_replace('#__', $this->tablePrefix, $this->quote($table, false));
        $this->setQuery($query)->execute();

        return ($this->getAffectedRows() > 0);
    }

    /**
     * Lock a table for writes.
     *
     * @param   string  $table
     * @return  void
     */
    protected function lockTableForWrite(string $table): void
    {
        $this->setQuery('LOCK TABLES ' . $this->quoteName($table) . ' WRITE')->execute();
    }

    /**
     * Unlock all locked tables.
     *
     * @return  void
     */
    protected function unlockAllTables(): void
    {
        $this->setQuery('UNLOCK TABLES')->execute();
    }

    /**
     * Rename a table using simple RENAME TABLE old TO new syntax.
     *
     * @param   string  $oldTable
     * @param   string  $newTable
     * @return  void
     */
    protected function renameTableSimple(string $oldTable, string $newTable): void
    {
        $this->setQuery('RENAME TABLE ' . $oldTable . ' TO ' . $newTable)->execute();
    }

    /**
     * Fetch server version via SELECT VERSION().
     *
     * @return  string
     */
    protected function getVersionFromSelectVersion(): string
    {
        $this->setQuery('SELECT VERSION()');
        return (string) $this->loadResult();
    }

    /**
     * Fetch database collation from SHOW VARIABLES.
     *
     * @return  string|bool
     */
    protected function getCollationFromShowVariables()
    {
        $this->setQuery('SHOW VARIABLES LIKE "collation_database"');
        $result = $this->loadObject();

        if ($result && property_exists($result, 'Value')) {
            return $result->Value;
        }

        return false;
    }

    /**
     * Fetch table engine via SHOW TABLE STATUS.
     *
     * @param   string  $table
     * @return  string|bool
     */
    protected function getEngineFromShowTableStatus($table)
    {
        $this->setQuery(
            'SHOW TABLE STATUS WHERE Name = '
            . str_replace('#__', $this->tablePrefix, $this->quote($table, false))
        );

        $info = $this->loadObjectList();
        return $info ? $info[0]->Engine : false;
    }

    /**
     * Start nested transaction with savepoint support.
     *
     * @param   string  $startSql
     * @param   string  $savepointPrefix
     * @return  void
     */
    protected function transactionStartWithSavepoints(
        string $startSql = 'START TRANSACTION',
        string $savepointPrefix = 'SP_'
    ): void {
        if ($this->transactionDepth == 0) {
            $this->setQuery($startSql)->execute();
        } else {
            $this->setQuery('SAVEPOINT ' . $savepointPrefix . $this->transactionDepth)->execute();
        }

        $this->transactionDepth++;
    }

    /**
     * Roll back nested transaction with savepoint support.
     *
     * @param   string  $rollbackSql
     * @param   string  $savepointPrefix
     * @return  void
     */
    protected function transactionRollbackWithSavepoints(
        string $rollbackSql = 'ROLLBACK',
        string $savepointPrefix = 'SP_'
    ): void {
        if ($this->transactionDepth <= 0) {
            return;
        }

        $this->transactionDepth--;

        if ($this->transactionDepth == 0) {
            $this->setQuery($rollbackSql)->execute();
        } else {
            $this->setQuery('ROLLBACK TO SAVEPOINT ' . $savepointPrefix . $this->transactionDepth)->execute();
        }
    }

    /**
     * Commit nested transaction with optional savepoint release.
     *
     * @param   bool    $releaseNestedSavepoints
     * @param   string  $commitSql
     * @param   string  $savepointPrefix
     * @return  void
     */
    protected function transactionCommitWithSavepoints(
        bool $releaseNestedSavepoints = true,
        string $commitSql = 'COMMIT',
        string $savepointPrefix = 'SP_'
    ): void {
        if ($this->transactionDepth <= 0) {
            return;
        }

        $this->transactionDepth--;

        if ($this->transactionDepth == 0) {
            $this->setQuery($commitSql)->execute();
            return;
        }

        if ($releaseNestedSavepoints) {
            $this->setQuery('RELEASE SAVEPOINT ' . $savepointPrefix . $this->transactionDepth)->execute();
        }
    }

    /**
     * Returns whether or not the given table has a given key
     *
     * This method provides a universal implementation that works across all drivers
     * by leveraging the abstract getTableKeys() method.
     *
     * @param   string  $table  A table name
     * @param   string  $key    A key name
     * @return  bool
     */
    public function tableHasKey($table, $key)
    {
        $keys = $this->getTableKeys($table);

        if (!is_array($keys)) {
            return false;
        }

        return isset($keys[$key]);
    }

    /**
     * Retrieves field information about the given table
     *
     * @param   string  $table     The name of the database table
     * @param   bool    $typeOnly  True (default) to only return field types
     * @return  array
     */
    abstract public function getTableColumns($table, $typeOnly = true);

    /**
     * Retrieves key information about the given table
     *
     * @param   string  $table  A table name
     * @return  array
     */
    abstract public function getTableKeys($table);

    /**
     * Gets all indexes for a table with their columns and properties
     *
     * Returns an array of index objects, each containing:
     * - name: The index name
     * - columns: Array of column names in the index
     * - unique: Whether the index enforces uniqueness
     * - primary: Whether this is the primary key (optional)
     *
     * @param   string  $table  A table name
     * @return  array
     */
    abstract public function getIndexes($table);

    /**
     * Gets an array of all tables in the database
     *
     * @return  array
     */
    abstract public function getTableList();

    /**
     * Shows the table CREATE statement that creates the given tables
     *
     * @param   string|array  $tables  A table name or a list of table names
     * @return  array
     */
    abstract public function getTableCreate($tables);

    /**
     * Gets the database collation in use
     *
     * @return  string|bool
     */
    abstract public function getCollation();

    /**
     * Gets the primary key of a table
     *
     * @param   string  $table  The table name
     * @return  string|bool
     */
    abstract public function getPrimaryKey($table);

    /**
     * Gets the database engine of the given table
     *
     * @param   string  $table  The table for which to retrieve the engine type
     * @return  string|bool
     */
    abstract public function getEngine($table);

    /**
     * Set the database engine of the given table
     *
     * Default implementation returns false as most databases do not support
     * pluggable storage engines. Drivers that do (like MySQL) should override this.
     *
     * @param   string  $table   The table for which to set the engine type
     * @param   string  $engine  The engine type to set
     * @return  bool
     */
    public function setEngine($table, $engine)
    {
        return false;
    }

    /**
     * Get the major version number of the database server
     *
     * Parses the standardized 'driver_version' from getServerInfo().
     *
     * @return  float|null  Major.minor version (e.g., 8.0) or null if unknown
     */
    public function getMajorVersion()
    {
        $info = $this->getServerInfo();
        $version = $info['driver_version'] ?? null;

        if ($version) {
            // Match major.minor (e.g., 8.0)
            if (preg_match('/^(\d+\.\d+)/', $version, $matches)) {
                return (float) $matches[1];
            }
            // Fallback: Match major only (e.g., 12, 19)
            if (preg_match('/^(\d+)/', $version, $matches)) {
                return (float) $matches[1];
            }
        }

        return null;
    }

    /**
     * Gets the database character set of the given table
     *
     * @param   string  $table  The table for which to retrieve the character set
     * @param   string  $field  The field to check (optional)
     * @return  string|bool
     */
    abstract public function getCharacterSet($table, $field = null);

    /**
     * Gets the auto-increment value for the given table
     *
     * @param   string  $table  The table for which to retrieve the auto-increment value
     * @return  int|bool
     */
    abstract public function getAutoIncrement($table);

    /**
     * Sets the auto-increment starting value for the given table
     *
     * @param   string  $table  The table name
     * @param   int     $value  The auto-increment starting value
     * @return  bool
     */
    abstract public function setAutoIncrement($table, $value);

    /**
     * Locks a table in the database
     *
     * @param   string  $table  The name of the table to lock
     * @return  $this
     */
    abstract public function lockTable($table);

    /**
     * Unlocks all tables in the database
     *
     * @return  $this
     */
    abstract public function unlockTables();

    /**
     * Renames a table in the database
     *
     * @param   string  $oldTable  The name of the table to be renamed
     * @param   string  $newTable  The new name for the table
     * @param   string  $backup    Table prefix
     * @param   string  $prefix    For the table - used to rename constraints in non-mysql databases
     * @return  $this
     */
    abstract public function renameTable($oldTable, $newTable, $backup = null, $prefix = null);

    /**
     * Commits a transaction
     *
     * @return  void
     */
    abstract public function transactionCommit();

    /**
     * Rolls back a transaction
     *
     * @return  void
     */
    abstract public function transactionRollback();

    /**
     * Initializes a transaction
     *
     * @return  void
     */
    abstract public function transactionStart();

    /**
     * Gets the version of the database connector
     *
     * Default implementation uses SELECT VERSION()-style retrieval.
     * Drivers with non-standard version queries can override.
     *
     * @return  string
     */
    public function getVersion()
    {
        return $this->getVersionFromSelectVersion();
    }

    /**
     * Selects a database for use
     *
     * @param   string  $database  The name of the database to select for use
     * @return  bool
     */
    abstract public function select($database);

    /**
     * Gets a new query for the current driver
     *
     * @return  \Hubzero\Database\Query
     */
    public function getQuery()
    {
        return new \Hubzero\Database\Query($this);
    }

    // =========================================================================
    // SQL Helper Methods - Dialect-specific SQL generation
    // =========================================================================
    //
    // These abstract methods must be implemented by each dialect driver to
    // provide database-specific SQL fragments for common operations.
    //
    // Each database has its own syntax for these operations:
    // - MySQL/MariaDB/Percona: MySQL-native functions
    // - SQLite: SQLite-specific functions
    // - PostgreSQL: PostgreSQL-specific syntax

    /**
     * Returns the SQL function for current timestamp
     *
     * Override $nowExpression in subclasses for dialect differences.
     *
     * @return  string
     */
    public function sqlNow(): string
    {
        return $this->nowExpression;
    }

    /**
     * Returns the SQL for null coalescing (return first non-null value)
     *
     * Override $ifNullFunction in subclasses for dialect differences.
     *
     * @param   string  $expr     The expression to check for null
     * @param   string  $default  The default value if expr is null
     * @return  string
     */
    public function sqlIfNull(string $expr, string $default): string
    {
        return $this->ifNullFunction . '(' . $expr . ', ' . $default . ')';
    }

    /**
     * Returns the SQL keyword for INSERT with ignore duplicates
     *
     * - MySQL/MariaDB/Percona: INSERT IGNORE INTO
     * - SQLite: INSERT OR IGNORE INTO
     * - PostgreSQL: INSERT INTO (requires suffix for conflict handling)
     *
     * For cross-database compatibility, use with sqlInsertIgnoreSuffix():
     *   $sql = $db->sqlInsertIgnore() . ' table (col) VALUES (?)' . $db->sqlInsertIgnoreSuffix();
     *
     * @return  string
     * @see     sqlInsertIgnoreSuffix()
     */
    abstract public function sqlInsertIgnore(): string;

    /**
     * Returns the SQL keyword for REPLACE (upsert)
     *
     * - MySQL/MariaDB/Percona: REPLACE INTO
     * - SQLite: INSERT OR REPLACE INTO
     * - PostgreSQL: INSERT INTO (requires suffix for conflict handling)
     *
     * For cross-database compatibility, use with sqlReplaceSuffix():
     *   $sql = $db->sqlReplace() . ' table (id, name) VALUES (?, ?)'
     *        . $db->sqlReplaceSuffix('id', ['name']);
     *
     * @return  string
     * @see     sqlReplaceSuffix()
     */
    abstract public function sqlReplace(): string;

    /**
     * Returns the suffix for INSERT IGNORE statements
     *
     * MySQL/SQLite: Returns empty string (no suffix needed)
     * PostgreSQL: Returns " ON CONFLICT DO NOTHING"
     *
     * @return  string
     */
    public function sqlInsertIgnoreSuffix(): string
    {
        // Default: no suffix needed (MySQL/SQLite)
        // PostgreSQL overrides to add ON CONFLICT DO NOTHING
        return '';
    }

    /**
     * Returns the suffix for REPLACE (upsert) statements
     *
     * MySQL/SQLite: Returns empty string (no suffix needed)
     * PostgreSQL: Returns " ON CONFLICT (pk) DO UPDATE SET col = EXCLUDED.col, ..."
     *
     * @param   string  $pkColumn       The primary key column name for conflict detection
     * @param   array   $updateColumns  Array of column names to update on conflict
     * @return  string
     */
    public function sqlReplaceSuffix(string $pkColumn, array $updateColumns): string
    {
        // Default: no suffix needed (MySQL/SQLite)
        // PostgreSQL overrides to add ON CONFLICT ... DO UPDATE
        return '';
    }

    /**
     * Returns whether the database supports REGEXP operator
     *
     * All supported databases have some form of regex support.
     *
     * @return  bool
     */
    public function supportsRegexp(): bool
    {
        return true;
    }

    /**
     * Returns the SQL for a REGEXP comparison
     *
     * - MySQL: column REGEXP pattern
     * - SQLite: column REGEXP pattern (via UDF)
     * - PostgreSQL: column ~ pattern
     *
     * @param   string  $column   The column to match
     * @param   string  $pattern  The regex pattern
     * @param   bool    $not      Whether to negate the match
     * @return  string
     */
    abstract public function sqlRegexp(string $column, string $pattern, bool $not = false): string;

    /**
     * Returns the SQL for date subtraction
     *
     * - MySQL: DATE_SUB(date, INTERVAL n unit)
     * - SQLite: date(date, '-n unit')
     * - PostgreSQL: (date - INTERVAL 'n units')
     *
     * @param   string  $date   The date column or value
     * @param   int     $value  The interval value
     * @param   string  $unit   The interval unit (DAY, MONTH, YEAR, HOUR, MINUTE, SECOND)
     * @return  string
     */
    abstract public function sqlDateSub(string $date, int $value, string $unit = 'DAY'): string;

    /**
     * Returns the SQL for date addition
     *
     * - MySQL: DATE_ADD(date, INTERVAL n unit)
     * - SQLite: date(date, '+n unit')
     * - PostgreSQL: (date + INTERVAL 'n units')
     *
     * @param   string  $date   The date column or value
     * @param   int     $value  The interval value
     * @param   string  $unit   The interval unit (DAY, MONTH, YEAR, HOUR, MINUTE, SECOND)
     * @return  string
     */
    abstract public function sqlDateAdd(string $date, int $value, string $unit = 'DAY'): string;

    /**
     * Returns the SQL for date formatting
     *
     * - MySQL: DATE_FORMAT(date, format)
     * - SQLite: strftime(format, date)
     * - PostgreSQL: TO_CHAR(date, format)
     *
     * Format codes use MySQL convention (%Y, %m, %d, etc.)
     *
     * @param   string  $date    The date column or value
     * @param   string  $format  The format string (MySQL format: %Y-%m-%d, etc.)
     * @return  string
     */
    abstract public function sqlDateFormat(string $date, string $format): string;

    /**
     * Returns the SQL for extracting year from a date
     *
     * - MySQL: YEAR(date)
     * - SQLite: strftime('%Y', date)
     * - PostgreSQL: EXTRACT(YEAR FROM date)
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    abstract public function sqlYear(string $date): string;

    /**
     * Returns the SQL for extracting month from a date
     *
     * - MySQL: MONTH(date)
     * - SQLite: strftime('%m', date)
     * - PostgreSQL: EXTRACT(MONTH FROM date)
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    abstract public function sqlMonth(string $date): string;

    /**
     * Returns the SQL for converting a date to Unix timestamp
     *
     * - MySQL: UNIX_TIMESTAMP(date)
     * - SQLite: strftime('%s', date)
     * - PostgreSQL: EXTRACT(EPOCH FROM date)::INTEGER
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    abstract public function sqlUnixTimestamp(string $date): string;

    /**
     * Returns the SQL for random ordering
     *
     * Override $randExpression in subclasses for dialect differences.
     *
     * @return  string
     */
    public function sqlRand(): string
    {
        return $this->randExpression;
    }

    /**
     * Returns the SQL for extracting a substring based on a delimiter
     *
     * - MySQL: SUBSTRING_INDEX(str, delim, count)
     * - SQLite: Custom expression using SUBSTR and INSTR
     * - PostgreSQL: Custom expression using split_part
     *
     * Count semantics:
     * - count > 0: returns everything before the nth occurrence of the delimiter
     * - count < 0: returns everything after the (n)th occurrence from the right
     *
     * @param   string  $str    The string expression (column or literal)
     * @param   string  $delim  The delimiter to search for
     * @param   int     $count  The occurrence count (positive = from left, negative = from right)
     * @return  string
     */
    abstract public function sqlSubstringIndex(string $str, string $delim, int $count): string;

    /**
     * Returns the SQL for concatenating strings
     *
     * - MySQL: CONCAT(str1, str2, ...)
     * - SQLite: str1 || str2 || ...
     * - PostgreSQL: CONCAT(str1, str2, ...)
     *
     * @param   array  $strings  Array of column names or quoted strings to concatenate
     * @return  string
     */
    abstract public function sqlConcat(array $strings): string;

    /**
     * Returns the SQL for concatenating strings with a separator
     *
     * - MySQL: CONCAT_WS(separator, str1, str2, ...)
     * - SQLite: str1 || separator || str2 || ...
     * - PostgreSQL: CONCAT_WS(separator, str1, str2, ...)
     *
     * @param   string  $separator  The separator string
     * @param   array   $strings    Array of column names or quoted strings to concatenate
     * @return  string
     */
    abstract public function sqlConcatWs(string $separator, array $strings): string;

    /**
     * Returns the SQL for getting string length in characters
     *
     * Override $lengthFunction in subclasses for dialect differences.
     *
     * @param   string  $str  The string expression (column or literal)
     * @return  string
     */
    public function sqlLength(string $str): string
    {
        return $this->lengthFunction . '(' . $str . ')';
    }

    // =========================================================================
    // Feature Detection Methods
    // =========================================================================
    //
    // These methods allow callers to detect database-specific feature support
    // before attempting operations that may silently fail or behave differently.

    /**
     * Check if this database supports column positioning (AFTER/BEFORE/FIRST)
     *
     * MySQL supports column positioning in ALTER TABLE ADD/MODIFY statements.
     * PostgreSQL does not support column reordering without table recreation.
     * SQLite supports it via table recreation (transparent to callers).
     *
     * @return  bool  True if column positioning is supported
     */
    public function supportsColumnPositioning(): bool
    {
        // Default: not supported. MySQL overrides to return true.
        // SQLite also returns true (handles via table recreation).
        return false;
    }

    /**
     * Check if this database supports storage engines
     *
     * MySQL/MariaDB/Percona support multiple storage engines (InnoDB, MyISAM, etc.)
     * PostgreSQL and SQLite do not have the concept of storage engines.
     *
     * @return  bool  True if storage engines are supported
     */
    public function supportsEngine(): bool
    {
        // Default: not supported. MySQL-family drivers override to return true.
        return false;
    }

    /**
     * Check if this database supports ENUM column types
     *
     * MySQL supports native ENUM types.
     * PostgreSQL requires CREATE TYPE for enums (not directly compatible).
     * SQLite treats ENUM as TEXT.
     *
     * @return  bool  True if native ENUM is supported
     */
    public function supportsEnum(): bool
    {
        // Default: not supported. MySQL-family drivers override to return true.
        return false;
    }

    /**
     * Check if this database supports full-text search indexes
     *
     * MySQL supports FULLTEXT indexes on InnoDB (5.6+) and MyISAM.
     * PostgreSQL uses GIN indexes with tsvector.
     * SQLite has FTS5 extension (virtual tables, different API).
     *
     * @return  bool  True if full-text indexing is supported
     */
    public function supportsFulltext(): bool
    {
        // All supported databases have some form of full-text search
        return true;
    }

    /**
     * Check if this database supports table-level character sets
     *
     * MySQL supports per-table and per-column character set and collation.
     * PostgreSQL sets encoding at database level only.
     * SQLite uses UTF-8 internally.
     *
     * @return  bool  True if per-table charset is supported
     */
    public function supportsTableCharset(): bool
    {
        // Default: not supported. MySQL-family drivers override to return true.
        return false;
    }

    /**
     * Check if this database supports UNSIGNED integer types
     *
     * MySQL supports UNSIGNED on integer types.
     * PostgreSQL, SQLite, and SQL Server do not.
     *
     * @return  bool  True if UNSIGNED is supported
     */
    public function supportsUnsigned(): bool
    {
        // Default: not supported. MySQL-family drivers override to return true.
        return false;
    }

    /**
     * Check if this database supports column comments in DDL
     *
     * MySQL supports COMMENT 'text' on column definitions.
     * PostgreSQL uses separate COMMENT ON COLUMN statement.
     * SQLite does not support column comments.
     *
     * @return  bool  True if inline column comments are supported
     */
    public function supportsColumnComments(): bool
    {
        // Default: not supported. MySQL-family drivers override to return true.
        return false;
    }

    /**
     * Check if this database supports prefix lengths on indexes
     *
     * MySQL supports INDEX idx_name (column(length)) for partial indexes.
     * PostgreSQL and SQLite do not support this syntax.
     *
     * @return  bool  True if index prefix lengths are supported
     */
    public function supportsIndexPrefixLength(): bool
    {
        // Default: not supported. MySQL-family drivers override to return true.
        return false;
    }

    // =========================================================================
    // Schema Building Methods - For CREATE TABLE and ALTER TABLE
    // =========================================================================

    /**
     * Map a MySQL-style column type to this database's native type
     *
     * Override in subclasses to handle type mapping.
     *
     * @param   string  $type  The MySQL column type (e.g., 'INT', 'VARCHAR(255)')
     * @return  string  The native column type for this database
     */
    public function mapColumnType(string $type): string
    {
        // Default: return type unchanged (MySQL-compatible)
        return $type;
    }

    /**
     * Build an auto-increment primary key column definition
     *
     * Different databases use different syntax:
     * - MySQL: `id` INT AUTO_INCREMENT PRIMARY KEY
     * - SQLite: `id` INTEGER PRIMARY KEY AUTOINCREMENT
     * - PostgreSQL: `id` SERIAL PRIMARY KEY
     * - SQL Server: `id` INT IDENTITY(1,1) PRIMARY KEY
     *
     * @param   string  $quotedName  The quoted column name
     * @param   string  $type        The column type (may be ignored for some DBs)
     * @return  string  The column definition SQL
     */
    public function buildAutoIncrementColumn(string $quotedName, string $type): string
    {
        // Default: MySQL syntax
        return "$quotedName $type AUTO_INCREMENT";
    }

    /**
     * Build a regular INDEX definition for CREATE TABLE
     *
     * Different databases use different syntax:
     * - MySQL: KEY `name` (`col1`, `col2`)
     * - PostgreSQL: handled via CREATE INDEX after table
     * - SQLite: handled via CREATE INDEX after table
     *
     * @param   string  $quotedName     The quoted index name
     * @param   string  $columnList     The column list SQL (already quoted and joined)
     * @return  string|null  The index definition SQL, or null if handled separately
     */
    public function buildIndexDefinition(string $quotedName, string $columnList): ?string
    {
        // Default: MySQL syntax (inline in CREATE TABLE)
        return "KEY $quotedName ($columnList)";
    }

    /**
     * Build a FULLTEXT index definition for CREATE TABLE
     *
     * @param   string  $quotedName     The quoted index name
     * @param   string  $columnList     The column list SQL
     * @return  string|null  The index definition SQL, or null if not supported inline
     */
    public function buildFulltextIndexDefinition(string $quotedName, string $columnList): ?string
    {
        // Default: MySQL syntax
        return "FULLTEXT KEY $quotedName ($columnList)";
    }

    /**
     * Build a CREATE INDEX statement (for databases that don't support inline indexes)
     *
     * @param   string  $indexName  The index name
     * @param   string  $tableName  The table name
     * @param   array   $columns    The columns (may include prefix lengths for MySQL)
     * @param   bool    $unique     Whether this is a unique index
     * @return  string  The CREATE INDEX SQL
     */
    public function buildCreateIndexStatement(
        string $indexName,
        string $tableName,
        array $columns,
        bool $unique = false
    ): string {
        $uniqueKeyword = $unique ? 'UNIQUE ' : '';
        $quotedIndex = $this->quoteName($indexName);
        $quotedTable = $this->quoteName($tableName);
        $columnList = implode(', ', array_map([$this, 'quoteName'], $columns));

        return "CREATE {$uniqueKeyword}INDEX $quotedIndex ON $quotedTable ($columnList)";
    }

    /**
     * Build the index column syntax (handles prefix lengths for MySQL)
     *
     * @param   string    $quotedColumn  The quoted column name
     * @param   int|null  $prefixLength  The prefix length (MySQL only)
     * @return  string  The column syntax for the index
     */
    public function buildIndexColumn(string $quotedColumn, ?int $prefixLength = null): string
    {
        if ($prefixLength !== null && $this->supportsIndexPrefixLength()) {
            return "$quotedColumn($prefixLength)";
        }
        return $quotedColumn;
    }

    /**
     * Quote a single identifier (table or column name) with proper escaping
     *
     * Different databases use different quoting characters:
     * - MySQL: backticks `name`
     * - PostgreSQL/SQLite: double quotes "name"
     * - SQL Server: square brackets [name]
     *
     * Note: This differs from quoteName() which handles dot notation and AS clauses.
     *       Use this method for simple identifier quoting in schema building.
     *
     * @param   string  $identifier  The identifier to quote
     * @return  string  The quoted identifier
     */
    public function quoteIdentifier(string $identifier): string
    {
        // Default: use double quotes (SQL standard)
        return '"' . str_replace('"', '""', $identifier) . '"';
    }

    /**
     * Check if auto-increment definition includes PRIMARY KEY
     *
     * Some databases (SQLite, PostgreSQL, SQL Server) require the auto-increment
     * column to be the primary key in the same definition. MySQL allows
     * AUTO_INCREMENT separately from PRIMARY KEY.
     *
     * @return  bool  True if auto-increment implies PRIMARY KEY
     */
    public function autoIncrementIncludesPrimaryKey(): bool
    {
        // Default: auto-increment definition includes PRIMARY KEY
        // MySQL overrides to return false (can have separate PRIMARY KEY)
        return true;
    }

    /**
     * Check if CREATE INDEX supports IF NOT EXISTS clause
     *
     * @return  bool  True if IF NOT EXISTS is supported
     */
    public function supportsIfNotExistsForIndex(): bool
    {
        // Default: supported (SQLite, PostgreSQL support it)
        // SQL Server overrides to return false
        return true;
    }

    /**

    /**
     * Build a fulltext index CREATE statement for databases that create them separately
     *
     * PostgreSQL uses GIN indexes with tsvector.
     * SQL Server requires fulltext catalogs.
     * MySQL creates fulltext indexes inline.
     *
     * @param   string  $indexName  The index name
     * @param   string  $tableName  The table name
     * @param   array   $columns    The column names to index
     * @return  string|null  The CREATE INDEX SQL, or null if not applicable
     */
    public function buildCreateFulltextIndexSql(string $indexName, string $tableName, array $columns): ?string
    {
        // Default: return null (MySQL handles fulltext inline)
        // PostgreSQL and SQL Server override with their specific syntax
        return null;
    }

    /**
     * Format a boolean value as a SQL literal
     *
     * Default uses SQL standard TRUE/FALSE literals. Databases without
     * native boolean types (MySQL, SQLite, Oracle) override to use 1/0.
     *
     * @param   bool  $value  The boolean value
     * @return  string  The SQL literal
     */
    public function formatBooleanLiteral(bool $value): string
    {
        return $value ? 'TRUE' : 'FALSE';
    }

    /**
     * Check if a value is a zero-date sentinel
     *
     * Zero-dates like '0000-00-00 00:00:00' are a legacy MySQL convention
     * for representing "no date." They are invalid on all modern databases
     * including MySQL 5.7+ with strict mode. The schema builder translates
     * these to DEFAULT NULL at DDL compilation time.
     *
     * @param   mixed  $value  The value to check
     * @return  bool
     */
    public static function isZeroDate($value): bool
    {
        return is_string($value) && in_array($value, [
            '0000-00-00 00:00:00',
            '0000-00-00',
            '0000-00-00T00:00:00',
        ], true);
    }

    /**
     * Get the abstract type to SQL type mapping for this driver
     *
     * Returns the $typeMap property, which each driver overrides
     * with its database-specific type mappings.
     *
     * @return  array  Mapping of abstract type => SQL type
     */
    public function getAbstractTypeMap(): array
    {
        return $this->typeMap;
    }

    /**
     * Normalize an abstract column type to a database-specific type
     *
     * Maps camelCase abstract type names (e.g., 'string', 'integer') to
     * concrete SQL type definitions, applying length/precision modifiers
     * as appropriate for the database.
     *
     * Override in drivers that need special fallthrough logic for
     * concrete MySQL-style types (e.g., 'BIGINT(20) UNSIGNED').
     *
     * @param   string  $type       The abstract or concrete type
     * @param   array   $modifiers  Column modifiers (length, precision, scale)
     * @return  string  The database-specific type definition
     */
    public function normalizeColumnType(
        string $type,
        array $modifiers = []
    ): string {
        $grammar = $this->getSchemaGrammar();
        $mappedType = $grammar->getTypeMapping($type);

        if ($mappedType !== null) {
            return $this->applyColumnModifiers(
                $type,
                $mappedType,
                $modifiers
            );
        }

        // Not an abstract type — return as-is
        return $type;
    }

    /**
     * Apply length/precision modifiers to a mapped column type
     *
     * Default handles the common cases:
     * - string/char: append (length), default 255
     * - float/double/decimal: append (precision,scale) if specified
     *
     * Override in drivers with different modifier behavior (e.g., SQLite
     * ignores all modifiers, Oracle skips precision for float/double).
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

            case 'float':
            case 'double':
            case 'decimal':
                if (
                    isset($modifiers['precision'])
                    || isset($modifiers['scale'])
                ) {
                    $precision = $modifiers['precision'] ?? 10;
                    $scale = $modifiers['scale'] ?? 2;
                    return "{$nativeType}({$precision},{$scale})";
                }
                return $nativeType;

            default:
                return $nativeType;
        }
    }

    /**
     * Check if this driver requires length parameters for string types
     *
     * SQLite doesn't need length for VARCHAR/CHAR since it treats all text as TEXT.
     *
     * @return  bool  True if length parameters are required
     */
    public function requiresStringLength(): bool
    {
        // Default: yes, most databases require length
        return true;
    }

    /**
     * Check if this driver supports decimal/numeric precision parameters
     *
     * PostgreSQL's REAL and DOUBLE PRECISION don't take parameters.
     *
     * @return  bool  True if precision parameters are supported for float/double
     */
    public function supportsFloatPrecision(): bool
    {
        // Default: yes, MySQL and SQL Server support precision
        return true;
    }

    /**
     * Check if this database supports invisible/hidden columns
     *
     * MySQL 8.0.23+ and MariaDB 10.3+ support invisible columns.
     * Most other databases do not.
     *
     * @return  bool  True if invisible columns are supported
     */
    public function supportsInvisibleColumns(): bool
    {
        return false;
    }

    /**
     * Check if this database supports JSON column types and operations
     *
     * @return  bool  True if JSON operations are supported
     */
    public function supportsJson(): bool
    {
        return false;
    }

    /**
     * Check if this database supports window functions (OVER, PARTITION BY)
     *
     * @return  bool  True if window functions are supported
     */
    public function supportsWindowFunctions(): bool
    {
        return false;
    }

    /**
     * Check if this database supports Common Table Expressions (WITH ... AS)
     *
     * @return  bool  True if CTEs are supported
     */
    public function supportsCTE(): bool
    {
        return false;
    }

    /**
     * Check if this database supports generated/computed columns
     *
     * @return  bool  True if generated columns are supported
     */
    public function supportsGeneratedColumns(): bool
    {
        return false;
    }

    /**
     * Check if this database supports table partitioning
     *
     * @return  bool  True if partitioning is supported
     */
    public function supportsPartitioning(): bool
    {
        return false;
    }

    /**
     * Build the table options string for CREATE TABLE
     *
     * MySQL: ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
     * Others: typically empty
     *
     * @param   string|null  $engine     The storage engine
     * @param   string|null  $charset    The character set
     * @param   string|null  $collation  The collation
     * @return  string  The table options SQL (may be empty)
     */
    public function buildTableOptions(
        ?string $engine = null,
        ?string $charset = null,
        ?string $collation = null
    ): string {
        // Default: no table options (most databases don't support them)
        return '';
    }

    /**
     * Build a foreign key definition for CREATE/ALTER TABLE statements.
     *
     * Drivers can override this when constraint syntax differs from the default.
     *
     * @param   array   $fk         Foreign key metadata
     * @param   string  $tableName  Table name (already prefix-replaced)
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
        $constraint = "CONSTRAINT $constraintName ";
        $constraint .= "FOREIGN KEY ($fkColumn) ";
        $constraint .= "REFERENCES $refTableQuoted ($refColumn) ";
        $constraint .= "ON DELETE {$fk['onDelete']} ON UPDATE {$fk['onUpdate']}";

        return $constraint;
    }

    /**
     * Build post-create auto-increment statements for engines that require them.
     *
     * Most drivers don't require extra statements beyond the column definition.
     * Firebird overrides this.
     *
     * @param   string  $table   Table name (already prefix-replaced)
     * @param   string  $column  Column name
     * @return  array
     */
    public function buildAutoIncrementStatements($table, $column = 'id'): array
    {
        return [];
    }

    // =========================================================================
    // Universal SQL Methods - Work identically on all databases
    // =========================================================================

    /**
     * Drops a table from the database
     *
     * DROP TABLE IF EXISTS syntax is supported by MySQL, PostgreSQL, and SQLite.
     *
     * @param   string   $tableName  The name of the database table to drop
     * @param   boolean  $ifExists   Optionally specify that the table must exist before it is dropped
     * @return  $this
     */
    public function dropTable($tableName, $ifExists = true)
    {
        $tableName = $this->replacePrefix($tableName);
        $grammar = $this->getSchemaGrammar();
        $this->setQuery($grammar->compileDrop($tableName, $ifExists))
             ->execute();

        return $this;
    }

    /**
     * Truncates a table (removes all rows and resets auto-increment)
     *
     * Emulates MySQL TRUNCATE semantics across all drivers:
     * 1. Deletes all rows from the table
     * 2. Resets auto-increment/identity counter to $nextId
     * 3. Always commits (MySQL TRUNCATE is DDL and auto-commits)
     * 4. Never cascades to other tables
     *
     * @param   string  $table   The table to truncate (with or without prefix)
     * @param   int     $nextId  The next auto-increment value (default 1)
     * @return  $this
     */
    public function truncateTable($table, int $nextId = 1)
    {
        $resolved = $this->replacePrefix($table);
        $this->setQuery('DELETE FROM ' . $this->quoteName($resolved));
        $this->execute();
        $this->setAutoIncrement($table, $nextId);

        // Always commit — MySQL TRUNCATE is DDL and auto-commits,
        // so all drivers should do the same for consistency
        $connection = $this->getConnection();
        if ($connection && $connection->inTransaction()) {
            $connection->commit();
        }

        return $this;
    }

    /**
     * Check if the database connection is still active
     *
     * Performs a lightweight query to verify the connection is responsive.
     *
     * @return  bool  True if connected and responsive, false otherwise
     */
    public function connected()
    {
        return $this->connection && $this->connection->isConnected();
    }

    /**
     * Convert a table's character set and collation
     *
     * This converts all text columns in the table to the new character set.
     * On databases that don't support per-table charsets (SQLite, PostgreSQL),
     * this is a no-op that returns true.
     *
     * @param   string       $table    The table to convert (with or without prefix)
     * @param   string       $charset  The character set (e.g., 'utf8', 'utf8mb4')
     * @param   string|null  $collate  Optional collation (e.g., 'utf8mb4_unicode_ci')
     * @return  bool
     */
    abstract public function convertToCharset($table, $charset, $collate = null);

    // =========================================================================
    // DDL Helper Abstract Methods - Dialect-specific DDL operations
    // =========================================================================

    /**
     * Modify a column definition
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  New column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    abstract public function modifyColumn(
        string $table,
        string $column,
        string $definition,
        string $comment = ''
    ): bool;

    /**
     * Modify a column definition and move it after a specific column
     *
     * @param   string  $table        Table name (with or without prefix)
     * @param   string  $column       Column name
     * @param   string  $definition   New column definition
     * @param   string  $afterColumn  Column to position after
     * @param   string  $comment      Optional column comment
     * @return  bool
     */
    abstract public function modifyColumnAfter(
        string $table,
        string $column,
        string $definition,
        string $afterColumn,
        string $comment = ''
    ): bool;

    /**
     * Modify a column definition and move it before a specific column
     *
     * @param   string  $table         Table name (with or without prefix)
     * @param   string  $column        Column name
     * @param   string  $definition    New column definition
     * @param   string  $beforeColumn  Column to position before
     * @param   string  $comment       Optional column comment
     * @return  bool
     */
    abstract public function modifyColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool;

    /**
     * Modify a column definition and move it to the first position
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  New column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    abstract public function modifyColumnFirst(
        string $table,
        string $column,
        string $definition,
        string $comment = ''
    ): bool;

    /**
     * Change a column name and/or definition
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $oldColumn   Current column name
     * @param   string  $newColumn   New column name
     * @param   string  $definition  New column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    abstract public function changeColumn(
        string $table,
        string $oldColumn,
        string $newColumn,
        string $definition,
        string $comment = ''
    ): bool;

    /**
     * Add a column to a table
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    abstract public function addColumn(string $table, string $column, string $definition, string $comment = ''): bool;

    /**
     * Add a column after a specific column
     *
     * @param   string  $table        Table name (with or without prefix)
     * @param   string  $column       Column name
     * @param   string  $definition   Column definition
     * @param   string  $afterColumn  Column to add after
     * @param   string  $comment      Optional column comment
     * @return  bool
     */
    abstract public function addColumnAfter(
        string $table,
        string $column,
        string $definition,
        string $afterColumn,
        string $comment = ''
    ): bool;

    /**
     * Add a column before a specific column
     *
     * @param   string  $table         Table name (with or without prefix)
     * @param   string  $column        Column name
     * @param   string  $definition    Column definition
     * @param   string  $beforeColumn  Column to add before
     * @param   string  $comment       Optional column comment
     * @return  bool
     */
    abstract public function addColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool;

    /**
     * Add a column at the beginning of a table
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    abstract public function addColumnFirst(
        string $table,
        string $column,
        string $definition,
        string $comment = ''
    ): bool;

    /**
     * Add a column at the end of a table
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional column comment
     * @return  bool
     */
    abstract public function addColumnLast(
        string $table,
        string $column,
        string $definition,
        string $comment = ''
    ): bool;

    /**
     * Set the storage engine for a table
     *
     * @param   string  $table   Table name (with or without prefix)
     * @param   string  $engine  Engine type (e.g., 'MYISAM', 'InnoDB')
     * @return  bool
     */
    abstract public function setTableEngine(string $table, string $engine = 'MYISAM'): bool;

    /**
     * Set the character set and collation for a table
     *
     * @param   string  $table      Table name (with or without prefix)
     * @param   string  $charset    Character set (e.g., 'utf8')
     * @param   string  $collation  Collation (e.g., 'utf8_general_ci')
     * @return  bool
     */
    abstract public function setTableCharset(
        string $table,
        string $charset = 'utf8',
        string $collation = 'utf8_general_ci'
    ): bool;

    /**
     * Drop a column from a table
     *
     * @param   string  $table   Table name (with or without prefix)
     * @param   string  $column  Column name
     * @return  bool
     */
    abstract public function dropColumn(string $table, string $column): bool;

    /**
     * Add a FULLTEXT index to a table
     *
     * @param   string        $table    Table name (with or without prefix)
     * @param   string        $name     Index name
     * @param   string|array  $columns  Column name(s) to index
     * @return  bool
     */
    abstract public function addFulltextIndex(string $table, string $name, $columns): bool;

    /**
     * Drop the primary key from a table
     *
     * @param   string  $table  Table name (with or without prefix)
     * @return  bool
     */
    abstract public function dropPrimaryKey(string $table): bool;

    /**
     * Add a primary key to a table
     *
     * @param   string        $table    Table name (with or without prefix)
     * @param   string|array  $columns  Column name(s) for the primary key
     * @return  bool
     */
    abstract public function addPrimaryKey(string $table, $columns): bool;

    /**
     * Add an auto-increment primary key column to a table
     *
     * @param   string  $table      Table name (with or without prefix)
     * @param   string  $column     Column name (usually 'id')
     * @param   bool    $first      Add as first column
     * @param   bool    $useBigInt  Use BIGINT/SERIAL (true) or INT (false)
     * @return  bool
     */
    abstract public function addAutoIncrementPrimaryKey(
        string $table,
        string $column = 'id',
        bool $first = false,
        bool $useBigInt = true
    ): bool;

    /**
     * Populate a column with sequential integer values for existing rows
     *
     * This is a cross-database portable method for assigning unique sequential
     * values to a column. Useful when adding a primary key column to a table
     * that already has data.
     *
     * @param   string       $table    Table name (with or without prefix)
     * @param   string       $column   Column name to populate
     * @param   string|null  $orderBy  Optional column to order by when assigning sequence
     * @return  bool
     */
    abstract public function populateSequentialValues(string $table, string $column, ?string $orderBy = null): bool;

    /**
     * Add an index to a table
     *
     * @param   string        $table    Table name (with or without prefix)
     * @param   string        $name     Index name
     * @param   string|array  $columns  Column name(s) to index
     * @param   bool          $unique   Whether to create a unique index
     * @return  bool
     */
    abstract public function addIndex(string $table, string $name, $columns, bool $unique = false): bool;

    /**
     * Add a unique index to a table
     *
     * @param   string        $table    Table name (with or without prefix)
     * @param   string        $name     Index name
     * @param   string|array  $columns  Column name(s) to index
     * @return  bool
     */
    abstract public function addUniqueIndex(string $table, string $name, $columns): bool;

    /**
     * Drop an index from a table
     *
     * @param   string  $table  Table name (with or without prefix)
     * @param   string  $name   Index name
     * @return  bool
     */
    abstract public function dropIndex(string $table, string $name): bool;

    /**
     * Create a fluent table builder for CREATE TABLE statements
     *
     * @param   string  $table  Table name (can include prefix placeholder #__)
     * @return  \Hubzero\Database\Schema\TableBuilder
     */
    public function createTable(string $table): \Hubzero\Database\Schema\TableBuilder
    {
        return new \Hubzero\Database\Schema\TableBuilder($this, $table);
    }

    /**
     * Create a fluent table builder for ALTER TABLE statements
     *
     * @param   string  $table  Table name (can include prefix placeholder #__)
     * @return  \Hubzero\Database\Schema\AlterTableBuilder
     */
    public function alterTable(string $table): \Hubzero\Database\Schema\AlterTableBuilder
    {
        return new \Hubzero\Database\Schema\AlterTableBuilder($this, $table);
    }

    /**
     * Compile ALTER TABLE operations into SQL statements
     *
     * Delegates to the Schema Grammar for DDL generation.
     * Kept for backward compatibility — prefer using Grammar directly.
     *
     * @param   \Hubzero\Database\Schema\AlterTableBuilder  $builder  The table builder
     * @return  array  Array of SQL statements to execute
     */
    public function compileAlterTable(
        \Hubzero\Database\Schema\AlterTableBuilder $builder
    ): array {
        return $this->getSchemaGrammar()->compileAlterTable($builder);
    }

    // =========================================================================
    // Universal Utility Methods - Shared Across All Drivers
    // =========================================================================

    /**
     * Drop a table from the database (convenience alias)
     *
     * This is a convenience method that calls dropTable() with ifExists=true.
     * Provides a shorter syntax for common drop operations.
     *
     * @param   string  $table  The table to drop
     * @return  $this
     */
    public function drop($table)
    {
        return $this->dropTable($table, true);
    }

    /**
     * Returns a list of all database names on the server (alias)
     *
     * This is an alias for getDatabaseList() to provide API consistency
     * with other database abstraction layers.
     *
     * @return  array  Array of database names
     */
    public function getDatabaseNames()
    {
        return $this->getDatabaseList();
    }

    /**
     * Get the full database version string
     *
     * This method provides a universal way to get the full version string
     * by leveraging the getServerInfo() method that drivers implement.
     *
     * @return  string|null  Full version string or null if unavailable
     */
    public function getFullVersion()
    {
        $info = $this->getServerInfo();
        return $info['version'] ?? null;
    }

    /**
     * Set the connection to use UTF-8 character encoding
     *
     * Default implementation is a no-op. Drivers that need specific
     * UTF-8 setup should override this method.
     *
     * @return  bool  True on success
     */
    public function setUTF()
    {
        // Default: no-op, character sets are typically set at connection time
        return true;
    }

    /**
     * Apply connection character set via SET NAMES.
     *
     * @param   string  $charset
     * @return  bool
     */
    protected function setNamesCharset(string $charset): bool
    {
        try {
            $this->setQuery("SET NAMES '{$charset}'");
            $this->execute();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    // =========================================================================
    // Universal Utility and Monitoring Methods
    // =========================================================================

    /**
     * Get connection information
     *
     * @return  array
     */
    public function getConnectionInfo()
    {
        return [
            'driver' => $this->name ?? 'unknown',
            'version' => $this->getVersion(),
            'database' => $this->getDatabase(),
        ];
    }

    /**
     * Get connection statistics
     *
     * @return  array
     */
    public function getConnectionStats()
    {
        // Default implementation - drivers can override
        return [];
    }

    /**
     * Get global status variables
     *
     * @return  array
     */
    public function getGlobalStatus()
    {
        // Default implementation - drivers can override
        return [
            'version' => $this->getVersion(),
            'database' => $this->getDatabase(),
        ];
    }

    /**
     * Get global configuration variables
     *
     * @return  array
     */
    public function getGlobalVariables()
    {
        // Default implementation - drivers can override
        return [];
    }

    /**
     * Get server uptime
     *
     * @return  int|null
     */
    public function getUptime()
    {
        // Default implementation - not supported
        return null;
    }

    /**
     * Get slow query count
     *
     * @return  int
     */
    public function getSlowQueries()
    {
        // Default implementation - not tracked
        return 0;
    }

    /**
     * Get table I/O statistics
     *
     * @return  array
     */
    public function getTableIoStats()
    {
        // Default implementation - drivers can override
        return [];
    }

    // =========================================================================
    // Universal Special Column Methods
    // =========================================================================

    /**
     * Add a CHECK constraint to a table
     *
     * @param   string  $table       Table name
     * @param   string  $name        Constraint name
     * @param   string  $expression  Check expression
     * @return  bool
     */
    public function addCheckConstraint(string $table, string $name, string $expression): bool
    {
        $table = $this->replacePrefix($table);
        $this->setQuery("ALTER TABLE $table ADD CONSTRAINT $name CHECK ($expression)");
        return $this->execute();
    }

    /**
     * Drop a CHECK constraint from a table
     *
     * @param   string  $table  Table name
     * @param   string  $name   Constraint name
     * @return  bool
     */
    public function dropCheckConstraint(string $table, string $name): bool
    {
        $table = $this->replacePrefix($table);
        $this->setQuery("ALTER TABLE $table DROP CONSTRAINT $name");
        return $this->execute();
    }

    /**
     * Get CHECK constraints for table
     *
     * @param   string  $table  Table name
     * @return  array
     */
    public function getCheckConstraints(string $table): array
    {
        // Default implementation - drivers should override with database-specific queries
        return [];
    }

    // =========================================================================
    // Universal Special Column Methods
    // =========================================================================

    /**
     * Add stored (computed) column
     *
     * @param   string       $table       Table name
     * @param   string       $column      Column name
     * @param   string       $expression  SQL expression
     * @param   string|null  $type        Column type (required by some drivers like MySQL)
     * @return  bool
     */
    public function addStoredColumn(string $table, string $column, string $expression, ?string $type = null): bool
    {
        // Default: not supported - drivers should override
        throw new \RuntimeException('Stored generated columns are not supported by this database driver.');
    }

    /**
     * Add virtual (computed) column
     *
     * @param   string       $table       Table name
     * @param   string       $column      Column name
     * @param   string       $expression  SQL expression
     * @param   string|null  $type        Column type (required by some drivers like MySQL)
     * @return  bool
     */
    public function addVirtualColumn(string $table, string $column, string $expression, ?string $type = null): bool
    {
        // Default: not supported - drivers should override
        throw new \RuntimeException('Virtual generated columns are not supported by this database driver.');
    }

    /**
     * Make column visible
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name
     * @return  bool
     */
    public function makeColumnVisible(string $table, string $column): bool
    {
        // Default: not supported - drivers should override
        throw new \RuntimeException('Invisible columns are not supported by this database driver.');
    }

    /**
     * Make column invisible
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name
     * @return  bool
     */
    public function makeColumnInvisible(string $table, string $column): bool
    {
        // Default: not supported - drivers should override
        throw new \RuntimeException('Invisible columns are not supported by this database driver.');
    }
}
