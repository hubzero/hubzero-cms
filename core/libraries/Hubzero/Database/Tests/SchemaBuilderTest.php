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
use Hubzero\Database\Schema\TableDefinition;
use Hubzero\Database\Schema\Column;
use Hubzero\Database\Query;

/**
 * Schema builder tests
 * Tests the schema definition API (Column, TableDefinition) and database operations
 * across multiple database backends.
 */
class SchemaBuilderTest extends AbstractDriverTestCase
{
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
    // Column Definition Tests (Pure Unit Tests - No Database Needed)
    // =========================================================================

    /**
     * Test column creation with fluent modifiers
     */
    #[Test]
    public function columnFluentInterface(): void
    {
        $column = new Column('email', 'string', ['length' => 255]);

        $column->nullable()
               ->unique()
               ->default('test@example.com');

        $this->assertEquals('email', $column->getName());
        $this->assertEquals('string', $column->getType());
        $this->assertTrue($column->isNullable());
        $this->assertTrue($column->isUnique());
        $this->assertTrue($column->hasDefault());
        $this->assertEquals('test@example.com', $column->getDefault());
    }

    /**
     * Test column unsigned and auto-increment
     */
    #[Test]
    public function columnUnsignedAutoIncrement(): void
    {
        $column = new Column('id', 'integer');
        $column->unsigned()->autoIncrement()->primary();

        $this->assertTrue($column->isUnsigned());
        $this->assertTrue($column->isAutoIncrement());
        $this->assertTrue($column->isPrimaryKey());
    }

    // =========================================================================
    // TableDefinition Tests (Pure Unit Tests - No Database Needed)
    // =========================================================================

    /**
     * Test blueprint creates id column correctly
     */
    #[Test]
    public function tableDefinitionIdColumn(): void
    {
        $blueprint = new TableDefinition('users');
        $blueprint->id();

        $columns = $blueprint->getColumns();
        $this->assertCount(1, $columns);

        $idColumn = $columns[0];
        $this->assertEquals('id', $idColumn->getName());
        $this->assertEquals('bigInteger', $idColumn->getType());
        $this->assertTrue($idColumn->isUnsigned());
        $this->assertTrue($idColumn->isAutoIncrement());
        $this->assertTrue($idColumn->isPrimaryKey());
    }

    /**
     * Test blueprint creates various column types
     */
    #[Test]
    public function tableDefinitionColumnTypes(): void
    {
        $blueprint = new TableDefinition('test_table');

        $blueprint->integer('count');
        $blueprint->string('name', 100);
        $blueprint->text('description');
        $blueprint->boolean('active');
        $blueprint->datetime('created_at');
        $blueprint->decimal('price', 10, 2);

        $columns = $blueprint->getColumns();
        $this->assertCount(6, $columns);

        // Check string column parameters
        $nameColumn = $columns[1];
        $this->assertEquals('name', $nameColumn->getName());
        $this->assertEquals('string', $nameColumn->getType());
        $this->assertEquals(['length' => 100], $nameColumn->getParameters());
    }

    /**
     * Test blueprint timestamps helper
     */
    #[Test]
    public function tableDefinitionTimestamps(): void
    {
        $blueprint = new TableDefinition('posts');
        $blueprint->timestamps();

        $columns = $blueprint->getColumns();
        $this->assertCount(2, $columns);

        $this->assertEquals('created_at', $columns[0]->getName());
        $this->assertEquals('timestamp', $columns[0]->getType());
        $this->assertTrue($columns[0]->isNullable());

        $this->assertEquals('updated_at', $columns[1]->getName());
        $this->assertTrue($columns[1]->isNullable());
    }

    /**
     * Test blueprint indexes
     */
    #[Test]
    public function tableDefinitionIndexes(): void
    {
        $blueprint = new TableDefinition('users');
        $blueprint->string('email')->unique();
        $blueprint->index(['created_at', 'updated_at']);
        $blueprint->uniqueIndex('username');

        $indexes = $blueprint->getIndexes();
        $this->assertCount(2, $indexes);

        $this->assertEquals('index', $indexes[0]['type']);
        $this->assertEquals(['created_at', 'updated_at'], $indexes[0]['columns']);

        $this->assertEquals('unique', $indexes[1]['type']);
        $this->assertEquals(['username'], $indexes[1]['columns']);
    }

    // =========================================================================
    // Database Integration Tests
    // =========================================================================

    /**
     * Test creating table with various column types across all databases
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function createTableWithTypes(string $dbName, Driver $driver): void
    {
        // Drop table if exists
        $driver->dropTable('test_schema_types', true);

        // Create table with various column types
        $driver->createTable('test_schema_types')
            ->id()
            ->string('name', 100)
            ->string('email', 255)->nullable()
            ->integer('age')->default(0)
            ->boolean('active')->default(true)
            ->text('bio')->nullable()
            ->datetime('created_at')->nullable()
            ->decimal('balance', 10, 2)->default(0.00)
            ->execute();

        // Verify table exists
        $this->assertTrue($driver->tableExists('test_schema_types'), "[$dbName]");

        // Verify columns exist
        $this->assertTrue($driver->tableHasField('test_schema_types', 'id'), "[$dbName]");
        $this->assertTrue($driver->tableHasField('test_schema_types', 'name'), "[$dbName]");
        $this->assertTrue($driver->tableHasField('test_schema_types', 'email'), "[$dbName]");
        $this->assertTrue($driver->tableHasField('test_schema_types', 'age'), "[$dbName]");
        $this->assertTrue($driver->tableHasField('test_schema_types', 'active'), "[$dbName]");
        $this->assertTrue($driver->tableHasField('test_schema_types', 'bio'), "[$dbName]");
        $this->assertTrue($driver->tableHasField('test_schema_types', 'created_at'), "[$dbName]");
        $this->assertTrue($driver->tableHasField('test_schema_types', 'balance'), "[$dbName]");

        // Clean up
        $driver->dropTable('test_schema_types', true);
    }

    /**
     * Test creating table with indexes across all databases
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function createTableWithIndexes(string $dbName, Driver $driver): void
    {
        // Drop table if exists
        $driver->dropTable('test_schema_indexes', true);

        // Create table with indexes
        $driver->createTable('test_schema_indexes')
            ->id()
            ->string('username', 50)
            ->string('email', 100)
            ->integer('status')->default(1)
            ->index('idx_username', 'username')
            ->index('idx_status_email', ['status', 'email'])
            ->execute();

        // Verify table exists
        $this->assertTrue($driver->tableExists('test_schema_indexes'), "[$dbName]");

        // Verify indexes exist
        $this->assertTrue($driver->tableHasKey('test_schema_indexes', 'idx_username'), "[$dbName]");
        $this->assertTrue($driver->tableHasKey('test_schema_indexes', 'idx_status_email'), "[$dbName]");

        // Clean up
        $driver->dropTable('test_schema_indexes', true);
    }

    /**
     * Test inserting and querying data in created table
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function insertAndQueryData(string $dbName, Driver $driver): void
    {
        // Drop table if exists
        $driver->dropTable('test_schema_integration', true);

        // Create table
        $driver->createTable('test_schema_integration')
            ->id()
            ->string('name', 100)
            ->string('email', 100)
            ->integer('score')->default(0)
            ->execute();

        // Insert test data
        $query = new Query($driver);
        $query->insert('test_schema_integration')
            ->values(['name' => 'Alice', 'email' => 'alice@example.com', 'score' => 95])
            ->execute();

        $query = new Query($driver);
        $query->insert('test_schema_integration')
            ->values(['name' => 'Bob', 'email' => 'bob@example.com', 'score' => 87])
            ->execute();

        // Query data back
        $query = new Query($driver);
        $results = $query->select('*')
            ->from('test_schema_integration')
            ->order('score', 'desc')
            ->fetch();

        $this->assertCount(2, $results, "[$dbName]");
        $this->assertEquals('Alice', $results[0]->name, "[$dbName]");
        $this->assertEquals(95, $results[0]->score, "[$dbName]");
        $this->assertEquals('Bob', $results[1]->name, "[$dbName]");
        $this->assertEquals(87, $results[1]->score, "[$dbName]");

        // Clean up
        $driver->dropTable('test_schema_integration', true);
    }

    /**
     * Test modifying table structure (add column)
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function modifyTableAddColumn(string $dbName, Driver $driver): void
    {
        $schema = $driver->schema();

        // Drop table if exists
        $driver->dropTable('test_schema_modify', true);

        // Create initial table
        $driver->createTable('test_schema_modify')
            ->id()
            ->string('name', 100)
            ->execute();

        // Verify initial state
        $this->assertTrue($driver->tableHasField('test_schema_modify', 'name'), "[$dbName]");
        $this->assertFalse($driver->tableHasField('test_schema_modify', 'email'), "[$dbName]");

        // Add new column
        $schema->alterTable('test_schema_modify')
            ->addColumn('email', 'VARCHAR(255)')
            ->nullable()
            ->execute();

        // Verify column was added
        $this->assertTrue($driver->tableHasField('test_schema_modify', 'email'), "[$dbName]");

        // Insert data with new column
        $query = new Query($driver);
        $query->insert('test_schema_modify')
            ->values(['name' => 'Test User', 'email' => 'test@example.com'])
            ->execute();

        // Query it back
        $query = new Query($driver);
        $result = $query->select('*')
            ->from('test_schema_modify')
            ->fetch('row');

        $this->assertEquals('Test User', $result->name, "[$dbName]");
        $this->assertEquals('test@example.com', $result->email, "[$dbName]");

        // Clean up
        $driver->dropTable('test_schema_modify', true);
    }

    /**
     * Test timestamps helper creates correct columns
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function timestampsHelper(string $dbName, Driver $driver): void
    {
        // Drop table if exists
        $driver->dropTable('test_schema_timestamps', true);

        // Create table with timestamps
        $driver->createTable('test_schema_timestamps')
            ->id()
            ->string('title', 100)
            ->timestamp('created_at')->nullable()
            ->timestamp('updated_at')->nullable()
            ->execute();

        // Verify timestamp columns exist
        $this->assertTrue($driver->tableHasField('test_schema_timestamps', 'created_at'), "[$dbName]");
        $this->assertTrue($driver->tableHasField('test_schema_timestamps', 'updated_at'), "[$dbName]");

        // Insert record (timestamps should be nullable, so no need to provide them)
        $query = new Query($driver);
        $query->insert('test_schema_timestamps')
            ->values(['title' => 'Test Post'])
            ->execute();

        // Verify insertion succeeded
        $query = new Query($driver);
        $result = $query->select('*')
            ->from('test_schema_timestamps')
            ->fetch('row');

        $this->assertEquals('Test Post', $result->title, "[$dbName]");

        // Clean up
        $driver->dropTable('test_schema_timestamps', true);
    }

    /**
     * Test drop table if exists
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function dropIfExists(string $dbName, Driver $driver): void
    {
        // Should not throw even if table doesn't exist
        $driver->dropTable('nonexistent_table_xyz', true);

        // Create then drop
        $driver->createTable('test_drop_integration')
            ->id()
            ->string('name', 50)
            ->execute();

        $this->assertTrue($driver->tableExists('test_drop_integration'), "[$dbName]");

        $driver->dropTable('test_drop_integration', true);

        $this->assertFalse($driver->tableExists('test_drop_integration'), "[$dbName]");
    }

    /**
     * Test table and column introspection
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableIntrospection(string $dbName, Driver $driver): void
    {
        // Drop table if exists
        $driver->dropTable('test_introspect', true);

        // Create table
        $driver->createTable('test_introspect')
            ->id()
            ->string('username', 50)
            ->string('email', 100)
            ->integer('age')->nullable()
            ->execute();

        // Verify table exists
        $this->assertTrue($driver->tableExists('test_introspect'), "[$dbName]");

        // Verify columns exist
        $this->assertTrue($driver->tableHasField('test_introspect', 'id'), "[$dbName]");
        $this->assertTrue($driver->tableHasField('test_introspect', 'username'), "[$dbName]");
        $this->assertTrue($driver->tableHasField('test_introspect', 'email'), "[$dbName]");
        $this->assertTrue($driver->tableHasField('test_introspect', 'age'), "[$dbName]");

        // Get column listing (case-insensitive check)
        $columns = $driver->getTableColumns('test_introspect');
        $columnNames = array_keys($columns);
        $columnsLower = array_map('strtolower', $columnNames);

        $this->assertContains('id', $columnsLower, "[$dbName]");
        $this->assertContains('username', $columnsLower, "[$dbName]");
        $this->assertContains('email', $columnsLower, "[$dbName]");
        $this->assertContains('age', $columnsLower, "[$dbName]");

        // Clean up
        $driver->dropTable('test_introspect', true);
    }

    /**
     * Test creating and dropping a table
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function createAndDropTable(string $dbName, Driver $driver): void
    {
        $schema = $driver->schema();

        // Drop if exists
        $driver->dropTable('test_schema_builder', true);

        // Create the table
        $driver->createTable('test_schema_builder')
            ->id()
            ->string('name', 100)
            ->string('email', 100)
            ->boolean('active')->default(true)
            ->execute();

        // Verify table exists
        $this->assertTrue($schema->tableExists('test_schema_builder'), "[$dbName]");

        // Verify columns exist
        $this->assertTrue($schema->hasColumn('test_schema_builder', 'id'), "[$dbName]");
        $this->assertTrue($schema->hasColumn('test_schema_builder', 'name'), "[$dbName]");
        $this->assertTrue($schema->hasColumn('test_schema_builder', 'email'), "[$dbName]");
        $this->assertTrue($schema->hasColumn('test_schema_builder', 'active'), "[$dbName]");

        // Get column listing
        $columns = $schema->getColumnListing('test_schema_builder');
        $columnsLower = array_map('strtolower', $columns);
        $this->assertContains('id', $columnsLower, "[$dbName]");
        $this->assertContains('name', $columnsLower, "[$dbName]");

        // Drop the table
        $schema->dropTable('test_schema_builder');

        // Verify table is gone
        $this->assertFalse($schema->tableExists('test_schema_builder'), "[$dbName]");
    }

    /**
     * Test modifying an existing table
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function modifyTable(string $dbName, Driver $driver): void
    {
        $schema = $driver->schema();

        // Drop if exists
        $driver->dropTable('test_modify', true);

        // Create initial table
        $driver->createTable('test_modify')
            ->id()
            ->string('name', 100)
            ->execute();

        // Add column using schema
        $schema->alterTable('test_modify')
            ->addColumn('email', 'VARCHAR(255)')
            ->nullable()
            ->execute();

        // Verify new column exists
        $this->assertTrue($schema->hasColumn('test_modify', 'email'), "[$dbName]");

        // Cleanup
        $schema->dropTable('test_modify');
    }
}
