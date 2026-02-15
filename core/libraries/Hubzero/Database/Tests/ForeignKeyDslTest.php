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
use Hubzero\Database\Schema\TableBuilder;
use Hubzero\Database\Schema\AlterTableBuilder;
use Hubzero\Database\Schema\ForeignKeyBuilder;

/**
 * Tests for the Foreign Key DSL (fluent API)
 */
class ForeignKeyDslTest extends AbstractDriverTestCase
{
    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('fkdsl_products', true);
        $driver->dropTable('fkdsl_orders', true);
        $driver->dropTable('fkdsl_posts', true);
        $driver->dropTable('fkdsl_comments', true);
        $driver->dropTable('fkdsl_categories', true);
        $driver->dropTable('fkdsl_users', true);

        $driver->createTable('fkdsl_users')
            ->id()
            ->string('name', 255)
            ->execute();
    }

    protected static function getTestTables(): array
    {
        return ['fkdsl_products', 'fkdsl_orders', 'fkdsl_posts', 'fkdsl_comments', 'fkdsl_categories', 'fkdsl_users'];
    }

    /**
     * Clean up child tables created by individual tests
     */
    protected function cleanUpChildTables(Driver $driver): void
    {
        $driver->dropTable('fkdsl_products', true);
        $driver->dropTable('fkdsl_orders', true);
        $driver->dropTable('fkdsl_posts', true);
        $driver->dropTable('fkdsl_comments', true);
        $driver->dropTable('fkdsl_categories', true);
    }

    // =========================================================================
    // ForeignKeyBuilder Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testForeignKeyBuilderFluentSyntax(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_test');

        $fk = new ForeignKeyBuilder($builder, 'user_id');
        $fk->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');

        $this->assertEquals('user_id', $fk->getColumn());
        $this->assertEquals('users', $fk->getReferencedTable());
        $this->assertEquals('id', $fk->getReferencedColumn());
        $this->assertEquals('CASCADE', $fk->getOnDelete());
        $this->assertEquals('CASCADE', $fk->getOnUpdate());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testForeignKeyBuilderConstrainedAutoDeriveTable(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_test');

        $fk = new ForeignKeyBuilder($builder, 'user_id');
        $fk->constrained();

        $this->assertEquals('users', $fk->getReferencedTable());
        $this->assertEquals('id', $fk->getReferencedColumn());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testForeignKeyBuilderConstrainedExplicitTable(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_test');

        $fk = new ForeignKeyBuilder($builder, 'author_id');
        $fk->constrained('users', 'id');

        $this->assertEquals('users', $fk->getReferencedTable());
        $this->assertEquals('id', $fk->getReferencedColumn());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTableDerivationFromColumnName(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_test');

        $fk1 = new ForeignKeyBuilder($builder, 'user_id');
        $fk1->constrained();
        $this->assertEquals('users', $fk1->getReferencedTable());

        $fk2 = new ForeignKeyBuilder($builder, 'category_id');
        $fk2->constrained();
        $this->assertEquals('categories', $fk2->getReferencedTable());

        $fk3 = new ForeignKeyBuilder($builder, 'person_id');
        $fk3->constrained();
        $this->assertEquals('people', $fk3->getReferencedTable());

        $fk4 = new ForeignKeyBuilder($builder, 'status_id');
        $fk4->constrained();
        $this->assertEquals('statuses', $fk4->getReferencedTable());

        $fk5 = new ForeignKeyBuilder($builder, 'post_id');
        $fk5->constrained();
        $this->assertEquals('posts', $fk5->getReferencedTable());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testConvenienceActionMethods(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_test');

        $fk = new ForeignKeyBuilder($builder, 'user_id');
        $fk->constrained()
           ->cascadeOnDelete()
           ->cascadeOnUpdate();

        $this->assertEquals('CASCADE', $fk->getOnDelete());
        $this->assertEquals('CASCADE', $fk->getOnUpdate());

        $fk->nullOnDelete();
        $this->assertEquals('SET NULL', $fk->getOnDelete());

        $fk->restrictOnDelete();
        $this->assertEquals('RESTRICT', $fk->getOnDelete());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCustomConstraintName(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_test');

        $fk = new ForeignKeyBuilder($builder, 'user_id');
        $fk->constrained()->name('fk_custom_user');

        $this->assertEquals('fk_custom_user', $fk->getName());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testToArray(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_test');

        $fk = new ForeignKeyBuilder($builder, 'user_id');
        $fk->constrained()->onDelete('CASCADE')->name('fk_custom');

        $array = $fk->toArray();

        $this->assertEquals('user_id', $array['column']);
        $this->assertEquals('users', $array['referencedTable']);
        $this->assertEquals('id', $array['referencedColumn']);
        $this->assertEquals('CASCADE', $array['onDelete']);
        $this->assertEquals('NO ACTION', $array['onUpdate']);
        $this->assertEquals('fk_custom', $array['name']);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testForeignKeyDefaultActions(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_test');

        $fk = new ForeignKeyBuilder($builder, 'user_id');
        $fk->constrained();

        $this->assertEquals('NO ACTION', $fk->getOnDelete());
        $this->assertEquals('NO ACTION', $fk->getOnUpdate());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testActionUppercasing(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_test');

        $fk = new ForeignKeyBuilder($builder, 'user_id');
        $fk->constrained()
           ->onDelete('cascade')
           ->onUpdate('set null');

        $this->assertEquals('CASCADE', $fk->getOnDelete());
        $this->assertEquals('SET NULL', $fk->getOnUpdate());
    }

    // =========================================================================
    // TableBuilder Integration Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTableBuilderForeignFluent(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_posts');
        $result = $builder->foreign('user_id');

        $this->assertInstanceOf(ForeignKeyBuilder::class, $result);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTableBuilderForeignLegacy(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_posts');
        $result = $builder->foreign('user_id', 'fkdsl_users', 'id', 'CASCADE', 'CASCADE');

        $this->assertInstanceOf(TableBuilder::class, $result);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTableBuilderForeignId(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_posts');
        $result = $builder->foreignId('user_id');

        $this->assertInstanceOf(ForeignKeyBuilder::class, $result);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTableBuilderCreateWithFluentFk(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_posts');
        $builder->id()
                ->string('title', 255)
                ->integer('user_id', true)
                ->foreign('user_id')
                    ->references('id')
                    ->on('fkdsl_users')
                    ->cascadeOnDelete();

        $sql = $builder->toSql();

        $this->assertStringContainsString('CREATE TABLE', $sql);
        $this->assertStringContainsString('FOREIGN KEY', $sql);
        $this->assertStringContainsString('REFERENCES', $sql);
        $this->assertStringContainsString('ON DELETE CASCADE', $sql);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTableBuilderCreateWithConstrained(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_posts');
        $builder->id()
                ->string('title', 255)
                ->foreignId('user_id')->constrained('fkdsl_users');

        $sql = $builder->toSql();
        $sqlLower = strtolower($sql);

        $this->assertStringContainsString('create table', $sqlLower);
        $this->assertStringContainsString('user_id', $sqlLower);
        $this->assertStringContainsString('foreign key', $sqlLower);
        $this->assertStringContainsString('fkdsl_users', $sqlLower);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTableBuilderCreateWithCustomFkName(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_posts');
        $builder->id()
                ->integer('user_id', true)
                ->foreign('user_id')
                    ->references('id')
                    ->on('fkdsl_users')
                    ->name('fk_posts_author');

        $sql = $builder->toSql();

        $this->assertStringContainsString('fk_posts_author', strtolower($sql));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTableBuilderExecuteWithFk(string $dbName, Driver $driver)
    {
        $this->cleanUpChildTables($driver);

        $builder = new TableBuilder($driver, 'fkdsl_posts');
        $builder->id()
                ->string('title', 255)
                ->integer('user_id', true)
                ->foreign('user_id')
                    ->references('id')
                    ->on('fkdsl_users')
                    ->cascadeOnDelete();

        try {
            $result = $builder->execute();
        } catch (\RuntimeException $e) {
            if (!$driver->supportsReferentialActions()) {
                $this->assertFalse($driver->supportsReferentialActions(), "[$dbName] Driver correctly refused unsupported FK referential actions");
                return;
            }
            throw $e;
        }
        $this->assertTrue($result);

        $this->assertTrue($driver->tableExists('fkdsl_posts'));

        $fks = $driver->getForeignKeys('fkdsl_posts');
        $this->assertCount(1, $fks);
        $this->assertContains('user_id', array_map('strtolower', $fks[0]->columns));
        $this->assertEquals('fkdsl_users', strtolower($fks[0]->foreign_table));

        $this->cleanUpChildTables($driver);
    }

    // =========================================================================
    // AlterTableBuilder Integration Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAlterTableBuilderForeign(string $dbName, Driver $driver)
    {
        $builder = new AlterTableBuilder($driver, 'fkdsl_comments');
        $result = $builder->foreign('user_id');

        $this->assertInstanceOf(ForeignKeyBuilder::class, $result);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAlterTableBuilderForeignId(string $dbName, Driver $driver)
    {
        $builder = new AlterTableBuilder($driver, 'fkdsl_comments');
        $result = $builder->foreignId('user_id');

        $this->assertInstanceOf(ForeignKeyBuilder::class, $result);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAlterTableBuilderAddForeignLegacy(string $dbName, Driver $driver)
    {
        $builder = new AlterTableBuilder($driver, 'fkdsl_comments');
        $result = $builder->addForeign('user_id', 'fkdsl_users', 'id', 'CASCADE', 'CASCADE');

        $this->assertInstanceOf(AlterTableBuilder::class, $result);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testAlterTableBuilderAddFluentFk(string $dbName, Driver $driver)
    {
        $this->cleanUpChildTables($driver);

        $driver->createTable('fkdsl_comments')
            ->id()
            ->text('content')
            ->integer('user_id')
            ->execute();

        $builder = new AlterTableBuilder($driver, 'fkdsl_comments');
        $builder->foreign('user_id')
                ->references('id')
                ->on('fkdsl_users')
                ->cascadeOnDelete();

        $statements = $builder->toSql();
        $this->assertIsArray($statements);

        $this->cleanUpChildTables($driver);
    }

    // =========================================================================
    // Method Chaining Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMethodChainingBackToTableBuilder(string $dbName, Driver $driver)
    {
        $builder = new TableBuilder($driver, 'fkdsl_orders');

        $builder->id()
                ->integer('user_id', true)
                ->foreign('user_id')
                    ->references('id')
                    ->on('fkdsl_users')
                    ->cascadeOnDelete()
                ->string('status', 50);

        $sql = $builder->toSql();
        $sqlLower = strtolower($sql);
        $this->assertStringContainsString('status', $sqlLower);
        $this->assertStringContainsString('foreign key', $sqlLower);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMultipleForeignKeys(string $dbName, Driver $driver)
    {
        $this->cleanUpChildTables($driver);

        $driver->createTable('fkdsl_categories')
            ->id()
            ->string('name', 255)
            ->execute();

        $builder = new TableBuilder($driver, 'fkdsl_products');
        $builder->id()
                ->string('name', 255)
                ->integer('user_id', true)
                ->integer('category_id', true)
                ->foreign('user_id')
                    ->references('id')
                    ->on('fkdsl_users')
                    ->cascadeOnDelete()
                ->foreign('category_id')
                    ->references('id')
                    ->on('fkdsl_categories')
                    ->onDelete('SET NULL');

        try {
            $result = $builder->execute();
        } catch (\RuntimeException $e) {
            if (!$driver->supportsReferentialActions()) {
                $this->assertFalse($driver->supportsReferentialActions(), "[$dbName] Driver correctly refused unsupported FK referential actions");
                return;
            }
            throw $e;
        }
        $this->assertTrue($result);

        $fks = $driver->getForeignKeys('fkdsl_products');
        $this->assertCount(2, $fks);

        $this->cleanUpChildTables($driver);
    }
}
