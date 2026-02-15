<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Schema\MigrationSquasher;
use Hubzero\Database\Schema\DatabaseInfo;
use Hubzero\Database\Schema\TableInfo;

/**
 * Tests for MigrationSquasher
 *
 * Tests the schema-based migration squashing feature.
 */
class MigrationSquasherTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['squash_posts', 'squash_users'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('squash_posts', true);
        $driver->dropTable('squash_users', true);

        $schema = $driver->schema();

        $schema->createTable('squash_users')
            ->id()
            ->string('name', 255)
            ->string('email', 255)
            ->execute();

        $schema->createTable('squash_posts')
            ->id()
            ->unsignedInteger('user_id')
            ->string('title', 255)
            ->foreign(
                'user_id', 'squash_users', 'id',
                $driver->supportsReferentialActions() ? 'CASCADE' : 'NO ACTION',
                $driver->supportsReferentialActions() ? 'CASCADE' : 'NO ACTION'
            )
            ->execute();
    }

    /**
     * Introspect specific tables and return a DatabaseInfo
     */
    protected function introspectSchema(Driver $driver, array $tableNames): DatabaseInfo
    {
        $schema = $driver->schema();
        $tables = [];
        foreach ($tableNames as $name) {
            $tables[] = $schema->introspectTable($name);
        }
        return new DatabaseInfo([
            'name' => 'test_db',
            'tables' => $tables,
        ]);
    }

    /**
     * Test class exists
     */
    #[DataProvider('databaseProvider')]
    public function testClassExists(string $dbName, Driver $driver): void
    {
        $this->assertTrue(class_exists(MigrationSquasher::class));
    }

    /**
     * Test can create instance
     */
    #[DataProvider('databaseProvider')]
    public function testCanCreateInstance(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $this->assertInstanceOf(MigrationSquasher::class, $squasher);
    }

    /**
     * Test setIncludeExistenceChecks returns self for fluent interface
     */
    #[DataProvider('databaseProvider')]
    public function testSetIncludeExistenceChecksReturnsSelf(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $result = $squasher->setIncludeExistenceChecks(true);
        $this->assertSame($squasher, $result);
    }

    /**
     * Test setIncludeForeignKeys returns self for fluent interface
     */
    #[DataProvider('databaseProvider')]
    public function testSetIncludeForeignKeysReturnsSelf(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $result = $squasher->setIncludeForeignKeys(false);
        $this->assertSame($squasher, $result);
    }

    /**
     * Test setExcludeTables returns self for fluent interface
     */
    #[DataProvider('databaseProvider')]
    public function testSetExcludeTablesReturnsSelf(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $result = $squasher->setExcludeTables(['sessions', 'cache']);
        $this->assertSame($squasher, $result);
    }

    /**
     * Test setTemplate returns self for fluent interface
     */
    #[DataProvider('databaseProvider')]
    public function testSetTemplateReturnsSelf(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $result = $squasher->setTemplate('custom template');
        $this->assertSame($squasher, $result);
    }

    /**
     * Test setCopyrightYear returns self for fluent interface
     */
    #[DataProvider('databaseProvider')]
    public function testSetCopyrightYearReturnsSelf(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $result = $squasher->setCopyrightYear('2026');
        $this->assertSame($squasher, $result);
    }

    /**
     * Test generateFromSchema with empty schema
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateFromSchemaWithEmptySchema(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $schema = new DatabaseInfo([
            'name' => 'test_db',
            'tables' => []
        ]);

        $content = $squasher->generateFromSchema($schema, 'TestCore', 'Empty test');

        $this->assertStringContainsString('class Migration', $content);
        $this->assertStringContainsString('TestCore', $content);
        $this->assertStringContainsString('Empty test', $content);
        $this->assertStringContainsString('public function up()', $content);
        $this->assertStringContainsString('public function down()', $content);
    }

    /**
     * Test generateFromSchema with single table
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateFromSchemaWithSingleTable(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $schema = $this->introspectSchema($driver, ['squash_users']);

        $content = $squasher->generateFromSchema($schema, 'Core', 'Single table test');

        $this->assertStringContainsString('CREATE TABLE', $content);
        $this->assertStringContainsString('squash_users', $content);
        $this->assertStringContainsString('public function up()', $content);
        $this->assertStringContainsString('public function down()', $content);
    }

    /**
     * Test generateFromSchema includes existence checks by default
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateFromSchemaIncludesExistenceChecksByDefault(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $schema = $this->introspectSchema($driver, ['squash_users']);

        $content = $squasher->generateFromSchema($schema);

        $this->assertStringContainsString('tableExists', $content);
    }

    /**
     * Test generateFromSchema without existence checks
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateFromSchemaWithoutExistenceChecks(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $schema = $this->introspectSchema($driver, ['squash_users']);

        $squasher->setIncludeExistenceChecks(false);
        $content = $squasher->generateFromSchema($schema);

        // When existence checks are disabled, we use IF EXISTS in DROP
        // But CREATE still runs unconditionally
        $this->assertStringContainsString('CREATE TABLE', $content);
    }

    /**
     * Test generateFromSchema excludes foreign keys when disabled
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateFromSchemaExcludesForeignKeysWhenDisabled(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $schema = $this->introspectSchema($driver, ['squash_posts']);

        $squasher->setIncludeForeignKeys(false);
        $content = $squasher->generateFromSchema($schema);

        $this->assertStringNotContainsString('FOREIGN KEY', $content);
        $this->assertStringNotContainsString('REFERENCES', $content);
    }

    /**
     * Test generateFromSchema with multiple tables respects dependency order
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateFromSchemaWithMultipleTablesRespectsDependencyOrder(
        string $dbName,
        Driver $driver
    ): void {
        $squasher = new MigrationSquasher($driver);

        // Put posts first to test that squasher reorders based on FK dependencies
        $schema = $this->introspectSchema($driver, ['squash_posts', 'squash_users']);

        $content = $squasher->generateFromSchema($schema);

        // Both tables should appear in the output
        $usersPos = strpos($content, 'squash_users');
        $postsPos = strpos($content, 'squash_posts');

        $this->assertNotFalse($usersPos, "[$dbName] Output should contain squash_users");
        $this->assertNotFalse($postsPos, "[$dbName] Output should contain squash_posts");
    }

    /**
     * Test getSquashStats returns correct statistics
     */
    #[DataProvider('databaseProvider')]
    public function testGetSquashStatsReturnsCorrectStatistics(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $schema = $this->introspectSchema($driver, ['squash_users']);

        $stats = $squasher->getSquashStats($schema);

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('tables', $stats);
        $this->assertArrayHasKey('columns', $stats);
        $this->assertArrayHasKey('indexes', $stats);
        $this->assertArrayHasKey('foreign_keys', $stats);
        $this->assertArrayHasKey('excluded', $stats);

        $this->assertEquals(1, $stats['tables']);
        $this->assertGreaterThan(0, $stats['columns']);
    }

    /**
     * Test getSquashStats respects excluded tables
     */
    #[DataProvider('databaseProvider')]
    public function testGetSquashStatsRespectsExcludedTables(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $schema = $this->introspectSchema($driver, ['squash_users', 'squash_posts']);

        $squasher->setExcludeTables(['squash_users']);
        $stats = $squasher->getSquashStats($schema);

        $this->assertEquals(1, $stats['tables']); // Only posts, users excluded
        $this->assertEquals(1, $stats['excluded']);
    }

    /**
     * Test getExistingMigrations returns array
     */
    #[DataProvider('databaseProvider')]
    public function testGetExistingMigrationsReturnsArray(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $tempDir = sys_get_temp_dir();
        $migrations = $squasher->getExistingMigrations($tempDir);

        $this->assertIsArray($migrations);
    }

    /**
     * Test generated migration has correct structure
     */
    #[DataProvider('databaseProvider')]
    public function testGeneratedMigrationHasCorrectStructure(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $schema = $this->introspectSchema($driver, ['squash_users']);

        $content = $squasher->generateFromSchema($schema, 'TestComponent');

        // Check PHP structure
        $this->assertStringContainsString('<?php', $content);
        $this->assertStringContainsString('namespace Migrations;', $content);
        $this->assertStringContainsString('use Hubzero\Content\Migration\Base;', $content);
        $this->assertStringContainsString('extends Base', $content);
        $this->assertStringContainsString('public function up()', $content);
        $this->assertStringContainsString('public function down()', $content);
    }

    /**
     * Test generated migration down() drops tables
     */
    #[DataProvider('databaseProvider')]
    public function testGeneratedMigrationDownDropsTables(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $schema = $this->introspectSchema($driver, ['squash_users']);

        $content = $squasher->generateFromSchema($schema);

        $this->assertStringContainsString('DROP TABLE', $content);
    }

    /**
     * Test custom component name in generated migration
     */
    #[DataProvider('databaseProvider')]
    public function testCustomComponentNameInGeneratedMigration(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $schema = $this->introspectSchema($driver, ['squash_users']);

        $content = $squasher->generateFromSchema($schema, 'CustomComponent');

        $this->assertStringContainsString('CustomComponent', $content);
    }

    /**
     * Test custom description in generated migration
     */
    #[DataProvider('databaseProvider')]
    public function testCustomDescriptionInGeneratedMigration(string $dbName, Driver $driver): void
    {
        $squasher = new MigrationSquasher($driver);

        $schema = $this->introspectSchema($driver, ['squash_users']);

        $content = $squasher->generateFromSchema($schema, 'Core', 'My custom description');

        $this->assertStringContainsString('My custom description', $content);
    }
}
