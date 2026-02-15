<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Schema\Grammar;
use Hubzero\Database\Schema\TableBuilder;
use Hubzero\Database\Schema\AlterTableBuilder;
use Hubzero\Database\Schema\TableDefinition;

/**
 * Tests for TableBuilder and AlterTableBuilder
 *
 * Tests schema building DSL for creating and altering tables
 * in a database-agnostic way.
 */
class TableBuilderTest extends AbstractDriverTestCase
{
    /**
     * Build a schema grammar stub that returns a fixed create-definition payload.
     */
    private function createStubGrammar(Driver $driver, array $compiled): Grammar
    {
        return new class ($driver, $compiled) extends Grammar {
            private array $compiled;

            public function __construct(Driver $driver, array $compiled)
            {
                parent::__construct($driver);
                $this->compiled = $compiled;
            }

            public function compileCreate(TableDefinition $blueprint): array
            {
                return [];
            }

            public function compileInlineIndex(string $name, array $columns): ?string
            {
                return null;
            }

            public function compileInlineUniqueIndex(string $name, array $columns): ?string
            {
                return null;
            }

            public function compileInlineFulltextIndex(string $name, array $columns): ?string
            {
                return null;
            }

            public function compileAlterAdd(TableDefinition $blueprint): array
            {
                return [];
            }

            public function compileAlterModify(TableDefinition $blueprint): array
            {
                return [];
            }

            public function compileAlterTable(AlterTableBuilder $builder): array
            {
                return [];
            }

            public function compileCreateTableFromDefinition(array $definition): array
            {
                return $this->compiled;
            }

            protected function getColumnType(\Hubzero\Database\Schema\Column $column): string
            {
                return 'TEXT';
            }
        };
    }

    private function createBuilderWithGrammar(Driver $driver, string $table, Grammar $grammar): TableBuilder
    {
        return new class ($driver, $table, $grammar) extends TableBuilder {
            private Grammar $grammar;

            public function __construct(Driver $driver, string $table, Grammar $grammar)
            {
                parent::__construct($driver, $table);
                $this->grammar = $grammar;
            }

            protected function getGrammar()
            {
                return $this->grammar;
            }
        };
    }

    /**
     * No tables created upfront - all created/dropped in individual tests
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        // Tables created and dropped in individual test methods
    }

    /**
     * No persistent test tables
     */
    protected static function getTestTables(): array
    {
        return [];
    }

    // =========================================================================
    // TableBuilder CREATE TABLE Tests
    // =========================================================================

    /**
     * Test create-definition adapter shape without executing SQL
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderCreateDefinitionShape(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'tdf_users');
        $builder->id()
                ->string('name', 100)
                ->string('email', 255)->nullable()
                ->integer('status')->default(1)
                ->index('idx_name', 'name')
                ->uniqueIndex('uidx_email', 'email')
                ->fulltextIndex('ft_name', 'name')
                ->foreign('group_id', 'tdf_groups', 'id', 'CASCADE', 'RESTRICT')
                ->engine('InnoDB')
                ->charset('utf8')
                ->collation('utf8_general_ci')
                ->ifNotExists(false);

        $definition = $builder->toCreateDefinition();

        $this->assertIsArray($definition, "[$dbName]");
        $this->assertSame('tdf_users', $definition['table'], "[$dbName]");
        $this->assertFalse($definition['ifNotExists'], "[$dbName]");
        $this->assertSame('InnoDB', $definition['options']['engine'], "[$dbName]");
        $this->assertSame('utf8', $definition['options']['charset'], "[$dbName]");
        $this->assertSame('utf8_general_ci', $definition['options']['collation'], "[$dbName]");

        $this->assertArrayHasKey('id', $definition['columns'], "[$dbName]");
        $this->assertArrayHasKey('name', $definition['columns'], "[$dbName]");
        $this->assertArrayHasKey('email', $definition['columns'], "[$dbName]");

        $this->assertSame(['id'], $definition['primaryKey'], "[$dbName]");
        $this->assertSame(['name'], $definition['indexes']['idx_name'], "[$dbName]");
        $this->assertSame(['email'], $definition['uniqueIndexes']['uidx_email'], "[$dbName]");
        $this->assertSame(['name'], $definition['fulltextIndexes']['ft_name'], "[$dbName]");

        $this->assertCount(1, $definition['foreignKeys'], "[$dbName]");
        $this->assertSame('group_id', $definition['foreignKeys'][0]['column'], "[$dbName]");
        $this->assertSame('tdf_groups', $definition['foreignKeys'][0]['referencedTable'], "[$dbName]");
        $this->assertSame('id', $definition['foreignKeys'][0]['referencedColumn'], "[$dbName]");
        $this->assertSame('CASCADE', $definition['foreignKeys'][0]['onDelete'], "[$dbName]");
        $this->assertSame('RESTRICT', $definition['foreignKeys'][0]['onUpdate'], "[$dbName]");
    }

    /**
     * Test create-definition captures composite primary keys
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderCreateDefinitionCompositePrimaryKey(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'tdf_composite');
        $builder->unsignedInteger('left_id')
                ->unsignedInteger('right_id')
                ->string('label', 64)
                ->primaryKey(['left_id', 'right_id']);

        $definition = $builder->toCreateDefinition();

        $this->assertSame(['left_id', 'right_id'], $definition['primaryKey'], "[$dbName]");
        $this->assertArrayHasKey('left_id', $definition['columns'], "[$dbName]");
        $this->assertArrayHasKey('right_id', $definition['columns'], "[$dbName]");
    }

    /**
     * Test create-definition normalizes multiple foreign keys to a list
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderCreateDefinitionMultipleForeignKeys(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'tdf_memberships');
        $builder->unsignedInteger('user_id')
                ->unsignedInteger('group_id')
                ->foreign('user_id', 'tdf_users', 'id', 'CASCADE', 'CASCADE')
                ->foreign('group_id', 'tdf_groups', 'id', 'RESTRICT', 'CASCADE');

        $definition = $builder->toCreateDefinition();

        $this->assertCount(2, $definition['foreignKeys'], "[$dbName]");
        $this->assertSame('user_id', $definition['foreignKeys'][0]['column'], "[$dbName]");
        $this->assertSame('group_id', $definition['foreignKeys'][1]['column'], "[$dbName]");
        $this->assertSame('tdf_users', $definition['foreignKeys'][0]['referencedTable'], "[$dbName]");
        $this->assertSame('tdf_groups', $definition['foreignKeys'][1]['referencedTable'], "[$dbName]");
    }

    /**
     * Test create-definition resolves #__ table prefixes via driver prefix
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderCreateDefinitionResolvesPrefix(string $dbName, Driver $driver)
    {
        $originalPrefix = $driver->getPrefix();

        try {
            $driver->setPrefix('tst_');
            $builder = new TableBuilder($driver, '#__widgets');
            $definition = $builder->toCreateDefinition();

            $this->assertSame('tst_widgets', $definition['table'], "[$dbName]");
        } finally {
            $driver->setPrefix($originalPrefix);
        }
    }

    /**
     * Test create-definition captures fluent foreignId()->constrained() keys
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderCreateDefinitionFluentForeignIdConstrained(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'tdf_posts');
        $builder->id()
                ->foreignId('user_id')->constrained('tdf_users')
                ->foreignId('team_id')->constrained()
                ->string('title', 150);

        $definition = $builder->toCreateDefinition();

        $this->assertArrayHasKey('user_id', $definition['columns'], "[$dbName]");
        $this->assertArrayHasKey('team_id', $definition['columns'], "[$dbName]");

        $this->assertCount(2, $definition['foreignKeys'], "[$dbName]");
        $this->assertSame('user_id', $definition['foreignKeys'][0]['column'], "[$dbName]");
        $this->assertSame('tdf_users', $definition['foreignKeys'][0]['referencedTable'], "[$dbName]");
        $this->assertSame('team_id', $definition['foreignKeys'][1]['column'], "[$dbName]");
        $this->assertSame('teams', $definition['foreignKeys'][1]['referencedTable'], "[$dbName]");
    }

    /**
     * Test toSqlStatements() prefers grammar output when it returns SQL strings.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderUsesGrammarDelegationWhenListOfSqlReturned(string $dbName, Driver $driver): void
    {
        $stub = $this->createStubGrammar($driver, [
            'CREATE TABLE delegated_stub (id INTEGER)',
            'CREATE INDEX delegated_stub_idx ON delegated_stub (id)',
        ]);

        $builder = $this->createBuilderWithGrammar($driver, 'delegation_test', $stub);
        $builder->id();

        $this->assertSame(
            [
                'CREATE TABLE delegated_stub (id INTEGER)',
                'CREATE INDEX delegated_stub_idx ON delegated_stub (id)',
            ],
            $builder->toSqlStatements()
        );
    }

    /**
     * Test toSqlStatements() rejects non-list grammar output.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderRejectsWhenGrammarDoesNotReturnSqlList(string $dbName, Driver $driver): void
    {
        $stub = $this->createStubGrammar($driver, ['table' => 'not_a_statement_list']);
        $builder = $this->createBuilderWithGrammar($driver, 'fallback_test', $stub);
        $builder->id();

        $this->expectException(\UnexpectedValueException::class);
        $builder->toSqlStatements();
    }

    /**
     * Test base Grammar default produces concrete SQL statements.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderBaseGrammarDefaultCompilesCreateDefinition(string $dbName, Driver $driver): void
    {
        $grammar = new class ($driver) extends Grammar {
            public function compileCreate(TableDefinition $blueprint): array
            {
                return [];
            }

            public function compileInlineIndex(string $name, array $columns): ?string
            {
                return null;
            }

            public function compileInlineUniqueIndex(string $name, array $columns): ?string
            {
                return null;
            }

            public function compileInlineFulltextIndex(string $name, array $columns): ?string
            {
                return null;
            }

            public function compileAlterAdd(TableDefinition $blueprint): array
            {
                return [];
            }

            public function compileAlterModify(TableDefinition $blueprint): array
            {
                return [];
            }

            public function compileAlterTable(AlterTableBuilder $builder): array
            {
                return [];
            }

            public function compileCreateTableFromDefinition(array $definition): array
            {
                return parent::compileCreateTableFromDefinition($definition);
            }

            protected function getColumnType(\Hubzero\Database\Schema\Column $column): string
            {
                return 'TEXT';
            }
        };

        $builder = $this->createBuilderWithGrammar($driver, 'fallback_delegate_test', $grammar);
        $builder->id();

        $statements = $builder->toSqlStatements();
        $this->assertNotEmpty($statements, "[$dbName]");
        $this->assertStringStartsWith('CREATE TABLE', $statements[0], "[$dbName]");
    }

    /**
     * Test actually creating a table with schema builder
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableCreationExecution(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_create_exec', true);

        $driver->createTable('test_create_exec')
               ->id()
               ->string('name', 100)
               ->string('email', 255)->nullable()
               ->boolean('active')->default(true)
               ->timestamps()
               ->uniqueIndex('uidx_email', 'email')
               ->execute();

        // Verify table exists
        $this->assertTrue($driver->tableExists('test_create_exec'));

        // Verify columns exist
        $this->assertTrue($driver->tableHasField('test_create_exec', 'id'));
        $this->assertTrue($driver->tableHasField('test_create_exec', 'name'));
        $this->assertTrue($driver->tableHasField('test_create_exec', 'email'));
        $this->assertTrue($driver->tableHasField('test_create_exec', 'active'));

        // Clean up
        $driver->dropTable('test_create_exec');
    }

    // =========================================================================
    // AlterTableBuilder Tests
    // =========================================================================

    /**
     * Test adding a column via ALTER TABLE
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function alterTableAddColumn(string $dbName, Driver $driver)
    {
        // Clean up leftovers from previous failed runs
        $driver->dropTable('test_alter_add', true);
        $driver->createTable('test_alter_add')
               ->id()
               ->string('name')
               ->execute();

        // Now alter it
        $driver->alterTable('test_alter_add')
               ->addColumn('email', 'VARCHAR(255)')
               ->nullable()
               ->execute();

        // Verify new column exists
        $this->assertTrue($driver->tableHasField('test_alter_add', 'email'));

        // Clean up
        $driver->dropTable('test_alter_add');
    }

    /**
     * Test adding multiple columns via ALTER TABLE
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function alterTableAddMultipleColumns(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_alter_multi', true);
        $driver->createTable('test_alter_multi')
               ->id()
               ->execute();

        // Add multiple columns
        $driver->alterTable('test_alter_multi')
               ->addColumn('name', 'VARCHAR(100)')
               ->addColumn('email', 'VARCHAR(255)')->nullable()
               ->addColumn('active', 'BOOLEAN')
               ->execute();

        // Verify columns exist
        $this->assertTrue($driver->tableHasField('test_alter_multi', 'name'));
        $this->assertTrue($driver->tableHasField('test_alter_multi', 'email'));
        $this->assertTrue($driver->tableHasField('test_alter_multi', 'active'));

        // Clean up
        $driver->dropTable('test_alter_multi');
    }

    /**
     * Test adding indexes via ALTER TABLE
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function alterTableAddIndex(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_alter_idx', true);
        $driver->createTable('test_alter_idx')
               ->id()
               ->string('email')
               ->execute();

        // Add index
        $driver->alterTable('test_alter_idx')
               ->addIndex('idx_email', 'email')
               ->execute();

        // Verify index exists
        $this->assertTrue($driver->tableHasKey('test_alter_idx', 'idx_email'));

        // Clean up
        $driver->dropTable('test_alter_idx');
    }

    /**
     * Test adding unique index via ALTER TABLE
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function alterTableAddUniqueIndex(string $dbName, Driver $driver)
    {
        $tableName = 'test_alter_uidx';
        $driver->dropTable($tableName, true);
        $driver->createTable($tableName)
               ->id()
               ->string('username')
               ->execute();

        // Add unique index
        $driver->alterTable($tableName)
               ->addUniqueIndex('uidx_username', 'username')
               ->execute();

        // Verify index exists
        $this->assertTrue($driver->tableHasKey($tableName, 'uidx_username'));

        // Clean up
        $driver->dropTable($tableName);
    }

    /**
     * Test dropping an index via ALTER TABLE
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function alterTableDropIndex(string $dbName, Driver $driver)
    {
        $tableName = 'test_drop_idx';
        $driver->dropTable($tableName, true);
        $driver->createTable($tableName)
               ->id()
               ->string('email')
               ->index('idx_email', 'email')
               ->execute();

        // Verify index exists
        $this->assertTrue($driver->tableHasKey($tableName, 'idx_email'));

        // Drop the index
        $driver->alterTable($tableName)
               ->dropIndex('idx_email')
               ->execute();

        // Verify index is gone
        $this->assertFalse($driver->tableHasKey($tableName, 'idx_email'));

        // Clean up
        $driver->dropTable($tableName);
    }

    // =========================================================================

    /**
     * Test Schema::addColumn() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function schemaAddColumn(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_schema_add', true);
        $driver->createTable('test_schema_add')
               ->id()
               ->string('name')
               ->execute();

        // Use Schema class to add a column (use schema builder for database compatibility)
        $driver->schema()->alterTable('test_schema_add')
               ->addColumn('email', 'VARCHAR(255)')
               ->nullable()
               ->execute();

        // Verify column was added
        $this->assertTrue($driver->tableHasField('test_schema_add', 'email'));

        // Clean up
        $driver->dropTable('test_schema_add');
    }

    /**
     * Test Schema::dropColumn() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function schemaDropColumn(string $dbName, Driver $driver)
    {
        $tableName = 'test_schema_drop';
        $driver->dropTable($tableName, true);
        $driver->createTable($tableName)
               ->id()
               ->string('name')
               ->string('to_remove')
               ->execute();

        $this->assertTrue($driver->tableHasField($tableName, 'to_remove'));

        // Use Schema class to drop the column
        $driver->schema()->dropColumn($tableName, 'to_remove');

        // Verify column was removed
        $this->assertFalse($driver->tableHasField($tableName, 'to_remove'));

        // Clean up
        $driver->dropTable($tableName);
    }

    /**
     * Test Schema::renameTable() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function schemaRenameTable(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_schema_rename_old', true);
        $driver->dropTable('test_schema_rename_new', true);
        $driver->createTable('test_schema_rename_old')
               ->id()
               ->string('name')
               ->execute();

        $this->assertTrue($driver->tableExists('test_schema_rename_old'));

        // Use Schema class to rename the table
        $driver->schema()->renameTable('test_schema_rename_old', 'test_schema_rename_new');

        // Verify old name doesn't exist, new name does
        $this->assertFalse($driver->tableExists('test_schema_rename_old'));
        $this->assertTrue($driver->tableExists('test_schema_rename_new'));

        // Clean up
        $driver->dropTable('test_schema_rename_new');
    }

    /**
     * Test Schema::addIndex() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function schemaAddIndex(string $dbName, Driver $driver)
    {
        $tableName = 'test_schema_idx';
        $driver->dropTable($tableName, true);
        $driver->createTable($tableName)
               ->id()
               ->string('email')
               ->execute();

        // Use Schema class to add index
        $driver->schema()->addIndex($tableName, 'idx_email', 'email');

        // Verify index exists
        $this->assertTrue($driver->tableHasKey($tableName, 'idx_email'));

        // Clean up
        $driver->dropTable($tableName);
    }

    /**
     * Test Schema::dropIndex() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function schemaDropIndex(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_schema_drop_idx', true);
        $driver->createTable('test_schema_drop_idx')
               ->id()
               ->string('email')
               ->index('idx_email', 'email')
               ->execute();

        $this->assertTrue($driver->tableHasKey('test_schema_drop_idx', 'idx_email'));

        // Use Schema class to drop index
        $driver->schema()->dropIndex('test_schema_drop_idx', 'idx_email');

        // Verify index was removed
        $this->assertFalse($driver->tableHasKey('test_schema_drop_idx', 'idx_email'));

        // Clean up
        $driver->dropTable('test_schema_drop_idx');
    }

    /**
     * Test Schema::addUniqueIndex() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function schemaAddUniqueIndex(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_schema_uidx', true);
        $driver->createTable('test_schema_uidx')
               ->id()
               ->string('username')
               ->execute();

        // Use Schema class to add unique index
        $driver->schema()->addUniqueIndex('test_schema_uidx', 'uidx_username', 'username');

        // Verify index exists
        $this->assertTrue($driver->tableHasKey('test_schema_uidx', 'uidx_username'));

        // Clean up
        $driver->dropTable('test_schema_uidx');
    }

    /**
     * Test Schema::tableExists() and tableNotExists() methods
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function schemaTableExistence(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_schema_exists', true);
        $this->assertFalse($driver->schema()->tableExists('test_schema_exists'));
        $this->assertTrue($driver->schema()->tableNotExists('test_schema_exists'));

        // Create table
        $driver->createTable('test_schema_exists')
               ->id()
               ->execute();

        // Now table exists
        $this->assertTrue($driver->schema()->tableExists('test_schema_exists'));
        $this->assertFalse($driver->schema()->tableNotExists('test_schema_exists'));

        // Clean up
        $driver->dropTable('test_schema_exists');
    }

    /**
     * Test Schema::hasColumn() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function schemaHasColumn(string $dbName, Driver $driver)
    {
        $tableName = 'test_schema_has_col';

        $driver->dropTable($tableName, true);
        $driver->createTable($tableName)
               ->id()
               ->string('name')
               ->execute();

        // Check column existence
        $this->assertTrue($driver->schema()->hasColumn($tableName, 'name'));
        $this->assertFalse($driver->schema()->hasColumn($tableName, 'nonexistent'));

        // Clean up
        $driver->dropTable($tableName);
    }

    /**
     * Test Schema::dropTable() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function schemaDropTable(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_schema_drop_tbl', true);
        $driver->createTable('test_schema_drop_tbl')
               ->id()
               ->execute();

        $this->assertTrue($driver->tableExists('test_schema_drop_tbl'));

        // Use Schema class to drop table
        $driver->schema()->dropTable('test_schema_drop_tbl');

        // Verify table was removed
        $this->assertFalse($driver->tableExists('test_schema_drop_tbl'));
    }

    /**
     * Test Schema::addColumn() with different type definitions
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function schemaAddColumnWithTypes(string $dbName, Driver $driver)
    {
        $tableName = 'test_schema_col_types';

        $driver->dropTable($tableName, true);
        $driver->createTable($tableName)
               ->id()
               ->execute();

        // Ensure DDL commits before next operation
        $connection = $driver->getConnection();
        if ($connection && method_exists($connection, 'commit')) {
            try {
                $connection->commit();
            } catch (\Exception $e) {
                // Ignore if no active transaction
            }
        }

        // Add various column types using abstract type methods for database compatibility
        $driver->schema()->alterTable($tableName)
               ->addColumn('int_col')->integer()->default(0)
               ->addColumn('text_col')->text()->nullable()
               ->addColumn('varchar_col')->string(255)->nullable()
               ->addColumn('tinyint_col')->tinyInteger()->default(0)
               ->execute();

        // Verify all columns exist
        $this->assertTrue($driver->tableHasField($tableName, 'int_col'));
        $this->assertTrue($driver->tableHasField($tableName, 'text_col'));
        $this->assertTrue($driver->tableHasField($tableName, 'varchar_col'));
        $this->assertTrue($driver->tableHasField($tableName, 'tinyint_col'));

        // Clean up
        $driver->dropTable($tableName);
    }

    /**
     * Test fluent alterTable builder via Schema class
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function schemaAlterTableFluent(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_schema_alter_fluent', true);
        $driver->createTable('test_schema_alter_fluent')
               ->id()
               ->string('name')
               ->execute();

        // Use Schema class alterTable fluent builder
        $driver->schema()->alterTable('test_schema_alter_fluent')
               ->addColumn('email', 'VARCHAR(255)')
               ->nullable()
               ->addColumn('status', 'INT(11)')
               ->default(0)
               ->execute();

        // Verify columns were added
        $this->assertTrue($driver->tableHasField('test_schema_alter_fluent', 'email'));
        $this->assertTrue($driver->tableHasField('test_schema_alter_fluent', 'status'));

        // Clean up
        $driver->dropTable('test_schema_alter_fluent');
    }
}
