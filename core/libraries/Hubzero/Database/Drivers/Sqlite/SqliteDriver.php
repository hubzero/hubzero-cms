<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Sqlite;

use Hubzero\Database\Drivers\Base\BaseSqlDriver;
use Hubzero\Database\Exception\ConnectionFailedException;
use Hubzero\Database\Exception\QueryFailedException;

/**
 * SQLite (PDO) database driver
 *
 * SQLite is a lightweight, file-based database engine. This driver provides
 * SQLite-specific functionality and implements the Sql abstract contract.
 *
 * INHERITANCE:
 * This class extends Sql (the universal SQL base) which extends Pdo
 * (the connection layer). All SQLite-specific SQL syntax is here.
 */
class SqliteDriver extends BaseSqlDriver
{
    /**
     * The driver name
     *
     * @var string
     */
    protected $name = 'sqlite';

    /**
     * SQLite uses backtick quoting for identifiers
     *
     * @var string
     */
    protected $wrapper = '`%s`';

    /**
     * SQLite-specific SQL expression overrides
     */
    protected string $nowExpression = "datetime('now')";
    protected string $randExpression = 'RANDOM()';
    protected string $lengthFunction = 'LENGTH';

    /**
     * Abstract-to-native type map for SQLite
     *
     * SQLite uses a simplified type system with just INTEGER, REAL, TEXT, and BLOB.
     * All abstract types map to one of these four storage classes.
     *
     * @var array
     */

    /**
     * The current transaction depth (for savepoint support)
     *
     * @var int
     */
    protected $transactionDepth = 0;

    /**
     * Whether the _sequences emulation table has been verified/created
     *
     * @var bool
     */
    private $sequenceTableReady = false;

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

        // Establish connection string
        if (!isset($options['dsn'])) {
            $options['dsn'] = "sqlite:{$options['database']}";
        }

        if (substr($options['dsn'], 0, 7) != 'sqlite:') {
            throw new ConnectionFailedException('Sqlite DSN for PDO connection does not appear to be valid.', 500);
        }

        // Call parent construct
        parent::__construct($options);

        // Configure SQLite-specific settings after connection is established
        $this->configureSqlite($options);
    }

    /**
     * Configure SQLite-specific connection settings
     *
     * @param   array  $options  The database connection params
     * @return  void
     */
    protected function configureSqlite($options)
    {
        // Enable foreign key constraints (disabled by default in SQLite)
        $foreignKeys = isset($options['foreign_keys']) ? $options['foreign_keys'] : true;
        if ($foreignKeys) {
            $this->connection->exec('PRAGMA foreign_keys = ON');
        }

        // Set journal mode (WAL is recommended for better concurrency)
        // Options: DELETE, TRUNCATE, PERSIST, MEMORY, WAL, OFF
        if (isset($options['journal_mode'])) {
            $this->connection->exec('PRAGMA journal_mode = ' . strtoupper($options['journal_mode']));
        }

        // Set busy timeout in milliseconds (how long to wait for locks)
        // Default to 5 seconds if not specified
        $busyTimeout = isset($options['busy_timeout']) ? (int) $options['busy_timeout'] : 5000;
        $this->connection->exec('PRAGMA busy_timeout = ' . $busyTimeout);

        // Set synchronous mode for durability vs performance tradeoff
        // Options: OFF, NORMAL, FULL, EXTRA
        if (isset($options['synchronous'])) {
            $this->connection->exec('PRAGMA synchronous = ' . strtoupper($options['synchronous']));
        }
    }

    /**
     * Retrieves field information about the given table
     *
     * @param   string   $table     The name of the database table.
     * @param   boolean  $typeOnly  True to only return field types.
     * @return  array    An array of fields for the database table.
     */
    public function getTableColumns($table, $typeOnly = true)
    {
        $columns = [];

        $table = $this->replacePrefix($table);

        $this->setQuery('PRAGMA table_info(' . $this->quoteName($table) . ')');
        $fields = $this->loadObjectList();

        if ($typeOnly) {
            foreach ($fields as $field) {
                $columns[$field->name] = $field->type;
            }
        } else {
            foreach ($fields as $field) {
                // Normalize output to match MySQL format
                $columns[$field->name] = (object) [
                    'Field'   => $field->name,
                    'Type'    => $field->type,
                    'Null'    => ($field->notnull == '1' ? 'NO' : 'YES'),
                    'Default' => $field->dflt_value,
                    'Key'     => ($field->pk != '0' ? 'PRI' : ''),
                    'Extra'   => ''
                ];
            }
        }

        return $columns;
    }

    /**
     * Retrieves key information about the given table
     *
     * Returns both primary keys and indexes
     *
     * @param   string  $table  A table name
     * @return  array
     */
    public function getTableKeys($table)
    {
        $keys = [];
        $table = $this->replacePrefix($table);

        // Get primary key info from table_info
        $this->setQuery('PRAGMA table_info(' . $this->quoteName($table) . ')');
        $columns = $this->loadObjectList();

        foreach ($columns as $column) {
            if ($column->pk > 0) {
                $keys['PRIMARY'] = (object) [
                    'Key_name'    => 'PRIMARY',
                    'Column_name' => $column->name,
                    'Non_unique'  => 0,
                    'Seq_in_index' => $column->pk
                ];
            }
        }

        // Get index info
        $this->setQuery('PRAGMA index_list(' . $this->quoteName($table) . ')');
        $indexes = $this->loadObjectList();

        foreach ($indexes as $index) {
            // Get columns in this index
            $this->setQuery('PRAGMA index_info(' . $this->quoteName($index->name) . ')');
            $indexColumns = $this->loadObjectList();

            foreach ($indexColumns as $indexColumn) {
                $keys[$index->name] = (object) [
                    'Key_name'     => $index->name,
                    'Column_name'  => $indexColumn->name,
                    'Non_unique'   => $index->unique ? 0 : 1,
                    'Seq_in_index' => $indexColumn->seqno + 1
                ];
            }
        }

        return $keys;
    }

    /**
     * Gets an array of all tables in the database
     *
     * @return  array
     */
    public function getTableList()
    {
        $this->setQuery(
            "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
        );

        return $this->loadColumn();
    }

    /**
     * Shows the table CREATE statement that creates the given tables
     *
     * @param   string|array  $tables  A table name or a list of table names
     * @return  array
     */
    public function getTableCreate($tables)
    {
        $result = [];

        foreach ((array) $tables as $table) {
            $table = $this->replacePrefix($table);

            $this->setQuery(
                "SELECT sql FROM sqlite_master WHERE type = 'table' AND name = " .
                $this->quote($table)
            );

            $row = $this->loadResult();
            $result[$table] = $row ?: '';
        }

        return $result;
    }

    /**
     * Gets the database collation in use
     *
     * SQLite doesn't have database-level collation like MySQL.
     * Collation in SQLite is defined per-column or per-expression.
     *
     * @return  string|bool
     */
    public function getCollation()
    {
        // SQLite doesn't have a database-level collation setting
        // Return the default collation used for string comparisons
        return 'BINARY';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTableExistsQuery(string $table): string
    {
        return "SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name = "
            . $this->quote($table);
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

        $this->setQuery('PRAGMA table_info(' . $this->quoteName($table) . ')');
        $columns = $this->loadObjectList();

        foreach ($columns as $column) {
            if ($column->pk > 0) {
                return $column->name;
            }
        }

        return false;
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

        $this->setQuery('PRAGMA table_info(' . $this->quoteName($table) . ')');
        $columns = $this->loadObjectList();

        $pk = [];
        foreach ($columns as $column) {
            if ($column->pk > 0) {
                $pk[(int) $column->pk] = $column->name;
            }
        }

        if (empty($pk)) {
            return [];
        }

        ksort($pk);
        return array_values($pk);
    }

    /**
     * Gets the auto-increment value for the given table
     *
     * @param   string    $table  The table for which to retrieve the auto-increment value
     * @return  int|bool
     */
    public function getAutoIncrement($table)
    {
        $table = $this->replacePrefix($table);

        // SQLite stores auto-increment values in sqlite_sequence table
        // This table only exists if AUTOINCREMENT has been used
        $this->setQuery(
            "SELECT seq FROM sqlite_sequence WHERE name = " . $this->quote($table)
        );

        $result = $this->loadResult();

        if ($result !== null) {
            return (int) $result + 1;
        }

        // If no entry in sqlite_sequence, check if table has INTEGER PRIMARY KEY
        // and get the max value
        $pk = $this->getPrimaryKey($table);
        if ($pk) {
            $this->setQuery(
                "SELECT MAX(" . $this->quoteName($pk) . ") FROM " . $this->quoteName($table)
            );
            $max = $this->loadResult();
            return $max !== null ? (int) $max + 1 : 1;
        }

        return false;
    }

    /**
     * Sets the auto-increment starting value for the given table
     *
     * SQLite manages auto-increment automatically via sqlite_sequence table.
     * This method updates the sequence if the table uses AUTOINCREMENT.
     *
     * @param   string  $table  The table name
     * @param   int     $value  The auto-increment starting value
     * @return  bool
     */
    public function setAutoIncrement($table, $value): bool
    {
        $table = $this->replacePrefix($table);
        $value = (int) $value - 1; // sqlite_sequence stores the last used value

        // Check if table has an entry in sqlite_sequence
        $this->setQuery(
            "SELECT COUNT(*) FROM sqlite_sequence WHERE name = " . $this->quote($table)
        );

        if ($this->loadResult() > 0) {
            // Update existing entry
            $this->setQuery(
                "UPDATE sqlite_sequence SET seq = $value WHERE name = " . $this->quote($table)
            );
            $this->execute();
            return true;
        }

        // If no entry exists, SQLite will use MAX(rowid)+1 automatically
        // We can insert an entry to set a higher starting value
        $this->setQuery(
            "INSERT OR IGNORE INTO sqlite_sequence (name, seq) VALUES (" . $this->quote($table) . ", $value)"
        );
        $this->execute();
        return true;
    }

    /**
     * Get the allowed values for an ENUM column
     *
     * SQLite doesn't have native ENUM support - columns are stored as TEXT.
     * Always returns empty array.
     *
     * @note    NO-OP: SQLite doesn't support ENUM types. Use CHECK constraints for value validation.
     * @see     supportsEnum() to detect if this operation is available
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @return  array   Always empty array (SQLite has no ENUM)
     **/
    public function getEnumValues($table, $column)
    {
        // SQLite doesn't have ENUM type - values are stored as TEXT
        return [];
    }

    /**
     * Add a value to an ENUM column
     *
     * SQLite doesn't have native ENUM support - this is a no-op.
     *
     * @note    NO-OP: SQLite doesn't support ENUM types. Use CHECK constraints for value validation.
     * @see     supportsEnum() to detect if this operation is available
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @param   string  $value   The value to add
     * @return  bool    Always true (no-op)
     **/
    public function addEnumValue($table, $column, $value)
    {
        // SQLite stores ENUM as TEXT with no enforcement - nothing to do
        return true;
    }

    /**
     * Remove a value from an ENUM column
     *
     * SQLite doesn't have native ENUM support - this is a no-op.
     *
     * @note    NO-OP: SQLite doesn't support ENUM types. Use CHECK constraints for value validation.
     * @see     supportsEnum() to detect if this operation is available
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @param   string  $value   The value to remove
     * @return  bool    Always true (no-op)
     **/
    public function removeEnumValue($table, $column, $value)
    {
        // SQLite stores ENUM as TEXT with no enforcement - nothing to do
        return true;
    }

    /**
     * Locks a table in the database
     *
     * SQLite uses file-level locking, not table-level locking.
     * This is a no-op for compatibility.
     *
     * @param   string  $tableName  The name of the table to lock
     * @return  $this
     */
    public function lockTable($tableName)
    {
        // SQLite uses file-level locking automatically
        // Begin an immediate transaction to acquire a reserved lock
        // This prevents other connections from writing
        return $this;
    }

    /**
     * Unlocks all tables in the database
     *
     * SQLite uses file-level locking, not table-level locking.
     * This is a no-op for compatibility.
     *
     * @return  $this
     */
    public function unlockTables()
    {
        return $this;
    }

    /**
     * Selects a database for use
     *
     * SQLite databases are single-file, so this is not supported.
     * Use ATTACH DATABASE if you need to work with multiple databases.
     *
     * @param   string  $database  The name of the database to select for use
     * @return  bool
     */
    public function select($database)
    {
        return false;
    }

    /**
     * Gets the database engine of the given table
     *
     * SQLite has only one storage engine.
     *
     * @param   string       $table  The table for which to retrieve the engine type
     * @return  string|bool
     */
    public function getEngine($table)
    {
        return 'SQLite';
    }


    /**
     * Gets the database character set of the given table
     *
     * SQLite only supports encodings for an entire database,
     * not per-table or per-column.
     *
     * @param   string       $table  The table for which to retrieve the character set
     * @param   string       $field  The field to check (optional)
     * @return  string|bool
     */
    public function getCharacterSet($table, $field = null)
    {
        $this->setQuery('PRAGMA encoding');

        return $this->loadResult();
    }

    /**
     * Converts a table to the specified character set
     *
     * SQLite handles encoding at the database level (PRAGMA encoding),
     * so this is a no-op for individual tables.
     *
     * @param   string       $table    The table to convert
     * @param   string       $charset  The character set
     * @param   string|null  $collate  Optional collation
     * @return  bool
     */
    public function convertToCharset($table, $charset, $collate = null)
    {
        // SQLite doesn't support per-table character sets
        return true;
    }

    /**
     * Creates or replaces a database view
     *
     * SQLite doesn't support CREATE OR REPLACE, so we drop first then create.
     * SQLite also doesn't support ALGORITHM, DEFINER, or SQL SECURITY - options are ignored.
     *
     * @param   string  $name       The view name (with or without prefix)
     * @param   string  $selectSql  The SELECT statement for the view (prefixes will be replaced)
     * @param   array   $options    MySQL-specific options (ignored on SQLite)
     * @return  bool
     **/
    public function createOrReplaceView($name, $selectSql, array $options = []): bool
    {
        // Note: $options (algorithm, definer, security) are MySQL-specific and ignored on SQLite

        $viewName = $this->replacePrefix($name);
        $selectSql = $this->replacePrefix($selectSql);

        // SQLite doesn't support CREATE OR REPLACE VIEW, so drop first
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
     * @param   bool    $ifExists  Whether to use IF EXISTS clause
     * @return  bool
     **/
    public function dropView($name, $ifExists = true): bool
    {
        $tableName = $this->replacePrefix($name);
        $sql = 'DROP VIEW ' . ($ifExists ? 'IF EXISTS ' : '') . $this->quoteName($tableName);
        $this->setQuery($sql);
        $this->execute();

        return true;
    }

    /**
     * Checks if a view exists in the database
     *
     * @param   string  $name  The view name (without prefix)
     * @return  bool
     **/
    public function viewExists($name): bool
    {
        $tableName = $this->replacePrefix($name);
        $this->setQuery(
            "SELECT COUNT(*) FROM sqlite_master WHERE type = 'view' AND name = " . $this->quote($tableName)
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
            "SELECT name FROM sqlite_master WHERE type = 'view' ORDER BY name"
        );

        return $this->loadColumn() ?: [];
    }

    /**
     * Returns a list of all database names (attached databases)
     *
     * SQLite is file-based, so this returns the list of attached databases.
     * The main database is always named 'main'.
     *
     * @return  array  Array of database names
     **/
    public function getDatabaseNames(): array
    {
        $this->setQuery("PRAGMA database_list");
        $results = $this->loadObjectList();

        $databases = [];
        if ($results) {
            foreach ($results as $row) {
                $databases[] = $row->name;
            }
        }

        return $databases ?: ['main'];
    }

    // =========================================================================
    // Sequence Emulation (table-based)
    //
    // SQLite does not have native sequence objects. This implementation
    // provides API-compatible sequence emulation via a `_sequences` table.
    // SQLite's single-writer guarantee means UPDATE + SELECT is atomic.
    // =========================================================================

    /**
     * Ensures the _sequences emulation table exists
     *
     * @return  void
     */
    private function ensureSequenceTable(): void
    {
        if ($this->sequenceTableReady) {
            return;
        }

        if (!$this->tableExists('_sequences')) {
            $this->setQuery(
                'CREATE TABLE "_sequences" ('
                . '"name" VARCHAR(255) NOT NULL PRIMARY KEY, '
                . '"current_value" BIGINT NOT NULL DEFAULT 0, '
                . '"increment_value" INT NOT NULL DEFAULT 1, '
                . '"table_name" VARCHAR(255) NULL'
                . ')'
            );
            $this->execute();
        } elseif (!$this->tableHasField('_sequences', 'table_name')) {
            $this->setQuery(
                'ALTER TABLE "_sequences" ADD COLUMN "table_name" VARCHAR(255) NULL'
            );
            $this->execute();
        }

        $this->sequenceTableReady = true;
    }

    /**
     * Returns a list of all emulated sequences
     *
     * @return  array  Array of SequenceInfo objects
     **/
    public function getSequences(): array
    {
        $this->ensureSequenceTable();
        $this->setQuery(
            'SELECT * FROM "_sequences" ORDER BY "name"'
        );
        $rows = $this->loadObjectList();

        return array_map(function ($row) {
            return new \Hubzero\Database\Schema\SequenceInfo([
                'name'          => $row->name,
                'current_value' => (int) $row->current_value,
                'increment'     => (int) $row->increment_value,
            ]);
        }, $rows);
    }

    /**
     * Creates a new emulated sequence
     *
     * @param   string  $name       The sequence name
     * @param   int     $start      Starting value (default: 1)
     * @param   int     $increment  Increment value (default: 1)
     * @param   array   $options    Additional options (ignored)
     * @return  bool
     **/
    public function createSequence(
        $name,
        $start = 1,
        $increment = 1,
        array $options = []
    ): bool {
        $this->ensureSequenceTable();
        $name = $this->replacePrefix($name);
        $seedValue = (int) $start - (int) $increment;
        $tableName = $options['table'] ?? null;
        if ($tableName) {
            $tableName = $this->replacePrefix($tableName);
        }

        $columns = '"name", "current_value", "increment_value", "table_name"';
        $values = $this->quote($name) . ', '
            . $seedValue . ', '
            . (int) $increment . ', '
            . ($tableName ? $this->quote($tableName) : 'NULL');

        $this->setQuery(
            "INSERT INTO \"_sequences\" ({$columns}) VALUES ({$values})"
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
     **/
    public function dropSequence($name, $ifExists = true): bool
    {
        $this->ensureSequenceTable();
        $name = $this->replacePrefix($name);

        $this->setQuery(
            'DELETE FROM "_sequences" WHERE "name" = '
            . $this->quote($name)
        );
        $this->execute();

        return true;
    }

    protected function cleanupSequencesForTable(string $tableName): void
    {
        if ($this->sequenceTableReady || $this->tableExists('_sequences')) {
            $this->setQuery(
                'DELETE FROM "_sequences" WHERE "table_name" = '
                . $this->quote($tableName)
            );
            $this->execute();
        }
    }

    /**
     * Checks if an emulated sequence exists
     *
     * @param   string  $name  The sequence name
     * @return  bool
     **/
    public function sequenceExists($name): bool
    {
        $this->ensureSequenceTable();
        $name = $this->replacePrefix($name);

        $this->setQuery(
            'SELECT COUNT(*) FROM "_sequences" WHERE "name" = '
            . $this->quote($name)
        );

        return (int) $this->loadResult() > 0;
    }

    /**
     * Gets the next value from an emulated sequence
     *
     * SQLite is single-writer, so UPDATE + SELECT is atomic.
     *
     * @param   string  $name  The sequence name
     * @return  int
     **/
    public function nextSequenceValue($name): int
    {
        $this->ensureSequenceTable();
        $name = $this->replacePrefix($name);

        $this->setQuery(
            'UPDATE "_sequences" SET "current_value" = '
            . '"current_value" + "increment_value" '
            . 'WHERE "name" = ' . $this->quote($name)
        );
        $this->execute();

        $this->setQuery(
            'SELECT "current_value" FROM "_sequences" '
            . 'WHERE "name" = ' . $this->quote($name)
        );

        return (int) $this->loadResult();
    }

    /**
     * Gets the current value of an emulated sequence (without incrementing)
     *
     * @param   string  $name  The sequence name
     * @return  int
     **/
    public function currentSequenceValue($name): int
    {
        $this->ensureSequenceTable();
        $name = $this->replacePrefix($name);

        $this->setQuery(
            'SELECT "current_value" FROM "_sequences" '
            . 'WHERE "name" = ' . $this->quote($name)
        );
        $result = $this->loadResult();

        return $result !== null ? (int) $result : 0;
    }

    /**
     * Check if this driver supports sequences
     *
     * SQLite provides table-based sequence emulation.
     *
     * @return  bool
     **/
    public function supportsSequences(): bool
    {
        return true;
    }

    /**
     * SQLite implements sequences via `_sequences` table emulation.
     *
     * @return  bool
     */
    public function usesSequenceEmulation(): bool
    {
        return true;
    }

    /**
     * Gets the version of the database connector
     *
     * @return  string
     */
    public function getVersion()
    {
        $this->setQuery('SELECT sqlite_version()');

        return $this->loadResult();
    }

    /**
     * Renames a table in the database
     *
     * @param   string  $oldTable  The name of the table to be renamed
     * @param   string  $newTable  The new name for the table
     * @param   string  $backup    Table prefix (unused in SQLite)
     * @param   string  $prefix    For the table (unused in SQLite)
     * @return  $this
     */
    public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
    {
        $oldTable = $this->replacePrefix($oldTable);
        $newTable = $this->replacePrefix($newTable);

        $this->setQuery(
            'ALTER TABLE ' . $this->quoteName($oldTable) .
            ' RENAME TO ' . $this->quoteName($newTable)
        )->execute();

        return $this;
    }

    /**
     * Initializes a transaction
     *
     * @return  void
     */
    public function transactionStart()
    {
        $this->transactionStartWithSavepoints('BEGIN TRANSACTION');
    }

    /**
     * Commits a transaction
     *
     * @return  void
     */
    public function transactionCommit()
    {
        $this->transactionCommitWithSavepoints();
    }

    /**
     * Rolls back a transaction
     *
     * @return  void
     */
    public function transactionRollback()
    {
        $this->transactionRollbackWithSavepoints();
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
            // Simple query to verify connection is still valid
            $this->setQuery('SELECT 1');
            $this->loadResult();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Sets the connection to use UTF-8 character encoding
     *
     * SQLite uses UTF-8 by default when the database is created.
     * This cannot be changed after creation.
     *
     * @return  bool
     */
    public function setUTF()
    {
        // SQLite uses UTF-8 by default
        return true;
    }

    // =========================================================================
    // Feature Detection Methods - SQLite Implementations
    // =========================================================================

    /**
     * Check if this database supports column positioning (AFTER/BEFORE/FIRST)
     *
     * SQLite supports column positioning via transparent table recreation.
     * The driver handles this automatically - callers can use positioning methods.
     *
     * @return  bool  True - SQLite supports column positioning via table recreation
     */
    public function supportsColumnPositioning(): bool
    {
        return true;
    }


    // =========================================================================
    // Schema Building Methods - SQLite Implementations
    // =========================================================================

    /**
     * Map a MySQL-style column type to SQLite's type
     *
     * SQLite has flexible typing with type affinity.
     *
     * @param   string  $type  The MySQL column type
     * @return  string  The SQLite column type
     */
    public function mapColumnType(string $type): string
    {
        $upper = strtoupper($type);

        // Integer types
        if (preg_match('/^(TINY|SMALL|MEDIUM|BIG)?INT(EGER)?(\s*\(\d+\))?(\s+UNSIGNED)?$/i', $upper)) {
            return 'INTEGER';
        }

        // Boolean -> INTEGER (SQLite stores as 0/1)
        if ($upper === 'BOOLEAN' || $upper === 'BOOL' || $upper === 'TINYINT(1)') {
            return 'INTEGER';
        }

        // Float/Double/Decimal -> REAL
        if (preg_match('/^(FLOAT|DOUBLE|DECIMAL|NUMERIC)(\s*\([^)]+\))?$/i', $upper)) {
            return 'REAL';
        }

        // Text types -> TEXT
        if (preg_match('/^(TINY|MEDIUM|LONG)?TEXT$/i', $upper)) {
            return 'TEXT';
        }

        // VARCHAR/CHAR -> TEXT
        if (preg_match('/^(VAR)?CHAR\s*\(\d+\)$/i', $upper)) {
            return 'TEXT';
        }

        // BLOB types -> BLOB
        if (preg_match('/^(TINY|MEDIUM|LONG)?BLOB$/i', $upper)) {
            return 'BLOB';
        }

        // DateTime types -> TEXT (SQLite stores as ISO8601 strings)
        if (preg_match('/^(DATE|TIME|DATETIME|TIMESTAMP)$/i', $upper)) {
            return 'TEXT';
        }

        // ENUM -> TEXT
        if (preg_match('/^ENUM\s*\(/i', $upper)) {
            return 'TEXT';
        }

        // SET -> TEXT (SQLite has no SET type)
        if (preg_match('/^SET\s*\(/i', $upper)) {
            return 'TEXT';
        }

        // YEAR -> INTEGER (SQLite has no YEAR type)
        if ($upper === 'YEAR' || preg_match('/^YEAR\s*\(\d+\)$/i', $upper)) {
            return 'INTEGER';
        }

        return $type;
    }

    /**
     * SQLite doesn't need length parameters for string types
     *
     * SQLite treats VARCHAR(N) and CHAR(N) as TEXT regardless of length.
     *
     * @return  bool  False - SQLite doesn't use length parameters
     */
    public function requiresStringLength(): bool
    {
        return false;
    }

    /**
     * Apply column modifiers to a mapped type
     *
     * SQLite uses dynamic typing — length and precision modifiers are
     * ignored entirely. All types collapse to their storage class.
     *
     * @param   string  $abstractType  The original abstract type name
     * @param   string  $nativeType    The mapped database type
     * @param   array   $modifiers     Column modifiers (ignored)
     * @return  string  The native type unchanged
     */
    protected function applyColumnModifiers(
        string $abstractType,
        string $nativeType,
        array $modifiers
    ): string {
        // SQLite uses dynamic typing — no length/precision needed
        return $nativeType;
    }

    /**
     * Build an auto-increment primary key column definition
     *
     * SQLite requires INTEGER (not INT) for AUTOINCREMENT.
     *
     * @param   string  $quotedName  The quoted column name
     * @param   string  $type        The column type (ignored - always INTEGER)
     * @return  string  The column definition SQL
     */
    public function buildAutoIncrementColumn(string $quotedName, string $type): string
    {
        return "$quotedName INTEGER PRIMARY KEY AUTOINCREMENT";
    }

    /**
     * Build a UNIQUE constraint definition for CREATE TABLE
     *
     * SQLite uses CONSTRAINT ... UNIQUE syntax.
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
     * SQLite does not support inline index definitions - use CREATE INDEX after.
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
     * SQLite uses FTS5 virtual tables for full-text search, not indexes.
     *
     * @param   string  $quotedName     The quoted index name
     * @param   string  $columnList     The column list SQL
     * @return  string|null  Null - FTS requires different approach
     */
    public function buildFulltextIndexDefinition(string $quotedName, string $columnList): ?string
    {
        return null;
    }

    // =========================================================================
    // Additional SQLite Introspection Methods
    // =========================================================================

    /**
     * Gets index information for a table
     *
     * @param   string  $table  The table name
     * @return  array
     */
    public function getIndexes($table)
    {
        $table = $this->replacePrefix($table);
        $indexes = [];

        $this->setQuery('PRAGMA index_list(' . $this->quoteName($table) . ')');
        $indexList = $this->loadObjectList();

        foreach ($indexList as $index) {
            $this->setQuery('PRAGMA index_info(' . $this->quoteName($index->name) . ')');
            $columns = $this->loadObjectList();

            $columnNames = [];
            foreach ($columns as $column) {
                $columnNames[] = $column->name;
            }

            $indexes[$index->name] = (object) [
                'name'     => $index->name,
                'unique'   => (bool) $index->unique,
                'columns'  => $columnNames,
                'partial'  => isset($index->partial) ? (bool) $index->partial : false
            ];
        }

        return $indexes;
    }

    /**
     * Gets foreign key constraints for a table
     *
     * Returns an array of foreign key constraint objects, each containing:
     * - name: The constraint name (SQLite generates names like 'fk_0', 'fk_1', etc.)
     * - columns: Array of local column names
     * - foreign_table: The referenced table name
     * - foreign_columns: Array of referenced column names
     * - on_update: The ON UPDATE action
     * - on_delete: The ON DELETE action
     *
     * @param   string  $table  The table name
     * @return  array   Array of foreign key constraint objects
     */
    public function getForeignKeys($table)
    {
        $table = $this->replacePrefix($table);

        $this->setQuery('PRAGMA foreign_key_list(' . $this->quoteName($table) . ')');

        return $this->groupForeignKeyRows($this->loadObjectList(), [
            'constraint_name' => fn($row) => 'fk_' . $row->id,
            'column_name'     => 'from',
            'foreign_table'   => 'table',
            'foreign_column'  => 'to',
            'on_update'       => fn($row) => $row->on_update ?? 'NO ACTION',
            'on_delete'       => fn($row) => $row->on_delete ?? 'NO ACTION',
        ]);
    }

    /**
     * Test to see if the SQLite connector is available
     *
     * @return  bool
     */
    public static function test()
    {
        return class_exists('\PDO') && in_array('sqlite', \PDO::getAvailableDrivers());
    }

    // =========================================================================
    // Server Information
    // =========================================================================

    /**
     * Get database server version information
     *
     * @return  array  Array with standardized keys:
     *                  - 'version': Version number (x.y.z format)
     *                  - 'driver_version': Normalized version (x.y.z format) - STANDARD KEY
     *                  - 'sqlite_version': Alias for driver_version (deprecated, use driver_version)
     *                  - 'comment': Version comment/description
     */
    public function getServerInfo()
    {
        $this->setQuery("SELECT sqlite_version() as version");
        $row = $this->loadObject();

        $version = $row->version ?? null;

        return [
            'version'        => $version,
            'driver_version' => $version,  // Standard key for all drivers
            'sqlite_version' => $version,  // Deprecated alias for backwards compatibility
            'comment'        => 'SQLite',
        ];
    }


    /**
     * Map a type to SQLite type affinity
     *
     * SQLite uses type affinities (TEXT, NUMERIC, INTEGER, REAL, BLOB).
     * This maps common SQL types to appropriate SQLite types.
     *
     * @param   string  $type  The input type
     * @return  string  The SQLite-compatible type
     */
    protected function mapTypeToSqliteAffinity(string $type): string
    {
        $type = strtoupper(trim($type));

        // Remove size specifiers and UNSIGNED for mapping
        $baseType = preg_replace('/\(.*?\)/', '', $type);
        $baseType = preg_replace('/\s+UNSIGNED$/i', '', $baseType);
        $baseType = preg_replace('/\s+AUTO_INCREMENT$/i', '', $baseType);

        // Map to SQLite affinity
        $typeMap = [
            // Integer types
            'TINYINTEGER' => 'INTEGER',
            'SMALLINTEGER' => 'INTEGER',
            'MEDIUMINTEGER' => 'INTEGER',
            'INTEGER' => 'INTEGER',
            'BIGINTEGER' => 'INTEGER',
            'TINYINT' => 'INTEGER',
            'SMALLINT' => 'INTEGER',
            'MEDIUMINT' => 'INTEGER',
            'INT' => 'INTEGER',
            'BIGINT' => 'INTEGER',
            'BOOLEAN' => 'INTEGER',
            'BOOL' => 'INTEGER',

            // String types (abstract and concrete)
            'STRING' => 'TEXT',
            'CHAR' => 'TEXT',
            'VARCHAR' => 'TEXT',
            'TEXT' => 'TEXT',
            'TINYTEXT' => 'TEXT',
            'MEDIUMTEXT' => 'TEXT',
            'LONGTEXT' => 'TEXT',

            // Numeric types
            'DECIMAL' => 'NUMERIC',
            'NUMERIC' => 'NUMERIC',
            'FLOAT' => 'REAL',
            'DOUBLE' => 'REAL',
            'REAL' => 'REAL',

            // Binary types
            'BLOB' => 'BLOB',
            'BINARY' => 'BLOB',
            'TINYBLOB' => 'BLOB',
            'MEDIUMBLOB' => 'BLOB',
            'LONGBLOB' => 'BLOB',

            // Date/time types
            'DATETIME' => 'TEXT',
            'TIMESTAMP' => 'TEXT',
            'TIMESTAMPTZ' => 'TEXT',
            'DATE' => 'TEXT',
            'TIME' => 'TEXT',
            'YEAR' => 'TEXT',

            // Special types
            'JSON' => 'TEXT',
            'UUID' => 'TEXT',
            'ULID' => 'TEXT',
            'IPADDRESS' => 'TEXT',
            'MACADDRESS' => 'TEXT',
        ];

        return $typeMap[$baseType] ?? 'TEXT';
    }


    /**
     * Get the full SQLite version string
     *
     * @return  string|null  Full version (e.g., '3.39.4') or null if unknown
     */
    public function getFullVersion()
    {
        $info = $this->getServerInfo();
        return $info['sqlite_version'] ?? null;
    }

    /**
     * Check if SQLite version supports DROP COLUMN
     *
     * DROP COLUMN was added in SQLite 3.35.0 (2021-03-12).
     * Older versions require table recreation.
     *
     * @return  bool
     */
    public function supportsDropColumn()
    {
        $version = $this->getMajorVersion();
        return $version !== null && $version >= 3.35;
    }

    /**
     * Check if SQLite version supports RENAME COLUMN
     *
     * RENAME COLUMN was added in SQLite 3.25.0 (2018-09-15).
     * Older versions require table recreation.
     *
     * @return  bool
     */
    public function supportsRenameColumn()
    {
        $version = $this->getMajorVersion();
        return $version !== null && $version >= 3.25;
    }

    // =========================================================================
    // SQL Compatibility Helpers - SQLite Overrides
    // =========================================================================

    /**
     * Returns the SQL keyword for INSERT with ignore duplicates
     *
     * SQLite uses "INSERT OR IGNORE INTO" instead of MySQL's "INSERT IGNORE INTO"
     *
     * @return  string
     */
    public function sqlInsertIgnore(): string
    {
        return 'INSERT OR IGNORE INTO';
    }

    /**
     * Returns the SQL keyword for REPLACE (upsert)
     *
     * SQLite uses "INSERT OR REPLACE INTO" instead of MySQL's "REPLACE INTO"
     *
     * @return  string
     */
    public function sqlReplace(): string
    {
        return 'INSERT OR REPLACE INTO';
    }

    /**
     * Returns whether the database supports REGEXP operator
     *
     * SQLite supports REGEXP only if a user-defined function is registered.
     * We register this function in configureSqlite().
     *
     * @return  bool
     */
    public function supportsRegexp(): bool
    {
        return $this->regexpRegistered;
    }

    /**
     * Whether the REGEXP function has been registered
     *
     * @var bool
     */
    protected $regexpRegistered = false;

    /**
     * @inheritdoc
     */
    protected function resetDriverState(array $options = []): void
    {
        $this->sequenceTableReady = false;
        $this->regexpRegistered = false;
    }

    /**
     * Format a boolean value as a SQL literal
     *
     * SQLite has no native boolean type; uses INTEGER with 1/0 values.
     *
     * @param   bool  $value  The boolean value
     * @return  string  The SQL literal
     */
    public function formatBooleanLiteral(bool $value): string
    {
        return $value ? '1' : '0';
    }

    /**
     * Normalize an abstract type to its SQLite equivalent
     *
     * @param  string $type      Abstract type name
     * @param  array  $modifiers Column modifiers (e.g., length, precision)
     * @return string
     */
    public function normalizeColumnType(
        string $type,
        array $modifiers = []
    ): string {
        $mappedType = $this->getSchemaGrammar()->getTypeMapping($type);
        if ($mappedType !== null) {
            return $mappedType;
        }

        // Fall back to mapColumnType for raw MySQL types
        return $this->mapColumnType($type);
    }


    /**
     * Register the REGEXP function for SQLite
     *
     * This enables MySQL-compatible REGEXP syntax in SQLite queries.
     * Call this method if you need REGEXP support.
     *
     * @return  $this
     */
    public function registerRegexp()
    {
        if (!$this->regexpRegistered && $this->connection) {
            $this->connection->sqliteCreateFunction('REGEXP', function ($pattern, $value) {
                if ($pattern === null || $value === null) {
                    return null;
                }
                // Convert MySQL-style regex to PHP preg_match format
                $delimiter = '/';
                // Escape delimiter if present in pattern
                $pattern = str_replace($delimiter, '\\' . $delimiter, $pattern);
                return @preg_match($delimiter . $pattern . $delimiter . 'i', $value) ? 1 : 0;
            }, 2);

            $this->regexpRegistered = true;
        }

        return $this;
    }

    /**
     * Returns the SQL for a REGEXP comparison
     *
     * For SQLite, ensures the REGEXP function is registered first.
     *
     * @param   string  $column   The column to match
     * @param   string  $pattern  The regex pattern
     * @param   bool    $not      Whether to negate the match
     * @return  string
     */
    public function sqlRegexp(string $column, string $pattern, bool $not = false): string
    {
        // Ensure REGEXP function is available
        $this->registerRegexp();

        $notStr = $not ? ' NOT' : '';
        return $column . $notStr . ' REGEXP ' . $this->quote($pattern);
    }

    /**
     * Returns the SQL for date subtraction
     *
     * SQLite uses date(date, '-n unit') or datetime() for timestamps
     *
     * @param   string  $date   The date column or value
     * @param   int     $value  The interval value
     * @param   string  $unit   The interval unit (DAY, MONTH, YEAR, HOUR, MINUTE, SECOND)
     * @return  string
     */
    public function sqlDateSub(string $date, int $value, string $unit = 'DAY'): string
    {
        $unit = strtolower($unit);
        // SQLite uses plural form for modifiers
        $modifier = '-' . $value . ' ' . $unit . 's';
        return "datetime(" . $date . ", '" . $modifier . "')";
    }

    /**
     * Returns the SQL for date addition
     *
     * SQLite uses date(date, '+n unit') or datetime() for timestamps
     *
     * @param   string  $date   The date column or value
     * @param   int     $value  The interval value
     * @param   string  $unit   The interval unit (DAY, MONTH, YEAR, HOUR, MINUTE, SECOND)
     * @return  string
     */
    public function sqlDateAdd(string $date, int $value, string $unit = 'DAY'): string
    {
        $unit = strtolower($unit);
        // SQLite uses plural form for modifiers
        $modifier = '+' . $value . ' ' . $unit . 's';
        return "datetime(" . $date . ", '" . $modifier . "')";
    }

    /**
     * Returns the SQL for date formatting
     *
     * SQLite uses strftime(format, date) with different format specifiers
     *
     * @param   string  $date    The date column or value
     * @param   string  $format  The format string (MySQL format will be converted)
     * @return  string
     */
    public function sqlDateFormat(string $date, string $format): string
    {
        // Convert MySQL format specifiers to SQLite strftime format
        // Most common ones are the same, but some differ
        $sqliteFormat = $format;
        // MySQL %i = minutes, SQLite %M = minutes
        $sqliteFormat = str_replace('%i', '%M', $sqliteFormat);
        // MySQL %s = seconds, SQLite %S = seconds (both work in SQLite)

        return "strftime(" . $this->quote($sqliteFormat) . ", " . $date . ")";
    }

    /**
     * Returns the SQL for extracting year from a date
     *
     * SQLite uses strftime('%Y', date) and casts to integer
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    public function sqlYear(string $date): string
    {
        return "CAST(strftime('%Y', " . $date . ") AS INTEGER)";
    }

    /**
     * Returns the SQL for extracting month from a date
     *
     * SQLite uses strftime('%m', date) and casts to integer to remove leading zero
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    public function sqlMonth(string $date): string
    {
        return "CAST(strftime('%m', " . $date . ") AS INTEGER)";
    }

    /**
     * Returns the SQL for converting a date to Unix timestamp
     *
     * SQLite uses strftime('%s', date)
     *
     * @param   string  $date  The date column or value
     * @return  string
     */
    public function sqlUnixTimestamp(string $date): string
    {
        return "strftime('%s', " . $date . ")";
    }

    /**
     * Returns the SQL for extracting a substring based on a delimiter
     *
     * SQLite doesn't have SUBSTRING_INDEX, so we use INSTR and SUBSTR.
     * This implementation handles the most common cases:
     * - count = -1: returns everything after the first occurrence of the delimiter
     * - count = 1: returns everything before the first occurrence of the delimiter
     *
     * Note: For |count| > 1, this falls back to a simpler behavior that may
     * not match MySQL exactly. Complex multi-delimiter cases should be
     * handled in PHP if exact MySQL compatibility is required.
     *
     * @param   string  $str    The string expression (column or literal)
     * @param   string  $delim  The delimiter to search for
     * @param   int     $count  The occurrence count (positive = from left, negative = from right)
     * @return  string
     */
    public function sqlSubstringIndex(string $str, string $delim, int $count): string
    {
        $quotedDelim = $this->quote($delim);
        $delimLen = strlen($delim);

        if ($count == -1) {
            // Get everything after the first occurrence of delimiter
            return "CASE WHEN INSTR(" . $str . ", " . $quotedDelim . ") > 0 "
                 . "THEN SUBSTR(" . $str . ", INSTR(" . $str . ", " . $quotedDelim . ") + " . $delimLen . ") "
                 . "ELSE " . $str . " END";
        } elseif ($count == 1) {
            // Get everything before the first occurrence of delimiter
            return "CASE WHEN INSTR(" . $str . ", " . $quotedDelim . ") > 0 "
                 . "THEN SUBSTR(" . $str . ", 1, INSTR(" . $str . ", " . $quotedDelim . ") - 1) "
                 . "ELSE " . $str . " END";
        } elseif ($count > 1) {
            // For count > 1, fall back to getting everything before first occurrence (approximate)
            return "CASE WHEN INSTR(" . $str . ", " . $quotedDelim . ") > 0 "
                 . "THEN SUBSTR(" . $str . ", 1, INSTR(" . $str . ", " . $quotedDelim . ") - 1) "
                 . "ELSE " . $str . " END";
        } else {
            // count < -1: Fall back to getting everything after first occurrence (approximate)
            return "CASE WHEN INSTR(" . $str . ", " . $quotedDelim . ") > 0 "
                 . "THEN SUBSTR(" . $str . ", INSTR(" . $str . ", " . $quotedDelim . ") + " . $delimLen . ") "
                 . "ELSE " . $str . " END";
        }
    }

    /**
     * Returns the SQL for concatenating strings
     *
     * SQLite uses the || operator for concatenation
     *
     * @param   array  $strings  Array of column names or quoted strings to concatenate
     * @return  string
     */
    public function sqlConcat(array $strings): string
    {
        if (empty($strings)) {
            return "''";
        }

        return implode(' || ', $strings);
    }

    /**
     * Returns the SQL for concatenating strings with a separator
     *
     * SQLite uses the || operator for concatenation
     *
     * @param   string  $separator  The separator string
     * @param   array   $strings    Array of column names or quoted strings to concatenate
     * @return  string
     */
    public function sqlConcatWs(string $separator, array $strings): string
    {
        if (empty($strings)) {
            return "''";
        }

        $quotedSep = $this->quote($separator);

        // SQLite doesn't have CONCAT_WS, use || operator
        return implode(' || ' . $quotedSep . ' || ', $strings);
    }

    // =========================================================================
    // DDL Helper Methods - SQLite Overrides
    // =========================================================================

    /**
     * Modify a column definition
     *
     * SQLite doesn't support ALTER TABLE MODIFY COLUMN, so we use table recreation.
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  New column definition
     * @param   string  $comment     Optional column comment (ignored on SQLite)
     * @return  bool
     */
    public function modifyColumn(string $table, string $column, string $definition, string $comment = ''): bool
    {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        if (!$this->tableHasField($table, $column)) {
            return false;
        }

        // SQLite doesn't support ALTER TABLE MODIFY COLUMN - uses table recreation
        return $this->sqliteModifyColumn($table, $column, $column, $definition);
    }

    /**
     * Modify a column definition and move it after a specific column
     *
     * SQLite doesn't support column repositioning natively, so we use table recreation.
     *
     * @param   string  $table        Table name (with or without prefix)
     * @param   string  $column       Column name
     * @param   string  $definition   New column definition
     * @param   string  $afterColumn  Column to position after
     * @param   string  $comment      Optional column comment (ignored on SQLite)
     * @return  bool
     */
    public function modifyColumnAfter(
        string $table,
        string $column,
        string $definition,
        string $afterColumn,
        string $comment = ''
    ): bool {
        return $this->sqliteModifyColumnAtPosition($table, $column, $definition, 'after', $afterColumn);
    }

    /**
     * Modify a column definition and move it before a specific column
     *
     * SQLite doesn't support column repositioning natively, so we use table recreation.
     *
     * @param   string  $table         Table name (with or without prefix)
     * @param   string  $column        Column name
     * @param   string  $definition    New column definition
     * @param   string  $beforeColumn  Column to position before
     * @param   string  $comment       Optional column comment (ignored on SQLite)
     * @return  bool
     */
    public function modifyColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool {
        return $this->sqliteModifyColumnAtPosition($table, $column, $definition, 'before', $beforeColumn);
    }

    /**
     * Modify a column definition and move it to the first position
     *
     * SQLite doesn't support column repositioning natively, so we use table recreation.
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  New column definition
     * @param   string  $comment     Optional column comment (ignored on SQLite)
     * @return  bool
     */
    public function modifyColumnFirst(string $table, string $column, string $definition, string $comment = ''): bool
    {
        return $this->sqliteModifyColumnAtPosition($table, $column, $definition, 'first', null);
    }

    /**
     * SQLite column modification with repositioning via table recreation
     *
     * SQLite doesn't support ALTER TABLE MODIFY with positioning, so we must:
     * 1. Get current table schema
     * 2. Create new table with column at desired position
     * 3. Copy data
     * 4. Drop old table
     * 5. Rename new table
     * 6. Recreate indexes
     *
     * @param   string       $table           Table name
     * @param   string       $column          Column name to modify
     * @param   string       $definition      New column definition
     * @param   string       $position        Position type: 'first', 'after', 'before'
     * @param   string|null  $referenceColumn Column to position relative to (for after/before)
     * @return  bool
     */
    protected function sqliteModifyColumnAtPosition(
        string $table,
        string $column,
        string $definition,
        string $position,
        ?string $referenceColumn
    ): bool {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        if (!$this->tableHasField($table, $column)) {
            return false;
        }

        // Get current table info
        $this->setQuery("PRAGMA table_info(`$table`)");
        $columns = $this->loadObjectList();

        if (empty($columns)) {
            return false;
        }

        // Get existing indexes (we'll need to recreate them)
        $indexes = $this->getIndexes($table);

        // Build column definitions for new table with column at correct position
        $newColumnDefs = [];
        $selectColumns = [];
        $columnPlaced = false;

        // For 'first' position, place the modified column at the beginning
        if ($position === 'first') {
            $newColumnDefs[] = "`$column` $definition";
            $columnPlaced = true;
        }

        foreach ($columns as $col) {
            // Skip the column we're modifying (we'll place it at the new position)
            if ($col->name === $column) {
                $selectColumns[] = "`{$col->name}`";
                continue;
            }

            // For 'before', insert the modified column before the reference column
            if ($position === 'before' && $col->name === $referenceColumn && !$columnPlaced) {
                $newColumnDefs[] = "`$column` $definition";
                $columnPlaced = true;
            }

            // Keep existing column as-is
            $type = $col->type;
            $notNull = $col->notnull ? ' NOT NULL' : '';
            $default = '';
            if ($col->dflt_value !== null) {
                $default = " DEFAULT " . $col->dflt_value;
            }
            $pk = $col->pk ? ' PRIMARY KEY' : '';

            $newColumnDefs[] = "`{$col->name}` {$type}{$notNull}{$default}{$pk}";
            $selectColumns[] = "`{$col->name}`";

            // For 'after', insert the modified column after the reference column
            if ($position === 'after' && $col->name === $referenceColumn && !$columnPlaced) {
                $newColumnDefs[] = "`$column` $definition";
                $columnPlaced = true;
            }
        }

        // If reference column not found, add at end
        if (!$columnPlaced) {
            $newColumnDefs[] = "`$column` $definition";
        }

        $tempTable = $table . '_migration_temp_' . time();

        // Start transaction
        $this->setQuery('BEGIN TRANSACTION');
        $this->execute();

        try {
            // Create new table with column at desired position
            $createSql = "CREATE TABLE `$tempTable` (\n  " . implode(",\n  ", $newColumnDefs) . "\n)";
            $this->setQuery($createSql);
            $this->execute();

            // Copy all data (including the column being modified)
            $copySql = "INSERT INTO `$tempTable` SELECT " . implode(', ', $selectColumns) . " FROM `$table`";
            $this->setQuery($copySql);
            $this->execute();

            // Drop old table
            $this->setQuery("DROP TABLE `$table`");
            $this->execute();

            // Rename new table
            $this->setQuery("ALTER TABLE `$tempTable` RENAME TO `$table`");
            $this->execute();

            // Recreate indexes (excluding auto-created ones and PRIMARY)
            foreach ($indexes as $indexName => $indexInfo) {
                // Skip auto-generated indexes (sqlite_autoindex_*) and PRIMARY
                if (strpos($indexName, 'sqlite_autoindex_') === 0 || $indexName === 'PRIMARY') {
                    continue;
                }

                $columnList = '`' . implode('`, `', $indexInfo->columns) . '`';
                $uniqueStr = $indexInfo->unique ? 'UNIQUE ' : '';
                $indexSql = "CREATE {$uniqueStr}INDEX IF NOT EXISTS `$indexName` ON `$table` ($columnList)";
                $this->setQuery($indexSql);
                $this->execute();
            }

            // Commit
            $this->setQuery('COMMIT');
            $this->execute();

            return true;
        } catch (\Exception $e) {
            // Rollback on error
            $this->setQuery('ROLLBACK');
            $this->execute();

            // Clean up temp table if it exists
            $this->setQuery("DROP TABLE IF EXISTS `$tempTable`");
            $this->execute();

            return false;
        }
    }

    /**
     * Change a column name and/or definition
     *
     * SQLite doesn't support ALTER TABLE CHANGE COLUMN, so we use table recreation.
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $oldColumn   Current column name
     * @param   string  $newColumn   New column name
     * @param   string  $definition  New column definition
     * @param   string  $comment     Optional column comment (ignored on SQLite)
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

        if (!$this->tableExists($table)) {
            return false;
        }

        if (!$this->tableHasField($table, $oldColumn)) {
            return false;
        }

        return $this->sqliteModifyColumn($table, $oldColumn, $newColumn, $definition);
    }

    /**
     * SQLite column modification via table recreation
     *
     * SQLite doesn't support ALTER TABLE MODIFY/CHANGE, so we must:
     * 1. Get current table schema
     * 2. Create new table with modified column
     * 3. Copy data
     * 4. Drop old table
     * 5. Rename new table
     *
     * @param   string  $table       Table name
     * @param   string  $oldColumn   Current column name
     * @param   string  $newColumn   New column name (can be same as old)
     * @param   string  $definition  New column definition
     * @return  bool
     */
    protected function sqliteModifyColumn(string $table, string $oldColumn, string $newColumn, string $definition): bool
    {
        // Get current table info
        $this->setQuery("PRAGMA table_info(`$table`)");
        $columns = $this->loadObjectList();

        if (empty($columns)) {
            return false;
        }

        // Build column list for new table
        $newColumns = [];
        $selectColumns = [];
        $foundColumn = false;

        foreach ($columns as $col) {
            if ($col->name === $oldColumn) {
                $foundColumn = true;
                // Use new definition for this column
                $newColumns[] = "`$newColumn` $definition";
                $selectColumns[] = "`$oldColumn` AS `$newColumn`";
            } else {
                // Keep existing column as-is
                $type = $col->type;
                $notNull = $col->notnull ? ' NOT NULL' : '';
                $default = '';
                if ($col->dflt_value !== null) {
                    $default = " DEFAULT " . $col->dflt_value;
                }
                $pk = $col->pk ? ' PRIMARY KEY' : '';

                $newColumns[] = "`{$col->name}` {$type}{$notNull}{$default}{$pk}";
                $selectColumns[] = "`{$col->name}`";
            }
        }

        if (!$foundColumn) {
            return false;
        }

        $tempTable = $table . '_migration_temp_' . time();

        // Start transaction
        $this->setQuery('BEGIN TRANSACTION');
        $this->execute();

        try {
            // Create new table with modified schema
            $createSql = "CREATE TABLE `$tempTable` (\n  " . implode(",\n  ", $newColumns) . "\n)";
            $this->setQuery($createSql);
            $this->execute();

            // Copy data
            $copySql = "INSERT INTO `$tempTable` SELECT " . implode(', ', $selectColumns) . " FROM `$table`";
            $this->setQuery($copySql);
            $this->execute();

            // Drop old table
            $this->setQuery("DROP TABLE `$table`");
            $this->execute();

            // Rename new table
            $this->setQuery("ALTER TABLE `$tempTable` RENAME TO `$table`");
            $this->execute();

            // Commit
            $this->setQuery('COMMIT');
            $this->execute();

            return true;
        } catch (\Exception $e) {
            // Rollback on error
            $this->setQuery('ROLLBACK');
            $this->execute();

            // Clean up temp table if it exists
            $this->setQuery("DROP TABLE IF EXISTS `$tempTable`");
            $this->execute();

            return false;
        }
    }

    /**
     * Add a column to a table
     *
     * SQLite supports ADD COLUMN but not COMMENT.
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional column comment (ignored on SQLite)
     * @return  bool
     */
    protected function buildAddColumnSql(
        string $table,
        string $column,
        string $definition,
        string $comment
    ): string {
        return "ALTER TABLE `$table` ADD COLUMN `$column` $definition";
    }

    /**
     * Add a column after a specific column
     *
     * SQLite doesn't support AFTER natively, so we use table recreation
     * to position the column correctly.
     *
     * @param   string  $table        Table name (with or without prefix)
     * @param   string  $column       Column name
     * @param   string  $definition   Column definition
     * @param   string  $afterColumn  Column to add after
     * @param   string  $comment      Optional column comment (ignored on SQLite)
     * @return  bool
     */
    public function addColumnAfter(
        string $table,
        string $column,
        string $definition,
        string $afterColumn,
        string $comment = ''
    ): bool {
        return $this->sqliteAddColumnAtPosition($table, $column, $definition, 'after', $afterColumn);
    }

    /**
     * Add a column before a specific column
     *
     * SQLite doesn't support BEFORE natively, so we use table recreation
     * to position the column correctly.
     *
     * @param   string  $table         Table name (with or without prefix)
     * @param   string  $column        Column name
     * @param   string  $definition    Column definition
     * @param   string  $beforeColumn  Column to add before
     * @param   string  $comment       Optional column comment (ignored on SQLite)
     * @return  bool
     */
    public function addColumnBefore(
        string $table,
        string $column,
        string $definition,
        string $beforeColumn,
        string $comment = ''
    ): bool {
        return $this->sqliteAddColumnAtPosition($table, $column, $definition, 'before', $beforeColumn);
    }

    /**
     * Add a column at the beginning of a table
     *
     * SQLite doesn't support FIRST natively, so we use table recreation
     * to position the column correctly.
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional column comment (ignored on SQLite)
     * @return  bool
     */
    public function addColumnFirst(string $table, string $column, string $definition, string $comment = ''): bool
    {
        return $this->sqliteAddColumnAtPosition($table, $column, $definition, 'first', null);
    }

    /**
     * Add a column at the end of a table
     *
     * This is the default behavior for SQLite's ADD COLUMN.
     *
     * @param   string  $table       Table name (with or without prefix)
     * @param   string  $column      Column name
     * @param   string  $definition  Column definition
     * @param   string  $comment     Optional column comment (ignored on SQLite)
     * @return  bool
     */
    public function addColumnLast(string $table, string $column, string $definition, string $comment = ''): bool
    {
        return $this->addColumn($table, $column, $definition, $comment);
    }

    /**
     * Drop a column from a table
     *
     * SQLite 3.35.0+ supports ALTER TABLE DROP COLUMN natively.
     * For older versions, we use table recreation.
     *
     * Note: Indexes that reference the dropped column must be removed first,
     * as SQLite's DROP COLUMN doesn't automatically handle this.
     *
     * @param   string  $table   Table name (with or without prefix)
     * @param   string  $column  Column name
     * @return  bool
     */
    public function dropColumn(string $table, string $column): bool
    {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return true; // Table doesn't exist, nothing to drop
        }

        if (!$this->tableHasField($table, $column)) {
            return true; // Column doesn't exist, nothing to drop
        }

        // First, drop any indexes that reference this column
        // (required for both native DROP COLUMN and table recreation)
        $this->dropIndexesForColumn($table, $column);

        // Use native DROP COLUMN if SQLite version supports it (3.35.0+)
        if ($this->supportsDropColumn()) {
            $query = "ALTER TABLE `$table` DROP COLUMN `$column`";
            $this->setQuery($query);
            return (bool) $this->execute();
        }

        // For older SQLite versions, use table recreation
        return $this->sqliteDropColumnViaRecreate($table, $column);
    }

    /**
     * Drop or rebuild indexes that reference a specific column
     *
     * For single-column indexes on the dropped column, the index is removed.
     * For multi-column indexes, the index is rebuilt without the dropped column.
     *
     * @param   string  $table   Table name (already prefix-replaced)
     * @param   string  $column  Column name
     * @return  void
     */
    protected function dropIndexesForColumn(string $table, string $column): void
    {
        $indexes = $this->getIndexes($table);

        foreach ($indexes as $indexName => $indexInfo) {
            // Skip auto-generated indexes (sqlite_autoindex_*) - they're managed by SQLite
            if (strpos($indexName, 'sqlite_autoindex_') === 0) {
                continue;
            }

            // Check if this index references the column we're dropping
            if (!in_array($column, $indexInfo->columns)) {
                continue;
            }

            // Drop the existing index
            $this->setQuery("DROP INDEX IF EXISTS `$indexName`");
            $this->execute();

            // For multi-column indexes, rebuild without the dropped column
            $remainingColumns = array_filter($indexInfo->columns, function ($col) use ($column) {
                return $col !== $column;
            });

            if (!empty($remainingColumns)) {
                $columnList = '`' . implode('`, `', $remainingColumns) . '`';
                $uniqueStr = $indexInfo->unique ? 'UNIQUE ' : '';
                $this->setQuery("CREATE {$uniqueStr}INDEX `$indexName` ON `$table` ($columnList)");
                $this->execute();
            }
        }
    }

    /**
     * Drop a column via table recreation (for SQLite < 3.35.0)
     *
     * @param   string  $table   Table name (already prefix-replaced)
     * @param   string  $column  Column name to drop
     * @return  bool
     */
    protected function sqliteDropColumnViaRecreate(string $table, string $column): bool
    {
        // Get current table info
        $this->setQuery("PRAGMA table_info(`$table`)");
        $columns = $this->loadObjectList();

        if (empty($columns)) {
            return false;
        }

        // Get existing indexes (we'll need to recreate them)
        $indexes = $this->getIndexes($table);

        // Build column definitions for new table without the dropped column
        $newColumnDefs = [];
        $selectColumns = [];

        foreach ($columns as $col) {
            // Skip the column we're dropping
            if ($col->name === $column) {
                continue;
            }

            // Keep existing column as-is
            $type = $col->type;
            $notNull = $col->notnull ? ' NOT NULL' : '';
            $default = '';
            if ($col->dflt_value !== null) {
                $default = " DEFAULT " . $col->dflt_value;
            }
            $pk = $col->pk ? ' PRIMARY KEY' : '';

            $newColumnDefs[] = "`{$col->name}` {$type}{$notNull}{$default}{$pk}";
            $selectColumns[] = "`{$col->name}`";
        }

        if (empty($newColumnDefs)) {
            return false; // Can't drop the last column
        }

        $tempTable = $table . '_migration_temp_' . time();

        // Start transaction
        $this->setQuery('BEGIN TRANSACTION');
        $this->execute();

        try {
            // Create new table without the dropped column
            $createSql = "CREATE TABLE `$tempTable` (\n  " . implode(",\n  ", $newColumnDefs) . "\n)";
            $this->setQuery($createSql);
            $this->execute();

            // Copy data (excluding the dropped column)
            $copySql = "INSERT INTO `$tempTable` ("
                . implode(', ', $selectColumns) . ") SELECT "
                . implode(', ', $selectColumns)
                . " FROM `$table`";
            $this->setQuery($copySql);
            $this->execute();

            // Drop old table
            $this->setQuery("DROP TABLE `$table`");
            $this->execute();

            // Rename new table
            $this->setQuery("ALTER TABLE `$tempTable` RENAME TO `$table`");
            $this->execute();

            // Recreate indexes (excluding those that referenced the dropped column)
            foreach ($indexes as $indexName => $indexInfo) {
                // Skip auto-generated indexes (sqlite_autoindex_*) and PRIMARY
                if (strpos($indexName, 'sqlite_autoindex_') === 0 || $indexName === 'PRIMARY') {
                    continue;
                }

                // Skip indexes that included the dropped column
                if (in_array($column, $indexInfo->columns)) {
                    continue;
                }

                $columnList = '`' . implode('`, `', $indexInfo->columns) . '`';
                $uniqueStr = $indexInfo->unique ? 'UNIQUE ' : '';
                $indexSql = "CREATE {$uniqueStr}INDEX IF NOT EXISTS `$indexName` ON `$table` ($columnList)";
                $this->setQuery($indexSql);
                $this->execute();
            }

            // Commit
            $this->setQuery('COMMIT');
            $this->execute();

            return true;
        } catch (\Exception $e) {
            // Rollback on error
            $this->setQuery('ROLLBACK');
            $this->execute();

            // Clean up temp table if it exists
            $this->setQuery("DROP TABLE IF EXISTS `$tempTable`");
            $this->execute();

            return false;
        }
    }

    /**
     * SQLite column addition at specific position via table recreation
     *
     * SQLite doesn't support ADD COLUMN with position, so we must:
     * 1. Get current table schema
     * 2. Create new table with column at desired position
     * 3. Copy data
     * 4. Drop old table
     * 5. Rename new table
     * 6. Recreate indexes
     *
     * @param   string       $table           Table name
     * @param   string       $column          New column name
     * @param   string       $definition      Column definition
     * @param   string       $position        Position type: 'first', 'after', 'before'
     * @param   string|null  $referenceColumn Column to position relative to (for after/before)
     * @return  bool
     */
    protected function sqliteAddColumnAtPosition(
        string $table,
        string $column,
        string $definition,
        string $position,
        ?string $referenceColumn
    ): bool {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        // Column already exists
        if ($this->tableHasField($table, $column)) {
            return true;
        }

        // Get current table info
        $this->setQuery("PRAGMA table_info(`$table`)");
        $columns = $this->loadObjectList();

        if (empty($columns)) {
            return false;
        }

        // Get existing indexes (we'll need to recreate them)
        $indexes = $this->getIndexes($table);

        // Build column definitions for new table with column at correct position
        $newColumnDefs = [];
        $selectColumns = [];
        $columnInserted = false;

        // For 'first' position, insert the new column at the beginning
        if ($position === 'first') {
            $newColumnDefs[] = "`$column` $definition";
            $columnInserted = true;
        }

        foreach ($columns as $col) {
            // For 'before', insert new column before the reference column
            if ($position === 'before' && $col->name === $referenceColumn && !$columnInserted) {
                $newColumnDefs[] = "`$column` $definition";
                $columnInserted = true;
            }

            // Keep existing column as-is
            $type = $col->type;
            $notNull = $col->notnull ? ' NOT NULL' : '';
            $default = '';
            if ($col->dflt_value !== null) {
                $default = " DEFAULT " . $col->dflt_value;
            }
            $pk = $col->pk ? ' PRIMARY KEY' : '';

            $newColumnDefs[] = "`{$col->name}` {$type}{$notNull}{$default}{$pk}";
            $selectColumns[] = "`{$col->name}`";

            // For 'after', insert new column after the reference column
            if ($position === 'after' && $col->name === $referenceColumn && !$columnInserted) {
                $newColumnDefs[] = "`$column` $definition";
                $columnInserted = true;
            }
        }

        // If reference column not found (or position is 'last'), add at end
        if (!$columnInserted) {
            $newColumnDefs[] = "`$column` $definition";
        }

        $tempTable = $table . '_migration_temp_' . time();

        // Start transaction
        $this->setQuery('BEGIN TRANSACTION');
        $this->execute();

        try {
            // Create new table with column at desired position
            $createSql = "CREATE TABLE `$tempTable` (\n  " . implode(",\n  ", $newColumnDefs) . "\n)";
            $this->setQuery($createSql);
            $this->execute();

            // Copy existing data (new column will get default value or NULL)
            $copySql = "INSERT INTO `$tempTable` ("
                . implode(', ', $selectColumns) . ") SELECT "
                . implode(', ', $selectColumns)
                . " FROM `$table`";
            $this->setQuery($copySql);
            $this->execute();

            // Drop old table
            $this->setQuery("DROP TABLE `$table`");
            $this->execute();

            // Rename new table
            $this->setQuery("ALTER TABLE `$tempTable` RENAME TO `$table`");
            $this->execute();

            // Recreate indexes (excluding auto-created ones and PRIMARY)
            foreach ($indexes as $indexName => $indexInfo) {
                // Skip auto-generated indexes (sqlite_autoindex_*) and PRIMARY
                if (strpos($indexName, 'sqlite_autoindex_') === 0 || $indexName === 'PRIMARY') {
                    continue;
                }

                $columnList = '`' . implode('`, `', $indexInfo->columns) . '`';
                $uniqueStr = $indexInfo->unique ? 'UNIQUE ' : '';
                $indexSql = "CREATE {$uniqueStr}INDEX IF NOT EXISTS `$indexName` ON `$table` ($columnList)";
                $this->setQuery($indexSql);
                $this->execute();
            }

            // Commit
            $this->setQuery('COMMIT');
            $this->execute();

            return true;
        } catch (\Exception $e) {
            // Rollback on error
            $this->setQuery('ROLLBACK');
            $this->execute();

            // Clean up temp table if it exists
            $this->setQuery("DROP TABLE IF EXISTS `$tempTable`");
            $this->execute();

            return false;
        }
    }

    /**
     * Set the storage engine for a table
     *
     * SQLite has only one storage engine - this is a no-op.
     *
     * @note    NO-OP: SQLite uses a single embedded storage engine.
     * @see     supportsEngine() to detect if this operation is available
     *
     * @param   string  $table   Table name (with or without prefix)
     * @param   string  $engine  Engine type (ignored on SQLite)
     * @return  bool
     */
    public function setTableEngine(string $table, string $engine = 'MYISAM'): bool
    {
        // SQLite doesn't have storage engines - no-op
        return true;
    }

    /**
     * Set the character set and collation for a table
     *
     * SQLite uses UTF-8 by default and doesn't support changing character sets.
     * This is a no-op for SQLite compatibility.
     *
     * @note    NO-OP: SQLite uses UTF-8 encoding by default.
     * @see     supportsTableCharset() to detect if this operation is available
     *
     * @param   string  $table      Table name (with or without prefix)
     * @param   string  $charset    Character set (e.g., 'utf8')
     * @param   string  $collation  Collation (e.g., 'utf8_general_ci')
     * @return  bool
     */
    public function setTableCharset(
        string $table,
        string $charset = 'utf8',
        string $collation = 'utf8_general_ci'
    ): bool {
        // SQLite uses UTF-8 by default - no-op
        return true;
    }

    /**
     * Add a FULLTEXT index to a table
     *
     * SQLite doesn't support FULLTEXT indexes - creates a regular index instead.
     * For true full-text search on SQLite, consider using FTS5 virtual tables.
     *
     * @param   string        $table    Table name (with or without prefix)
     * @param   string        $name     Index name
     * @param   string|array  $columns  Column name(s) to index
     * @return  bool
     */
    public function addFulltextIndex(string $table, string $name, $columns): bool
    {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return false;
        }

        if ($this->tableHasKey($table, $name)) {
            return true; // Index already exists
        }

        if (is_string($columns)) {
            $columns = [$columns];
        }

        $columnList = '`' . implode('`, `', $columns) . '`';

        // Create regular index instead of FULLTEXT
        $query = "CREATE INDEX IF NOT EXISTS `$name` ON `$table` ($columnList)";
        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Drop the primary key from a table
     *
     * SQLite doesn't support ALTER TABLE DROP PRIMARY KEY - this is a no-op.
     * Changing primary keys in SQLite requires recreating the table.
     *
     * @param   string  $table  Table name (with or without prefix)
     * @return  bool
     */
    public function dropPrimaryKey(string $table): bool
    {
        // SQLite doesn't support DROP PRIMARY KEY - no-op
        return true;
    }

    /**
     * Add a primary key to a table
     *
     * SQLite doesn't support ADD PRIMARY KEY via ALTER TABLE.
     * This would require recreating the table.
     *
     * @param   string        $table    Table name (with or without prefix)
     * @param   string|array  $columns  Column name(s) for the primary key
     * @return  bool
     */
    public function addPrimaryKey(string $table, $columns): bool
    {
        // SQLite doesn't support ADD PRIMARY KEY - would require table recreation
        // This is a no-op for SQLite, returning true to allow migrations to proceed
        return true;
    }

    /**
     * Add an auto-increment primary key column to a table
     *
     * SQLite uses INTEGER PRIMARY KEY which auto-increments via ROWID.
     * Must be exactly "INTEGER PRIMARY KEY" (not INT, not BIGINT).
     *
     * @param   string  $table      Table name (with or without prefix)
     * @param   string  $column     Column name (usually 'id')
     * @param   bool    $first      Add as first column (ignored on SQLite)
     * @param   bool    $useBigInt  Use BIGINT/SERIAL (ignored on SQLite)
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
            return true; // Column already exists
        }

        // SQLite doesn't support adding PRIMARY KEY via ALTER TABLE.
        // We need to rebuild the table with the new column.
        $tempTable = $table . '_rebuild_' . time();

        // Get current table structure
        $this->setQuery("PRAGMA table_info(`$table`)");
        $currentColumns = $this->loadObjectList();

        if (empty($currentColumns)) {
            return false;
        }

        // Build new column definitions with the auto-increment ID column first (if $first) or last
        $columnDefs = [];
        $columnNames = [];

        // If $first, add the ID column first
        if ($first) {
            $columnDefs[] = "`$column` INTEGER PRIMARY KEY AUTOINCREMENT";
        }

        // Add existing columns
        foreach ($currentColumns as $col) {
            $type = $col->type ?: 'TEXT';
            $notNull = $col->notnull ? ' NOT NULL' : '';
            $default = '';
            if ($col->dflt_value !== null) {
                $default = ' DEFAULT ' . $col->dflt_value;
            }
            $columnDefs[] = "`{$col->name}` {$type}{$notNull}{$default}";
            $columnNames[] = "`{$col->name}`";
        }

        // If not $first, add the ID column last
        if (!$first) {
            $columnDefs[] = "`$column` INTEGER PRIMARY KEY AUTOINCREMENT";
        }

        // Get current indexes (excluding auto-created ones)
        $this->setQuery("PRAGMA index_list(`$table`)");
        $currentIndexes = $this->loadObjectList();

        $indexesToRecreate = [];
        foreach ($currentIndexes as $index) {
            if (strpos($index->name, 'sqlite_autoindex_') === 0) {
                continue;
            }
            $this->setQuery("PRAGMA index_info(`{$index->name}`)");
            $indexColumns = $this->loadObjectList();
            $indexColNames = [];
            foreach ($indexColumns as $col) {
                $indexColNames[] = $col->name;
            }
            if (!empty($indexColNames)) {
                $indexesToRecreate[] = [
                    'name' => $index->name,
                    'unique' => (bool) $index->unique,
                    'columns' => $indexColNames,
                ];
            }
        }

        // Execute table rebuild in transaction
        $this->transactionStart();

        try {
            // Create new table
            $createSql = "CREATE TABLE `$tempTable` (\n  " . implode(",\n  ", $columnDefs) . "\n)";
            $this->setQuery($createSql);
            $this->execute();

            // Copy data (ID column will auto-populate)
            $selectList = implode(', ', $columnNames);
            $this->setQuery("INSERT INTO `$tempTable` ($selectList) SELECT $selectList FROM `$table`");
            $this->execute();

            // Drop old table
            $this->setQuery("DROP TABLE `$table`");
            $this->execute();

            // Rename new table
            $this->setQuery("ALTER TABLE `$tempTable` RENAME TO `$table`");
            $this->execute();

            // Recreate indexes
            foreach ($indexesToRecreate as $index) {
                $unique = $index['unique'] ? 'UNIQUE ' : '';
                $columnList = '`' . implode('`, `', $index['columns']) . '`';
                $this->setQuery("CREATE {$unique}INDEX `{$index['name']}` ON `$table` ($columnList)");
                $this->execute();
            }

            $this->transactionCommit();
            return true;
        } catch (\Exception $e) {
            $this->transactionRollback();
            return false;
        }
    }

    /**
     * Populate a column with sequential integer values for existing rows
     *
     * Uses SQLite's rowid and ROW_NUMBER() window function (SQLite 3.25+).
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

        $orderClause = $orderBy ? "`$orderBy`" : 'rowid';

        // Use SQLite's rowid and ROW_NUMBER() window function (requires SQLite 3.25+)
        $query = "UPDATE `$table` SET `$column` = (" .
                 "SELECT rn FROM (" .
                 "SELECT rowid, ROW_NUMBER() OVER (ORDER BY $orderClause) AS rn FROM `$table`" .
                 ") sub WHERE sub.rowid = `$table`.rowid)";

        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Add an index to a table
     *
     * SQLite uses CREATE INDEX instead of ALTER TABLE ADD INDEX.
     *
     * @param   string        $table    Table name (with or without prefix)
     * @param   string        $name     Index name
     * @param   string|array  $columns  Column name(s) to index
     * @param   bool          $unique   Whether to create a unique index
     * @return  bool
     */
    protected function buildCreateIndexSql(string $table, string $name, array $columns, bool $unique): string
    {
        $columnList = '`' . implode('`, `', $columns) . '`';
        $uniqueStr = $unique ? 'UNIQUE ' : '';
        return "CREATE {$uniqueStr}INDEX IF NOT EXISTS `$name` ON `$table` ($columnList)";
    }

    /**
     * Drop an index from a table
     *
     * SQLite uses DROP INDEX without table name (indexes are database-scoped).
     *
     * @param   string  $table  Table name (with or without prefix, used for existence check)
     * @param   string  $name   Index name
     * @return  bool
     */
    public function dropIndex(string $table, string $name): bool
    {
        $table = $this->replacePrefix($table);

        if (!$this->tableExists($table)) {
            return true; // Table doesn't exist, nothing to drop
        }

        if (!$this->tableHasKey($table, $name)) {
            return true; // Index doesn't exist, nothing to drop
        }

        // SQLite: DROP INDEX doesn't need table name (indexes are database-scoped)
        $query = "DROP INDEX IF EXISTS `$name`";
        $this->setQuery($query);
        return (bool) $this->execute();
    }

    /**
     * Get the schema grammar instance for SQLite
     *
     * @return  \Hubzero\Database\Drivers\Base\BaseSchemaGrammar
     */
    public function getSchemaGrammar()
    {
        return $this->makeSchemaGrammarFromRegistry();
    }


    /**
     * Convert TableInfo to PRAGMA table_info structure
     *
     * Converts ColumnInfo objects from TableInfo into the same structure
     * that PRAGMA table_info returns, for use in table rebuild operations.
     *
     * @param   \Hubzero\Database\Schema\TableInfo  $tableInfo  Table information
     * @return  array  Array of objects with PRAGMA table_info structure
     */
    protected function convertTableInfoToColumnStructure(\Hubzero\Database\Schema\TableInfo $tableInfo): array
    {
        $columns = [];
        $pkIndex = $tableInfo->getPrimaryKeyIndex();
        $pkColumns = $pkIndex ? $pkIndex->getColumns() : [];
        $cid = 0;

        foreach ($tableInfo->getColumns() as $columnInfo) {
            $obj = new \stdClass();
            $obj->cid = $cid++;
            $obj->name = $columnInfo->getName();
            $obj->type = strtoupper($columnInfo->getFullType());
            $obj->notnull = !$columnInfo->isNullable() ? 1 : 0;
            $obj->dflt_value = $columnInfo->getDefault();

            // Check if this column is part of the primary key
            $pkIndex = array_search($columnInfo->getName(), $pkColumns);
            $obj->pk = $pkIndex !== false ? ($pkIndex + 1) : 0;

            $columns[] = $obj;
        }

        return $columns;
    }

    /**
     * Rebuild a SQLite table to apply unsupported schema changes
     *
     * @param   string  $table    Table name (already prefixed)
     * @param   \Hubzero\Database\Schema\AlterTableBuilder  $builder  The table builder
     * @return  array  Array of SQL statements
     */
    protected function buildSqliteTableRebuild(
        string $table,
        \Hubzero\Database\Schema\AlterTableBuilder $builder
    ): array {
        $statements = [];
        $tempTable = $table . '_rebuild_' . time();

        // Get current table structure from TableInfo if available, otherwise query database
        $sourceTableInfo = $builder->getSourceTableInfo();
        if ($sourceTableInfo !== null) {
            // Use TableInfo from schema comparison
            $currentColumns = $this->convertTableInfoToColumnStructure($sourceTableInfo);
        } else {
            // Query actual database table
            $this->setQuery("PRAGMA table_info(`$table`)");
            $currentColumns = $this->loadObjectList();
        }

        if (empty($currentColumns)) {
            return $statements;
        }

        // Get current indexes from TableInfo if available, otherwise query database
        $indexesToRecreate = [];
        if ($sourceTableInfo !== null) {
            // Use indexes from TableInfo
            foreach ($sourceTableInfo->getIndexes() as $indexInfo) {
                if ($indexInfo->isPrimary()) {
                    continue; // Skip primary key indexes
                }

                $columnNames = [];
                foreach ($indexInfo->getColumns() as $colName) {
                    if (isset($builder->getRenameColumns()[$colName])) {
                        $colName = $builder->getRenameColumns()[$colName]['newName'];
                    }
                    if (!in_array($colName, $builder->getDropColumns())) {
                        $columnNames[] = $colName;
                    }
                }

                if (!empty($columnNames)) {
                    $indexesToRecreate[] = [
                        'name' => $indexInfo->getName(),
                        'unique' => $indexInfo->isUnique(),
                        'columns' => $columnNames,
                    ];
                }
            }
        } else {
            // Query actual database table
            $this->setQuery("PRAGMA index_list(`$table`)");
            $currentIndexes = $this->loadObjectList();

            foreach ($currentIndexes as $index) {
                if (strpos($index->name, 'sqlite_autoindex_') === 0) {
                    continue;
                }

                $this->setQuery("PRAGMA index_info(`{$index->name}`)");
                $indexColumns = $this->loadObjectList();

                $columnNames = [];
                foreach ($indexColumns as $col) {
                    $colName = $col->name;
                    if (isset($builder->getRenameColumns()[$colName])) {
                        $colName = $builder->getRenameColumns()[$colName]['newName'];
                    }
                    if (!in_array($col->name, $builder->getDropColumns())) {
                        $columnNames[] = $colName;
                    }
                }

                if (!empty($columnNames)) {
                    $indexesToRecreate[] = [
                        'name' => $index->name,
                        'unique' => (bool) $index->unique,
                        'columns' => $columnNames,
                    ];
                }
            }
        }

        // Determine primary key columns
        $pkColumns = [];
        if ($builder->getAddPrimaryKey() !== null) {
            $pkColumns = $builder->getAddPrimaryKey();
        } elseif (!$builder->getDropPrimaryKeyFlag()) {
            foreach ($currentColumns as $col) {
                if ($col->pk > 0 && !in_array($col->name, $builder->getDropColumns())) {
                    $colName = isset($builder->getRenameColumns()[$col->name])
                        ? $builder->getRenameColumns()[$col->name]['newName']
                        : $col->name;
                    $pkColumns[$col->pk] = $colName;
                }
            }
            ksort($pkColumns);
            $pkColumns = array_values($pkColumns);
        }

        // Build new column definitions
        $newColumnDefs = [];
        $selectColumns = [];
        $targetColumns = [];

        foreach ($currentColumns as $col) {
            if (in_array($col->name, $builder->getDropColumns())) {
                continue;
            }

            $colName = $col->name;

            if (isset($builder->getRenameColumns()[$col->name])) {
                $colName = $builder->getRenameColumns()[$col->name]['newName'];
            }

            if (isset($builder->getModifyColumns()[$col->name])) {
                $def = $builder->getModifyColumns()[$col->name];
                $colDef = $this->buildAlterColumnDefinition($colName, $def);
            } else {
                $colDef = "`$colName` {$col->type}";
                if ($col->notnull && $col->dflt_value === null) {
                    $colDef .= ' NOT NULL';
                }
                if ($col->dflt_value !== null) {
                    $colDef .= ' DEFAULT ' . $col->dflt_value;
                }
            }

            $newColumnDefs[] = $colDef;
            $selectColumns[] = "`{$col->name}`";
            $targetColumns[] = "`$colName`";
        }

        foreach ($builder->getAddColumns() as $name => $definition) {
            $newColumnDefs[] = $this->buildAlterColumnDefinition($name, $definition);
        }

        if (!empty($pkColumns)) {
            $pkList = '`' . implode('`, `', $pkColumns) . '`';
            $newColumnDefs[] = "PRIMARY KEY ($pkList)";
        }

        // Execute rebuild
        $statements[] = "CREATE TABLE `$tempTable` (" . implode(', ', $newColumnDefs) . ")";

        if (!empty($selectColumns)) {
            $statements[] = "INSERT INTO `$tempTable` ("
                . implode(', ', $targetColumns) . ") SELECT "
                . implode(', ', $selectColumns)
                . " FROM `$table`";
        }

        $statements[] = "DROP TABLE " . $this->quoteName($table);
        $statements[] = "ALTER TABLE " . $this->quoteName($tempTable) . " RENAME TO " . $this->quoteName($table);

        foreach ($indexesToRecreate as $index) {
            $columns = array_map([$this, 'quoteName'], $index['columns']);
            $columnList = implode(', ', $columns);
            $unique = $index['unique'] ? 'UNIQUE ' : '';
            $statements[] = "CREATE {$unique}INDEX IF NOT EXISTS "
                . $this->quoteName($index['name'])
                . " ON " . $this->quoteName($table)
                . " ($columnList)";
        }

        foreach ($builder->getAddIndexes() as $name => $info) {
            $columns = array_map([$this, 'quoteName'], $info['columns']);
            $columnList = implode(', ', $columns);
            $unique = $info['unique'] ? 'UNIQUE ' : '';
            $statements[] = "CREATE {$unique}INDEX IF NOT EXISTS "
                . $this->quoteName($name)
                . " ON " . $this->quoteName($table)
                . " ($columnList)";
        }

        return $statements;
    }

    // =========================================================================
    // Important Overrides
    // =========================================================================

    /**
     * Get CHECK constraints for table
     *
     * SQLite stores CHECK constraints in the CREATE TABLE statement.
     * We need to parse sqlite_master to find them.
     *
     * @param   string  $table  Table name
     * @return  array
     */
    public function getCheckConstraints(string $table): array
    {
        $table = $this->replacePrefix($table);
        $this->setQuery("SELECT sql FROM sqlite_master WHERE type='table' AND name=" . $this->quote($table));
        $sql = $this->loadResult();

        if (!$sql) {
            return [];
        }

        $constraints = [];
        // Basic regex to find CHECK (...) patterns
        // This is imperfect but works for simple standard definitions
        if (preg_match_all('/CONSTRAINT\s+["`]?(\w+)["`]?\s+CHECK\s*\(([^,]+)\)/i', $sql, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $constraints[] = (object) [
                    'name' => $match[1],
                    'expression' => $match[2]
                ];
            }
        }

        return $constraints;
    }

    /**
     * Get table column information via PRAGMA table_info
     *
     * Returns an array of column information objects with cid, name, type,
     * notnull, dflt_value, and pk properties. Used by Grammar for table
     * rebuild operations.
     *
     * @param   string  $table  Table name
     * @return  array   Array of column info objects
     */
    public function getTableColumnInfo(string $table): array
    {
        $table = $this->replacePrefix($table);
        $this->setQuery("PRAGMA table_info(" . $this->quoteName($table) . ")");
        return $this->loadObjectList() ?: [];
    }

    /**
     * Get table index information via PRAGMA index_list
     *
     * Returns an array of index information objects with seq, name, unique,
     * origin, and partial properties. Used by Grammar for table rebuild
     * operations.
     *
     * @param   string  $table  Table name
     * @return  array   Array of index info objects
     */
    public function getTableIndexList(string $table): array
    {
        $table = $this->replacePrefix($table);
        $this->setQuery("PRAGMA index_list(" . $this->quoteName($table) . ")");
        return $this->loadObjectList() ?: [];
    }

    /**
     * Get index column information via PRAGMA index_info
     *
     * Returns an array of column information objects for the specified index
     * with seqno, cid, and name properties. Used by Grammar for table rebuild
     * operations.
     *
     * @param   string  $index  Index name
     * @return  array   Array of column info objects
     */
    public function getIndexColumnInfo(string $index): array
    {
        $this->setQuery("PRAGMA index_info(" . $this->quoteName($index) . ")");
        return $this->loadObjectList() ?: [];
    }

    /**
     * Add stored (computed) column
     *
     * SQLite 3.31+ supports GENERATED ALWAYS AS ... STORED
     *
     * @param   string       $table       Table name
     * @param   string       $column      Column name
     * @param   string       $expression  SQL expression
     * @param   string|null  $type        Column type (ignored by SQLite)
     * @return  bool
     */
    public function addStoredColumn(string $table, string $column, string $expression, ?string $type = null): bool
    {
        // Simple version check before attempting syntax
        $version = $this->getVersion();
        if (version_compare($version, '3.31.0', '<')) {
            throw new \RuntimeException('Generated columns require SQLite 3.31.0+');
        }

        $table = $this->replacePrefix($table);
        $this->setQuery("ALTER TABLE $table ADD COLUMN $column GENERATED ALWAYS AS ($expression) STORED");
        return $this->execute();
    }

    /**
     * Add virtual (computed) column
     *
     * SQLite 3.31+ supports GENERATED ALWAYS AS ... VIRTUAL
     *
     * @param   string       $table       Table name
     * @param   string       $column      Column name
     * @param   string       $expression  SQL expression
     * @param   string|null  $type        Column type (ignored by SQLite)
     * @return  bool
     */
    public function addVirtualColumn(string $table, string $column, string $expression, ?string $type = null): bool
    {
        // Simple version check
        $version = $this->getVersion();
        if (version_compare($version, '3.31.0', '<')) {
            throw new \RuntimeException('Generated columns require SQLite 3.31.0+');
        }

        $table = $this->replacePrefix($table);
        $this->setQuery("ALTER TABLE $table ADD COLUMN $column GENERATED ALWAYS AS ($expression) VIRTUAL");
        return $this->execute();
    }

    /**
     * Get global configuration variables
     *
     * Uses PRAGMA statments to get current settings
     *
     * @return  array
     */
    public function getGlobalVariables(): array
    {
        // SQLite doesn't have a single "show variables" command
        // We fetch a few common pragmas relevant to configuration
        $vars = [];
        $pragmas = ['journal_mode', 'synchronous', 'foreign_keys', 'encoding', 'page_size'];

        foreach ($pragmas as $pragma) {
            try {
                $this->setQuery("PRAGMA $pragma");
                $val = $this->loadResult();
                $vars[$pragma] = $val;
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        return $vars;
    }

    /**
     * Get server uptime
     *
     * SQLite is file-based/embedded, so uptime isn't applicable in the server sense.
     * We return null to indicate this metric is not available.
     *
     * @return  int|null
     */
    public function getUptime(): ?int
    {
        return null;
    }
}
