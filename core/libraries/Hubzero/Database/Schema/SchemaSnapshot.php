<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

use Hubzero\Database\Driver;

/**
 * Schema Snapshot Manager
 *
 * Captures and stores database schema state at specific points in time.
 * Useful for detecting schema drift between environments or tracking
 * schema changes over time.
 *
 * Usage:
 * ```php
 * $snapshot = new SchemaSnapshot($driver, '/path/to/snapshots');
 *
 * // Save current schema state
 * $snapshot->save('production-2024-01');
 *
 * // List all snapshots
 * $list = $snapshot->list();
 *
 * // Load a specific snapshot
 * $schemaInfo = $snapshot->load('production-2024-01');
 *
 * // Compare current schema to a snapshot
 * $diff = $snapshot->compareToSnapshot('production-2024-01');
 *
 * // Compare two snapshots
 * $diff = $snapshot->compareSnapshots('dev-schema', 'production-2024-01');
 *
 * // Delete a snapshot
 * $snapshot->delete('old-snapshot');
 * ```
 */
class SchemaSnapshot
{
    /**
     * Database driver
     *
     * @var Driver
     */
    protected $driver;

    /**
     * Directory to store snapshots
     *
     * @var string
     */
    protected $snapshotDir;

    /**
     * File extension for snapshot files
     *
     * @var string
     */
    protected $extension = '.schema.json';

    /**
     * Table prefix filter
     *
     * @var string|null
     */
    protected $prefix;

    /**
     * Create a new SchemaSnapshot manager
     *
     * @param Driver      $driver
     * @param string      $snapshotDir  Directory to store snapshots
     * @param string|null $prefix       Optional table prefix filter
     */
    public function __construct(Driver $driver, string $snapshotDir, ?string $prefix = null)
    {
        $this->driver = $driver;
        $this->snapshotDir = rtrim($snapshotDir, '/');
        $this->prefix = $prefix ?? $driver->getPrefix();
    }

    /**
     * Set the table prefix filter
     *
     * @param  string|null  $prefix
     * @return self
     */
    public function setPrefix(?string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Get the snapshot directory
     *
     * @return string
     */
    public function getSnapshotDir(): string
    {
        return $this->snapshotDir;
    }

    /**
     * Save the current schema state as a snapshot
     *
     * @param  string         $name        Snapshot name (alphanumeric, dashes, underscores)
     * @param  string|null    $description Optional description
     * @param  callable|null  $filter      Optional table filter callback (receives table name, returns bool)
     * @param  array          $metadata    Optional additional metadata
     * @return string         Full path to the saved snapshot file
     * @throws \InvalidArgumentException If name is invalid
     * @throws \RuntimeException If directory is not writable
     */
    public function save(
        string $name,
        ?string $description = null,
        ?callable $filter = null,
        array $metadata = []
    ): string {
        $this->validateName($name);
        $this->ensureDirectoryExists();

        $schema = $this->driver->schema();
        $databaseInfo = $schema->introspectDatabase($this->prefix, $filter);

        $snapshot = [
            'name' => $name,
            'description' => $description,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            'database' => $this->driver->getDatabase(),
            'driver' => $this->getDriverName(),
            'prefix' => $this->prefix,
            'table_count' => $databaseInfo->getTableCount(),
            'metadata' => $metadata,
            'schema' => $databaseInfo->toArray(),
        ];

        $filepath = $this->getFilePath($name);
        $json = json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if (file_put_contents($filepath, $json) === false) {
            throw new \RuntimeException("Failed to write snapshot to: {$filepath}");
        }

        return $filepath;
    }

    /**
     * Load a snapshot by name
     *
     * @param  string  $name
     * @return DatabaseInfo
     * @throws \InvalidArgumentException If snapshot doesn't exist
     */
    public function load(string $name): DatabaseInfo
    {
        $data = $this->loadRaw($name);
        return DatabaseInfo::fromArray($data['schema']);
    }

    /**
     * Load raw snapshot data (including metadata)
     *
     * @param  string  $name
     * @return array
     * @throws \InvalidArgumentException If snapshot doesn't exist
     */
    public function loadRaw(string $name): array
    {
        $filepath = $this->getFilePath($name);

        if (!file_exists($filepath)) {
            throw new \InvalidArgumentException("Snapshot not found: {$name}");
        }

        $json = file_get_contents($filepath);
        $data = json_decode($json, true);

        if ($data === null) {
            throw new \RuntimeException("Invalid JSON in snapshot: {$name}");
        }

        return $data;
    }

    /**
     * Check if a snapshot exists
     *
     * @param  string  $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        return file_exists($this->getFilePath($name));
    }

    /**
     * Delete a snapshot
     *
     * @param  string  $name
     * @return bool True if deleted, false if didn't exist
     */
    public function delete(string $name): bool
    {
        $filepath = $this->getFilePath($name);

        if (!file_exists($filepath)) {
            return false;
        }

        return unlink($filepath);
    }

    /**
     * List all available snapshots
     *
     * @param  string  $sortBy  Sort by: 'name', 'date', 'tables' (default: 'date')
     * @param  bool    $descending  Sort descending (default: true for date, false for name)
     * @return array   Array of snapshot info arrays
     */
    public function list(string $sortBy = 'date', bool $descending = true): array
    {
        if (!is_dir($this->snapshotDir)) {
            return [];
        }

        $snapshots = [];
        $pattern = $this->snapshotDir . '/*' . $this->extension;

        foreach (glob($pattern) as $filepath) {
            $json = file_get_contents($filepath);
            $data = json_decode($json, true);

            if ($data !== null) {
                $snapshots[] = [
                    'name' => $data['name'] ?? basename($filepath, $this->extension),
                    'description' => $data['description'] ?? null,
                    'created_at' => $data['created_at'] ?? null,
                    'table_count' => $data['table_count'] ?? 0,
                    'database' => $data['database'] ?? null,
                    'driver' => $data['driver'] ?? null,
                    'filepath' => $filepath,
                    'size' => filesize($filepath),
                ];
            }
        }

        // Sort
        usort($snapshots, function ($a, $b) use ($sortBy, $descending) {
            switch ($sortBy) {
                case 'name':
                    $cmp = strcasecmp($a['name'], $b['name']);
                    break;
                case 'tables':
                    $cmp = $a['table_count'] <=> $b['table_count'];
                    break;
                case 'date':
                default:
                    $cmp = strcmp($a['created_at'] ?? '', $b['created_at'] ?? '');
                    break;
            }
            return $descending ? -$cmp : $cmp;
        });

        return $snapshots;
    }

    /**
     * Compare current database schema to a snapshot
     *
     * @param  string         $snapshotName
     * @param  bool           $enableRenameDetection
     * @param  float          $renameThreshold
     * @param  callable|null  $filter  Optional table filter callback for current database
     * @return Diff\SchemaDiff
     */
    public function compareToSnapshot(
        string $snapshotName,
        bool $enableRenameDetection = false,
        float $renameThreshold = 0.7,
        ?callable $filter = null
    ): Diff\SchemaDiff {
        $snapshotSchema = $this->load($snapshotName);

        $schema = $this->driver->schema();
        $currentSchema = $schema->introspectDatabase($this->prefix, $filter);

        $comparator = new Comparator();

        if ($enableRenameDetection) {
            $comparator->enableHeuristicRenameDetection($renameThreshold);
        }

        return $comparator->compareSchemas($currentSchema, $snapshotSchema);
    }

    /**
     * Compare two snapshots
     *
     * @param  string  $fromSnapshot  Base snapshot name
     * @param  string  $toSnapshot    Target snapshot name
     * @param  bool    $enableRenameDetection
     * @param  float   $renameThreshold
     * @return Diff\SchemaDiff
     */
    public function compareSnapshots(
        string $fromSnapshot,
        string $toSnapshot,
        bool $enableRenameDetection = false,
        float $renameThreshold = 0.7
    ): Diff\SchemaDiff {
        $fromSchema = $this->load($fromSnapshot);
        $toSchema = $this->load($toSnapshot);

        $comparator = new Comparator();

        if ($enableRenameDetection) {
            $comparator->enableHeuristicRenameDetection($renameThreshold);
        }

        return $comparator->compareSchemas($fromSchema, $toSchema);
    }

    /**
     * Get a summary of differences between current schema and a snapshot
     *
     * @param  string         $snapshotName
     * @param  callable|null  $filter  Optional table filter callback for current database
     * @return array   Summary with counts and lists of changes
     */
    public function getDriftSummary(string $snapshotName, ?callable $filter = null): array
    {
        $diff = $this->compareToSnapshot($snapshotName, false, 0.7, $filter);

        $summary = [
            'has_drift' => !$diff->isEmpty(),
            'added_tables' => [],
            'removed_tables' => [],
            'modified_tables' => [],
            'renamed_tables' => [],
            'counts' => [
                'added_tables' => 0,
                'removed_tables' => 0,
                'modified_tables' => 0,
                'renamed_tables' => 0,
                'total_changes' => 0,
            ],
        ];

        foreach ($diff->getAddedTables() as $table) {
            $summary['added_tables'][] = $table->getName();
            $summary['counts']['added_tables']++;
        }

        foreach ($diff->getRemovedTables() as $table) {
            $summary['removed_tables'][] = $table->getName();
            $summary['counts']['removed_tables']++;
        }

        foreach ($diff->getChangedTables() as $tableDiff) {
            $tableName = $tableDiff->getFromTable()->getName();
            $summary['modified_tables'][$tableName] = [
                'added_columns' => count($tableDiff->getAddedColumns()),
                'removed_columns' => count($tableDiff->getRemovedColumns()),
                'modified_columns' => count($tableDiff->getChangedColumns()),
                'renamed_columns' => count($tableDiff->getRenamedColumns()),
                'added_indexes' => count($tableDiff->getAddedIndexes()),
                'removed_indexes' => count($tableDiff->getRemovedIndexes()),
                'added_foreign_keys' => count($tableDiff->getAddedForeignKeys()),
                'removed_foreign_keys' => count($tableDiff->getRemovedForeignKeys()),
            ];
            $summary['counts']['modified_tables']++;
        }

        foreach ($diff->getRenamedTables() as $oldName => $newName) {
            $summary['renamed_tables'][$oldName] = $newName;
            $summary['counts']['renamed_tables']++;
        }

        $summary['counts']['total_changes'] =
            $summary['counts']['added_tables'] +
            $summary['counts']['removed_tables'] +
            $summary['counts']['modified_tables'] +
            $summary['counts']['renamed_tables'];

        return $summary;
    }

    /**
     * Create a snapshot from a schema file (JSON export)
     *
     * @param  string       $name        Snapshot name
     * @param  string       $schemaFile  Path to schema JSON file
     * @param  string|null  $description Optional description
     * @return string       Path to saved snapshot
     */
    public function createFromFile(string $name, string $schemaFile, ?string $description = null): string
    {
        $this->validateName($name);

        if (!file_exists($schemaFile)) {
            throw new \InvalidArgumentException("Schema file not found: {$schemaFile}");
        }

        $schemaData = json_decode(file_get_contents($schemaFile), true);

        if ($schemaData === null) {
            throw new \RuntimeException("Invalid JSON in schema file: {$schemaFile}");
        }

        $this->ensureDirectoryExists();

        $snapshot = [
            'name' => $name,
            'description' => $description ?? "Imported from {$schemaFile}",
            'created_at' => date('Y-m-d H:i:s'),
            'database' => $schemaData['name'] ?? 'imported',
            'driver' => 'imported',
            'prefix' => $schemaData['prefix'] ?? '',
            'table_count' => count($schemaData['tables'] ?? []),
            'metadata' => [
                'source_file' => basename($schemaFile),
            ],
            'schema' => $schemaData,
        ];

        $filepath = $this->getFilePath($name);
        $json = json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if (file_put_contents($filepath, $json) === false) {
            throw new \RuntimeException("Failed to write snapshot to: {$filepath}");
        }

        return $filepath;
    }

    /**
     * Rename a snapshot
     *
     * @param  string  $oldName
     * @param  string  $newName
     * @return bool
     */
    public function rename(string $oldName, string $newName): bool
    {
        $this->validateName($newName);

        $oldPath = $this->getFilePath($oldName);
        $newPath = $this->getFilePath($newName);

        if (!file_exists($oldPath)) {
            throw new \InvalidArgumentException("Snapshot not found: {$oldName}");
        }

        if (file_exists($newPath)) {
            throw new \InvalidArgumentException("Snapshot already exists: {$newName}");
        }

        // Update the name inside the file
        $data = $this->loadRaw($oldName);
        $data['name'] = $newName;
        $data['metadata']['renamed_from'] = $oldName;
        $data['metadata']['renamed_at'] = date('Y-m-d H:i:s');

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($newPath, $json);

        return unlink($oldPath);
    }

    /**
     * Get the file path for a snapshot
     *
     * @param  string  $name
     * @return string
     */
    protected function getFilePath(string $name): string
    {
        return $this->snapshotDir . '/' . $name . $this->extension;
    }

    /**
     * Validate a snapshot name
     *
     * @param  string  $name
     * @throws \InvalidArgumentException
     */
    protected function validateName(string $name): void
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Snapshot name cannot be empty');
        }

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
            throw new \InvalidArgumentException(
                'Snapshot name can only contain letters, numbers, dashes, and underscores'
            );
        }

        if (strlen($name) > 100) {
            throw new \InvalidArgumentException('Snapshot name cannot exceed 100 characters');
        }
    }

    /**
     * Ensure the snapshot directory exists
     *
     * @throws \RuntimeException
     */
    protected function ensureDirectoryExists(): void
    {
        if (!is_dir($this->snapshotDir)) {
            if (!mkdir($this->snapshotDir, 0755, true)) {
                throw new \RuntimeException("Failed to create snapshot directory: {$this->snapshotDir}");
            }
        }

        if (!is_writable($this->snapshotDir)) {
            throw new \RuntimeException("Snapshot directory is not writable: {$this->snapshotDir}");
        }
    }

    /**
     * Get the driver name
     *
     * @return string
     */
    protected function getDriverName(): string
    {
        if (method_exists($this->driver, 'getDriverType')) {
            $name = strtolower((string) $this->driver->getDriverType());
            if ($name !== '' && $name !== 'unknown') {
                return $name;
            }
        }

        $class = get_class($this->driver);
        $parts = explode('\\', $class);
        return strtolower(end($parts));
    }
}
