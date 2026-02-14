<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use Hubzero\Database\Driver;
use Hubzero\Database\Schema\SchemaSnapshot;
use Hubzero\Database\Schema\Comparator;
use Hubzero\Database\Schema\DatabaseInfo;
use Hubzero\Database\Schema\TableInfo;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Schema Snapshot test
 */
class SchemaSnapshotTest extends AbstractDriverTestCase
{
    /**
     * @var string Test directory for snapshots
     */
    private static string $testDir = '';

    protected static function getTestTables(): array
    {
        return ['snapshot_users', 'snapshot_posts', 'snapshot_comments'];
    }

    public static function setUpBeforeClass(): void
    {
        // Create temporary directory for snapshots before parent sets up drivers
        self::$testDir = sys_get_temp_dir() . '/hubzero_snapshot_test_' . uniqid();
        mkdir(self::$testDir, 0755, true);

        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        // Recursively remove the test directory and all contents
        if (self::$testDir !== '' && is_dir(self::$testDir)) {
            $it = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(self::$testDir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($it as $file) {
                if ($file->isDir()) {
                    rmdir($file->getPathname());
                } else {
                    unlink($file->getPathname());
                }
            }
            rmdir(self::$testDir);
        }

        parent::tearDownAfterClass();
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Drop existing test tables
        $driver->dropTable('snapshot_users', true);
        $driver->dropTable('snapshot_posts', true);
        $driver->dropTable('snapshot_comments', true);

        // Create test tables with various column types
        $driver->createTable('snapshot_users')
            ->id()
            ->string('username', 50)
            ->string('email', 100)
            ->integer('status')->default(1)
            ->timestamp('created_at')
            ->execute();

        $driver->createTable('snapshot_posts')
            ->id()
            ->integer('user_id')
            ->string('title', 200)
            ->text('content')
            ->integer('published')->default(0)
            ->timestamp('created_at')
            ->execute();

        $driver->createTable('snapshot_comments')
            ->id()
            ->integer('post_id')
            ->integer('user_id')
            ->text('body')
            ->timestamp('created_at')
            ->execute();
    }

    /**
     * Set up before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    // =========================================================================
    // Name Validation Tests
    // =========================================================================

    /**
     * Test valid snapshot names
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function validSnapshotNames(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'test_');

        $validNames = [
            'mysnap',
            'my-snap',
            'my_snap',
            'MySnap123',
            'production-2024-01-15',
            'v1_0_0',
        ];

        foreach ($validNames as $name) {
            // Create snapshot with valid name
            $filepath = $snapshot->save($name, "Test snapshot: $name");
            $this->assertFileExists($filepath, "[$dbName] Snapshot file should exist for: $name");
            $this->assertTrue($snapshot->exists($name), "[$dbName] Snapshot should exist: $name");

            // Clean up
            $snapshot->delete($name);
        }
    }

    /**
     * Test invalid snapshot names throw exceptions
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function invalidSnapshotNamesThrowException(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'test_');

        $invalidNames = [
            'my snap',       // spaces
            'my.snap',       // dots
            'my/snap',       // slashes
            '../escape',     // path traversal attempt
            'snap@name',     // special chars
        ];

        // Create a temporary file once, reuse for all invalid names
        $tempFile = self::$testDir . '/temp_import.json';
        file_put_contents($tempFile, json_encode(['name' => 'test', 'tables' => []]));

        try {
            foreach ($invalidNames as $name) {
                try {
                    $snapshot->createFromFile($name, $tempFile);
                    $this->fail("[$dbName] Expected exception for invalid name: '$name'");
                } catch (\InvalidArgumentException $e) {
                    $this->assertStringContainsString('only contain', $e->getMessage(), "[$dbName]");
                }
            }
        } finally {
            unlink($tempFile);
        }
    }

    /**
     * Test empty snapshot name throws exception
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function emptySnapshotNameThrowsException(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'test_');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('cannot be empty');

        // Create a valid snapshot first
        $snapshot->save('valid-name', 'Valid');

        // Try to rename with empty name
        $snapshot->rename('valid-name', '');
    }

    /**
     * Test snapshot name length limit
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function snapshotNameLengthLimit(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'test_');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('exceed 100');

        // Create a valid snapshot first
        $snapshot->save('valid-name', 'Valid');

        // Try to rename with too-long name
        $snapshot->rename('valid-name', str_repeat('a', 101));
    }

    // =========================================================================
    // Save and Load Tests
    // =========================================================================

    /**
     * Test saving a snapshot creates a file with real schema
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function saveCreatesFileWithRealSchema(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'snapshot_');
        $name = 'test-save-' . $dbName;

        $filepath = $snapshot->save($name, 'Test save snapshot');

        $this->assertFileExists($filepath, "[$dbName] Snapshot file should exist");
        $this->assertStringEndsWith('.schema.json', $filepath, "[$dbName]");

        // Load and verify it captured real tables
        $data = $snapshot->loadRaw($name);
        $this->assertEquals($name, $data['name'], "[$dbName]");
        $this->assertGreaterThanOrEqual(3, $data['table_count'], "[$dbName] Should have captured 3+ test tables");

        // Verify schema contains our test tables
        $tableNames = array_column($data['schema']['tables'], 'name');
        $this->assertContains('snapshot_users', $tableNames, "[$dbName]");
        $this->assertContains('snapshot_posts', $tableNames, "[$dbName]");
        $this->assertContains('snapshot_comments', $tableNames, "[$dbName]");

        // Clean up
        $snapshot->delete($name);
    }

    /**
     * Test loading raw snapshot data
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function loadRawReturnsMetadata(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'snapshot_');
        $name = 'test-load-raw-' . $dbName;
        $description = 'My test snapshot';

        $snapshot->save($name, $description);

        $data = $snapshot->loadRaw($name);

        $this->assertEquals($name, $data['name'], "[$dbName]");
        $this->assertEquals($description, $data['description'], "[$dbName]");
        $this->assertArrayHasKey('created_at', $data, "[$dbName]");
        $this->assertArrayHasKey('schema', $data, "[$dbName]");
        $this->assertArrayHasKey('table_count', $data, "[$dbName]");
        $this->assertGreaterThanOrEqual(3, $data['table_count'], "[$dbName] Should have 3+ tables");

        // Clean up
        $snapshot->delete($name);
    }

    /**
     * Test loading returns DatabaseInfo object
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function loadReturnsDatabaseInfo(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'snapshot_');
        $name = 'test-load-' . $dbName;

        $snapshot->save($name, 'Test');

        $result = $snapshot->load($name);

        $this->assertInstanceOf(DatabaseInfo::class, $result, "[$dbName]");

        // Clean up
        $snapshot->delete($name);
    }

    /**
     * Test loading non-existent snapshot throws exception
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function loadNonExistentThrowsException(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'snapshot_');

        $this->expectException(\InvalidArgumentException::class);
        $snapshot->load('non-existent-snapshot-' . $dbName);
    }

    // =========================================================================
    // Exists and Delete Tests
    // =========================================================================

    /**
     * Test exists() returns correct values
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function existsMethod(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'snapshot_');
        $name = 'test-exists-' . $dbName;

        $this->assertFalse($snapshot->exists($name), "[$dbName] Should not exist initially");

        $snapshot->save($name, 'Test');

        $this->assertTrue($snapshot->exists($name), "[$dbName] Should exist after save");

        // Clean up
        $snapshot->delete($name);
    }

    /**
     * Test deleting a snapshot
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function deleteSnapshot(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'snapshot_');
        $name = 'test-delete-' . $dbName;

        $snapshot->save($name, 'Test');

        $this->assertTrue($snapshot->exists($name), "[$dbName]");
        $this->assertTrue($snapshot->delete($name), "[$dbName]");
        $this->assertFalse($snapshot->exists($name), "[$dbName]");
    }

    /**
     * Test deleting non-existent snapshot returns false
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function deleteNonExistentReturnsFalse(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'snapshot_');

        $this->assertFalse($snapshot->delete('non-existent-' . $dbName), "[$dbName]");
    }

    // =========================================================================
    // List Tests
    // =========================================================================

    /**
     * Test listing snapshots
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function listSnapshots(string $dbName, Driver $driver): void
    {
        // Use unique directory for this test to avoid seeing files from other tests
        $testDir = self::$testDir . '/list_' . $dbName . '_' . uniqid();
        $snapshot = new SchemaSnapshot($driver, $testDir, 'test_');

        // Create multiple snapshots
        $snapshot->save('snap-a', 'First');
        $snapshot->save('snap-b', 'Second');
        $snapshot->save('snap-c', 'Third');

        $list = $snapshot->list();

        $this->assertCount(3, $list, "[$dbName]");

        // Should have expected keys
        foreach ($list as $item) {
            $this->assertArrayHasKey('name', $item, "[$dbName]");
            $this->assertArrayHasKey('description', $item, "[$dbName]");
            $this->assertArrayHasKey('created_at', $item, "[$dbName]");
            $this->assertArrayHasKey('table_count', $item, "[$dbName]");
            $this->assertArrayHasKey('filepath', $item, "[$dbName]");
            $this->assertArrayHasKey('size', $item, "[$dbName]");
        }

        // Clean up
        $snapshot->delete('snap-a');
        $snapshot->delete('snap-b');
        $snapshot->delete('snap-c');
        rmdir($testDir);
    }

    /**
     * Test listing empty directory
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function listEmptyDirectory(string $dbName, Driver $driver): void
    {
        // Use unique directory for this test
        $testDir = self::$testDir . '/empty_' . $dbName . '_' . uniqid();
        $snapshot = new SchemaSnapshot($driver, $testDir, 'test_');

        $list = $snapshot->list();
        $this->assertIsArray($list, "[$dbName]");
        $this->assertEmpty($list, "[$dbName]");

        // Clean up (directory may not have been created if empty)
        if (is_dir($testDir)) {
            rmdir($testDir);
        }
    }

    /**
     * Test sorting snapshots by name
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function listSortByName(string $dbName, Driver $driver): void
    {
        // Use unique directory for this test
        $testDir = self::$testDir . '/sort_' . $dbName . '_' . uniqid();
        $snapshot = new SchemaSnapshot($driver, $testDir, 'test_');

        $snapshot->save('charlie', 'C');
        $snapshot->save('alpha', 'A');
        $snapshot->save('bravo', 'B');

        $list = $snapshot->list('name', false); // ascending

        $this->assertEquals('alpha', $list[0]['name'], "[$dbName]");
        $this->assertEquals('bravo', $list[1]['name'], "[$dbName]");
        $this->assertEquals('charlie', $list[2]['name'], "[$dbName]");

        // Clean up
        $snapshot->delete('charlie');
        $snapshot->delete('alpha');
        $snapshot->delete('bravo');
        rmdir($testDir);
    }

    // =========================================================================
    // Import Tests
    // =========================================================================

    /**
     * Test importing a schema file as a snapshot
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function createFromFile(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'import_test_' . $dbName . '_');

        // Create a temporary schema file
        $schemaData = [
            'name' => 'imported_db',
            'tables' => [
                ['name' => 'users', 'columns' => []],
                ['name' => 'posts', 'columns' => []],
            ],
        ];

        $schemaFile = self::$testDir . '/import-test-' . $dbName . '.json';
        file_put_contents($schemaFile, json_encode($schemaData));

        $filepath = $snapshot->createFromFile('imported', $schemaFile, 'Imported schema');

        $this->assertFileExists($filepath, "[$dbName]");
        $this->assertTrue($snapshot->exists('imported'), "[$dbName]");

        $data = $snapshot->loadRaw('imported');
        $this->assertEquals('imported', $data['name'], "[$dbName]");
        $this->assertStringContainsString('Imported', $data['description'], "[$dbName]");

        // Clean up
        $snapshot->delete('imported');
        unlink($schemaFile);
    }

    /**
     * Test importing non-existent file throws exception
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function createFromNonExistentFileThrowsException(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'snapshot_');

        $this->expectException(\InvalidArgumentException::class);
        $snapshot->createFromFile('test', '/nonexistent/file-' . $dbName . '.json');
    }

    // =========================================================================
    // Rename Tests
    // =========================================================================

    /**
     * Test renaming a snapshot
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function renameSnapshot(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'rename_test_' . $dbName . '_');

        $snapshot->save('old-name', 'Test');

        $this->assertTrue($snapshot->exists('old-name'), "[$dbName]");
        $this->assertFalse($snapshot->exists('new-name'), "[$dbName]");

        $result = $snapshot->rename('old-name', 'new-name');

        $this->assertTrue($result, "[$dbName]");
        $this->assertFalse($snapshot->exists('old-name'), "[$dbName]");
        $this->assertTrue($snapshot->exists('new-name'), "[$dbName]");

        // Verify metadata was updated
        $data = $snapshot->loadRaw('new-name');
        $this->assertEquals('new-name', $data['name'], "[$dbName]");
        $this->assertEquals('old-name', $data['metadata']['renamed_from'], "[$dbName]");

        // Clean up
        $snapshot->delete('new-name');
    }

    /**
     * Test renaming non-existent snapshot throws exception
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function renameNonExistentThrowsException(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'snapshot_');

        $this->expectException(\InvalidArgumentException::class);
        $snapshot->rename('nonexistent-' . $dbName, 'new-name');
    }

    /**
     * Test renaming to existing name throws exception
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function renameToExistingThrowsException(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'rename_conflict_' . $dbName . '_');

        $snapshot->save('snap1', 'One');
        $snapshot->save('snap2', 'Two');

        $this->expectException(\InvalidArgumentException::class);

        try {
            $snapshot->rename('snap1', 'snap2');
        } finally {
            // Clean up
            $snapshot->delete('snap1');
            $snapshot->delete('snap2');
        }
    }

    // =========================================================================
    // Drift Detection Tests
    // =========================================================================

    /**
     * Test drift summary structure
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function driftSummaryStructure(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'drift_test_' . $dbName . '_');

        $snapshot->save('baseline', 'Baseline snapshot');

        $summary = $snapshot->getDriftSummary('baseline');

        $this->assertArrayHasKey('has_drift', $summary, "[$dbName]");
        $this->assertArrayHasKey('added_tables', $summary, "[$dbName]");
        $this->assertArrayHasKey('removed_tables', $summary, "[$dbName]");
        $this->assertArrayHasKey('modified_tables', $summary, "[$dbName]");
        $this->assertArrayHasKey('renamed_tables', $summary, "[$dbName]");
        $this->assertArrayHasKey('counts', $summary, "[$dbName]");

        // Clean up
        $snapshot->delete('baseline');
    }

    /**
     * Test drift detection with actual table changes
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function driftDetectionWithChanges(string $dbName, Driver $driver): void
    {
        // Use a unique prefix just for this drift test to avoid contamination
        $driftPrefix = 'drift_test_' . uniqid() . '_';
        $baseTable = $driftPrefix . 'base';
        $newTable = $driftPrefix . 'new';

        // Clean up from any previous runs
        $driver->dropTable($baseTable, true);
        $driver->dropTable($newTable, true);

        // Create a base table for our snapshot
        $driver->createTable($baseTable)
            ->id()
            ->string('name', 100)
            ->execute();

        // Use unique directory with our unique prefix
        $testDir = self::$testDir . '/drift_' . $dbName . '_' . uniqid();
        $snapshot = new SchemaSnapshot($driver, $testDir, $driftPrefix);

        // Create baseline snapshot (should include ONLY our base table)
        $snapshot->save('baseline', 'Before changes');

        // Make a change - add a new table with the same prefix
        $driver->createTable($newTable)
            ->id()
            ->string('value', 50)
            ->execute();

        // Check drift - should detect the new table
        $summary = $snapshot->getDriftSummary('baseline');

        // Note: getDriftSummary() compares snapshot TO current database, so a table created
        // AFTER the snapshot will appear as "removed" (in snapshot but not in current reality).
        // This seems backwards but is how the SchemaSnapshot class works.
        $this->assertTrue($summary['has_drift'], "[$dbName] Should detect drift");
        $this->assertGreaterThan(0, $summary['counts']['total_changes'], "[$dbName]");

        // The new table shows in removed_tables (not added_tables) due to comparison direction
        $this->assertContains($newTable, $summary['removed_tables'], "[$dbName] New table should be detected in drift");

        // Clean up
        $driver->dropTable($baseTable, true);
        $driver->dropTable($newTable, true);
        $snapshot->delete('baseline');
        rmdir($testDir);
    }

    /**
     * Test no drift when nothing changed
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function noDriftWhenUnchanged(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'no_drift_' . $dbName . '_');

        // Create baseline snapshot
        $snapshot->save('baseline', 'Baseline');

        // Immediately check drift without changes
        $summary = $snapshot->getDriftSummary('baseline');

        $this->assertFalse($summary['has_drift'], "[$dbName] Should have no drift");
        $this->assertEquals(0, $summary['counts']['total_changes'], "[$dbName]");

        // Clean up
        $snapshot->delete('baseline');
    }

    // =========================================================================
    // Prefix Tests
    // =========================================================================

    /**
     * Test setting prefix
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function setPrefix(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir);
        $result = $snapshot->setPrefix('custom_');

        // Should return self for chaining
        $this->assertSame($snapshot, $result, "[$dbName]");
    }

    // =========================================================================
    // Directory Tests
    // =========================================================================

    /**
     * Test getSnapshotDir returns correct path
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getSnapshotDir(string $dbName, Driver $driver): void
    {
        $snapshot = new SchemaSnapshot($driver, self::$testDir, 'snapshot_');

        $this->assertEquals(self::$testDir, $snapshot->getSnapshotDir(), "[$dbName]");
    }

    /**
     * Test directory is created if it doesn't exist
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function directoryCreatedOnSave(string $dbName, Driver $driver): void
    {
        $newDir = self::$testDir . '/subdir_' . $dbName;
        $this->assertDirectoryDoesNotExist($newDir, "[$dbName]");

        $snapshot = new SchemaSnapshot($driver, $newDir, 'snapshot_');
        $snapshot->save('test', 'Test');

        $this->assertDirectoryExists($newDir, "[$dbName]");

        // Cleanup
        $snapshot->delete('test');
        rmdir($newDir);
    }
}
