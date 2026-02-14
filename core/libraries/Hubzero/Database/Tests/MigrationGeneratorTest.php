<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Schema\MigrationGenerator;
use Hubzero\Database\Schema\Comparator;
use Hubzero\Database\Schema\DiffSqlGenerator;
use Hubzero\Database\Schema\DatabaseInfo;
use Hubzero\Database\Schema\TableInfo;
use Hubzero\Database\Schema\ColumnInfo;
use Hubzero\Database\Schema\Diff\TableDiff;
use Hubzero\Database\Schema\Diff\SchemaDiff;
use Hubzero\Database\Schema\Diff\ColumnDiff;

/**
 * Migration Generator test
 */
class MigrationGeneratorTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return [];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // No persistent tables - tests use in-memory schema objects
    }

    /**
     * Create a TableInfo for testing
     */
    protected function createTableInfo(
        string $name,
        array $columns,
        array $indexes = [],
        array $foreignKeys = []
    ): TableInfo {
        $colDefs = [];
        foreach ($columns as $colName => $colDef) {
            if (is_string($colDef)) {
                $colDef = ['full_type' => $colDef];
            }
            $colDefs[] = array_merge([
                'name' => $colName,
                'full_type' => 'VARCHAR(255)',
                'nullable' => true,
                'default' => null,
                'auto_increment' => false,
                'unsigned' => false,
            ], $colDef);
        }

        return new TableInfo([
            'name' => $name,
            'columns' => $colDefs,
            'indexes' => $indexes,
            'foreign_keys' => $foreignKeys,
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
        ]);
    }

    /**
     * Create a DatabaseInfo for testing
     */
    protected function createDatabaseInfo(array $tables): DatabaseInfo
    {
        $tableInfos = [];
        foreach ($tables as $name => $columns) {
            $tableInfos[] = $this->createTableInfo($name, $columns)->toArray();
        }

        return new DatabaseInfo([
            'name' => 'test_db',
            'tables' => $tableInfos,
        ]);
    }

    // =========================================================================
    // Blank Migration Tests
    // =========================================================================

    /**
     * Test generating a blank migration stub
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateBlank(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);

        $content = $generator->generateBlank('Core', 'Test migration');

        // Verify PHP opening tag
        $this->assertStringContainsString('<?php', $content);

        // Verify namespace
        $this->assertStringContainsString('namespace Migrations;', $content);

        // Verify extends Base
        $this->assertStringContainsString('extends Base', $content);

        // Verify up() method
        $this->assertStringContainsString('public function up()', $content);

        // Verify down() method
        $this->assertStringContainsString('public function down()', $content);

        // Verify TODO comments for blank stubs
        $this->assertStringContainsString('TODO: Implement migration', $content);
        $this->assertStringContainsString('TODO: Implement rollback', $content);

        // Verify description in docblock
        $this->assertStringContainsString('Test migration', $content);
    }

    /**
     * Test generating blank migration with different component names
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateBlankWithComponent(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);

        $content = $generator->generateBlank('ComUsers', 'User table changes');

        // Class name should include component
        $this->assertMatchesRegularExpression('/class Migration\d+ComUsers extends Base/', $content);
    }

    /**
     * Test strict blank migration mode rejects TODO stubs
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateBlankStrictModeRejectsTodoStub(string $dbName, Driver $driver): void
    {
        $generator = (new MigrationGenerator($driver))->setStrictBlankMigrations(true);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Strict blank migration mode is enabled');

        $generator->generateBlank('Core', 'Strict migration');
    }

    /**
     * Test strict blank migration mode accepts explicit bodies
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateBlankWithBodiesStrictModeAllowsExplicitCode(string $dbName, Driver $driver): void
    {
        $generator = (new MigrationGenerator($driver))->setStrictBlankMigrations(true);

        $content = $generator->generateBlankWithBodies(
            "\$this->db->setQuery('SELECT 1')->execute();",
            "\$this->db->setQuery('SELECT 0')->execute();",
            'Core',
            'Strict explicit body'
        );

        $this->assertStringContainsString("\$this->db->setQuery('SELECT 1')->execute();", $content);
        $this->assertStringContainsString("\$this->db->setQuery('SELECT 0')->execute();", $content);
        $this->assertStringNotContainsString('TODO: Implement migration', $content);
        $this->assertStringNotContainsString('TODO: Implement rollback', $content);
    }

    /**
     * Test strict blank migration mode rejects TODO placeholders in provided bodies
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateBlankWithBodiesStrictModeRejectsTodoPlaceholders(string $dbName, Driver $driver): void
    {
        $generator = (new MigrationGenerator($driver))->setStrictBlankMigrations(true);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('rejects TODO placeholder bodies');

        $generator->generateBlankWithBodies(
            '// TODO: Implement migration',
            '// TODO: Implement rollback',
            'Core',
            'Strict placeholder rejection'
        );
    }

    /**
     * Test generateBlankWithBodies rejects empty content
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateBlankWithBodiesRejectsEmptyBodies(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('cannot be empty');

        $generator->generateBlankWithBodies(" \n ", "\t");
    }

    // =========================================================================
    // Table Diff Migration Tests
    // =========================================================================

    /**
     * Test generating migration from table diff with added columns
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateFromTableDiffWithAddedColumns(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);
        $comparator = new Comparator();

        $table1 = $this->createTableInfo('test_users', [
            'id' => ['full_type' => 'INT(11)', 'auto_increment' => true],
            'name' => 'VARCHAR(255)',
        ]);

        $table2 = $this->createTableInfo('test_users', [
            'id' => ['full_type' => 'INT(11)', 'auto_increment' => true],
            'name' => 'VARCHAR(255)',
            'email' => 'VARCHAR(255)',
        ]);

        $diff = $comparator->compareTables($table1, $table2);
        $this->assertFalse($diff->isEmpty(), 'Diff should detect added column');

        $content = $generator->generateFromTableDiff($diff, 'ComUsers');

        // Verify class structure
        $this->assertStringContainsString('namespace Migrations;', $content);
        $this->assertStringContainsString('extends Base', $content);
        $this->assertStringContainsString('public function up()', $content);
        $this->assertStringContainsString('public function down()', $content);

        // Verify auto-generated description mentions adding column
        $this->assertStringContainsString('add 1 column', $content);
    }

    /**
     * Test generating migration from table diff with removed columns
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateFromTableDiffWithRemovedColumns(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);
        $comparator = new Comparator();

        $table1 = $this->createTableInfo('test_users', [
            'id' => 'INT(11)',
            'name' => 'VARCHAR(255)',
            'deprecated_field' => 'VARCHAR(100)',
        ]);

        $table2 = $this->createTableInfo('test_users', [
            'id' => 'INT(11)',
            'name' => 'VARCHAR(255)',
        ]);

        $diff = $comparator->compareTables($table1, $table2);
        $this->assertFalse($diff->isEmpty(), 'Diff should detect removed column');

        $content = $generator->generateFromTableDiff($diff, 'Core');

        // Verify description mentions removing columns
        $this->assertStringContainsString('remove 1 column', $content);
    }

    /**
     * Test generating migration from table diff with modified columns
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateFromTableDiffWithModifiedColumns(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);
        $comparator = new Comparator();

        $table1 = $this->createTableInfo('test_posts', [
            'id' => 'INT(11)',
            'description' => 'VARCHAR(100)',
        ]);

        $table2 = $this->createTableInfo('test_posts', [
            'id' => 'INT(11)',
            'description' => 'VARCHAR(500)',
        ]);

        $diff = $comparator->compareTables($table1, $table2);
        $this->assertFalse($diff->isEmpty(), 'Diff should detect modified column');

        $content = $generator->generateFromTableDiff($diff, 'ComBlog');

        // Verify description mentions modifying columns
        $this->assertStringContainsString('modify 1 column', $content);
    }

    // =========================================================================
    // Schema Diff Migration Tests
    // =========================================================================

    /**
     * Test generating migration from schema diff with added tables
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateFromSchemaDiffWithAddedTables(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);
        $comparator = new Comparator();

        $schema1 = $this->createDatabaseInfo([
            'users' => ['id' => 'INT', 'name' => 'VARCHAR(255)'],
        ]);

        $schema2 = $this->createDatabaseInfo([
            'users' => ['id' => 'INT', 'name' => 'VARCHAR(255)'],
            'new_table' => ['id' => 'INT', 'data' => 'TEXT'],
        ]);

        $diff = $comparator->compareSchemas($schema1, $schema2);
        $this->assertFalse($diff->isEmpty(), 'Diff should detect added table');

        $content = $generator->generateFromSchemaDiff($diff, 'Core');

        // Verify class structure
        $this->assertStringContainsString('namespace Migrations;', $content);
        $this->assertStringContainsString('extends Base', $content);

        // Verify description mentions adding tables
        $this->assertStringContainsString('add 1 table', $content);
    }

    /**
     * Test generating migration from schema diff with multiple changes
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateFromSchemaDiffWithMultipleChanges(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);
        $comparator = new Comparator();

        $schema1 = $this->createDatabaseInfo([
            'users' => ['id' => 'INT', 'name' => 'VARCHAR(255)'],
            'old_table' => ['id' => 'INT', 'data' => 'TEXT'],
        ]);

        $schema2 = $this->createDatabaseInfo([
            'users' => ['id' => 'INT', 'name' => 'VARCHAR(255)'],
            'added_table' => ['id' => 'INT', 'info' => 'TEXT'],
        ]);

        $diff = $comparator->compareSchemas($schema1, $schema2);
        $this->assertFalse($diff->isEmpty(), 'Diff should detect changes');

        $content = $generator->generateFromSchemaDiff($diff, 'Core');

        // Verify description mentions both operations
        $this->assertStringContainsString('add 1 table', $content);
        $this->assertStringContainsString('remove 1 table', $content);
    }

    // =========================================================================
    // Configuration Tests
    // =========================================================================

    /**
     * Test custom template support
     */
    #[DataProvider('databaseProvider')]
    public function testSetTemplate(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);

        $customTemplate = <<<'PHP'
<?php
// Custom migration template
namespace <namespace>;

class <className> {
    // <description>
    public function up() {
<upCode>
    }
    public function down() {
<downCode>
    }
}
PHP;

        $generator->setTemplate($customTemplate);
        $content = $generator->generateBlank('Core', 'Custom test');

        $this->assertStringContainsString('// Custom migration template', $content);
        $this->assertStringContainsString('// Custom test', $content);
        $this->assertStringContainsString('namespace Migrations;', $content);
    }

    /**
     * Test copyright year setting
     */
    #[DataProvider('databaseProvider')]
    public function testSetCopyrightYear(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);

        $generator->setCopyrightYear('2025');
        $content = $generator->generateBlank('Core', 'Test');

        $this->assertStringContainsString('2005-2025', $content);
    }

    /**
     * Test existence checks setting
     */
    #[DataProvider('databaseProvider')]
    public function testSetIncludeExistenceChecks(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);
        $comparator = new Comparator();

        $table1 = $this->createTableInfo('test_table', [
            'id' => 'INT(11)',
        ]);

        $table2 = $this->createTableInfo('test_table', [
            'id' => 'INT(11)',
            'new_col' => 'VARCHAR(255)',
        ]);

        $diff = $comparator->compareTables($table1, $table2);

        // With existence checks (default)
        $generator->setIncludeExistenceChecks(true);
        $content = $generator->generateFromTableDiff($diff, 'Core');
        $this->assertStringContainsString('tableExists', $content);

        // Without existence checks
        $generator->setIncludeExistenceChecks(false);
        $content = $generator->generateFromTableDiff($diff, 'Core');
        $this->assertStringNotContainsString('tableExists', $content);
    }

    /**
     * Test safe mode flag setting
     */
    #[DataProvider('databaseProvider')]
    public function testSetSafeFlags(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);
        $comparator = new Comparator();

        $generator->setSafeFlags(DiffSqlGenerator::SAFE_ALL);

        $table1 = $this->createTableInfo('test_table', [
            'id' => 'INT(11)',
            'to_remove' => 'VARCHAR(255)',
        ]);

        $table2 = $this->createTableInfo('test_table', [
            'id' => 'INT(11)',
        ]);

        $diff = $comparator->compareTables($table1, $table2);
        $content = $generator->generateFromTableDiff($diff, 'Core');

        // In safe mode, the content should still have the method structure
        $this->assertStringContainsString('public function up()', $content);
        $this->assertStringContainsString('public function down()', $content);
    }

    // =========================================================================
    // Utility Method Tests
    // =========================================================================

    /**
     * Test getClassName method
     */
    #[DataProvider('databaseProvider')]
    public function testGetClassName(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);

        $className = $generator->getClassName('ComUsers', '20260203120000');
        $this->assertEquals('Migration20260203120000ComUsers', $className);
    }

    /**
     * Test getClassName with auto-generated timestamp
     */
    #[DataProvider('databaseProvider')]
    public function testGetClassNameAutoTimestamp(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);

        $className = $generator->getClassName('Core');

        // Should start with 'Migration' followed by numbers and 'Core'
        $this->assertMatchesRegularExpression('/^Migration\d{14}Core$/', $className);
    }

    // =========================================================================
    // Empty Diff Tests
    // =========================================================================

    /**
     * Test empty table diff generates appropriate message
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateFromEmptyTableDiff(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);
        $comparator = new Comparator();

        $table = $this->createTableInfo('test_table', [
            'id' => 'INT(11)',
        ]);

        $diff = $comparator->compareTables($table, $table);
        // Empty diff - no changes
        $this->assertTrue($diff->isEmpty(), 'Diff should be empty for identical tables');

        $content = $generator->generateFromTableDiff($diff, 'Core');

        // Should have "no changes" comments
        $this->assertStringContainsString('No changes to apply', $content);
        $this->assertStringContainsString('No changes to revert', $content);
    }

    /**
     * Test empty schema diff generates appropriate message
     */
    #[DataProvider('databaseProvider')]
    public function testGenerateFromEmptySchemaDiff(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);
        $comparator = new Comparator();

        $schema = $this->createDatabaseInfo([
            'users' => ['id' => 'INT', 'name' => 'VARCHAR(255)'],
        ]);

        $diff = $comparator->compareSchemas($schema, $schema);
        $this->assertTrue($diff->isEmpty(), 'Diff should be empty for identical schemas');

        $content = $generator->generateFromSchemaDiff($diff, 'Core');

        // Should have "no changes" comments
        $this->assertStringContainsString('No changes to apply', $content);
        $this->assertStringContainsString('No changes to revert', $content);

        // Description should be generic
        $this->assertStringContainsString('Schema migration script', $content);
    }

    // =========================================================================
    // Structure Validation Tests
    // =========================================================================

    /**
     * Test that generated content has proper PHP structure
     */
    #[DataProvider('databaseProvider')]
    public function testGeneratedContentHasProperPhpStructure(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);

        $content = $generator->generateBlank('Core', 'Test');

        // Should be valid PHP (starts with <?php, has matching braces, etc.)
        $this->assertStringStartsWith('<?php', $content);

        // Count braces - should be balanced
        $openBraces = substr_count($content, '{');
        $closeBraces = substr_count($content, '}');
        $this->assertEquals($openBraces, $closeBraces, 'Braces should be balanced');

        // Should have proper class definition
        $this->assertMatchesRegularExpression('/class\s+Migration\d+Core\s+extends\s+Base/', $content);
    }

    /**
     * Test multiple column changes in description
     */
    #[DataProvider('databaseProvider')]
    public function testDescriptionWithMultipleChanges(string $dbName, Driver $driver): void
    {
        $generator = new MigrationGenerator($driver);
        $comparator = new Comparator();

        $table1 = $this->createTableInfo('test_table', [
            'id' => 'INT(11)',
            'old_col' => 'VARCHAR(100)',
        ]);

        $table2 = $this->createTableInfo('test_table', [
            'id' => 'INT(11)',
            'new_col1' => 'VARCHAR(255)',
            'new_col2' => 'TEXT',
        ]);

        $diff = $comparator->compareTables($table1, $table2);

        $content = $generator->generateFromTableDiff($diff, 'Core');

        // Description should mention both add and remove
        $this->assertStringContainsString('add 2 column(s)', $content);
        $this->assertStringContainsString('remove 1 column(s)', $content);
    }
}
