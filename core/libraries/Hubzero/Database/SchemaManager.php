<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

use Hubzero\Database\Schema\Table;
use Hubzero\Database\Schema\TableBuilder;
use Hubzero\Database\Schema\AlterTableBuilder;
use Hubzero\Database\Schema\ColumnBuilder;
use Hubzero\Database\Schema\ViewBuilder;
use Hubzero\Database\Schema\DatabaseInfo;
use Hubzero\Database\Schema\TableInfo;
use Hubzero\Database\Schema\ColumnInfo;
use Hubzero\Database\Schema\IndexInfo;
use Hubzero\Database\Schema\ForeignKeyInfo;
use Hubzero\Database\Schema\Comparator;
use Hubzero\Database\Schema\DiffSqlGenerator;
use Hubzero\Database\Schema\Diff\SchemaDiff;
use Hubzero\Database\Schema\Diff\TableDiff;

/**
 * Database Schema Manager
 *
 * Provides a unified interface for all schema operations including:
 * - Creating and altering tables (fluent builders)
 * - Dropping tables
 * - Table introspection (columns, keys, existence checks)
 * - Database introspection (full schema as portable value objects)
 * - Index management
 *
 * Access via the driver's schema() method:
 * ```php
 * // Create a table
 * $db->schema()->createTable('#__users')
 *     ->id()
 *     ->string('name', 255)
 *     ->string('email', 255)->unique()
 *     ->timestamps()
 *     ->execute();
 *
 * // Check if table exists
 * if ($db->schema()->tableExists('#__users')) {
 *     // ...
 * }
 *
 * // Get table columns
 * $columns = $db->schema()->getTableColumns('#__users');
 *
 * // Introspect entire database schema
 * $dbSchema = $db->schema()->introspectDatabase();
 * foreach ($dbSchema->getTables() as $table) {
 *     echo $table->getName();
 * }
 *
 * // Drop a table
 * $db->schema()->dropTable('#__old_table');
 * ```
 *
 */
class SchemaManager
{
    /**
     * Database driver instance
     *
     * @var Driver
     */
    protected $driver;

    /**
     * Create a new schema manager
     *
     * @param  Driver  $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Get the database driver
     *
     * @return Driver
     */
    public function getDriver(): Driver
    {
        return $this->driver;
    }

    // =========================================================================
    // Table Creation and Alteration (Fluent Builders)
    // =========================================================================

    /**
     * Create a fluent table builder for CREATE TABLE statements
     *
     * Usage:
     *   $db->schema()->createTable('#__my_table')
     *       ->id()
     *       ->string('name', 255)->nullable()
     *       ->integer('status')->default(0)
     *       ->timestamps()
     *       ->primaryKey('id')
     *       ->index('idx_status', 'status')
     *       ->engine('InnoDB')
     *       ->charset('utf8')
     *       ->execute();
     *
     * @param   string  $table  Table name (can include prefix placeholder #__)
     * @return  TableBuilder
     */
    public function createTable(string $table): TableBuilder
    {
        return new TableBuilder($this->driver, $table);
    }

    /**
     * Create a fluent table builder for ALTER TABLE statements
     *
     * Usage:
     *   $db->schema()->alterTable('#__my_table')
     *       ->addColumn('email', 'VARCHAR(255)')->nullable()->after('name')
     *       ->dropColumn('old_field')
     *       ->addIndex('idx_email', 'email')
     *       ->execute();
     *
     * @param   string  $table  Table name (can include prefix placeholder #__)
     * @return  AlterTableBuilder
     */
    public function alterTable(string $table): AlterTableBuilder
    {
        return new AlterTableBuilder($this->driver, $table);
    }

    /**
     * Get a Table gateway for fluent table operations
     *
     * The Table class acts as a factory for builders and provides introspection:
     *   - table()->create() returns TableBuilder for CREATE TABLE
     *   - table()->alter() returns AlterTableBuilder for ALTER TABLE
     *   - table()->exists(), hasColumn(), etc. for introspection
     *
     * Usage:
     *   // Create a new table
     *   $db->schema()->table('#__users')->create()
     *       ->id()
     *       ->string('name', 255)
     *       ->timestamps()
     *       ->execute();
     *
     *   // Alter an existing table
     *   $db->schema()->table('#__users')->alter()
     *       ->addColumn('email', 'VARCHAR(255)')
     *       ->dropColumn('old_field')
     *       ->execute();
     *
     *   // Introspection
     *   if ($db->schema()->table('#__users')->exists()) {
     *       // ...
     *   }
     *
     * @param   string  $table  Table name (can include prefix placeholder #__)
     * @return  Table
     */
    public function table(string $table): Table
    {
        return new Table($this->driver, $table);
    }

    // =========================================================================
    // Table Operations
    // =========================================================================

    /**
     * Drop a table from the database
     *
     * @param   string  $table     The name of the database table to drop
     * @param   bool    $ifExists  Only drop if the table exists (default: true)
     * @return  $this
     */
    public function dropTable(string $table, bool $ifExists = true): self
    {
        $this->driver->dropTable($table, $ifExists);

        return $this;
    }

    /**
     * Rename a table in the database
     *
     * @param   string       $oldTable  The current table name
     * @param   string       $newTable  The new table name
     * @param   string|null  $backup    Table prefix (optional)
     * @param   string|null  $prefix    For the table - used to rename constraints in non-mysql databases
     * @return  $this
     */
    public function renameTable(
        string $oldTable,
        string $newTable,
        ?string $backup = null,
        ?string $prefix = null
    ): self {
        $this->driver->renameTable($oldTable, $newTable, $backup, $prefix);

        return $this;
    }

    // =========================================================================
    // Table Introspection
    // =========================================================================

    /**
     * Check if a table exists
     *
     * @param   string  $table  The table name to check
     * @return  bool
     */
    public function tableExists(string $table): bool
    {
        return $this->driver->tableExists($table);
    }

    /**
     * Check if a table does not exist
     *
     * Convenience method for cleaner conditional logic
     *
     * @param   string  $table  The table name to check
     * @return  bool
     */
    public function tableNotExists(string $table): bool
    {
        return !$this->driver->tableExists($table);
    }

    /**
     * Get column information for a table
     *
     * @param   string  $table     The table name
     * @param   bool    $typeOnly  True (default) to only return field types
     * @return  array
     */
    public function getTableColumns(string $table, bool $typeOnly = true): array
    {
        return $this->driver->getTableColumns($table, $typeOnly);
    }

    /**
     * Get the column names for a table
     *
     * @param   string  $table  The table name
     * @return  array
     */
    public function getColumnListing(string $table): array
    {
        return array_keys($this->driver->getTableColumns($table));
    }

    /**
     * Get complete table metadata as a structured TableInfo object
     *
     * This method consolidates multiple introspection calls into a single
     * structured object with typed column, index, and foreign key information.
     *
     * Usage:
     * ```php
     * $table = $schema->introspectTable('#__users');
     *
     * // Table properties
     * echo $table->getName();        // 'jos_users'
     * echo $table->getEngine();      // 'InnoDB'
     *
     * // Columns as ColumnInfo objects
     * foreach ($table->getColumns() as $column) {
     *     echo $column->getName() . ': ' . $column->getType();
     *     if ($column->isNullable()) {
     *         echo ' (nullable)';
     *     }
     * }
     *
     * // Get specific column
     * $emailCol = $table->getColumn('email');
     *
     * // Indexes as IndexInfo objects
     * foreach ($table->getIndexes() as $index) {
     *     echo $index->getName() . ': ' . implode(', ', $index->getColumns());
     * }
     *
     * // Foreign keys as ForeignKeyInfo objects
     * foreach ($table->getForeignKeys() as $fk) {
     *     echo $fk->getName() . ' -> ' . $fk->getForeignTable();
     * }
     * ```
     *
     * @param   string  $table  The table name
     * @return  TableInfo
     */
    public function introspectTable(string $table): TableInfo
    {
        // Get table name with prefix resolved
        $resolvedTable = $this->driver->replacePrefix($table);

        // Gather all metadata
        $columns = [];
        $rawColumns = $this->driver->getTableColumns($table, false);
        $pk = $this->driver->getPrimaryKey($table);

        foreach ($rawColumns as $name => $info) {
            // Convert stdClass to array if needed
            if (is_object($info)) {
                $info = (array) $info;
            }

            // Handle both string type (from typeOnly=true) and full info array
            if (is_string($info)) {
                $columnData = [
                    'name' => $name,
                    'full_type' => $info,
                    'nullable' => true,
                    'default' => null,
                    'auto_increment' => false,
                    'comment' => null,
                    'collation' => null,
                ];
            } else {
                $columnData = [
                    'name' => $name,
                    'full_type' => $info['Type'] ?? $info['type'] ?? '',
                    'nullable' => (($info['Null'] ?? $info['null'] ?? $info['notnull'] ?? 'YES') === 'YES') ||
                                  (isset($info['notnull']) && $info['notnull'] == 0),
                    'default' => $info['Default'] ?? $info['default'] ?? $info['dflt_value'] ?? null,
                    'auto_increment' =>
                        (isset($info['Extra'])
                            && stripos($info['Extra'], 'auto_increment') !== false)
                        || (isset($info['pk']) && $info['pk'] == 1),
                    'comment' => $info['Comment'] ?? $info['comment'] ?? null,
                    'collation' => $info['Collation'] ?? $info['collation'] ?? null,
                ];
            }

            $columnData['primary_key'] = ($pk === $name);

            $columns[] = $columnData;
        }

        // Gather indexes
        $indexes = [];
        $rawKeys = $this->driver->getTableKeys($table);
        $indexGroups = [];

        // getTableKeys() returns array grouped by index name: ['PRIMARY' => [...], 'idx_name' => [...]]
        // Each group contains an array of column info objects
        foreach ($rawKeys as $keyName => $keyColumns) {
            // Handle both flat arrays and grouped arrays
            $columnsArray = is_array($keyColumns) && isset($keyColumns[0]) ? $keyColumns : [$keyColumns];

            if (!isset($indexGroups[$keyName])) {
                // Get metadata from first column's info
                $firstCol = $columnsArray[0];
                $indexGroups[$keyName] = [
                    'name' => $keyName,
                    'columns' => [],
                    'primary' => $keyName === 'PRIMARY',
                    'unique' => is_array($firstCol)
                        ? (($firstCol['Non_unique'] ?? 1) == 0)
                        : (is_object($firstCol)
                            ? (($firstCol->Non_unique ?? 1) == 0)
                            : false),
                    'type' => is_array($firstCol)
                        ? ($firstCol['Index_type'] ?? 'BTREE')
                        : (is_object($firstCol)
                            ? ($firstCol->Index_type ?? 'BTREE')
                            : 'BTREE'),
                ];
            }

            // Add all columns for this index
            foreach ($columnsArray as $key) {
                $colName = is_array($key)
                    ? ($key['Column_name'] ?? '')
                    : (is_object($key)
                        ? ($key->Column_name ?? '')
                        : '');
                if ($colName) {
                    $indexGroups[$keyName]['columns'][] = $colName;
                }
            }
        }

        $indexes = array_values($indexGroups);

        // Gather foreign keys
        $foreignKeys = [];
        $rawFks = $this->driver->getForeignKeys($table);
        foreach ($rawFks as $fk) {
            $foreignKeys[] = is_array($fk) ? $fk : (array) $fk;
        }

        // Get table metadata
        $engine = $this->driver->getEngine($table);
        $charset = $this->driver->getCharacterSet($table);
        $autoIncrement = $this->driver->getAutoIncrement($table);
        $primaryKey = $this->driver->getPrimaryKey($table);

        return new TableInfo([
            'name' => $resolvedTable,
            'engine' => $engine !== false ? $engine : null,
            'charset' => $charset !== false ? $charset : null,
            'auto_increment' => $autoIncrement !== false ? $autoIncrement : null,
            'primary_key' => $primaryKey !== false ? $primaryKey : null,
            'columns' => $columns,
            'indexes' => $indexes,
            'foreign_keys' => $foreignKeys,
        ]);
    }

    /**
     * Introspect the entire database schema
     *
     * Returns a portable DatabaseInfo value object containing all tables
     * with their columns, indexes, and foreign keys. This representation
     * is driver-agnostic and can be used for schema comparison or
     * cross-database migration.
     *
     * Usage:
     * ```php
     * $schema = $db->schema()->introspectDatabase();
     *
     * // Database properties
     * echo $schema->getName();           // 'my_database'
     * echo $schema->getTableCount();     // 42
     *
     * // Iterate all tables
     * foreach ($schema->getTables() as $table) {
     *     echo $table->getName();
     *     foreach ($table->getColumns() as $column) {
     *         echo "  " . $column->getName() . ": " . $column->getType();
     *     }
     * }
     *
     * // Get specific table
     * $usersTable = $schema->getTable('jos_users');
     *
     * // Serialize for storage/comparison
     * $data = $schema->toArray();
     *
     * // With custom filter callback
     * $schema = $db->schema()->introspectDatabase('jos_', function($table) {
     *     return strpos($table, '_session') === false; // Exclude session tables
     * });
     * ```
     *
     * @param   string|null    $prefix  Optional table prefix filter (e.g., 'jos_')
     *                                  Only tables starting with this prefix will be included
     * @param   callable|null  $filter  Optional callback filter function.
     *                                  Receives table name, returns true to include, false to exclude.
     *                                  Applied after prefix filtering.
     * @return  DatabaseInfo
     */
    public function introspectDatabase(?string $prefix = null, ?callable $filter = null): DatabaseInfo
    {
        $databaseName = $this->driver->getDatabase();
        $tableList = $this->driver->getTableList();
        $tables = [];
        $firstIncludedTable = '';

        foreach ($tableList as $tableName) {
            // Filter by prefix if specified
            if ($prefix !== null && strpos($tableName, $prefix) !== 0) {
                continue;
            }

            // Apply custom filter callback if provided
            if ($filter !== null && !$filter($tableName)) {
                continue;
            }

            if ($firstIncludedTable === '') {
                $firstIncludedTable = $tableName;
            }
            $tables[] = $this->introspectTable($tableName);
        }

        // Get charset (some drivers like SQLite require a table name argument even if unused)
        $charset = null;
        $collation = null;
        try {
            // Use first table name or empty string for drivers that require an argument
            $charset = $this->driver->getCharacterSet($firstIncludedTable) ?: null;
            $collation = $this->driver->getCollation() ?: null;
        } catch (\Throwable $e) {
            // Some drivers may not support these methods
        }

        return new DatabaseInfo([
            'name' => $databaseName,
            'tables' => $tables,
            'charset' => $charset,
            'collation' => $collation,
        ]);
    }

    /**
     * Check if a table has a specific column
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @return  bool
     */
    public function hasColumn(string $table, string $column): bool
    {
        return $this->driver->tableHasField($table, $column);
    }

    /**
     * Get key/index information for a table
     *
     * @param   string  $table  The table name
     * @return  array
     */
    public function getTableKeys(string $table): array
    {
        return $this->driver->getTableKeys($table);
    }

    /**
     * Get index/key names for a table
     *
     * @param   string  $table  The table name
     * @return  array
     */
    public function getIndexNames(string $table): array
    {
        $keys = $this->driver->getTableKeys($table);
        $names = [];

        foreach ($keys as $keyName => $key) {
            if (is_string($keyName) && $keyName !== '') {
                $names[] = $keyName;
                continue;
            }

            if (is_object($key)) {
                $candidate = $key->Key_name ?? $key->name ?? null;
                if ($candidate) {
                    $names[] = $candidate;
                }
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * Check if a table has a specific key/index
     *
     * @param   string  $table  The table name
     * @param   string  $key    The key/index name
     * @return  bool
     */
    public function hasKey(string $table, string $key): bool
    {
        return $this->driver->tableHasKey($table, $key);
    }

    /**
     * Check if a table has a primary key
     *
     * @param   string  $table  The table name
     * @return  bool
     */
    public function hasPrimaryKey(string $table): bool
    {
        return (bool) $this->driver->getPrimaryKey($table);
    }

    /**
     * Get the primary key column name for a table
     *
     * @param   string  $table  The table name
     * @return  string|false
     */
    public function getPrimaryKey(string $table)
    {
        return $this->driver->getPrimaryKey($table);
    }

    /**
     * Get primary key column names for a table
     *
     * @param   string  $table  The table name
     * @return  array
     */
    public function getPrimaryKeyColumns(string $table): array
    {
        return $this->driver->getPrimaryKeyColumns($table);
    }

    /**
     * Check if a primary key includes a specific column
     *
     * @param   string  $table   The table name
     * @param   string  $column  Column name
     * @return  bool
     */
    public function hasPrimaryKeyColumn(string $table, string $column): bool
    {
        return in_array($column, $this->getPrimaryKeyColumns($table), true);
    }

    /**
     * Get foreign key constraints for a table
     *
     * Returns an array of foreign key constraint objects with:
     * - name: Constraint name
     * - columns: Array of local column names
     * - foreign_table: Referenced table name
     * - foreign_columns: Array of referenced column names
     * - on_update: Update action (CASCADE, SET NULL, RESTRICT, NO ACTION)
     * - on_delete: Delete action (CASCADE, SET NULL, RESTRICT, NO ACTION)
     *
     * @param   string  $table  The table name
     * @return  array
     */
    public function getForeignKeys(string $table): array
    {
        return $this->driver->getForeignKeys($table);
    }

    /**
     * Check if a table has a specific foreign key constraint
     *
     * @param   string  $table  The table name
     * @param   string  $name   The foreign key constraint name
     * @return  bool
     */
    public function hasForeignKey(string $table, string $name): bool
    {
        return $this->driver->hasForeignKey($table, $name);
    }

    /**
     * Get the CREATE TABLE statement for a table
     *
     * @param   string|array  $tables  A table name or list of table names
     * @return  array
     */
    public function getTableCreate($tables): array
    {
        return $this->driver->getTableCreate($tables);
    }

    /**
     * Get all tables in the database
     *
     * @return  array
     */
    public function getTableList(): array
    {
        return $this->driver->getTableList();
    }

    /**
     * Get the storage engine for a table
     *
     * @param   string  $table  The table name
     * @return  string|bool
     */
    public function getEngine(string $table)
    {
        return $this->driver->getEngine($table);
    }

    /**
     * Set the storage engine for a table
     *
     * @param   string  $table   The table name
     * @param   string  $engine  The engine type (e.g., 'InnoDB', 'MyISAM')
     * @return  bool
     */
    public function setEngine(string $table, string $engine): bool
    {
        return $this->driver->setEngine($table, $engine);
    }

    /**
     * Set the storage engine for a table (alias for setEngine)
     *
     * This is MySQL-specific - on SQLite this is a no-op that returns true.
     *
     * @param   string  $table   The table name
     * @param   string  $engine  The engine type (e.g., 'InnoDB', 'MyISAM')
     * @return  bool
     */
    public function setTableEngine(string $table, string $engine = 'MYISAM'): bool
    {
        return $this->driver->setTableEngine($table, $engine);
    }

    /**
     * Get the character set for a table or column
     *
     * @param   string       $table  The table name
     * @param   string|null  $field  Optional column name
     * @return  string|bool
     */
    public function getCharacterSet(string $table, ?string $field = null)
    {
        return $this->driver->getCharacterSet($table, $field);
    }

    /**
     * Convert a table to the specified character set
     *
     * This converts all text columns in the table to the new character set.
     * On SQLite this is a no-op as SQLite handles encoding at the database level.
     *
     * @param   string       $table    The table name
     * @param   string       $charset  The character set (e.g., 'utf8', 'utf8mb4')
     * @param   string|null  $collate  Optional collation (e.g., 'utf8mb4_unicode_ci')
     * @return  bool
     */
    public function convertToCharset(string $table, string $charset, ?string $collate = null): bool
    {
        return $this->driver->convertToCharset($table, $charset, $collate);
    }

    /**
     * Get the auto-increment value for a table
     *
     * @param   string  $table  The table name
     * @return  int|bool
     */
    public function getAutoIncrement(string $table)
    {
        return $this->driver->getAutoIncrement($table);
    }

    /**
     * Set the auto-increment starting value for a table
     *
     * This is database-agnostic:
     * - MySQL: ALTER TABLE ... AUTO_INCREMENT = value
     * - SQLite: Updates sqlite_sequence table
     * - PostgreSQL: Uses setval() on the sequence
     *
     * @param   string  $table  The table name
     * @param   int     $value  The auto-increment starting value
     * @return  bool
     */
    public function setAutoIncrement(string $table, int $value): bool
    {
        return $this->driver->setAutoIncrement($table, $value);
    }

    // =========================================================================
    // ENUM Column Operations
    // =========================================================================

    /**
     * Get the allowed values for an ENUM column
     *
     * This is database-agnostic:
     * - MySQL: Parses ENUM definition from column type
     * - SQLite: Returns empty array (no native ENUM support)
     * - PostgreSQL: Returns empty array (uses custom types)
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @return  array   Array of allowed values, empty if not an ENUM or unsupported
     */
    public function getEnumValues(string $table, string $column): array
    {
        return $this->driver->getEnumValues($table, $column);
    }

    /**
     * Add a value to an ENUM column
     *
     * This is database-agnostic:
     * - MySQL: Modifies the ENUM definition to include the new value
     * - SQLite: No-op (ENUM stored as TEXT, any value allowed)
     * - PostgreSQL: No-op (would need ALTER TYPE)
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @param   string  $value   The value to add
     * @return  bool
     */
    public function addEnumValue(string $table, string $column, string $value): bool
    {
        return $this->driver->addEnumValue($table, $column, $value);
    }


    /**
     * Remove a value from an ENUM column
     *
     * This is database-agnostic:
     * - MySQL: Modifies the ENUM definition to exclude the value
     * - SQLite: No-op (ENUM stored as TEXT)
     * - PostgreSQL: No-op (can't remove ENUM values easily)
     *
     * Warning: On MySQL, existing rows with this value will become invalid.
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @param   string  $value   The value to remove
     * @return  bool
     */
    public function removeEnumValue(string $table, string $column, string $value): bool
    {
        return $this->driver->removeEnumValue($table, $column, $value);
    }

    // =========================================================================
    // View Operations
    // =========================================================================

    /**
     * Start building a view with fluent interface
     *
     * Returns a ViewBuilder for fluent configuration of MySQL-specific options
     * (algorithm, definer, security) before creating the view.
     *
     * Usage:
     * ```php
     * $schema->createView('#__active_users')
     *     ->algorithm('UNDEFINED')
     *     ->definer('CURRENT_USER')
     *     ->security('INVOKER')
     *     ->as('SELECT * FROM #__users WHERE active = 1');
     * ```
     *
     * @param   string  $name  The view name (with or without prefix)
     * @return  ViewBuilder
     */
    public function createView(string $name): ViewBuilder
    {
        return new ViewBuilder($this->driver, $name);
    }

    /**
     * Creates or replaces a database view (non-fluent alternative)
     *
     * MySQL uses CREATE OR REPLACE VIEW with ALGORITHM, DEFINER, and SQL SECURITY.
     * SQLite requires DROP then CREATE (options ignored).
     * PostgreSQL uses CREATE OR REPLACE VIEW (options ignored).
     *
     * @param   string  $name       The view name (with or without prefix)
     * @param   string  $selectSql  The SELECT statement for the view
     * @param   array   $options    MySQL-specific view options (ignored on SQLite/PostgreSQL):
     *                              - algorithm: UNDEFINED, MERGE, or TEMPTABLE (default: UNDEFINED)
     *                              - definer: User who owns the view (default: CURRENT_USER)
     *                              - security: DEFINER or INVOKER (default: INVOKER)
     * @return  bool
     */
    public function createOrReplaceView(string $name, string $selectSql, array $options = []): bool
    {
        return $this->driver->createOrReplaceView($name, $selectSql, $options);
    }

    /**
     * Drops a database view
     *
     * @param   string  $name      The view name (with or without prefix)
     * @param   bool    $ifExists  Whether to use IF EXISTS clause
     * @return  bool
     */
    public function dropView(string $name, bool $ifExists = true): bool
    {
        return $this->driver->dropView($name, $ifExists);
    }

    /**
     * Checks if a view exists in the database
     *
     * @param   string  $name  The view name (with or without prefix)
     * @return  bool
     */
    public function viewExists(string $name): bool
    {
        return $this->driver->viewExists($name);
    }

    /**
     * Returns a list of all views in the current database
     *
     * @return  array  Array of view names
     */
    public function getViews(): array
    {
        return $this->driver->getViews();
    }

    /**
     * Returns a list of all database names on the server
     *
     * Note: SQLite is file-based and returns attached database names.
     *
     * @return  array  Array of database names
     */
    public function getDatabaseNames(): array
    {
        return $this->driver->getDatabaseNames();
    }

    // =========================================================================
    // Sequence Operations
    // =========================================================================

    /**
     * Returns a list of all sequences in the current database
     *
     * Sequences are supported by PostgreSQL, Oracle, and SQL Server (2012+).
     * MySQL and SQLite do not support sequences and will return an empty array.
     *
     * @return  array  Array of SequenceInfo objects
     */
    public function getSequences(): array
    {
        return $this->driver->getSequences();
    }

    /**
     * Creates a new sequence
     *
     * @param   string  $name       The sequence name
     * @param   int     $start      Starting value (default: 1)
     * @param   int     $increment  Increment value (default: 1)
     * @param   array   $options    Additional options (min, max, cycle, cache, schema)
     * @return  bool
     * @throws  \RuntimeException  If sequences are not supported by the driver
     */
    public function createSequence(string $name, int $start = 1, int $increment = 1, array $options = []): bool
    {
        return $this->driver->createSequence($name, $start, $increment, $options);
    }

    /**
     * Drops a sequence
     *
     * @param   string  $name      The sequence name
     * @param   bool    $ifExists  Whether to use IF EXISTS clause
     * @return  bool
     * @throws  \RuntimeException  If sequences are not supported by the driver
     */
    public function dropSequence(string $name, bool $ifExists = true): bool
    {
        return $this->driver->dropSequence($name, $ifExists);
    }

    /**
     * Checks if a sequence exists
     *
     * @param   string  $name  The sequence name
     * @return  bool
     */
    public function sequenceExists(string $name): bool
    {
        return $this->driver->sequenceExists($name);
    }

    /**
     * Gets the next value from a sequence
     *
     * @param   string  $name  The sequence name
     * @return  int
     * @throws  \RuntimeException  If sequences are not supported by the driver
     */
    public function nextSequenceValue(string $name): int
    {
        return $this->driver->nextSequenceValue($name);
    }

    /**
     * Check if the current driver supports sequences
     *
     * @return  bool
     */
    public function supportsSequences(): bool
    {
        return $this->driver->supportsSequences();
    }

    // =========================================================================
    // Index Operations
    // =========================================================================

    /**
     * Add a fulltext index to a table
     *
     * Note: This is a no-op on SQLite as it doesn't support MySQL-style fulltext indexes
     *
     * @param   string        $table    The table name
     * @param   string        $name     The index name
     * @param   string|array  $columns  Column name(s) to index
     * @return  bool
     */
    public function addFulltextIndex(string $table, string $name, $columns): bool
    {
        return $this->driver->addFulltextIndex($table, $name, $columns);
    }

    /**
     * Add an index to a table
     *
     * @param   string        $table    The table name
     * @param   string        $name     The index name
     * @param   string|array  $columns  Column name(s) to index
     * @param   bool          $unique   Whether to create a unique index
     * @return  bool
     */
    public function addIndex(string $table, string $name, $columns, bool $unique = false): bool
    {
        return $this->driver->addIndex($table, $name, $columns, $unique);
    }

    /**
     * Add a unique index to a table
     *
     * @param   string        $table    The table name
     * @param   string        $name     The index name
     * @param   string|array  $columns  Column name(s) to index
     * @return  bool
     */
    public function addUniqueIndex(string $table, string $name, $columns): bool
    {
        return $this->driver->addUniqueIndex($table, $name, $columns);
    }

    /**
     * Drop an index from a table
     *
     * @param   string  $table  The table name
     * @param   string  $name   The index name
     * @return  bool
     */
    public function dropIndex(string $table, string $name): bool
    {
        return $this->driver->dropIndex($table, $name);
    }

    /**
     * Drop the primary key from a table
     *
     * @param   string  $table  The table name
     * @return  bool
     */
    public function dropPrimaryKey(string $table): bool
    {
        return $this->driver->dropPrimaryKey($table);
    }

    /**
     * Add a primary key to a table
     *
     * @param   string        $table    The table name
     * @param   string|array  $columns  Column name(s) for the primary key
     * @return  bool
     */
    public function addPrimaryKey(string $table, $columns): bool
    {
        return $this->driver->addPrimaryKey($table, $columns);
    }

    // =========================================================================
    // Column Operations
    // =========================================================================

    /**
     * Add a column to a table with fluent positioning and optional abstract types
     *
     * Returns a ColumnBuilder for fluent positioning and type definition.
     *
     * Usage with raw SQL (backward compatible):
     * ```php
     * $schema->addColumn('#__users', 'email', 'VARCHAR(255)')->after('name');
     * $schema->addColumn('#__users', 'uuid', 'CHAR(36)')->first();
     * $schema->addColumn('#__users', 'notes', 'TEXT'); // defaults to last
     * ```
     *
     * Usage with abstract types (new fluent API):
     * ```php
     * $schema->addColumn('#__users', 'email')->string(255)->nullable()->after('name');
     * $schema->addColumn('#__users', 'score')->integer()->unsigned()->default(0);
     * $schema->addColumn('#__users', 'uuid')->uuid();
     * $schema->addColumn('#__users', 'data')->json()->nullable();
     * ```
     *
     * @param   string       $table       The table name
     * @param   string       $column      The column name
     * @param   string|null  $definition  The column definition (null for fluent abstract type API)
     * @param   string       $comment     Optional column comment
     * @return  ColumnBuilder
     */
    public function addColumn(
        string $table,
        string $column,
        ?string $definition = null,
        string $comment = ''
    ): ColumnBuilder {
        return new ColumnBuilder($this->driver, $table, $column, $definition, 'add', $comment);
    }

    /**
     * Drop a column from a table
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @return  bool
     */
    public function dropColumn(string $table, string $column): bool
    {
        return $this->driver->dropColumn($table, $column);
    }

    /**
     * Modify a column definition with fluent positioning and optional abstract types
     *
     * Returns a ColumnBuilder for fluent positioning and type definition.
     *
     * Usage with raw SQL (backward compatible):
     * ```php
     * $schema->modifyColumn('#__users', 'email', 'VARCHAR(500)')->after('name');
     * $schema->modifyColumn('#__users', 'id', 'BIGINT')->first();
     * $schema->modifyColumn('#__users', 'notes', 'MEDIUMTEXT'); // stays in place
     * ```
     *
     * Usage with abstract types (new fluent API):
     * ```php
     * $schema->modifyColumn('#__users', 'score')->bigInteger()->default(100);
     * $schema->modifyColumn('#__users', 'email')->string(500)->nullable();
     * $schema->modifyColumn('#__users', 'data')->json();
     * ```
     *
     * @param   string       $table       The table name
     * @param   string       $column      The column name
     * @param   string|null  $definition  The new column definition (null for fluent abstract type API)
     * @param   string       $comment     Optional column comment
     * @return  ColumnBuilder
     */
    public function modifyColumn(
        string $table,
        string $column,
        ?string $definition = null,
        string $comment = ''
    ): ColumnBuilder {
        return new ColumnBuilder($this->driver, $table, $column, $definition, 'modify', $comment);
    }


    /**
     * Rename a column with fluent positioning and optional abstract types
     *
     * Returns a ColumnBuilder for fluent positioning and type definition.
     *
     * Usage with raw SQL (backward compatible):
     * ```php
     * $schema->renameColumn('#__users', 'old_name', 'new_name', 'VARCHAR(255)');
     * ```
     *
     * Usage with abstract types (new fluent API):
     * ```php
     * $schema->renameColumn('#__users', 'old_name', 'new_name')->string(255)->nullable();
     * $schema->renameColumn('#__users', 'old_name')->to('new_name')->integer();
     * ```
     *
     * @param   string       $table       The table name
     * @param   string       $oldColumn   The current column name
     * @param   string       $newColumn   The new column name (optional if using ->to())
     * @param   string|null  $definition  The column definition
     * @param   string       $comment     Optional column comment
     * @return  ColumnBuilder
     */
    public function renameColumn(
        string $table,
        string $oldColumn,
        string $newColumn = '',
        ?string $definition = null,
        string $comment = ''
    ): ColumnBuilder {
        return new ColumnBuilder(
            $this->driver,
            $table,
            $oldColumn,
            $definition,
            'rename',
            $comment,
            $newColumn ?: null
        );
    }

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
    public function populateSequentialValues(string $table, string $column, ?string $orderBy = null): bool
    {
        return $this->driver->populateSequentialValues($table, $column, $orderBy);
    }

    // =========================================================================
    // Schema Comparison
    // =========================================================================

    /**
     * Compare two tables and return the differences
     *
     * Accepts table names (strings) or TableInfo objects. When table names
     * are provided, they will be introspected automatically.
     *
     * Usage:
     * ```php
     * // Compare by table names
     * $diff = $schema->compareTables('#__users_v1', '#__users_v2');
     *
     * // Compare TableInfo objects (e.g., from snapshots)
     * $oldTable = TableInfo::fromArray(json_decode($snapshot, true));
     * $currentTable = $schema->introspectTable('#__users');
     * $diff = $schema->compareTables($oldTable, $currentTable);
     *
     * // Check for changes
     * if (!$diff->isEmpty()) {
     *     foreach ($diff->getAddedColumns() as $col) {
     *         echo "Added: {$col->getName()}\n";
     *     }
     * }
     * ```
     *
     * @param   string|TableInfo  $from  Original table (name or TableInfo)
     * @param   string|TableInfo  $to    New table (name or TableInfo)
     * @return  TableDiff
     */
    public function compareTables($from, $to): TableDiff
    {
        // Introspect if given table names
        if (is_string($from)) {
            $from = $this->introspectTable($from);
        }
        if (is_string($to)) {
            $to = $this->introspectTable($to);
        }

        $comparator = new Comparator();
        return $comparator->compareTables($from, $to);
    }

    /**
     * Generate SQL to transform one table schema into another
     *
     * Returns an array of SQL statements that, when executed in order,
     * will modify the "from" table to match the "to" table.
     *
     * Usage:
     * ```php
     * // Generate SQL for forward migration
     * $sql = $schema->generateTableDiffSql('#__users_old', '#__users_new');
     * foreach ($sql as $statement) {
     *     echo "$statement;\n";
     * }
     *
     * // Execute the migration
     * foreach ($sql as $statement) {
     *     $db->setQuery($statement)->execute();
     * }
     * ```
     *
     * @param   string|TableInfo  $from  Original table (name or TableInfo)
     * @param   string|TableInfo  $to    New table (name or TableInfo)
     * @return  array  Array of SQL statements
     */
    public function generateTableDiffSql($from, $to): array
    {
        $diff = $this->compareTables($from, $to);
        $generator = new DiffSqlGenerator($this->driver);
        return $generator->generateUp($diff);
    }

    /**
     * Generate SQL to reverse a table transformation
     *
     * Returns SQL statements to transform the "to" table back to the "from" table.
     * Useful for generating "down" migrations.
     *
     * @param   string|TableInfo  $from  Original table (name or TableInfo)
     * @param   string|TableInfo  $to    New table (name or TableInfo)
     * @return  array  Array of SQL statements
     */
    public function generateTableDiffSqlReverse($from, $to): array
    {
        $diff = $this->compareTables($from, $to);
        $generator = new DiffSqlGenerator($this->driver);
        return $generator->generateDown($diff);
    }

    /**
     * Get a Comparator instance
     *
     * Useful when you need direct access to comparison logic.
     *
     * @return Comparator
     */
    public function getComparator(): Comparator
    {
        return new Comparator();
    }

    /**
     * Get a DiffSqlGenerator instance
     *
     * Useful when you need direct access to SQL generation.
     *
     * @return DiffSqlGenerator
     */
    public function getDiffSqlGenerator(): DiffSqlGenerator
    {
        return new DiffSqlGenerator($this->driver);
    }

    // =========================================================================
    // Database Schema Comparison
    // =========================================================================

    /**
     * Compare two database schemas and return the differences
     *
     * Accepts DatabaseInfo objects or introspects the current database if
     * no arguments provided for the "from" schema.
     *
     * Usage:
     * ```php
     * // Compare two DatabaseInfo objects
     * $diff = $schema->compareSchemas($oldSchema, $newSchema);
     *
     * // Compare current database to a target schema
     * $currentSchema = $schema->introspectDatabase('jos_');
     * $targetSchema = DatabaseInfo::fromArray(json_decode($schemaFile, true));
     * $diff = $schema->compareSchemas($currentSchema, $targetSchema);
     *
     * // Check for changes
     * if (!$diff->isEmpty()) {
     *     foreach ($diff->getAddedTables() as $table) {
     *         echo "New table: {$table->getName()}\n";
     *     }
     *     foreach ($diff->getChangedTables() as $tableDiff) {
     *         echo "Modified: {$tableDiff->getName()}\n";
     *     }
     * }
     * ```
     *
     * @param   DatabaseInfo  $from  Original database schema
     * @param   DatabaseInfo  $to    New database schema
     * @return  SchemaDiff
     */
    public function compareSchemas(DatabaseInfo $from, DatabaseInfo $to): SchemaDiff
    {
        $comparator = new Comparator();
        return $comparator->compareSchemas($from, $to);
    }

    /**
     * Generate SQL to transform one database schema into another
     *
     * Returns an array of SQL statements that, when executed in order,
     * will modify the database to match the target schema.
     *
     * Usage:
     * ```php
     * $sql = $schema->generateSchemaDiffSql($currentSchema, $targetSchema);
     * foreach ($sql as $statement) {
     *     echo "$statement;\n";
     * }
     * ```
     *
     * @param   DatabaseInfo  $from  Original database schema
     * @param   DatabaseInfo  $to    Target database schema
     * @return  array  Array of SQL statements
     */
    public function generateSchemaDiffSql(DatabaseInfo $from, DatabaseInfo $to): array
    {
        $diff = $this->compareSchemas($from, $to);
        $generator = new DiffSqlGenerator($this->driver);
        return $generator->generateSchemaUp($diff);
    }

    /**
     * Generate SQL to reverse a schema transformation
     *
     * Returns SQL statements to transform the target schema back to the original.
     * Useful for generating "down" migrations.
     *
     * @param   DatabaseInfo  $from  Original database schema
     * @param   DatabaseInfo  $to    Target database schema
     * @return  array  Array of SQL statements
     */
    public function generateSchemaDiffSqlReverse(DatabaseInfo $from, DatabaseInfo $to): array
    {
        $diff = $this->compareSchemas($from, $to);
        $generator = new DiffSqlGenerator($this->driver);
        return $generator->generateSchemaDown($diff);
    }

    /**
     * Compare current database to a saved schema snapshot
     *
     * Convenience method that introspects the current database and compares
     * it to a provided schema definition.
     *
     * Usage:
     * ```php
     * // Load schema from JSON file
     * $targetSchema = DatabaseInfo::fromArray(json_decode(file_get_contents('schema.json'), true));
     *
     * // Compare to current database
     * $diff = $schema->compareToSchema($targetSchema, 'jos_');
     *
     * if (!$diff->isEmpty()) {
     *     echo "Database is out of sync!\n";
     *     print_r($diff->getSummary());
     * }
     * ```
     *
     * @param   DatabaseInfo  $targetSchema  The schema to compare against
     * @param   string|null   $prefix        Table prefix filter for introspection
     * @return  SchemaDiff
     */
    public function compareToSchema(DatabaseInfo $targetSchema, ?string $prefix = null): SchemaDiff
    {
        $currentSchema = $this->introspectDatabase($prefix);
        return $this->compareSchemas($currentSchema, $targetSchema);
    }

    /**
     * Generate a migration from current database to target schema
     *
     * Convenience method that introspects and generates SQL in one step.
     *
     * @param   DatabaseInfo  $targetSchema  The target schema
     * @param   string|null   $prefix        Table prefix filter for introspection
     * @return  array  Array of SQL statements
     */
    public function generateMigrationTo(DatabaseInfo $targetSchema, ?string $prefix = null): array
    {
        $currentSchema = $this->introspectDatabase($prefix);
        return $this->generateSchemaDiffSql($currentSchema, $targetSchema);
    }
}
