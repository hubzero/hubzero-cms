<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Query;
use Hubzero\Database\SchemaManager;

/**
 * Foreign key introspection tests
 *
 * Tests the getForeignKeys() and hasForeignKey() methods across all layers:
 * - Driver level
 * - SchemaManager level
 * - Table gateway level
 */
class ForeignKeyIntrospectionTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['fki_child', 'fki_parent', 'fki_grandparent'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Drop in reverse dependency order
        $driver->dropTable('fki_child', true);
        $driver->dropTable('fki_parent', true);
        $driver->dropTable('fki_grandparent', true);

        // Grandparent table (no foreign keys)
        $driver->createTable('fki_grandparent')
            ->id()
            ->string('name', 255)->nullable()
            ->execute();
        // Parent table (FK to grandparent with ON DELETE SET NULL)
        // Use unsignedInteger to match id() column type for FK compatibility
        $driver->createTable('fki_parent')
            ->id()
            ->unsignedInteger('grandparent_id')->nullable()
            ->string('name', 255)->nullable()
            ->foreign('grandparent_id', 'fki_grandparent', 'id', 'SET NULL', 'NO ACTION')
            ->execute();
        // Child table (FK to parent with ON DELETE CASCADE ON UPDATE CASCADE)
        $driver->createTable('fki_child')
            ->id()
            ->unsignedInteger('parent_id')->nullable()
            ->string('description', 255)->nullable()
            ->foreign('parent_id', 'fki_parent', 'id', 'CASCADE', 'CASCADE')
            ->execute();

        // Commit DDL so metadata is visible for all drivers
        try {
            $connection = $driver->getConnection();
            if ($connection && $connection->inTransaction()) {
                $connection->commit();
            }
        } catch (\Exception $e) {
            // Ignore - transaction may already be committed
        }
    }

    /**
     * Normalize a foreign key object for consistent comparison
     *
     * All drivers return: name, columns, foreign_table, foreign_columns, on_update, on_delete.
     * This method lowercases names and uppercases actions for cross-database comparison.
     *
     * @param object $fk Foreign key object from getForeignKeys()
     * @return object Normalized FK object
     */
    private function normalizeFk(object $fk): object
    {
        return (object) [
            'name'            => strtolower($fk->name ?? ''),
            'columns'         => array_map('strtolower', $fk->columns ?? []),
            'foreign_table'   => strtolower($fk->foreign_table ?? ''),
            'foreign_columns' => array_map('strtolower', $fk->foreign_columns ?? []),
            'on_update'       => strtoupper($fk->on_update ?? 'NO ACTION'),
            'on_delete'       => strtoupper($fk->on_delete ?? 'NO ACTION'),
        ];
    }

    // =========================================================================
    // Driver Level Tests
    // =========================================================================

    /**
     * Test getForeignKeys returns empty array for table without foreign keys
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testGetForeignKeysReturnsEmptyArrayForTableWithoutForeignKeys(string $dbName, Driver $driver)
    {
        // The fki_grandparent table has no foreign keys
        $foreignKeys = $driver->getForeignKeys('fki_grandparent');

        $this->assertIsArray($foreignKeys, "[$dbName] getForeignKeys should return an array");
        $this->assertCount(0, $foreignKeys, "[$dbName] Table without FKs should return empty array");
    }

    /**
     * Test getForeignKeys returns foreign key information
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testGetForeignKeysReturnsConstraintInfo(string $dbName, Driver $driver)
    {
        $foreignKeys = $driver->getForeignKeys('fki_child');

        $this->assertIsArray($foreignKeys, "[$dbName] getForeignKeys should return an array");
        $this->assertCount(1, $foreignKeys, "[$dbName] fki_child should have one foreign key");

        $fk = $this->normalizeFk($foreignKeys[0]);

        // Check required properties exist on the raw object
        $rawFk = $foreignKeys[0];
        $this->assertObjectHasProperty('name', $rawFk, "[$dbName] FK should have name property");
        $this->assertObjectHasProperty('columns', $rawFk, "[$dbName] FK should have columns property");

        // Check normalized values
        $this->assertContains('parent_id', $fk->columns, "[$dbName] FK columns should include parent_id");
        $this->assertEquals('fki_parent', $fk->foreign_table, "[$dbName] FK should reference fki_parent");
        $this->assertContains('id', $fk->foreign_columns, "[$dbName] FK foreign_columns should include id");
        $this->assertEquals('CASCADE', $fk->on_delete, "[$dbName] FK on_delete should be CASCADE");
        // Some engines do not preserve explicit ON UPDATE actions during introspection.
        $this->assertContains(
            $fk->on_update,
            ['CASCADE', 'RESTRICT', 'NO ACTION'],
            "[$dbName] FK on_update should be a valid action"
        );
    }

    /**
     * Test getForeignKeys with different referential actions
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testGetForeignKeysWithDifferentActions(string $dbName, Driver $driver)
    {
        // Check parent table FK (SET NULL on delete)
        $foreignKeys = $driver->getForeignKeys('fki_parent');

        $this->assertCount(1, $foreignKeys, "[$dbName] fki_parent should have one foreign key");

        $fk = $this->normalizeFk($foreignKeys[0]);
        $this->assertEquals('fki_grandparent', $fk->foreign_table, "[$dbName] FK should reference fki_grandparent");
        // Some engines normalize unsupported actions to fallback semantics.
        $this->assertContains(
            $fk->on_delete,
            ['SET NULL', 'RESTRICT', 'NO ACTION'],
            "[$dbName] FK on_delete should be SET NULL or a fallback action"
        );
    }

    /**
     * Test hasForeignKey returns true for existing constraint
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testHasForeignKeyReturnsTrueForExistingConstraint(string $dbName, Driver $driver)
    {
        // Get the FK name from getForeignKeys
        $foreignKeys = $driver->getForeignKeys('fki_child');
        $this->assertCount(1, $foreignKeys, "[$dbName] Should have one foreign key");

        $fkName = $foreignKeys[0]->name;

        // Test hasForeignKey
        $this->assertTrue(
            $driver->hasForeignKey('fki_child', $fkName),
            "[$dbName] hasForeignKey should return true for existing constraint"
        );
    }

    /**
     * Test hasForeignKey returns false for nonexistent constraint
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testHasForeignKeyReturnsFalseForNonexistentConstraint(string $dbName, Driver $driver)
    {
        $this->assertFalse(
            $driver->hasForeignKey('fki_grandparent', 'nonexistent_fk_xyz'),
            "[$dbName] hasForeignKey should return false for nonexistent constraint"
        );
    }

    // =========================================================================
    // SchemaManager Level Tests
    // =========================================================================

    /**
     * Test SchemaManager::getForeignKeys delegates to driver
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testSchemaGetForeignKeysDelegatesToDriver(string $dbName, Driver $driver)
    {
        $schema = new SchemaManager($driver);

        $foreignKeys = $schema->getForeignKeys('fki_child');

        $this->assertIsArray($foreignKeys, "[$dbName] SchemaManager::getForeignKeys should return an array");
        $this->assertCount(1, $foreignKeys, "[$dbName] Should have one foreign key");

        $fk = $this->normalizeFk($foreignKeys[0]);
        $this->assertEquals('fki_parent', $fk->foreign_table, "[$dbName] FK should reference fki_parent");
    }

    /**
     * Test SchemaManager::hasForeignKey delegates to driver
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testSchemaHasForeignKeyDelegatesToDriver(string $dbName, Driver $driver)
    {
        $schema = new SchemaManager($driver);

        // Get the FK name
        $foreignKeys = $schema->getForeignKeys('fki_child');
        $fkName = $foreignKeys[0]->name;

        $this->assertTrue(
            $schema->hasForeignKey('fki_child', $fkName),
            "[$dbName] SchemaManager::hasForeignKey should return true for existing constraint"
        );

        $this->assertFalse(
            $schema->hasForeignKey('fki_child', 'nonexistent_fk'),
            "[$dbName] SchemaManager::hasForeignKey should return false for nonexistent constraint"
        );
    }

    // =========================================================================
    // Table Gateway Level Tests
    // =========================================================================

    /**
     * Test Table::getForeignKeys returns constraint info
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testTableGetForeignKeysReturnsConstraintInfo(string $dbName, Driver $driver)
    {
        $schema = new SchemaManager($driver);
        $table = $schema->table('fki_child');

        $foreignKeys = $table->getForeignKeys();

        $this->assertIsArray($foreignKeys, "[$dbName] Table::getForeignKeys should return an array");
        $this->assertCount(1, $foreignKeys, "[$dbName] Should have one foreign key");

        $fk = $this->normalizeFk($foreignKeys[0]);
        $this->assertEquals('fki_parent', $fk->foreign_table, "[$dbName] FK should reference fki_parent");
    }

    /**
     * Test Table::hasForeignKey checks constraint existence
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testTableHasForeignKeyChecksExistence(string $dbName, Driver $driver)
    {
        $schema = new SchemaManager($driver);
        $table = $schema->table('fki_child');

        // Get the FK name
        $foreignKeys = $table->getForeignKeys();
        $fkName = $foreignKeys[0]->name;

        $this->assertTrue(
            $table->hasForeignKey($fkName),
            "[$dbName] Table::hasForeignKey should return true for existing constraint"
        );

        $this->assertFalse(
            $table->hasForeignKey('nonexistent_fk'),
            "[$dbName] Table::hasForeignKey should return false for nonexistent constraint"
        );
    }

    /**
     * Test Table gateway fluent introspection chain
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testTableGatewayFluentIntrospection(string $dbName, Driver $driver)
    {
        $schema = new SchemaManager($driver);

        // Fluent chain test
        $childTable = $schema->table('fki_child');

        // Can chain introspection methods
        $this->assertTrue($childTable->exists(), "[$dbName] Table should exist");
        $this->assertTrue($childTable->hasColumn('parent_id'), "[$dbName] Should have parent_id column");
        $this->assertCount(1, $childTable->getForeignKeys(), "[$dbName] Should have one FK");

        // Test table without FKs
        $grandparentTable = $schema->table('fki_grandparent');
        $this->assertCount(0, $grandparentTable->getForeignKeys(), "[$dbName] Grandparent should have no FKs");
    }

    // =========================================================================
    // Edge Case Tests
    // =========================================================================

    /**
     * Test getForeignKeys for nonexistent table returns empty array
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testGetForeignKeysForNonexistentTableReturnsEmptyArray(string $dbName, Driver $driver)
    {
        $foreignKeys = $driver->getForeignKeys('nonexistent_table_xyz');

        $this->assertIsArray($foreignKeys, "[$dbName] Should return an array");
        $this->assertCount(0, $foreignKeys, "[$dbName] Should be empty for nonexistent table");
    }

    /**
     * Test hasForeignKey for nonexistent table returns false
     *
     * @return void
     */
    #[DataProvider('databaseProvider')]
    public function testHasForeignKeyForNonexistentTableReturnsFalse(string $dbName, Driver $driver)
    {
        $this->assertFalse(
            $driver->hasForeignKey('nonexistent_table_xyz', 'any_fk'),
            "[$dbName] Should return false for nonexistent table"
        );
    }
}
