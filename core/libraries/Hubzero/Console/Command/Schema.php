<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Database\Schema\Comparator;
use Hubzero\Database\Schema\DiffSqlGenerator;
use Hubzero\Database\Schema\MigrationGenerator;
use Hubzero\Database\Schema\MigrationSquasher;
use Hubzero\Database\Schema\SchemaSnapshot;
use Hubzero\Database\Schema\DatabaseInfo;
use Hubzero\Utility\Date;

/**
 * Schema management command
 *
 * Provides tools for comparing database schemas, generating diffs,
 * and producing migration SQL.
 **/
class Schema extends Base implements CommandInterface
{
    /**
     * Default (required) command - show help
     *
     * @return  void
     **/
    public function execute()
    {
        $this->output = $this->output->getHelpOutput();
        $this->help();
        $this->output->render();
    }

    /**
     * Compare two tables and show differences
     *
     * @museDescription  Compares two tables and shows the structural differences
     *
     * @return  void
     **/
    public function diff()
    {
        $db = \Hubzero\Facades\App::get('db');
        $schema = $db->schema();

        $table1 = $this->arguments->getOpt('table1') ?: $this->arguments->getOpt(3);
        $table2 = $this->arguments->getOpt('table2') ?: $this->arguments->getOpt(4);

        // If comparing full schemas
        if ($this->arguments->getOpt('full') || (!$table1 && !$table2)) {
            $this->diffFullSchema();
            return;
        }

        if (!$table1) {
            $this->output->error('Please provide at least one table name with --table1=tablename');
            return;
        }

        // If only one table, introspect it
        if (!$table2) {
            $this->introspectTable($table1);
            return;
        }

        // Compare two tables
        if (!$schema->tableExists($table1)) {
            $this->output->error("Table '{$table1}' does not exist");
            return;
        }

        if (!$schema->tableExists($table2)) {
            $this->output->error("Table '{$table2}' does not exist");
            return;
        }

        $tableInfo1 = $schema->introspectTable($table1);
        $tableInfo2 = $schema->introspectTable($table2);

        $comparator = new Comparator();

        // Check for rename hints
        $renameHints = $this->arguments->getOpt('column-renames');
        if ($renameHints) {
            $pairs = explode(',', $renameHints);
            foreach ($pairs as $pair) {
                list($old, $new) = explode(':', $pair);
                $comparator->addColumnRenameHint($table1, trim($old), trim($new));
            }
        }

        $diff = $comparator->compareTables($tableInfo1, $tableInfo2);

        if ($diff->isEmpty()) {
            $this->output->addLine("Tables '{$table1}' and '{$table2}' are identical.", 'success');
            return;
        }

        $this->outputTableDiff($diff, $table1, $table2);

        // Generate SQL if requested
        if ($this->arguments->getOpt('sql')) {
            $this->outputDiffSql($diff, $db);
        }
    }

    /**
     * Introspect and display a single table's structure
     *
     * @museDescription  Shows the structure of a single table
     *
     * @return  void
     **/
    public function introspect()
    {
        $table = $this->arguments->getOpt('table') ?: $this->arguments->getOpt(3);

        if (!$table) {
            $this->output->error('Please provide a table name: muse schema introspect --table=tablename');
            return;
        }

        $this->introspectTable($table);
    }

    /**
     * Generate SQL to transform one schema to another
     *
     * @museDescription  Generates migration SQL from schema differences
     *
     * @return  void
     **/
    public function sql()
    {
        $db = \Hubzero\Facades\App::get('db');
        $schema = $db->schema();

        $table1 = $this->arguments->getOpt('from') ?: $this->arguments->getOpt(3);
        $table2 = $this->arguments->getOpt('to') ?: $this->arguments->getOpt(4);

        if (!$table1 || !$table2) {
            $this->output->error('Please provide both tables: muse schema sql --from=table1 --to=table2');
            return;
        }

        if (!$schema->tableExists($table1)) {
            $this->output->error("Table '{$table1}' does not exist");
            return;
        }

        if (!$schema->tableExists($table2)) {
            $this->output->error("Table '{$table2}' does not exist");
            return;
        }

        $tableInfo1 = $schema->introspectTable($table1);
        $tableInfo2 = $schema->introspectTable($table2);

        $comparator = new Comparator();
        $diff = $comparator->compareTables($tableInfo1, $tableInfo2);

        if ($diff->isEmpty()) {
            $this->output->addLine("Tables are identical. No SQL needed.", 'success');
            return;
        }

        $generator = new DiffSqlGenerator($db);

        // Determine safe mode flags
        $safeFlags = $this->getSafeFlags();

        // Build SQL arrays
        $upSql = $generator->generateUp($diff, $safeFlags);
        $downSql = $generator->generateDown($diff, $safeFlags);

        // Check for --write-sql option
        $writeSqlPath = $this->arguments->getOpt('write-sql');
        if ($writeSqlPath) {
            $this->writeSqlToFile($writeSqlPath, $upSql, $downSql, [
                'description' => "Transform '{$table1}' to match '{$table2}'",
                'safe_flags' => $safeFlags,
            ]);
            return;
        }

        // Output to console
        $this->output->addLine("-- SQL to transform '{$table1}' to match '{$table2}'", 'info');
        $this->output->addSpacer();

        if ($safeFlags > 0) {
            $this->output->addLine("-- Safe mode enabled: " . $this->describeSafeFlags($safeFlags), 'warning');
            $this->output->addSpacer();
        }

        // Generate forward migration
        $this->output->addLine("-- UP Migration (forward):");
        foreach ($upSql as $statement) {
            $this->output->addLine($statement . ';');
        }

        $this->output->addSpacer();

        // Generate reverse migration
        $this->output->addLine("-- DOWN Migration (reverse):");
        foreach ($downSql as $statement) {
            $this->output->addLine($statement . ';');
        }
    }

    /**
     * List all tables in the database
     *
     * @museDescription  Lists all tables in the current database
     *
     * @return  void
     **/
    public function tables()
    {
        $db = \Hubzero\Facades\App::get('db');
        $tables = $db->getTableList();
        $prefix = $db->getPrefix();

        $showAll = $this->arguments->getOpt('all');
        $filter = $this->arguments->getOpt('filter');

        $this->output->addLine("Tables in database '" . \Hubzero\Facades\Config::get('db') . "':");
        $this->output->addSpacer();

        // Apply prefix filter (unless --all)
        if (!$showAll) {
            $tables = array_filter($tables, function ($table) use ($prefix) {
                return strpos($table, $prefix) === 0;
            });
        }

        // Apply filter if specified (supports regex)
        if ($filter) {
            $tables = $this->filterTables($tables, $filter);

            if ($tables === false) {
                $this->output->error("Invalid filter pattern: {$filter}");
                return;
            }
        }

        $count = 0;
        foreach ($tables as $table) {
            $this->output->addLine("  {$table}");
            $count++;
        }

        $this->output->addSpacer();
        $this->output->addLine("Total: {$count} tables", 'info');
    }

    /**
     * Export database schema to a portable format
     *
     * @museDescription  Exports the current database schema to JSON format
     *
     * @return  void
     **/
    public function export()
    {
        $db = \Hubzero\Facades\App::get('db');
        $schema = $db->schema();
        $prefix = $this->arguments->getOpt('prefix') ?: $db->getPrefix();
        $filter = $this->arguments->getOpt('filter');

        $this->output->addLine("Introspecting database schema...");

        // Build filter callback if filter is specified
        $filterCallback = null;
        if ($filter) {
            $filterCallback = $this->buildTableFilterCallback($filter);
            if ($filterCallback === false) {
                $this->output->error("Invalid filter pattern: {$filter}");
                return;
            }
            $this->output->addLine("Applying filter: {$filter}", 'info');
        }

        $databaseInfo = $schema->introspectDatabase($prefix, $filterCallback);

        $outputFile = $this->arguments->getOpt('output');
        if (!$outputFile) {
            $now = new Date();
            $outputFile = getenv('HOME') . '/schema-' . $now->format('Y-m-d-His') . '.json';
        }

        $json = json_encode($databaseInfo->toArray(), JSON_PRETTY_PRINT);
        file_put_contents($outputFile, $json);

        $this->output->addLine("Schema exported to: {$outputFile}", 'success');
        $this->output->addLine("Tables exported: " . $databaseInfo->getTableCount(), 'info');
    }

    /**
     * Compare current schema against an exported schema file
     *
     * @museDescription  Compares current database against a schema JSON file
     *
     * @return  void
     **/
    public function compare()
    {
        $inputFile = $this->arguments->getOpt('input') ?: $this->arguments->getOpt(3);

        if (!$inputFile) {
            $this->output->error('Please provide a schema file: muse schema compare --input=schema.json');
            return;
        }

        if (!file_exists($inputFile)) {
            $this->output->error("File not found: {$inputFile}");
            return;
        }

        $db = \Hubzero\Facades\App::get('db');
        $schema = $db->schema();
        $prefix = $this->arguments->getOpt('prefix') ?: $db->getPrefix();
        $filter = $this->arguments->getOpt('filter');

        $this->output->addLine("Loading schema from file...");
        $targetData = json_decode(file_get_contents($inputFile), true);
        $targetSchema = DatabaseInfo::fromArray($targetData);

        // Build filter callback if filter is specified
        $filterCallback = null;
        if ($filter) {
            $filterCallback = $this->buildTableFilterCallback($filter);
            if ($filterCallback === false) {
                $this->output->error("Invalid filter pattern: {$filter}");
                return;
            }
            $this->output->addLine("Applying filter: {$filter}", 'info');
        }

        $this->output->addLine("Introspecting current database...");
        $currentSchema = $schema->introspectDatabase($prefix, $filterCallback);

        $comparator = new Comparator();

        // Enable heuristic rename detection if requested
        if ($this->arguments->getOpt('detect-renames')) {
            $threshold = (float) ($this->arguments->getOpt('rename-threshold') ?: 0.7);
            $comparator->enableHeuristicRenameDetection($threshold);
        }

        $diff = $comparator->compareSchemas($currentSchema, $targetSchema);

        if ($diff->isEmpty()) {
            $this->output->addLine("Database schema matches the target schema!", 'success');
            return;
        }

        $this->outputSchemaDiff($diff);

        // Generate migration SQL if requested
        if ($this->arguments->getOpt('sql')) {
            $generator = new DiffSqlGenerator($db);
            $safeFlags = $this->getSafeFlags();

            $upSql = $generator->generateSchemaUp($diff, $safeFlags);
            $downSql = $generator->generateSchemaDown($diff, $safeFlags);

            // Check for --write-sql option
            $writeSqlPath = $this->arguments->getOpt('write-sql');
            if ($writeSqlPath) {
                $this->writeSqlToFile($writeSqlPath, $upSql, $downSql, [
                    'description' => "Compare current schema to {$inputFile}",
                    'safe_flags' => $safeFlags,
                ]);
                return;
            }

            $this->output->addSpacer();
            $this->output->addLine("-- Migration SQL:", 'info');

            if ($safeFlags > 0) {
                $this->output->addLine("-- Safe mode: " . $this->describeSafeFlags($safeFlags), 'warning');
            }

            foreach ($upSql as $statement) {
                $this->output->addLine($statement . ';');
            }
        }
    }

    /**
     * Generate a migration file from schema diff
     *
     * @museDescription  Generates a migration PHP file from schema differences
     *
     * @return  void
     **/
    public function generate()
    {
        $db = \Hubzero\Facades\App::get('db');
        $schema = $db->schema();

        // Check for dry-run mode
        $dryRun = $this->arguments->getOpt('dry-run');

        // Determine migration directory (skip validation in dry-run mode)
        $migrationDir = $this->arguments->getOpt('dir')
            ?: $this->arguments->getOpt('directory')
            ?: PATH_CORE . '/migrations';

        if (!$dryRun) {
            if (!is_dir($migrationDir)) {
                $this->output->error("Migration directory does not exist: {$migrationDir}");
                return;
            }

            if (!is_writable($migrationDir)) {
                $this->output->error("Migration directory is not writable: {$migrationDir}");
                return;
            }
        }

        // Get component name
        $component = $this->arguments->getOpt('component') ?: 'Core';

        // Get description
        $description = $this->arguments->getOpt('description') ?: '';

        // Initialize the generator
        $generator = new MigrationGenerator($db);
        $generator->setSafeFlags($this->getSafeFlags());

        // Check if we should include existence checks
        if ($this->arguments->getOpt('no-checks')) {
            $generator->setIncludeExistenceChecks(false);
        }

        // Check for input schema file (compare mode)
        $inputFile = $this->arguments->getOpt('input');

        if ($inputFile) {
            // Generate from schema comparison
            $this->generateFromSchemaFile($generator, $inputFile, $migrationDir, $component, $description, $dryRun);
            return;
        }

        // Check for table comparison
        $table1 = $this->arguments->getOpt('from') ?: $this->arguments->getOpt('table1');
        $table2 = $this->arguments->getOpt('to') ?: $this->arguments->getOpt('table2');

        if ($table1 && $table2) {
            // Generate from table comparison
            $this->generateFromTableComparison(
                $generator,
                $table1,
                $table2,
                $migrationDir,
                $component,
                $description,
                $dryRun
            );
            return;
        }

        // No valid source specified
        $this->output->error(
            "Please specify what to generate migration from:\n" .
            "  --from=table1 --to=table2    Compare two tables\n" .
            "  --input=schema.json          Compare current DB to schema file\n\n" .
            "Or use 'muse schema blank' to create an empty migration stub."
        );
    }

    /**
     * Generate a blank migration stub
     *
     * @museDescription  Creates an empty migration file with up() and down() stubs
     *
     * @return  void
     **/
    public function blank()
    {
        $db = \Hubzero\Facades\App::get('db');

        // Determine migration directory
        $migrationDir = $this->arguments->getOpt('dir')
            ?: $this->arguments->getOpt('directory')
            ?: PATH_CORE . '/migrations';

        if (!is_dir($migrationDir)) {
            $this->output->error("Migration directory does not exist: {$migrationDir}");
            return;
        }

        if (!is_writable($migrationDir)) {
            $this->output->error("Migration directory is not writable: {$migrationDir}");
            return;
        }

        // Get component name
        $component = $this->arguments->getOpt('component') ?: $this->arguments->getOpt(3) ?: 'Core';

        // Get description
        $description = $this->arguments->getOpt('description') ?: 'Migration script';

        // Initialize the generator
        $generator = new MigrationGenerator($db);

        // Generate blank stub
        $content = $generator->generateBlank($component, $description);

        // Write to file
        $filepath = $generator->writeToFile($content, $migrationDir, $component);

        $this->output->addLine("Migration file created:", 'success');
        $this->output->addLine("  {$filepath}");
        $this->output->addSpacer();
        $this->output->addLine("Edit the up() and down() methods to implement your migration.", 'info');
    }

    /**
     * Generate migration from schema file comparison
     *
     * @param  MigrationGenerator  $generator
     * @param  string              $inputFile
     * @param  string              $migrationDir
     * @param  string              $component
     * @param  string              $description
     * @param  bool                $dryRun
     * @return void
     */
    protected function generateFromSchemaFile(
        MigrationGenerator $generator,
        string $inputFile,
        string $migrationDir,
        string $component,
        string $description,
        bool $dryRun = false
    ): void {
        if (!file_exists($inputFile)) {
            $this->output->error("Schema file not found: {$inputFile}");
            return;
        }

        $db = \Hubzero\Facades\App::get('db');
        $schema = $db->schema();
        $prefix = $this->arguments->getOpt('prefix') ?: $db->getPrefix();

        $this->output->addLine("Loading target schema from file...");
        $targetData = json_decode(file_get_contents($inputFile), true);

        if ($targetData === null) {
            $this->output->error("Invalid JSON in schema file: {$inputFile}");
            return;
        }

        $targetSchema = DatabaseInfo::fromArray($targetData);

        $this->output->addLine("Introspecting current database...");
        $currentSchema = $schema->introspectDatabase($prefix);

        $comparator = new Comparator();

        // Enable heuristic rename detection if requested
        if ($this->arguments->getOpt('detect-renames')) {
            $threshold = (float) ($this->arguments->getOpt('rename-threshold') ?: 0.7);
            $comparator->enableHeuristicRenameDetection($threshold);
        }

        $diff = $comparator->compareSchemas($currentSchema, $targetSchema);

        if ($diff->isEmpty()) {
            $this->output->addLine("Database schema matches the target. No migration needed.", 'success');
            return;
        }

        // Show what's changing
        $addedTables = count($diff->getAddedTables());
        $removedTables = count($diff->getRemovedTables());
        $changedTables = count($diff->getChangedTables());

        $this->output->addLine("Detected changes:");
        if ($addedTables > 0) {
            $this->output->addLine("  + {$addedTables} table(s) to add", 'success');
        }
        if ($removedTables > 0) {
            $this->output->addLine("  - {$removedTables} table(s) to remove", 'error');
        }
        if ($changedTables > 0) {
            $this->output->addLine("  ~ {$changedTables} table(s) to modify", 'warning');
        }

        $this->output->addSpacer();

        // Generate migration content
        $content = $generator->generateFromSchemaDiff($diff, $component, $description);

        // Dry-run mode: show content without writing
        if ($dryRun) {
            $this->output->addLine("=== DRY RUN - Migration Preview ===", 'warning');
            $this->output->addLine("The following migration would be created:");
            $this->output->addSpacer();
            $this->output->addLine($content);
            $this->output->addSpacer();
            $this->output->addLine("=== END DRY RUN ===", 'warning');
            $this->output->addLine("No files were written. Remove --dry-run to create the migration file.");
            return;
        }

        // Write the migration
        $filepath = $generator->writeToFile($content, $migrationDir, $component);

        $this->output->addLine("Migration file created:", 'success');
        $this->output->addLine("  {$filepath}");

        $safeFlags = $this->getSafeFlags();
        if ($safeFlags > 0) {
            $this->output->addSpacer();
            $this->output->addLine("Safe mode was enabled: " . $this->describeSafeFlags($safeFlags), 'warning');
            $this->output->addLine("Some destructive operations may have been omitted.");
        }
    }

    /**
     * Generate migration from table comparison
     *
     * @param  MigrationGenerator  $generator
     * @param  string              $table1
     * @param  string              $table2
     * @param  string              $migrationDir
     * @param  string              $component
     * @param  string              $description
     * @param  bool                $dryRun
     * @return void
     */
    protected function generateFromTableComparison(
        MigrationGenerator $generator,
        string $table1,
        string $table2,
        string $migrationDir,
        string $component,
        string $description,
        bool $dryRun = false
    ): void {
        $db = \Hubzero\Facades\App::get('db');
        $schema = $db->schema();

        if (!$schema->tableExists($table1)) {
            $this->output->error("Table '{$table1}' does not exist");
            return;
        }

        if (!$schema->tableExists($table2)) {
            $this->output->error("Table '{$table2}' does not exist");
            return;
        }

        $this->output->addLine("Comparing tables '{$table1}' and '{$table2}'...");

        $tableInfo1 = $schema->introspectTable($table1);
        $tableInfo2 = $schema->introspectTable($table2);

        $comparator = new Comparator();

        // Check for rename hints
        $renameHints = $this->arguments->getOpt('column-renames');
        if ($renameHints) {
            $pairs = explode(',', $renameHints);
            foreach ($pairs as $pair) {
                list($old, $new) = explode(':', $pair);
                $comparator->addColumnRenameHint($table1, trim($old), trim($new));
            }
        }

        $diff = $comparator->compareTables($tableInfo1, $tableInfo2);

        if ($diff->isEmpty()) {
            $this->output->addLine("Tables are identical. No migration needed.", 'success');
            return;
        }

        // Show what's changing
        $addedCols = count($diff->getAddedColumns());
        $removedCols = count($diff->getRemovedColumns());
        $changedCols = count($diff->getChangedColumns());
        $renamedCols = count($diff->getRenamedColumns());

        $this->output->addLine("Detected changes:");
        if ($addedCols > 0) {
            $this->output->addLine("  + {$addedCols} column(s) to add", 'success');
        }
        if ($removedCols > 0) {
            $this->output->addLine("  - {$removedCols} column(s) to remove", 'error');
        }
        if ($changedCols > 0) {
            $this->output->addLine("  ~ {$changedCols} column(s) to modify", 'warning');
        }
        if ($renamedCols > 0) {
            $this->output->addLine("  > {$renamedCols} column(s) to rename", 'info');
        }

        $this->output->addSpacer();

        // Generate migration content
        $content = $generator->generateFromTableDiff($diff, $component, $description);

        // Dry-run mode: show content without writing
        if ($dryRun) {
            $this->output->addLine("=== DRY RUN - Migration Preview ===", 'warning');
            $this->output->addLine("The following migration would be created:");
            $this->output->addSpacer();
            $this->output->addLine($content);
            $this->output->addSpacer();
            $this->output->addLine("=== END DRY RUN ===", 'warning');
            $this->output->addLine("No files were written. Remove --dry-run to create the migration file.");
            return;
        }

        // Write to file
        $filepath = $generator->writeToFile($content, $migrationDir, $component);

        $this->output->addLine("Migration file created:", 'success');
        $this->output->addLine("  {$filepath}");

        $safeFlags = $this->getSafeFlags();
        if ($safeFlags > 0) {
            $this->output->addSpacer();
            $this->output->addLine("Safe mode was enabled: " . $this->describeSafeFlags($safeFlags), 'warning');
            $this->output->addLine("Some destructive operations may have been omitted.");
        }
    }

    /**
     * Manage schema snapshots
     *
     * @museDescription  Save, list, compare, and manage schema snapshots
     *
     * @return  void
     **/
    public function snapshot()
    {
        // Get sub-command
        $action = $this->arguments->getOpt(3) ?: 'list';

        switch ($action) {
            case 'save':
                $this->snapshotSave();
                break;
            case 'list':
                $this->snapshotList();
                break;
            case 'show':
                $this->snapshotShow();
                break;
            case 'compare':
                $this->snapshotCompare();
                break;
            case 'drift':
                $this->snapshotDrift();
                break;
            case 'delete':
                $this->snapshotDelete();
                break;
            case 'import':
                $this->snapshotImport();
                break;
            default:
                $this->output->error("Unknown snapshot action: {$action}");
                $this->output->addLine("Available actions: save, list, show, compare, drift, delete, import");
        }
    }

    /**
     * Save current schema as a snapshot
     *
     * @return void
     */
    protected function snapshotSave(): void
    {
        $name = $this->arguments->getOpt('name') ?: $this->arguments->getOpt(4);

        if (!$name) {
            // Generate a name based on current date
            $name = 'snapshot-' . (new Date())->format('Y-m-d-His');
        }

        $description = $this->arguments->getOpt('description');
        $snapshotDir = $this->getSnapshotDirectory();
        $filter = $this->arguments->getOpt('filter');

        $db = \Hubzero\Facades\App::get('db');
        $prefix = $this->arguments->getOpt('prefix') ?: $db->getPrefix();

        // Build filter callback if filter is specified
        $filterCallback = null;
        if ($filter) {
            $filterCallback = $this->buildTableFilterCallback($filter);
            if ($filterCallback === false) {
                $this->output->error("Invalid filter pattern: {$filter}");
                return;
            }
            $this->output->addLine("Applying filter: {$filter}", 'info');
        }

        try {
            $snapshot = new SchemaSnapshot($db, $snapshotDir, $prefix);
            $filepath = $snapshot->save($name, $description, $filterCallback);

            $this->output->addLine("Snapshot saved successfully!", 'success');
            $this->output->addLine("  Name: {$name}");
            $this->output->addLine("  File: {$filepath}");
        } catch (\Exception $e) {
            $this->output->error($e->getMessage());
        }
    }

    /**
     * List all available snapshots
     *
     * @return void
     */
    protected function snapshotList(): void
    {
        $snapshotDir = $this->getSnapshotDirectory();

        if (!is_dir($snapshotDir)) {
            $this->output->addLine("No snapshots found. Use 'muse schema snapshot save' to create one.");
            return;
        }

        $db = \Hubzero\Facades\App::get('db');
        $snapshot = new SchemaSnapshot($db, $snapshotDir);

        $sortBy = $this->arguments->getOpt('sort') ?: 'date';
        $descending = !$this->arguments->getOpt('asc');

        $list = $snapshot->list($sortBy, $descending);

        if (empty($list)) {
            $this->output->addLine("No snapshots found. Use 'muse schema snapshot save' to create one.");
            return;
        }

        $this->output->addLine("Available schema snapshots:", 'info');
        $this->output->addSpacer();

        foreach ($list as $info) {
            $this->output->addLine("  {$info['name']}", 'success');
            if ($info['description']) {
                $this->output->addLine("    Description: {$info['description']}");
            }
            $this->output->addLine("    Created: {$info['created_at']}");
            $this->output->addLine("    Tables: {$info['table_count']}");
            $this->output->addLine("    Size: " . $this->formatBytes($info['size']));
            $this->output->addSpacer();
        }

        $this->output->addLine("Total: " . count($list) . " snapshot(s)", 'info');
    }

    /**
     * Show details of a specific snapshot
     *
     * @return void
     */
    protected function snapshotShow(): void
    {
        $name = $this->arguments->getOpt('name') ?: $this->arguments->getOpt(4);

        if (!$name) {
            $this->output->error("Please provide a snapshot name: muse schema snapshot show <name>");
            return;
        }

        $snapshotDir = $this->getSnapshotDirectory();
        $db = \Hubzero\Facades\App::get('db');

        try {
            $snapshot = new SchemaSnapshot($db, $snapshotDir);
            $data = $snapshot->loadRaw($name);

            $this->output->addLine("Snapshot: {$data['name']}", 'info');
            $this->output->addSpacer();

            if ($data['description']) {
                $this->output->addLine("Description: {$data['description']}");
            }
            $this->output->addLine("Created: {$data['created_at']}");
            $this->output->addLine("Database: {$data['database']}");
            $this->output->addLine("Driver: {$data['driver']}");
            $this->output->addLine("Prefix: {$data['prefix']}");
            $this->output->addLine("Tables: {$data['table_count']}");

            if (!empty($data['metadata'])) {
                $this->output->addSpacer();
                $this->output->addLine("Metadata:", 'info');
                foreach ($data['metadata'] as $key => $value) {
                    $this->output->addLine("  {$key}: " . (is_array($value) ? json_encode($value) : $value));
                }
            }

            // Show table list if requested
            if ($this->arguments->getOpt('tables')) {
                $this->output->addSpacer();
                $this->output->addLine("Tables:", 'info');
                foreach ($data['schema']['tables'] ?? [] as $table) {
                    $tableName = is_array($table) ? ($table['name'] ?? 'unknown') : $table;
                    $colCount = is_array($table) ? count($table['columns'] ?? []) : 0;
                    $this->output->addLine("  {$tableName} ({$colCount} columns)");
                }
            }
        } catch (\Exception $e) {
            $this->output->error($e->getMessage());
        }
    }

    /**
     * Compare snapshots or current schema to a snapshot
     *
     * @return void
     */
    protected function snapshotCompare(): void
    {
        $name1 = $this->arguments->getOpt('name') ?: $this->arguments->getOpt(4);
        $name2 = $this->arguments->getOpt('to') ?: $this->arguments->getOpt(5);

        if (!$name1) {
            $this->output->error(
                "Please provide snapshot name(s):\n" .
                "  muse schema snapshot compare <name>           Compare current DB to snapshot\n" .
                "  muse schema snapshot compare <from> --to=<to> Compare two snapshots"
            );
            return;
        }

        $snapshotDir = $this->getSnapshotDirectory();
        $db = \Hubzero\Facades\App::get('db');
        $filter = $this->arguments->getOpt('filter');

        // Build filter callback if filter is specified
        $filterCallback = null;
        if ($filter) {
            $filterCallback = $this->buildTableFilterCallback($filter);
            if ($filterCallback === false) {
                $this->output->error("Invalid filter pattern: {$filter}");
                return;
            }
            $this->output->addLine("Applying filter: {$filter}", 'info');
        }

        try {
            $snapshot = new SchemaSnapshot($db, $snapshotDir);

            $enableRenames = $this->arguments->getOpt('detect-renames');
            $threshold = (float) ($this->arguments->getOpt('rename-threshold') ?: 0.7);

            if ($name2) {
                // Compare two snapshots
                $this->output->addLine("Comparing snapshots: '{$name1}' -> '{$name2}'", 'info');
                $diff = $snapshot->compareSnapshots($name1, $name2, $enableRenames, $threshold);
            } else {
                // Compare current schema to snapshot
                $this->output->addLine("Comparing current database to snapshot: '{$name1}'", 'info');
                $diff = $snapshot->compareToSnapshot($name1, $enableRenames, $threshold, $filterCallback);
            }

            $this->output->addSpacer();

            if ($diff->isEmpty()) {
                $this->output->addLine("No differences found!", 'success');
                return;
            }

            $this->outputSchemaDiff($diff);

            // Generate SQL if requested
            if ($this->arguments->getOpt('sql')) {
                $generator = new DiffSqlGenerator($db);
                $safeFlags = $this->getSafeFlags();

                $upSql = $generator->generateSchemaUp($diff, $safeFlags);
                $downSql = $generator->generateSchemaDown($diff, $safeFlags);

                // Check for --write-sql option
                $writeSqlPath = $this->arguments->getOpt('write-sql');
                if ($writeSqlPath) {
                    $desc = $name2
                        ? "Compare snapshots: {$name1} -> {$name2}"
                        : "Compare current database to snapshot: {$name1}";
                    $this->writeSqlToFile($writeSqlPath, $upSql, $downSql, [
                        'description' => $desc,
                        'safe_flags' => $safeFlags,
                    ]);
                    return;
                }

                $this->output->addSpacer();
                $this->output->addLine("-- Migration SQL:", 'info');

                if ($safeFlags > 0) {
                    $this->output->addLine("-- Safe mode: " . $this->describeSafeFlags($safeFlags), 'warning');
                }

                foreach ($upSql as $statement) {
                    $this->output->addLine($statement . ';');
                }
            }
        } catch (\Exception $e) {
            $this->output->error($e->getMessage());
        }
    }

    /**
     * Show drift summary between current schema and a snapshot
     *
     * @return void
     */
    protected function snapshotDrift(): void
    {
        $name = $this->arguments->getOpt('name') ?: $this->arguments->getOpt(4);

        if (!$name) {
            $this->output->error("Please provide a snapshot name: muse schema snapshot drift <name>");
            return;
        }

        $snapshotDir = $this->getSnapshotDirectory();
        $db = \Hubzero\Facades\App::get('db');
        $filter = $this->arguments->getOpt('filter');

        // Build filter callback if filter is specified
        $filterCallback = null;
        if ($filter) {
            $filterCallback = $this->buildTableFilterCallback($filter);
            if ($filterCallback === false) {
                $this->output->error("Invalid filter pattern: {$filter}");
                return;
            }
            $this->output->addLine("Applying filter: {$filter}", 'info');
        }

        try {
            $snapshot = new SchemaSnapshot($db, $snapshotDir);
            $summary = $snapshot->getDriftSummary($name, $filterCallback);

            $this->output->addLine("Schema Drift Report: '{$name}'", 'info');
            $this->output->addSpacer();

            if (!$summary['has_drift']) {
                $this->output->addLine("No drift detected - schema matches snapshot!", 'success');
                return;
            }

            $this->output->addLine("Drift detected!", 'warning');
            $this->output->addSpacer();

            // Summary counts
            $this->output->addLine("Summary:");
            $this->output->addLine("  Added tables:    {$summary['counts']['added_tables']}");
            $this->output->addLine("  Removed tables:  {$summary['counts']['removed_tables']}");
            $this->output->addLine("  Modified tables: {$summary['counts']['modified_tables']}");
            $this->output->addLine("  Renamed tables:  {$summary['counts']['renamed_tables']}");
            $this->output->addLine("  Total changes:   {$summary['counts']['total_changes']}");

            // Details
            if (!empty($summary['added_tables'])) {
                $this->output->addSpacer();
                $this->output->addLine("Added tables:", 'success');
                foreach ($summary['added_tables'] as $table) {
                    $this->output->addLine("  + {$table}");
                }
            }

            if (!empty($summary['removed_tables'])) {
                $this->output->addSpacer();
                $this->output->addLine("Removed tables:", 'error');
                foreach ($summary['removed_tables'] as $table) {
                    $this->output->addLine("  - {$table}");
                }
            }

            if (!empty($summary['renamed_tables'])) {
                $this->output->addSpacer();
                $this->output->addLine("Renamed tables:");
                foreach ($summary['renamed_tables'] as $oldName => $newName) {
                    $this->output->addLine("  {$oldName} -> {$newName}");
                }
            }

            if (!empty($summary['modified_tables'])) {
                $this->output->addSpacer();
                $this->output->addLine("Modified tables:", 'warning');
                foreach ($summary['modified_tables'] as $tableName => $changes) {
                    $parts = [];
                    if ($changes['added_columns'] > 0) {
                        $parts[] = "+{$changes['added_columns']} cols";
                    }
                    if ($changes['removed_columns'] > 0) {
                        $parts[] = "-{$changes['removed_columns']} cols";
                    }
                    if ($changes['modified_columns'] > 0) {
                        $parts[] = "~{$changes['modified_columns']} cols";
                    }
                    if ($changes['renamed_columns'] > 0) {
                        $parts[] = ">{$changes['renamed_columns']} renamed";
                    }
                    $this->output->addLine("  ~ {$tableName}: " . implode(', ', $parts));
                }
            }
        } catch (\Exception $e) {
            $this->output->error($e->getMessage());
        }
    }

    /**
     * Delete a snapshot
     *
     * @return void
     */
    protected function snapshotDelete(): void
    {
        $name = $this->arguments->getOpt('name') ?: $this->arguments->getOpt(4);

        if (!$name) {
            $this->output->error("Please provide a snapshot name: muse schema snapshot delete <name>");
            return;
        }

        $snapshotDir = $this->getSnapshotDirectory();
        $db = \Hubzero\Facades\App::get('db');

        try {
            $snapshot = new SchemaSnapshot($db, $snapshotDir);

            if (!$snapshot->exists($name)) {
                $this->output->error("Snapshot not found: {$name}");
                return;
            }

            // Confirm deletion unless --force is used
            if (!$this->arguments->getOpt('force')) {
                $this->output->addLine("Are you sure you want to delete snapshot '{$name}'?", 'warning');
                $this->output->addLine("Use --force to skip this confirmation.");
                return;
            }

            if ($snapshot->delete($name)) {
                $this->output->addLine("Snapshot deleted: {$name}", 'success');
            } else {
                $this->output->error("Failed to delete snapshot: {$name}");
            }
        } catch (\Exception $e) {
            $this->output->error($e->getMessage());
        }
    }

    /**
     * Import a schema file as a snapshot
     *
     * @return void
     */
    protected function snapshotImport(): void
    {
        $name = $this->arguments->getOpt('name') ?: $this->arguments->getOpt(4);
        $file = $this->arguments->getOpt('file') ?: $this->arguments->getOpt(5);

        if (!$name || !$file) {
            $this->output->error(
                "Please provide both name and file:\n" .
                "  muse schema snapshot import <name> --file=<path>\n" .
                "  muse schema snapshot import <name> <path>"
            );
            return;
        }

        $snapshotDir = $this->getSnapshotDirectory();
        $db = \Hubzero\Facades\App::get('db');
        $description = $this->arguments->getOpt('description');

        try {
            $snapshot = new SchemaSnapshot($db, $snapshotDir);
            $filepath = $snapshot->createFromFile($name, $file, $description);

            $this->output->addLine("Snapshot imported successfully!", 'success');
            $this->output->addLine("  Name: {$name}");
            $this->output->addLine("  File: {$filepath}");
        } catch (\Exception $e) {
            $this->output->error($e->getMessage());
        }
    }

    /**
     * Get the snapshot directory path
     *
     * @return string
     */
    protected function getSnapshotDirectory(): string
    {
        $dir = $this->arguments->getOpt('snapshot-dir');

        if ($dir) {
            return $dir;
        }

        // Default to a snapshots directory under bootstrap
        return PATH_CORE . '/bootstrap/Install/snapshots';
    }

    /**
     * Format bytes to human-readable size
     *
     * @param  int  $bytes
     * @return string
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Squash all existing migrations into a single schema migration
     *
     * This generates a single migration file that creates all tables from
     * the current database state. Unlike Laravel's raw SQL dump, this produces
     * cross-database-compatible PHP code.
     *
     * Use cases:
     * - Simplify migration history for new installations
     * - Create a baseline for testing
     * - Reduce migration file count (400+ files → 1 file)
     *
     * @museDescription  Generates a single migration from current database schema
     *
     * @return  void
     **/
    public function squash()
    {
        $db = \Hubzero\Facades\App::get('db');

        // Check for dry-run mode
        $dryRun = $this->arguments->getOpt('dry-run');

        // Determine migration directory
        $migrationDir = $this->arguments->getOpt('dir')
            ?: $this->arguments->getOpt('directory')
            ?: PATH_CORE . '/migrations';

        if (!$dryRun) {
            if (!is_dir($migrationDir)) {
                $this->output->error("Migration directory does not exist: {$migrationDir}");
                return;
            }

            if (!is_writable($migrationDir)) {
                $this->output->error("Migration directory is not writable: {$migrationDir}");
                return;
            }
        }

        // Get component name
        $component = $this->arguments->getOpt('component') ?: 'SquashedSchema';

        // Get description
        $description = $this->arguments->getOpt('description') ?: '';

        // Get table prefix
        $prefix = $this->arguments->getOpt('prefix') ?: '#__';

        // Initialize the squasher
        $squasher = new MigrationSquasher($db);

        // Handle existence checks option
        if ($this->arguments->getOpt('no-checks')) {
            $squasher->setIncludeExistenceChecks(false);
        }

        // Handle foreign keys option
        if ($this->arguments->getOpt('no-foreign-keys')) {
            $squasher->setIncludeForeignKeys(false);
        }

        // Handle excluded tables
        $excludeTables = $this->arguments->getOpt('exclude');
        if ($excludeTables) {
            $tables = array_map('trim', explode(',', $excludeTables));
            $squasher->setExcludeTables($tables);
        }

        // Apply filter
        $filter = $this->arguments->getOpt('filter');

        $this->output->addLine("Analyzing database schema...", 'info');
        $this->output->addSpacer();

        // Introspect database
        $filterCallback = $filter ? $this->buildTableFilterCallback($filter) : null;
        $databaseInfo = $db->schema()->introspectDatabase($prefix, $filterCallback);

        // Get statistics
        $stats = $squasher->getSquashStats($databaseInfo);

        $this->output->addLine("Schema Summary:", 'warning');
        $this->output->addLine("  Tables:       {$stats['tables']}");
        $this->output->addLine("  Columns:      {$stats['columns']}");
        $this->output->addLine("  Indexes:      {$stats['indexes']}");
        $this->output->addLine("  Foreign Keys: {$stats['foreign_keys']}");

        if ($stats['excluded'] > 0) {
            $this->output->addLine("  Excluded:     {$stats['excluded']}");
        }

        $this->output->addSpacer();

        if ($stats['tables'] === 0) {
            $this->output->error("No tables found to squash. Check your prefix and filter options.");
            return;
        }

        // Show existing migration count
        $existingMigrations = $squasher->getExistingMigrations($migrationDir);
        $migrationCount = count($existingMigrations);

        if ($migrationCount > 0) {
            $this->output->addLine("Existing migrations: {$migrationCount} files", 'info');
            $this->output->addSpacer();
        }

        // Generate the squashed migration
        $this->output->addLine("Generating squashed migration...", 'info');

        $content = $squasher->generateFromSchema($databaseInfo, $component, $description);

        if ($dryRun) {
            $this->output->addLine("DRY RUN - Migration content preview:", 'warning');
            $this->output->addSpacer();
            $this->output->addLine($content);
            $this->output->addSpacer();
            $this->output->addLine("No files were written (dry-run mode).", 'info');
            return;
        }

        // Write the file
        $filepath = $squasher->writeToFile($content, $migrationDir, $component);

        $this->output->addSpacer();
        $this->output->addLine("Squashed migration created:", 'success');
        $this->output->addLine("  {$filepath}");
        $this->output->addSpacer();

        // Warn about pruning
        if ($migrationCount > 0 && $this->arguments->getOpt('prune')) {
            $this->output->addLine("PRUNE MODE NOT YET IMPLEMENTED", 'warning');
            $this->output->addLine("To safely prune old migrations:");
            $this->output->addLine("  1. Back up your migrations directory");
            $this->output->addLine("  2. Run the squashed migration on a test database");
            $this->output->addLine("  3. Manually remove old migration files");
            $this->output->addLine("  4. Mark old migrations as executed in #__migrations table");
        } else {
            $this->output->addLine("IMPORTANT:", 'warning');
            $this->output->addLine("  - This migration should only run on fresh installations");
            $this->output->addLine("  - Existing databases will skip table creation (IF NOT EXISTS)");
            $this->output->addLine("  - Old migration files are NOT automatically removed");
            $this->output->addSpacer();
            $this->output->addLine("To use this for fresh installs:");
            $this->output->addLine("  1. Test the squashed migration on a fresh database");
            $this->output->addLine("  2. Back up and remove old migration files");
            $this->output->addLine("  3. Update your deployment process to use the squashed migration");
        }
    }

    /**
     * Output help documentation
     *
     * @return  void
     **/
    public function help()
    {
        $this
            ->output
            ->addOverview(
                'Schema management tools for comparing database structures,
                generating diffs, producing migration SQL, and managing snapshots.

                Supports safe mode to prevent destructive operations like
                DROP TABLE and DROP COLUMN from being generated.'
            )
            ->addTasks($this)
            ->addArgument(
                '--table1, --table2: Tables to compare',
                'Specify two tables to compare their structures.',
                'Example: muse schema diff --table1=users --table2=users_backup'
            )
            ->addArgument(
                '--from, --to: Source and target for SQL generation',
                'Generate SQL to transform one table to match another.',
                'Example: muse schema sql --from=old_table --to=new_table'
            )
            ->addArgument(
                '--sql: Output SQL statements',
                'Include this flag to output the SQL needed to apply changes.',
                'Example: muse schema diff --table1=a --table2=b --sql'
            )
            ->addArgument(
                '--safe: Enable safe mode (all protections)',
                'Prevents all destructive operations (DROP TABLE, DROP COLUMN, etc.)',
                'Example: muse schema sql --from=a --to=b --safe'
            )
            ->addArgument(
                '--safe-tables: Prevent DROP TABLE',
                'Only prevent DROP TABLE operations.',
                'Example: muse schema compare --input=schema.json --sql --safe-tables'
            )
            ->addArgument(
                '--safe-columns: Prevent DROP COLUMN',
                'Only prevent DROP COLUMN operations.',
                'Example: muse schema sql --from=a --to=b --safe-columns'
            )
            ->addArgument(
                '--safe-indexes: Prevent DROP INDEX',
                'Only prevent DROP INDEX operations.'
            )
            ->addArgument(
                '--safe-foreign-keys: Prevent DROP FOREIGN KEY',
                'Only prevent DROP FOREIGN KEY operations.'
            )
            ->addArgument(
                '--safe-shrinking: Prevent data-losing type changes',
                'Prevents column modifications that could lose data
                (e.g., VARCHAR(255) to VARCHAR(100), INT to TINYINT).'
            )
            ->addArgument(
                '--column-renames: Hint for column renames',
                'Provide hints for detecting column renames (old:new pairs).',
                'Example: --column-renames=old_name:new_name,foo:bar'
            )
            ->addArgument(
                '--detect-renames: Enable heuristic rename detection',
                'Automatically detect table and column renames using similarity analysis.',
                'Example: muse schema compare --input=schema.json --detect-renames'
            )
            ->addArgument(
                '--rename-threshold: Rename detection threshold',
                'Similarity threshold for rename detection (0.0-1.0, default 0.7).',
                'Example: --detect-renames --rename-threshold=0.8'
            )
            ->addArgument(
                '--all: Show all tables',
                'Include non-prefixed tables in the list.',
                'Example: muse schema tables --all'
            )
            ->addArgument(
                '--filter: Filter tables with pattern',
                'Filter tables using substring, regex, or negation patterns.
                Supports multiple comma-separated patterns. Pattern types:
                  - Substring: "user" matches tables containing "user"
                  - Regex: "/^jos_user/" matches tables starting with "jos_user"
                  - Negation: "!session" excludes tables containing "session"
                  - Negated regex: "!/^jos_session/" excludes matching tables
                Applied to tables, export, compare, and snapshot commands.',
                'Examples:
                  muse schema tables --filter=user
                  muse schema tables --filter="/^jos_/"
                  muse schema export --filter="user,!session"
                  muse schema snapshot save --filter="!/^jos_session/"'
            )
            ->addArgument(
                '--full: Compare full database schema',
                'Compare the entire database schema instead of individual tables.'
            )
            ->addArgument(
                '--input: Input schema file',
                'JSON file containing the target schema for comparison.',
                'Example: muse schema compare --input=schema.json'
            )
            ->addArgument(
                '--output: Output file path',
                'Path for export output file.',
                'Example: muse schema export --output=/path/to/schema.json'
            )
            ->addArgument(
                '--prefix: Table prefix filter',
                'Only include tables with this prefix.',
                'Example: muse schema export --prefix=jos_'
            )
            ->addArgument(
                '--component: Component name for migration',
                'Name appended to migration class (e.g., ComUsers, Core).',
                'Example: muse schema generate --component=ComUsers'
            )
            ->addArgument(
                '--dir, --directory: Migration directory',
                'Directory to write migration files to. Defaults to core/migrations.',
                'Example: muse schema generate --dir=/path/to/migrations'
            )
            ->addArgument(
                '--description: Migration description',
                'Description for the generated migration file.',
                'Example: muse schema generate --description="Add user preferences table"'
            )
            ->addArgument(
                '--no-checks: Skip table existence checks',
                'Do not generate if (tableExists) checks in migration code.',
                'Example: muse schema generate --from=a --to=b --no-checks'
            )
            ->addArgument(
                '--snapshot-dir: Snapshot storage directory',
                'Directory to store schema snapshots. Defaults to core/bootstrap/Install/snapshots.',
                'Example: muse schema snapshot save --snapshot-dir=/path/to/snapshots'
            )
            ->addArgument(
                '--name: Snapshot name',
                'Name for the snapshot (alphanumeric, dashes, underscores).',
                'Example: muse schema snapshot save --name=production-v1'
            )
            ->addArgument(
                '--sort: Sort order for snapshot list',
                'Sort snapshots by: name, date, or tables.',
                'Example: muse schema snapshot list --sort=name'
            )
            ->addArgument(
                '--asc: Ascending sort order',
                'Sort in ascending order (default is descending for dates).',
                'Example: muse schema snapshot list --sort=date --asc'
            )
            ->addArgument(
                '--tables: Show table list in snapshot',
                'Include table listing when showing snapshot details.',
                'Example: muse schema snapshot show mysnap --tables'
            )
            ->addArgument(
                '--force: Skip confirmation prompts',
                'Skip confirmation when deleting snapshots.',
                'Example: muse schema snapshot delete oldsnap --force'
            )
            ->addArgument(
                '--file: Schema file for import',
                'Path to schema JSON file when importing as a snapshot.',
                'Example: muse schema snapshot import mysnap --file=schema.json'
            )
            ->addArgument(
                '--dry-run: Preview without creating files',
                'Show what migration would be generated without writing files.
                Useful for reviewing changes before committing.',
                'Example: muse schema generate --from=a --to=b --dry-run'
            )
            ->addArgument(
                '--write-sql: Write SQL to file',
                'Output generated SQL to a file instead of console.
                If a directory is specified, a timestamped filename is generated.
                Useful for DBA review or audit trails.',
                'Example: muse schema sql --from=a --to=b --write-sql=/path/to/output.sql'
            )
            ->addArgument(
                '--exclude: Tables to exclude from squash',
                'Comma-separated list of table names to exclude from squashing.',
                'Example: muse schema squash --exclude=sessions,cache'
            )
            ->addArgument(
                '--no-foreign-keys: Exclude foreign keys from squash',
                'Generate squashed migration without foreign key constraints.
                Useful for databases with circular references.',
                'Example: muse schema squash --no-foreign-keys'
            )
            ->addArgument(
                '--prune: Prune old migrations after squash',
                'NOT YET IMPLEMENTED. Would remove old migration files after squashing.',
                'Example: muse schema squash --prune (future feature)'
            );
    }

    /**
     * Introspect and display a table's structure
     *
     * @param  string  $table  Table name
     * @return void
     */
    protected function introspectTable(string $table): void
    {
        $db = \Hubzero\Facades\App::get('db');
        $schema = $db->schema();

        if (!$schema->tableExists($table)) {
            $this->output->error("Table '{$table}' does not exist");
            return;
        }

        $tableInfo = $schema->introspectTable($table);

        $this->output->addLine("Table: {$table}", 'info');
        $this->output->addSpacer();

        // Table properties
        if ($engine = $tableInfo->getEngine()) {
            $this->output->addLine("  Engine: {$engine}");
        }
        if ($charset = $tableInfo->getCharset()) {
            $this->output->addLine("  Charset: {$charset}");
        }
        if ($collation = $tableInfo->getCollation()) {
            $this->output->addLine("  Collation: {$collation}");
        }

        $this->output->addSpacer();
        $this->output->addLine("Columns:", 'info');

        foreach ($tableInfo->getColumns() as $column) {
            $line = "  {$column->getName()}: {$column->getFullType()}";
            $line .= $column->isNullable() ? ' NULL' : ' NOT NULL';
            if ($column->hasDefault()) {
                $default = $column->getDefault();
                $line .= " DEFAULT " . ($default === null ? 'NULL' : "'{$default}'");
            }
            if ($column->isAutoIncrement()) {
                $line .= ' AUTO_INCREMENT';
            }
            if ($column->isPrimaryKey()) {
                $line .= ' [PK]';
            }
            $this->output->addLine($line);
        }

        // Indexes
        $indexes = $tableInfo->getIndexes();
        if (count($indexes) > 0) {
            $this->output->addSpacer();
            $this->output->addLine("Indexes:", 'info');
            foreach ($indexes as $index) {
                $type = $index->isPrimary() ? 'PRIMARY' : ($index->isUnique() ? 'UNIQUE' : 'INDEX');
                $cols = implode(', ', $index->getColumns());
                $this->output->addLine("  {$index->getName()}: {$type} ({$cols})");
            }
        }

        // Foreign keys
        $foreignKeys = $tableInfo->getForeignKeys();
        if (count($foreignKeys) > 0) {
            $this->output->addSpacer();
            $this->output->addLine("Foreign Keys:", 'info');
            foreach ($foreignKeys as $fk) {
                $localCols = implode(', ', $fk->getColumns());
                $foreignCols = implode(', ', $fk->getForeignColumns());
                $fkName = $fk->getName();
                $fkTable = $fk->getForeignTable();
                $this->output->addLine("  {$fkName}: ({$localCols}) -> {$fkTable}({$foreignCols})");
                if ($fk->getOnDelete()) {
                    $this->output->addLine("    ON DELETE {$fk->getOnDelete()}");
                }
                if ($fk->getOnUpdate()) {
                    $this->output->addLine("    ON UPDATE {$fk->getOnUpdate()}");
                }
            }
        }
    }

    /**
     * Output a table diff in human-readable format
     *
     * @param  \Hubzero\Database\Schema\Diff\TableDiff  $diff
     * @param  string  $table1
     * @param  string  $table2
     * @return void
     */
    protected function outputTableDiff($diff, string $table1, string $table2): void
    {
        $this->output->addLine("Differences between '{$table1}' and '{$table2}':", 'info');
        $this->output->addSpacer();

        // Added columns
        $added = $diff->getAddedColumns();
        if (count($added) > 0) {
            $this->output->addLine("Columns added in '{$table2}':", 'success');
            foreach ($added as $column) {
                $this->output->addLine("  + {$column->getName()}: {$column->getFullType()}");
            }
            $this->output->addSpacer();
        }

        // Removed columns
        $removed = $diff->getRemovedColumns();
        if (count($removed) > 0) {
            $this->output->addLine("Columns removed from '{$table2}':", 'error');
            foreach ($removed as $column) {
                $this->output->addLine("  - {$column->getName()}: {$column->getFullType()}");
            }
            $this->output->addSpacer();
        }

        // Changed columns
        $changed = $diff->getChangedColumns();
        if (count($changed) > 0) {
            $this->output->addLine("Columns modified:", 'warning');
            foreach ($changed as $colDiff) {
                $from = $colDiff->getFromColumn();
                $to = $colDiff->getToColumn();
                $this->output->addLine("  ~ {$from->getName()}:");

                if ($colDiff->hasTypeChanged()) {
                    $this->output->addLine("      type: {$from->getFullType()} -> {$to->getFullType()}");
                }
                if ($colDiff->hasNullableChanged()) {
                    $fromNull = $from->isNullable() ? 'NULL' : 'NOT NULL';
                    $toNull = $to->isNullable() ? 'NULL' : 'NOT NULL';
                    $this->output->addLine("      nullable: {$fromNull} -> {$toNull}");
                }
                if ($colDiff->hasDefaultChanged()) {
                    $fromDef = $from->hasDefault() ? "'{$from->getDefault()}'" : 'none';
                    $toDef = $to->hasDefault() ? "'{$to->getDefault()}'" : 'none';
                    $this->output->addLine("      default: {$fromDef} -> {$toDef}");
                }
            }
            $this->output->addSpacer();
        }

        // Renamed columns
        $renamed = $diff->getRenamedColumns();
        if (count($renamed) > 0) {
            $this->output->addLine("Columns renamed:", 'info');
            foreach ($renamed as $oldName => $newName) {
                $this->output->addLine("  {$oldName} -> {$newName}");
            }
            $this->output->addSpacer();
        }

        // Index changes
        $addedIdx = $diff->getAddedIndexes();
        $removedIdx = $diff->getRemovedIndexes();

        if (count($addedIdx) > 0 || count($removedIdx) > 0) {
            $this->output->addLine("Index changes:");
            foreach ($addedIdx as $index) {
                $cols = implode(', ', $index->getColumns());
                $this->output->addLine("  + {$index->getName()} ({$cols})", 'success');
            }
            foreach ($removedIdx as $index) {
                $cols = implode(', ', $index->getColumns());
                $this->output->addLine("  - {$index->getName()} ({$cols})", 'error');
            }
            $this->output->addSpacer();
        }

        // Foreign key changes
        $addedFk = $diff->getAddedForeignKeys();
        $removedFk = $diff->getRemovedForeignKeys();

        if (count($addedFk) > 0 || count($removedFk) > 0) {
            $this->output->addLine("Foreign key changes:");
            foreach ($addedFk as $fk) {
                $this->output->addLine("  + {$fk->getName()}", 'success');
            }
            foreach ($removedFk as $fk) {
                $this->output->addLine("  - {$fk->getName()}", 'error');
            }
            $this->output->addSpacer();
        }
    }

    /**
     * Output a schema diff in human-readable format
     *
     * @param  \Hubzero\Database\Schema\Diff\SchemaDiff  $diff
     * @return void
     */
    protected function outputSchemaDiff($diff): void
    {
        $this->output->addLine("Schema differences:", 'info');
        $this->output->addSpacer();

        // Added tables
        $added = $diff->getAddedTables();
        if (count($added) > 0) {
            $this->output->addLine("Tables to add:", 'success');
            foreach ($added as $table) {
                $colCount = count($table->getColumns());
                $this->output->addLine("  + {$table->getName()} ({$colCount} columns)");
            }
            $this->output->addSpacer();
        }

        // Removed tables
        $removed = $diff->getRemovedTables();
        if (count($removed) > 0) {
            $this->output->addLine("Tables to remove:", 'error');
            foreach ($removed as $table) {
                $this->output->addLine("  - {$table->getName()}");
            }
            $this->output->addSpacer();
        }

        // Renamed tables
        $renamed = $diff->getRenamedTables();
        if (count($renamed) > 0) {
            $this->output->addLine("Tables renamed:", 'info');
            foreach ($renamed as $oldName => $newName) {
                $this->output->addLine("  {$oldName} -> {$newName}");
            }
            $this->output->addSpacer();
        }

        // Changed tables
        $changed = $diff->getChangedTables();
        if (count($changed) > 0) {
            $this->output->addLine("Tables modified:", 'warning');
            foreach ($changed as $tableDiff) {
                $name = $tableDiff->getFromTable()->getName();
                $addedCols = count($tableDiff->getAddedColumns());
                $removedCols = count($tableDiff->getRemovedColumns());
                $changedCols = count($tableDiff->getChangedColumns());

                $changes = [];
                if ($addedCols > 0) {
                    $changes[] = "+{$addedCols} cols";
                }
                if ($removedCols > 0) {
                    $changes[] = "-{$removedCols} cols";
                }
                if ($changedCols > 0) {
                    $changes[] = "~{$changedCols} cols";
                }

                $this->output->addLine("  ~ {$name}: " . implode(', ', $changes));
            }
            $this->output->addSpacer();
        }
    }

    /**
     * Output SQL for a table diff
     *
     * @param  \Hubzero\Database\Schema\Diff\TableDiff  $diff
     * @param  \Hubzero\Database\Driver  $db
     * @return void
     */
    protected function outputDiffSql($diff, $db): void
    {
        $generator = new DiffSqlGenerator($db);
        $safeFlags = $this->getSafeFlags();

        $this->output->addSpacer();
        $this->output->addLine("-- Migration SQL:", 'info');

        if ($safeFlags > 0) {
            $this->output->addLine("-- Safe mode: " . $this->describeSafeFlags($safeFlags), 'warning');
        }

        $sql = $generator->generateUp($diff, $safeFlags);
        foreach ($sql as $statement) {
            $this->output->addLine($statement . ';');
        }
    }

    /**
     * Compare full database schema
     *
     * @return void
     */
    protected function diffFullSchema(): void
    {
        $this->output->addLine("Full schema diff not yet implemented.");
        $this->output->addLine("Use 'muse schema export' to export schema, then 'muse schema compare' to compare.");
    }

    /**
     * Get safe mode flags from command line arguments
     *
     * @return int
     */
    protected function getSafeFlags(): int
    {
        $flags = 0;

        // --safe enables all protections
        if ($this->arguments->getOpt('safe')) {
            return DiffSqlGenerator::SAFE_ALL;
        }

        // Individual flags
        if ($this->arguments->getOpt('safe-tables')) {
            $flags |= DiffSqlGenerator::SAFE_NO_DROP_TABLES;
        }
        if ($this->arguments->getOpt('safe-columns')) {
            $flags |= DiffSqlGenerator::SAFE_NO_DROP_COLUMNS;
        }
        if ($this->arguments->getOpt('safe-indexes')) {
            $flags |= DiffSqlGenerator::SAFE_NO_DROP_INDEXES;
        }
        if ($this->arguments->getOpt('safe-foreign-keys')) {
            $flags |= DiffSqlGenerator::SAFE_NO_DROP_FOREIGN_KEYS;
        }
        if ($this->arguments->getOpt('safe-shrinking')) {
            $flags |= DiffSqlGenerator::SAFE_NO_SHRINKING_MODIFICATIONS;
        }

        return $flags;
    }

    /**
     * Describe the active safe flags in human-readable format
     *
     * @param  int  $flags
     * @return string
     */
    protected function describeSafeFlags(int $flags): string
    {
        if ($flags === DiffSqlGenerator::SAFE_ALL) {
            return 'ALL (no destructive operations)';
        }

        $descriptions = [];

        if ($flags & DiffSqlGenerator::SAFE_NO_DROP_TABLES) {
            $descriptions[] = 'no DROP TABLE';
        }
        if ($flags & DiffSqlGenerator::SAFE_NO_DROP_COLUMNS) {
            $descriptions[] = 'no DROP COLUMN';
        }
        if ($flags & DiffSqlGenerator::SAFE_NO_DROP_INDEXES) {
            $descriptions[] = 'no DROP INDEX';
        }
        if ($flags & DiffSqlGenerator::SAFE_NO_DROP_FOREIGN_KEYS) {
            $descriptions[] = 'no DROP FOREIGN KEY';
        }
        if ($flags & DiffSqlGenerator::SAFE_NO_SHRINKING_MODIFICATIONS) {
            $descriptions[] = 'no shrinking type changes';
        }

        return implode(', ', $descriptions);
    }

    /**
     * Build a table filter callback from a filter string
     *
     * Returns a callable that accepts a table name and returns true if it
     * should be included, false if excluded.
     *
     * @param  string  $filter  Filter pattern(s)
     * @return callable|false   Callback function or false on invalid regex
     */
    protected function buildTableFilterCallback(string $filter)
    {
        // Parse patterns
        $patterns = array_map('trim', explode(',', $filter));

        $includePatterns = [];
        $excludePatterns = [];

        foreach ($patterns as $pattern) {
            if (empty($pattern)) {
                continue;
            }

            // Check for negation prefix
            $isNegation = false;
            if (strpos($pattern, '!') === 0) {
                $isNegation = true;
                $pattern = substr($pattern, 1);
            }

            // Check if it's a regex (starts with /)
            $isRegex = false;
            if (strpos($pattern, '/') === 0) {
                $isRegex = true;
                // Validate regex
                if (@preg_match($pattern, '') === false) {
                    return false;
                }
            }

            $patternInfo = [
                'pattern' => $pattern,
                'isRegex' => $isRegex,
            ];

            if ($isNegation) {
                $excludePatterns[] = $patternInfo;
            } else {
                $includePatterns[] = $patternInfo;
            }
        }

        // Return callback
        return function ($table) use ($includePatterns, $excludePatterns) {
            // If include patterns exist, table must match at least one
            if (!empty($includePatterns)) {
                $included = false;
                foreach ($includePatterns as $p) {
                    if ($p['isRegex']) {
                        if (preg_match($p['pattern'], $table)) {
                            $included = true;
                            break;
                        }
                    } else {
                        if (strpos($table, $p['pattern']) !== false) {
                            $included = true;
                            break;
                        }
                    }
                }
                if (!$included) {
                    return false;
                }
            }

            // Table must not match any exclude patterns
            foreach ($excludePatterns as $p) {
                if ($p['isRegex']) {
                    if (preg_match($p['pattern'], $table)) {
                        return false;
                    }
                } else {
                    if (strpos($table, $p['pattern']) !== false) {
                        return false;
                    }
                }
            }

            return true;
        };
    }

    /**
     * Filter an array of table names using a pattern
     *
     * Supports:
     * - Simple substring match: "user" matches any table containing "user"
     * - Regex patterns: "/^jos_user/" matches tables starting with "jos_user"
     * - Negation: "!session" excludes tables containing "session"
     * - Negated regex: "!/^jos_session/" excludes tables matching the regex
     *
     * Multiple patterns can be comma-separated: "user,!session"
     *
     * @param  array   $tables   Array of table names
     * @param  string  $filter   Filter pattern(s)
     * @return array|false       Filtered array or false on invalid regex
     */
    protected function filterTables(array $tables, string $filter)
    {
        // Support multiple comma-separated patterns
        $patterns = array_map('trim', explode(',', $filter));

        $includePatterns = [];
        $excludePatterns = [];

        foreach ($patterns as $pattern) {
            if (empty($pattern)) {
                continue;
            }

            // Check for negation prefix
            $isNegation = false;
            if (strpos($pattern, '!') === 0) {
                $isNegation = true;
                $pattern = substr($pattern, 1);
            }

            // Check if it's a regex (starts with /)
            $isRegex = false;
            if (strpos($pattern, '/') === 0) {
                $isRegex = true;
                // Validate regex
                if (@preg_match($pattern, '') === false) {
                    return false;
                }
            }

            $patternInfo = [
                'pattern' => $pattern,
                'isRegex' => $isRegex,
            ];

            if ($isNegation) {
                $excludePatterns[] = $patternInfo;
            } else {
                $includePatterns[] = $patternInfo;
            }
        }

        return array_filter($tables, function ($table) use ($includePatterns, $excludePatterns) {
            // If include patterns exist, table must match at least one
            if (!empty($includePatterns)) {
                $included = false;
                foreach ($includePatterns as $p) {
                    if ($p['isRegex']) {
                        if (preg_match($p['pattern'], $table)) {
                            $included = true;
                            break;
                        }
                    } else {
                        if (strpos($table, $p['pattern']) !== false) {
                            $included = true;
                            break;
                        }
                    }
                }
                if (!$included) {
                    return false;
                }
            }

            // Table must not match any exclude patterns
            foreach ($excludePatterns as $p) {
                if ($p['isRegex']) {
                    if (preg_match($p['pattern'], $table)) {
                        return false;
                    }
                } else {
                    if (strpos($table, $p['pattern']) !== false) {
                        return false;
                    }
                }
            }

            return true;
        });
    }

    /**
     * Write SQL statements to a file
     *
     * @param  string  $path     Output file path (can be directory or file)
     * @param  array   $upSql    Array of UP migration SQL statements
     * @param  array   $downSql  Array of DOWN migration SQL statements
     * @param  array   $options  Options: description, safe_flags
     * @return void
     */
    protected function writeSqlToFile(string $path, array $upSql, array $downSql, array $options = []): void
    {
        // If path is a directory, generate filename
        if (is_dir($path)) {
            $timestamp = (new Date())->format('Y-m-d-His');
            $path = rtrim($path, '/') . "/migration-{$timestamp}.sql";
        }

        // Ensure directory exists
        $dir = dirname($path);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                $this->output->error("Failed to create directory: {$dir}");
                return;
            }
        }

        // Build SQL file content
        $description = $options['description'] ?? 'Schema migration';
        $safeFlags = $options['safe_flags'] ?? 0;

        $content = [];
        $content[] = "-- ============================================================";
        $content[] = "-- HubZero Schema Migration SQL";
        $content[] = "-- Generated: " . (new Date())->format('Y-m-d H:i:s');
        $content[] = "-- Description: {$description}";
        if ($safeFlags > 0) {
            $content[] = "-- Safe mode: " . $this->describeSafeFlags($safeFlags);
        }
        $content[] = "-- ============================================================";
        $content[] = "";
        $content[] = "-- ============================================================";
        $content[] = "-- UP Migration (forward)";
        $content[] = "-- Apply these statements to migrate the schema forward";
        $content[] = "-- ============================================================";
        $content[] = "";

        if (empty($upSql)) {
            $content[] = "-- No forward migration statements";
        } else {
            foreach ($upSql as $statement) {
                $content[] = $statement . ";";
            }
        }

        $content[] = "";
        $content[] = "-- ============================================================";
        $content[] = "-- DOWN Migration (rollback)";
        $content[] = "-- Apply these statements to revert the migration";
        $content[] = "-- ============================================================";
        $content[] = "";

        if (empty($downSql)) {
            $content[] = "-- No rollback statements";
        } else {
            foreach ($downSql as $statement) {
                $content[] = $statement . ";";
            }
        }

        $content[] = "";
        $content[] = "-- End of migration SQL";

        // Write to file
        $sqlContent = implode("\n", $content);
        if (file_put_contents($path, $sqlContent) === false) {
            $this->output->error("Failed to write SQL file: {$path}");
            return;
        }

        $this->output->addLine("SQL written to file:", 'success');
        $this->output->addLine("  {$path}");
        $this->output->addSpacer();
        $upCount = count($upSql);
        $downCount = count($downSql);
        $this->output->addLine(
            "File contains {$upCount} UP statement(s) and {$downCount} DOWN statement(s).",
            'info'
        );

        if ($safeFlags > 0) {
            $this->output->addLine("Safe mode was enabled: " . $this->describeSafeFlags($safeFlags), 'warning');
        }
    }
}
