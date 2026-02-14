<?php

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Query;
use Hubzero\Database\Driver;

/**
 * Single union test to verify basic UNION functionality
 */
class UnionQuerySingleTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['union_users', 'union_admins', 'union_logs', 'union_archived_logs'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Clean slate
        $driver->dropTable('union_users', true);
        $driver->dropTable('union_admins', true);
        $driver->dropTable('union_logs', true);
        $driver->dropTable('union_archived_logs', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function unionMethodExistsAndReturnsQuery(string $dbName, Driver $driver)
    {
        // Create tables
        $schema = $driver->schema();

        $schema->createTable('union_users')
            ->id()
            ->string('name', 100)
            ->execute();

        $schema->createTable('union_admins')
            ->id()
            ->string('name', 100)
            ->execute();

        // Test
        $query = new Query($driver);
        $this->assertTrue(method_exists($query, 'union'));

        $query->select('name')->from('union_users');
        $result = $query->union(function ($q) {
            $q->select('name')->from('union_admins');
        });

        $this->assertSame($query, $result);

        // Cleanup
        $driver->dropTable('union_users', true);
        $driver->dropTable('union_admins', true);
    }
}
