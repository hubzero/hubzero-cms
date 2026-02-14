<?php

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Query;
use Hubzero\Database\Driver;

/**
 * Minimal union test to debug DB2 issues
 */
class SimpleUnionTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['TEST_USERS', 'TEST_ADMINS'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('TEST_USERS', true);
        $driver->dropTable('TEST_ADMINS', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function basicUnionWorks(string $dbName, Driver $driver)
    {
        $driver->dropTable('TEST_USERS', true);
        $driver->dropTable('TEST_ADMINS', true);

        // Setup - create tables
        $schema = $driver->schema();

        $schema->createTable('TEST_USERS')
            ->id()
            ->string('name', 100)
            ->execute();

        $schema->createTable('TEST_ADMINS')
            ->id()
            ->string('name', 100)
            ->execute();

        // Insert test data
        $query = new Query($driver);
        $query->insertMany('TEST_USERS', [
            ['name' => 'User1'],
            ['name' => 'User2']
        ]);

        $query->insertMany('TEST_ADMINS', [
            ['name' => 'Admin1']
        ]);

        // Test union query
        $query = new Query($driver);
        $results = $query->select('name')
            ->from('TEST_USERS')
            ->union(function ($q) {
                $q->select('name')->from('TEST_ADMINS');
            })
            ->fetch('rows');

        // Assert
        $this->assertCount(3, $results);

        // Cleanup
        $driver->dropTable('TEST_USERS', true);
        $driver->dropTable('TEST_ADMINS', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function basicFetchWorks(string $dbName, Driver $driver)
    {
        $driver->dropTable('TEST_USERS', true);

        // Setup - create table
        $schema = $driver->schema();

        $schema->createTable('TEST_USERS')
            ->id()
            ->string('name', 100)
            ->string('email', 255)
            ->execute();

        // Insert test data
        $query = new Query($driver);
        $query->insertMany('TEST_USERS', [
            ['id' => 1, 'name' => 'Sam Wilson', 'email' => 'sam@example.com'],
            ['id' => 2, 'name' => 'Sally Smith', 'email' => 'sally@example.com'],
            ['id' => 3, 'name' => 'Steve Rogers', 'email' => 'steve@example.com']
        ]);

        // Test basic fetch with WHERE clause
        $query = new Query($driver);
        $rows = $query->select('*')
                      ->from('TEST_USERS')
                      ->whereEquals('id', '1')
                      ->fetch();

        // Assert
        $this->assertCount(1, $rows, 'Query should have returned one result');

        // Cleanup
        $driver->dropTable('TEST_USERS', true);
    }
}
