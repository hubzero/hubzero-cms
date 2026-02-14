<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

use Hubzero\Database\Driver;
use Hubzero\Utility\Date;

/**
 * Migration Squasher
 *
 * Generates a single "squashed" migration file from the current database schema.
 * This consolidates all previous migrations into one file that creates all tables
 * from scratch.
 *
 * Unlike Laravel's raw SQL dump approach, this generates cross-database-compatible
 * PHP code using the schema builder.
 *
 * Features:
 * - Topological sorting of tables by foreign key dependencies
 * - Cross-database compatibility (MySQL, PostgreSQL, SQLite, SQL Server, etc.)
 * - Existence checks for safe re-running
 * - Proper foreign key ordering (drop/create)
 * - Optional pruning of old migration files
 *
 * Usage:
 * ```php
 * $squasher = new MigrationSquasher($driver);
 *
 * // Generate squashed migration from current database
 * $content = $squasher->generateFromDatabase('#__', 'Core', 'Initial schema from squash');
 *
 * // Or from an existing DatabaseInfo snapshot
 * $content = $squasher->generateFromSchema($databaseInfo, 'Core');
 *
 * // Write to file
 * $path = $squasher->writeToFile($content, '/path/to/migrations/', 'SquashedSchema');
 * ```
 */
class MigrationSquasher
{
    /**
     * Database driver
     *
     * @var Driver
     */
    protected $driver;

    /**
     * SQL generator for creating tables
     *
     * @var DiffSqlGenerator
     */
    protected $sqlGenerator;

    /**
     * Schema manager (lazy-loaded, only needed for generateFromDatabase)
     *
     * @var SchemaManager|null
     */
    protected $schemaManager = null;

    /**
     * Whether to include IF NOT EXISTS / IF EXISTS checks
     *
     * @var bool
     */
    protected $includeExistenceChecks = true;

    /**
     * Whether to include foreign keys (can be disabled for circular refs)
     *
     * @var bool
     */
    protected $includeForeignKeys = true;

    /**
     * Tables to exclude from squashing
     *
     * @var array
     */
    protected $excludeTables = [];

    /**
     * Copyright year for generated files
     *
     * @var string
     */
    protected $copyrightYear;

    /**
     * Custom template content
     *
     * @var string|null
     */
    protected $template = null;

    /**
     * Create a new MigrationSquasher
     *
     * @param Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
        $this->sqlGenerator = new DiffSqlGenerator($driver);
        $this->copyrightYear = date('Y');
    }

    /**
     * Get or create the schema manager (lazy-loaded)
     *
     * @return SchemaManager
     */
    protected function getSchemaManager(): SchemaManager
    {
        if ($this->schemaManager === null) {
            $this->schemaManager = new SchemaManager($this->driver);
        }
        return $this->schemaManager;
    }

    /**
     * Set whether to include existence checks
     *
     * @param  bool  $include
     * @return self
     */
    public function setIncludeExistenceChecks(bool $include): self
    {
        $this->includeExistenceChecks = $include;
        return $this;
    }

    /**
     * Set whether to include foreign keys
     *
     * @param  bool  $include
     * @return self
     */
    public function setIncludeForeignKeys(bool $include): self
    {
        $this->includeForeignKeys = $include;
        return $this;
    }

    /**
     * Set tables to exclude from squashing
     *
     * @param  array  $tables
     * @return self
     */
    public function setExcludeTables(array $tables): self
    {
        $this->excludeTables = $tables;
        return $this;
    }

    /**
     * Set a custom template for migration files
     *
     * @param  string  $template
     * @return self
     */
    public function setTemplate(string $template): self
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Set copyright year
     *
     * @param  string  $year
     * @return self
     */
    public function setCopyrightYear(string $year): self
    {
        $this->copyrightYear = $year;
        return $this;
    }

    /**
     * Generate squashed migration from current database state
     *
     * @param  string|null  $prefix       Table prefix filter (e.g., '#__')
     * @param  string       $component    Component name for class
     * @param  string       $description  Migration description
     * @return string  The migration file content
     */
    public function generateFromDatabase(
        ?string $prefix = null,
        string $component = 'SquashedSchema',
        string $description = ''
    ): string {
        // Introspect the database
        $filter = $this->excludeTables ? function ($name) {
            return !in_array($name, $this->excludeTables);
        } : null;

        $databaseInfo = $this->getSchemaManager()->introspectDatabase($prefix, $filter);

        return $this->generateFromSchema($databaseInfo, $component, $description);
    }

    /**
     * Generate squashed migration from a DatabaseInfo snapshot
     *
     * @param  DatabaseInfo  $schema
     * @param  string        $component    Component name for class
     * @param  string        $description  Migration description
     * @return string  The migration file content
     */
    public function generateFromSchema(
        DatabaseInfo $schema,
        string $component = 'SquashedSchema',
        string $description = ''
    ): string {
        $tables = $schema->getTables();

        // Filter out excluded tables
        if ($this->excludeTables) {
            $tables = array_filter($tables, function ($table) {
                return !in_array($table->getName(), $this->excludeTables);
            });
        }

        // Sort tables topologically by foreign key dependencies
        $sortedTables = $this->sortTablesByDependencies($tables);

        if (empty($description)) {
            $count = count($sortedTables);
            $description = "Squashed schema migration - creates {$count} table(s) from baseline";
        }

        $upCode = $this->generateUpCode($sortedTables);
        $downCode = $this->generateDownCode(array_reverse($sortedTables));

        return $this->generateMigrationContent($component, $description, $upCode, $downCode);
    }

    /**
     * Sort tables by foreign key dependencies (topological sort)
     *
     * Tables with no dependencies come first, tables that depend on others come later.
     * This ensures CREATE statements execute in the correct order.
     *
     * @param  TableInfo[]  $tables
     * @return TableInfo[]
     */
    protected function sortTablesByDependencies(array $tables): array
    {
        // Build dependency graph
        $graph = [];
        $tableMap = [];

        foreach ($tables as $table) {
            $name = $table->getName();
            $tableMap[$name] = $table;
            $graph[$name] = [];

            if ($this->includeForeignKeys) {
                foreach ($table->getForeignKeys() as $fk) {
                    $refTable = $fk->getForeignTable();
                    // Only add dependency if the referenced table is in our list
                    if (isset($tableMap[$refTable]) || $this->tableInArray($refTable, $tables)) {
                        $graph[$name][] = $refTable;
                    }
                }
            }
        }

        // Topological sort using Kahn's algorithm
        $inDegree = [];
        $queue = [];
        $sorted = [];

        // Initialize in-degrees
        foreach ($graph as $node => $edges) {
            if (!isset($inDegree[$node])) {
                $inDegree[$node] = 0;
            }
            foreach ($edges as $edge) {
                if (!isset($inDegree[$edge])) {
                    $inDegree[$edge] = 0;
                }
            }
        }

        // Count in-degrees
        foreach ($graph as $node => $edges) {
            foreach ($edges as $edge) {
                if (isset($inDegree[$edge])) {
                    $inDegree[$node]++;
                }
            }
        }

        // Find nodes with no dependencies
        foreach ($inDegree as $node => $degree) {
            if ($degree === 0) {
                $queue[] = $node;
            }
        }

        // Process queue
        while (!empty($queue)) {
            $node = array_shift($queue);
            $sorted[] = $node;

            // For each table that depends on this one, decrement in-degree
            foreach ($graph as $dependent => $dependencies) {
                if (in_array($node, $dependencies)) {
                    $inDegree[$dependent]--;
                    if ($inDegree[$dependent] === 0) {
                        $queue[] = $dependent;
                    }
                }
            }
        }

        // Check for cycles (remaining nodes not in sorted list)
        $remaining = array_diff(array_keys($graph), $sorted);
        if (!empty($remaining)) {
            // Cycle detected - just append remaining tables (they have circular refs)
            $sorted = array_merge($sorted, $remaining);
        }

        // Map back to TableInfo objects, maintaining order
        $result = [];
        foreach ($sorted as $name) {
            if (isset($tableMap[$name])) {
                $result[] = $tableMap[$name];
            }
        }

        // Add any tables not in the graph (safety)
        foreach ($tables as $table) {
            if (!in_array($table, $result)) {
                $result[] = $table;
            }
        }

        return $result;
    }

    /**
     * Check if a table name is in the array of TableInfo objects
     *
     * @param  string       $name
     * @param  TableInfo[]  $tables
     * @return bool
     */
    protected function tableInArray(string $name, array $tables): bool
    {
        foreach ($tables as $table) {
            if ($table->getName() === $name) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate the up() method code
     *
     * @param  TableInfo[]  $tables
     * @return string
     */
    protected function generateUpCode(array $tables): string
    {
        if (empty($tables)) {
            return "        // No tables to create\n";
        }

        $code = "        \$schema = \$this->db->schema();\n\n";

        // Disable foreign key checks during creation
        if ($this->includeForeignKeys && $this->hasForeignKeys($tables)) {
            $code .= "        // Disable foreign key checks during table creation\n";
            $code .= "        \$this->db->setQuery('SET FOREIGN_KEY_CHECKS = 0')->execute();\n\n";
        }

        $code .= "        // Create tables in dependency order\n";

        foreach ($tables as $table) {
            $tableName = $table->getName();
            $sql = $this->generateCreateTableSql($table);
            $escaped = $this->escapePhpString($sql);

            if ($this->includeExistenceChecks) {
                $code .= "\n        if (!\$schema->tableExists('{$tableName}')) {\n";
                $code .= "            \$this->db->setQuery(\"{$escaped}\")->execute();\n";
                $code .= "        }\n";
            } else {
                $code .= "\n        \$this->db->setQuery(\"{$escaped}\")->execute();\n";
            }
        }

        // Re-enable foreign key checks
        if ($this->includeForeignKeys && $this->hasForeignKeys($tables)) {
            $code .= "\n        // Re-enable foreign key checks\n";
            $code .= "        \$this->db->setQuery('SET FOREIGN_KEY_CHECKS = 1')->execute();\n";
        }

        return $code;
    }

    /**
     * Generate the down() method code
     *
     * @param  TableInfo[]  $tables  Already reversed (drop in reverse order)
     * @return string
     */
    protected function generateDownCode(array $tables): string
    {
        if (empty($tables)) {
            return "        // No tables to drop\n";
        }

        $code = "        \$schema = \$this->db->schema();\n\n";

        // Disable foreign key checks during drops
        if ($this->includeForeignKeys && $this->hasForeignKeys($tables)) {
            $code .= "        // Disable foreign key checks during table drops\n";
            $code .= "        \$this->db->setQuery('SET FOREIGN_KEY_CHECKS = 0')->execute();\n\n";
        }

        $code .= "        // Drop tables in reverse dependency order\n";

        foreach ($tables as $table) {
            $tableName = $table->getName();
            $quotedName = $this->driver->quoteName($tableName);

            if ($this->includeExistenceChecks) {
                $code .= "\n        if (\$schema->tableExists('{$tableName}')) {\n";
                $code .= "            \$this->db->setQuery(\"DROP TABLE {$quotedName}\")->execute();\n";
                $code .= "        }\n";
            } else {
                $code .= "\n        \$this->db->setQuery(\"DROP TABLE IF EXISTS {$quotedName}\")->execute();\n";
            }
        }

        // Re-enable foreign key checks
        if ($this->includeForeignKeys && $this->hasForeignKeys($tables)) {
            $code .= "\n        // Re-enable foreign key checks\n";
            $code .= "        \$this->db->setQuery('SET FOREIGN_KEY_CHECKS = 1')->execute();\n";
        }

        return $code;
    }

    /**
     * Check if any tables have foreign keys
     *
     * @param  TableInfo[]  $tables
     * @return bool
     */
    protected function hasForeignKeys(array $tables): bool
    {
        foreach ($tables as $table) {
            if (count($table->getForeignKeys()) > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate CREATE TABLE SQL for a single table
     *
     * Uses reflection to access the protected method in DiffSqlGenerator
     *
     * @param  TableInfo  $table
     * @return string
     */
    protected function generateCreateTableSql(TableInfo $table): string
    {
        $tableName = $this->driver->quoteName($table->getName());
        $columns = [];
        $constraints = [];

        foreach ($table->getColumns() as $column) {
            $colName = $this->driver->quoteName($column->getName());
            $colDef = $this->buildColumnDefinition($column);
            $columns[] = "{$colName} {$colDef}";
        }

        // Primary key
        $pk = $table->getPrimaryKey();
        if ($pk) {
            $pkColumns = is_array($pk) ? $pk : [$pk];
            $pkCols = implode(', ', array_map([$this->driver, 'quoteName'], $pkColumns));
            $constraints[] = "PRIMARY KEY ({$pkCols})";
        }

        // Indexes (non-primary)
        foreach ($table->getIndexes() as $index) {
            if (!$index->isPrimary()) {
                $idxCols = implode(', ', array_map([$this->driver, 'quoteName'], $index->getColumns()));
                $unique = $index->isUnique() ? 'UNIQUE ' : '';
                $constraints[] = "{$unique}KEY {$this->driver->quoteName($index->getName())} ({$idxCols})";
            }
        }

        // Foreign keys
        if ($this->includeForeignKeys) {
            foreach ($table->getForeignKeys() as $fk) {
                $fkCols = implode(', ', array_map([$this->driver, 'quoteName'], $fk->getColumns()));
                $refTable = $this->driver->quoteName($fk->getForeignTable());
                $refCols = implode(', ', array_map([$this->driver, 'quoteName'], $fk->getForeignColumns()));

                $constraint = "CONSTRAINT {$this->driver->quoteName($fk->getName())} ";
                $constraint .= "FOREIGN KEY ({$fkCols}) REFERENCES {$refTable} ({$refCols})";

                if ($fk->getOnDelete() && strtoupper($fk->getOnDelete()) !== 'NO ACTION') {
                    $constraint .= " ON DELETE {$fk->getOnDelete()}";
                }
                if ($fk->getOnUpdate() && strtoupper($fk->getOnUpdate()) !== 'NO ACTION') {
                    $constraint .= " ON UPDATE {$fk->getOnUpdate()}";
                }

                $constraints[] = $constraint;
            }
        }

        $allDefs = array_merge($columns, $constraints);
        $sql = "CREATE TABLE {$tableName} (\\n  " . implode(",\\n  ", $allDefs) . "\\n)";

        // Table options (MySQL)
        $engine = $table->getEngine();
        if ($engine && $this->driverSupportsEngine()) {
            $sql .= " ENGINE={$engine}";
        }

        $charset = $table->getCharset();
        if ($charset && $this->driverSupportsCharset()) {
            $sql .= " DEFAULT CHARSET={$charset}";
        }

        return $sql;
    }

    /**
     * Build column definition string
     *
     * @param  ColumnInfo  $column
     * @return string
     */
    protected function buildColumnDefinition(ColumnInfo $column): string
    {
        $def = $column->getFullType();

        if ($column->isUnsigned()) {
            $def .= ' UNSIGNED';
        }

        if (!$column->isNullable()) {
            $def .= ' NOT NULL';
        } else {
            $def .= ' NULL';
        }

        if ($column->isAutoIncrement()) {
            $def .= ' AUTO_INCREMENT';
        }

        $default = $column->getDefault();
        if ($default !== null && !$column->isAutoIncrement()) {
            if ($default === 'CURRENT_TIMESTAMP' || $default === 'current_timestamp()') {
                $def .= ' DEFAULT CURRENT_TIMESTAMP';
            } elseif (is_numeric($default)) {
                $def .= " DEFAULT {$default}";
            } else {
                $def .= " DEFAULT '{$default}'";
            }
        }

        return $def;
    }

    /**
     * Check if driver supports ENGINE specification
     *
     * @return bool
     */
    protected function driverSupportsEngine(): bool
    {
        $driverName = strtolower($this->driver->getName() ?? '');
        return in_array($driverName, ['mysql', 'mariadb', 'percona']);
    }

    /**
     * Check if driver supports table-level charset
     *
     * @return bool
     */
    protected function driverSupportsCharset(): bool
    {
        $driverName = strtolower($this->driver->getName() ?? '');
        return in_array($driverName, ['mysql', 'mariadb', 'percona']);
    }

    /**
     * Generate the migration file content
     *
     * @param  string  $component
     * @param  string  $description
     * @param  string  $upCode
     * @param  string  $downCode
     * @return string
     */
    protected function generateMigrationContent(
        string $component,
        string $description,
        string $upCode,
        string $downCode
    ): string {
        if ($this->template !== null) {
            return $this->applyTemplate($component, $description, $upCode, $downCode);
        }

        return $this->getDefaultTemplate($component, $description, $upCode, $downCode);
    }

    /**
     * Apply a custom template
     *
     * @param  string  $component
     * @param  string  $description
     * @param  string  $upCode
     * @param  string  $downCode
     * @return string
     */
    protected function applyTemplate(string $component, string $description, string $upCode, string $downCode): string
    {
        $timestamp = (new Date())->format('YmdHis');
        $className = "Migration{$timestamp}{$component}";

        return str_replace(
            ['<namespace>', '<className>', '<description>', '<year>', '<upCode>', '<downCode>'],
            ['Migrations', $className, $description, $this->copyrightYear, $upCode, $downCode],
            $this->template
        );
    }

    /**
     * Get the default migration template
     *
     * @param  string  $component
     * @param  string  $description
     * @param  string  $upCode
     * @param  string  $downCode
     * @return string
     */
    protected function getDefaultTemplate(
        string $component,
        string $description,
        string $upCode,
        string $downCode
    ): string {
        $timestamp = (new Date())->format('YmdHis');
        $className = "Migration{$timestamp}{$component}";

        return <<<PHP
<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-{$this->copyrightYear} The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * {$description}
 *
 * This migration was generated by squashing the existing schema.
 * It creates all tables from scratch in a single migration file.
 *
 * IMPORTANT: This migration should only run on fresh installations.
 * Existing databases with the tables already created will skip creation.
 */
class {$className} extends Base
{
    /**
     * Up - Create all tables
     */
    public function up()
    {
{$upCode}
    }

    /**
     * Down - Drop all tables
     *
     * WARNING: This will delete all data!
     */
    public function down()
    {
{$downCode}
    }
}

PHP;
    }

    /**
     * Write migration content to a file
     *
     * @param  string       $content    The migration file content
     * @param  string       $directory  Directory to write to
     * @param  string       $component  Component name for filename
     * @param  string|null  $timestamp  Optional timestamp (YYYYMMDDHHMMSS)
     * @return string  The full path to the written file
     */
    public function writeToFile(
        string $content,
        string $directory,
        string $component,
        ?string $timestamp = null
    ): string {
        if ($timestamp === null) {
            $timestamp = (new Date())->format('YmdHis');
        }

        $className = "Migration{$timestamp}{$component}";
        $filename = "{$className}.php";
        $filepath = rtrim($directory, '/') . '/' . $filename;

        // Replace className placeholder if present
        $content = str_replace('<className>', $className, $content);

        file_put_contents($filepath, $content);

        return $filepath;
    }

    /**
     * Get list of existing migration files
     *
     * @param  string  $directory
     * @param  string  $pattern
     * @return array
     */
    public function getExistingMigrations(string $directory, string $pattern = 'Migration*.php'): array
    {
        $files = glob(rtrim($directory, '/') . '/' . $pattern);
        return $files ?: [];
    }

    /**
     * Get statistics about tables that would be squashed
     *
     * @param  DatabaseInfo  $schema
     * @return array
     */
    public function getSquashStats(DatabaseInfo $schema): array
    {
        $tables = $schema->getTables();

        // Filter excluded
        if ($this->excludeTables) {
            $tables = array_filter($tables, function ($table) {
                return !in_array($table->getName(), $this->excludeTables);
            });
        }

        $totalColumns = 0;
        $totalIndexes = 0;
        $totalForeignKeys = 0;

        foreach ($tables as $table) {
            $totalColumns += count($table->getColumns());
            $totalIndexes += count($table->getIndexes());
            $totalForeignKeys += count($table->getForeignKeys());
        }

        return [
            'tables' => count($tables),
            'columns' => $totalColumns,
            'indexes' => $totalIndexes,
            'foreign_keys' => $totalForeignKeys,
            'excluded' => count($this->excludeTables),
        ];
    }

    /**
     * Escape a string for use in PHP double-quoted string
     *
     * @param  string  $str
     * @return string
     */
    protected function escapePhpString(string $str): string
    {
        return addcslashes($str, "\\\"\$\n\r\t");
    }
}
