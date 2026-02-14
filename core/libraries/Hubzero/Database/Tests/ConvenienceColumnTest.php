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

/**
 * Tests for convenience column methods in TableBuilder and AlterTableBuilder
 */
class ConvenienceColumnTest extends AbstractDriverTestCase
{
    /**
     * No tables needed upfront - all created/dropped in individual tests
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
    // TableBuilder Tests
    // =========================================================================

    /**
     * Test uuid() method creates CHAR(36) column
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderUuid(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_uuid', true);

        $builder = new TableBuilder($driver, 'test_uuid');
        $builder->id()->uuid('user_uuid');
        $builder->execute();

        $this->assertTrue($driver->tableHasField('test_uuid', 'user_uuid'));

        $driver->dropTable('test_uuid');
    }

    /**
     * Test ulid() method creates CHAR(26) column
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderUlid(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_ulid', true);

        $builder = new TableBuilder($driver, 'test_ulid');
        $builder->id()->ulid('unique_id');
        $builder->execute();

        $this->assertTrue($driver->tableHasField('test_ulid', 'unique_id'));

        $driver->dropTable('test_ulid');
    }

    /**
     * Test rememberToken() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderRememberToken(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_token', true);

        $builder = new TableBuilder($driver, 'test_token');
        $builder->id()->rememberToken();
        $builder->execute();

        $this->assertTrue($driver->tableHasField('test_token', 'remember_token'));

        $driver->dropTable('test_token');
    }

    /**
     * Test softDeletes() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderSoftDeletes(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_soft', true);

        $builder = new TableBuilder($driver, 'test_soft');
        $builder->id()->softDeletes();
        $builder->execute();

        $this->assertTrue($driver->tableHasField('test_soft', 'deleted_at'));

        $driver->dropTable('test_soft');
    }

    /**
     * Test softDeletes() with custom column name
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderSoftDeletesCustomColumn(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_soft_custom', true);

        $builder = new TableBuilder($driver, 'test_soft_custom');
        $builder->id()->softDeletes('removed_at');
        $builder->execute();

        $this->assertTrue($driver->tableHasField('test_soft_custom', 'removed_at'));

        $driver->dropTable('test_soft_custom');
    }

    /**
     * Test morphs() method creates type and id columns with index
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderMorphs(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_morphs', true);

        $builder = new TableBuilder($driver, 'test_morphs');
        $builder->id()->morphs('taggable');
        $builder->execute();

        $this->assertTrue($driver->tableHasField('test_morphs', 'taggable_type'));
        $this->assertTrue($driver->tableHasField('test_morphs', 'taggable_id'));

        $driver->dropTable('test_morphs');
    }

    /**
     * Test nullableMorphs() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderNullableMorphs(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_nullable_morphs', true);

        $builder = new TableBuilder($driver, 'test_nullable_morphs');
        $builder->id()->nullableMorphs('item');
        $builder->execute();

        $this->assertTrue($driver->tableHasField('test_nullable_morphs', 'item_type'));
        $this->assertTrue($driver->tableHasField('test_nullable_morphs', 'item_id'));

        $driver->dropTable('test_nullable_morphs');
    }

    /**
     * Test uuidMorphs() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderUuidMorphs(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_uuid_morphs', true);

        $builder = new TableBuilder($driver, 'test_uuid_morphs');
        $builder->id()->uuidMorphs('img');
        $builder->execute();

        $this->assertTrue($driver->tableHasField('test_uuid_morphs', 'img_type'));
        $this->assertTrue($driver->tableHasField('test_uuid_morphs', 'img_id'));

        $driver->dropTable('test_uuid_morphs');
    }

    /**
     * Test ipAddress() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderIpAddress(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_ip', true);

        $builder = new TableBuilder($driver, 'test_ip');
        $builder->id()->ipAddress('visitor_ip');
        $builder->execute();

        $this->assertTrue($driver->tableHasField('test_ip', 'visitor_ip'));

        $driver->dropTable('test_ip');
    }

    /**
     * Test macAddress() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderMacAddress(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_mac', true);

        $builder = new TableBuilder($driver, 'test_mac');
        $builder->id()->macAddress('device_mac');
        $builder->execute();

        $this->assertTrue($driver->tableHasField('test_mac', 'device_mac'));

        $driver->dropTable('test_mac');
    }

    /**
     * Test year() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderYear(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_year', true);

        $builder = new TableBuilder($driver, 'test_year');
        $builder->id()->year('birth_year');
        $builder->execute();

        $this->assertTrue($driver->tableHasField('test_year', 'birth_year'));

        $driver->dropTable('test_year');
    }

    /**
     * Test set() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderSet(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_set', true);

        $builder = new TableBuilder($driver, 'test_set');
        $builder->id()->set('days', ['Mon', 'Tue', 'Wed', 'Thu', 'Fri']);
        $builder->execute();

        $this->assertTrue($driver->tableHasField('test_set', 'days'));

        $driver->dropTable('test_set');
    }

    /**
     * Test nullableTimestamps() is alias for timestamps()
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderNullableTimestamps(string $dbName, Driver $driver)
    {
        $driver->dropTable('test_timestamps', true);

        $builder = new TableBuilder($driver, 'test_timestamps');
        $builder->id()->nullableTimestamps();
        $builder->execute();

        $this->assertTrue($driver->tableHasField('test_timestamps', 'created'));
        $this->assertTrue($driver->tableHasField('test_timestamps', 'modified'));

        $driver->dropTable('test_timestamps');
    }

    /**
     * Test actual table creation with convenience columns
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function tableBuilderExecuteWithConvenienceColumns(string $dbName, Driver $driver)
    {
        // Cleanup
        $driver->dropTable('convtest_users', true);

        $builder = new TableBuilder($driver, 'convtest_users');
        $builder->id()
                ->string('email', 255)
                ->rememberToken()
                ->softDeletes()
                ->timestamps();

        $result = $builder->execute();
        $this->assertTrue($result);

        // Verify columns exist
        $this->assertTrue($driver->tableHasField('convtest_users', 'remember_token'));
        $this->assertTrue($driver->tableHasField('convtest_users', 'deleted_at'));
        $this->assertTrue($driver->tableHasField('convtest_users', 'created'));
        $this->assertTrue($driver->tableHasField('convtest_users', 'modified'));

        // Cleanup
        $driver->dropTable('convtest_users');
    }

    // =========================================================================
    // AlterTableBuilder Tests
    // =========================================================================

    /**
     * Test addColumn() with uuid type (addUuid convenience method doesn't exist yet)
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function alterTableBuilderAddUuidColumn(string $dbName, Driver $driver)
    {
        // Create a table first using schema builder
        $driver->dropTable('altertest_users', true);
        $schema = $driver->schema();
        $schema->createTable('altertest_users')
            ->id()
            ->string('name', 255)
            ->execute();

        // addUuid() doesn't exist, but we can add a uuid column manually
        $builder = new AlterTableBuilder($driver, 'altertest_users');
        $builder->addColumn('public_id', 'char', ['length' => 36]);

        $statements = $builder->toSql();
        $this->assertIsArray($statements);
        $this->assertNotEmpty($statements);

        // Execute and verify
        $builder->execute();
        $this->assertTrue($driver->tableHasField('altertest_users', 'public_id'));

        // Cleanup
        $driver->dropTable('altertest_users');
    }

    /**
     * Test addSoftDeletes() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function alterTableBuilderAddSoftDeletes(string $dbName, Driver $driver)
    {
        // Create a table first using schema builder
        $driver->dropTable('altertest_posts', true);
        $schema = $driver->schema();
        $schema->createTable('altertest_posts')
            ->id()
            ->string('title', 255)
            ->execute();

        $builder = new AlterTableBuilder($driver, 'altertest_posts');
        $builder->addSoftDeletes();

        $builder->execute();
        $this->assertTrue($driver->tableHasField('altertest_posts', 'deleted_at'));

        // Cleanup
        $driver->dropTable('altertest_posts');
    }

    /**
     * Test addRememberToken() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function alterTableBuilderAddRememberToken(string $dbName, Driver $driver)
    {
        // Create a table first using schema builder
        $driver->dropTable('altertest_accounts', true);
        $schema = $driver->schema();
        $schema->createTable('altertest_accounts')
            ->id()
            ->string('email', 255)
            ->execute();

        $builder = new AlterTableBuilder($driver, 'altertest_accounts');
        $builder->addRememberToken();

        $builder->execute();
        $this->assertTrue($driver->tableHasField('altertest_accounts', 'remember_token'));

        // Cleanup
        $driver->dropTable('altertest_accounts');
    }

    /**
     * Test addTimestamps() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function alterTableBuilderAddTimestamps(string $dbName, Driver $driver)
    {
        // Create a table first using schema builder
        $driver->dropTable('altertest_items', true);
        $schema = $driver->schema();
        $schema->createTable('altertest_items')
            ->id()
            ->string('name', 255)
            ->execute();

        $builder = new AlterTableBuilder($driver, 'altertest_items');
        $builder->addTimestamps();

        $builder->execute();
        $this->assertTrue($driver->tableHasField('altertest_items', 'created'));
        $this->assertTrue($driver->tableHasField('altertest_items', 'modified'));

        // Cleanup
        $driver->dropTable('altertest_items');
    }

    /**
     * Test that we can add network address columns manually
     * Note: addIpAddress() and addMacAddress() convenience methods don't exist yet
     * This test uses string() method which is available
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function alterTableBuilderAddNetworkColumns(string $dbName, Driver $driver)
    {
        // Create a table first using schema builder
        $driver->dropTable('altertest_sessions', true);
        $schema = $driver->schema();
        $schema->createTable('altertest_sessions')
            ->id()
            ->integer('user_id')
            ->execute();

        // Add network address columns using addColumn() + string()
        $builder = new AlterTableBuilder($driver, 'altertest_sessions');
        $builder->addColumn('client_ip')->string(45)
                ->addColumn('device_mac')->string(17);

        $builder->execute();
        $this->assertTrue($driver->tableHasField('altertest_sessions', 'client_ip'));
        $this->assertTrue($driver->tableHasField('altertest_sessions', 'device_mac'));

        // Cleanup
        $driver->dropTable('altertest_sessions');
    }

    /**
     * Test addMorphs() method
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function alterTableBuilderAddMorphs(string $dbName, Driver $driver)
    {
        // Create a table first using schema builder
        $driver->dropTable('altertest_comments', true);
        $schema = $driver->schema();
        $schema->createTable('altertest_comments')
            ->id()
            ->text('body')
            ->execute();

        $builder = new AlterTableBuilder($driver, 'altertest_comments');
        $builder->addMorphs('item');

        $builder->execute();
        $this->assertTrue($driver->tableHasField('altertest_comments', 'item_type'));
        $this->assertTrue($driver->tableHasField('altertest_comments', 'item_id'));

        // Cleanup
        $driver->dropTable('altertest_comments');
    }
}
