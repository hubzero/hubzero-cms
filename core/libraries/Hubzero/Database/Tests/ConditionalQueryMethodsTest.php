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
use Hubzero\Database\Query;

/**
 * Tests for conditional query methods (*When / *Unless)
 *
 * These methods provide closure-free conditional query building,
 * offering a simpler alternative to Laravel's when() method.
 */
class ConditionalQueryMethodsTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['conditional_test_users'];
    }

    /**
     * Create test tables and insert seed data
     *
     * @param Driver $driver Database driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('conditional_test_users', true);
        $driver->createTable('conditional_test_users')
            ->id()
            ->string('name', 255)
            ->string('email', 255)
            ->string('status', 50)->default('active')
            ->integer('age')->nullable()
            ->datetime('created_at')->nullable()
            ->execute();
    }

    /**
     * Seed users table with deterministic data for functional tests
     *
     * @param  Driver  $driver
     * @return void
     */
    private function seedUsers(Driver $driver): void
    {
        $query = new Query($driver);
        $query->delete('conditional_test_users')->execute();
        $query->insertMany('conditional_test_users', [
            [
                'id' => 1, 'name' => 'test user', 'email' => 'test1@example.com',
                'status' => 'active', 'age' => 30, 'created_at' => '2020-01-01 00:00:00',
            ],
            [
                'id' => 2, 'name' => 'Bob', 'email' => 'bob@example.com',
                'status' => 'inactive', 'age' => 40, 'created_at' => '2020-01-02 00:00:00',
            ],
            [
                'id' => 3, 'name' => 'Bob', 'email' => 'bob2@example.com',
                'status' => 'active', 'age' => 25, 'created_at' => '2020-01-03 00:00:00',
            ],
            [
                'id' => 4, 'name' => 'Alice', 'email' => 'alice@example.com',
                'status' => 'active', 'age' => null, 'created_at' => null,
            ],
        ]);
    }

    /**
     * Test orderWhen applies ORDER BY when condition is truthy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testOrderWhenWithTruthyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('id')
            ->from('conditional_test_users')
            ->orderWhen(true, 'id', 'desc')
            ->fetch('rows');

        $this->assertEquals(4, (int) $rows[0]->id);
    }

    /**
     * Test orderWhen does not apply ORDER BY when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testOrderWhenWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('id')
            ->from('conditional_test_users')
            ->order('id', 'asc')
            ->orderWhen(false, 'id', 'desc')
            ->fetch('rows');

        $this->assertEquals(1, (int) $rows[0]->id);
    }

    /**
     * Test orderWhen with non-boolean truthy value
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testOrderWhenWithNonBooleanTruthyValue(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $sortField = 'name';
        $rows = $query->select('name')
            ->from('conditional_test_users')
            ->orderWhen($sortField, $sortField, 'asc')
            ->fetch('rows');

        $this->assertEquals('Alice', $rows[0]->name);
    }

    /**
     * Test orderUnless applies ORDER BY when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testOrderUnlessWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('id')
            ->from('conditional_test_users')
            ->orderUnless(false, 'id', 'desc')
            ->fetch('rows');

        $this->assertEquals(4, (int) $rows[0]->id);
    }

    /**
     * Test orderUnless does not apply ORDER BY when condition is truthy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testOrderUnlessWithTruthyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('id')
            ->from('conditional_test_users')
            ->order('id', 'asc')
            ->orderUnless(true, 'id', 'desc')
            ->fetch('rows');

        $this->assertEquals(1, (int) $rows[0]->id);
    }

    /**
     * Test limitWhen applies LIMIT when condition is truthy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testLimitWhenWithTruthyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->limitWhen(true, 2)
            ->fetch('rows');

        $this->assertCount(2, $rows);
    }

    /**
     * Test limitWhen does not apply LIMIT when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testLimitWhenWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->limitWhen(false, 2)
            ->fetch('rows');

        $this->assertCount(4, $rows);
    }

    /**
     * Test limitUnless applies LIMIT when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testLimitUnlessWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $unlimited = false;
        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->limitUnless($unlimited, 2)
            ->fetch('rows');

        $this->assertCount(2, $rows);
    }

    /**
     * Test limitUnless does not apply LIMIT when condition is truthy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testLimitUnlessWithTruthyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $unlimited = true;
        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->limitUnless($unlimited, 2)
            ->fetch('rows');

        $this->assertCount(4, $rows);
    }

    /**
     * Test startWhen applies OFFSET when condition is truthy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testStartWhenWithTruthyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $page = 2;
        $rows = $query->select('id')
            ->from('conditional_test_users')
            ->order('id', 'asc')
            ->limit(2)
            ->startWhen($page > 1, ($page - 1) * 2)
            ->fetch('rows');

        $this->assertEquals(3, (int) $rows[0]->id);
    }

    /**
     * Test startWhen does not apply OFFSET when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testStartWhenWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $page = 1;
        $rows = $query->select('id')
            ->from('conditional_test_users')
            ->order('id', 'asc')
            ->limit(2)
            ->startWhen($page > 1, 0)
            ->fetch('rows');

        $this->assertEquals(1, (int) $rows[0]->id);
    }

    /**
     * Test startUnless applies OFFSET when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testStartUnlessWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('id')
            ->from('conditional_test_users')
            ->order('id', 'asc')
            ->limit(2)
            ->startUnless(false, 2)
            ->fetch('rows');

        $this->assertEquals(3, (int) $rows[0]->id);
    }

    /**
     * Test groupWhen applies GROUP BY when condition is truthy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testGroupWhenWithTruthyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('name')
            ->from('conditional_test_users')
            ->groupWhen(true, 'name')
            ->fetch('rows');

        $this->assertCount(3, $rows);
    }

    /**
     * Test groupWhen does not apply GROUP BY when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testGroupWhenWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('name')
            ->from('conditional_test_users')
            ->groupWhen(false, 'name')
            ->fetch('rows');

        $this->assertCount(4, $rows);
    }

    /**
     * Test groupUnless applies GROUP BY when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testGroupUnlessWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('name')
            ->from('conditional_test_users')
            ->groupUnless(false, 'name')
            ->fetch('rows');

        $this->assertCount(3, $rows);
    }

    /**
     * Test joinWhen applies JOIN when condition is truthy
     *
     * Note: We use a self-join to avoid needing additional test tables
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testJoinWhenWithTruthyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $includeRelated = true;
        $rows = $query->select('u1.id')
            ->from('conditional_test_users', 'u1')
            ->joinWhen($includeRelated, 'conditional_test_users AS u2', 'u1.name', 'u2.name')
            ->fetch('rows');

        $this->assertCount(6, $rows);
    }

    /**
     * Test joinWhen does not apply JOIN when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testJoinWhenWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $includeRelated = false;
        $rows = $query->select('u1.id')
            ->from('conditional_test_users', 'u1')
            ->joinWhen($includeRelated, 'conditional_test_users AS u2', 'u1.name', 'u2.name')
            ->fetch('rows');

        $this->assertCount(4, $rows);
    }

    /**
     * Test joinWhen with left join type
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testJoinWhenWithLeftJoin(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('u1.id')
            ->from('conditional_test_users', 'u1')
            ->joinWhen(true, 'conditional_test_users AS u2', 'u1.name', 'u2.name', 'left')
            ->fetch('rows');

        $this->assertCount(6, $rows);
    }

    /**
     * Test joinUnless applies JOIN when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testJoinUnlessWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('u1.id')
            ->from('conditional_test_users', 'u1')
            ->joinUnless(false, 'conditional_test_users AS u2', 'u1.name', 'u2.name')
            ->fetch('rows');

        $this->assertCount(6, $rows);
    }

    /**
     * Test selectWhen applies columns when condition is truthy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testSelectWhenWithTruthyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $isAdmin = true;
        $row = $query->selectWhen($isAdmin, ['id', 'name', 'email'], ['id', 'name'])
            ->from('conditional_test_users')
            ->fetch('row');

        $this->assertTrue(property_exists($row, 'email'));
    }

    /**
     * Test selectWhen applies fallback columns when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testSelectWhenWithFalsyConditionAndFallback(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $isAdmin = false;
        $row = $query->selectWhen($isAdmin, ['email'], ['id', 'name'])
            ->from('conditional_test_users')
            ->fetch('row');

        $this->assertFalse(property_exists($row, 'email'));
    }

    /**
     * Test whereWhen applies WHERE when condition is truthy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testWhereWhenWithTruthyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $nameFilter = 'Bob';
        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->whereWhen($nameFilter, 'name', '=', $nameFilter)
            ->fetch('rows');

        $this->assertCount(2, $rows);
    }

    /**
     * Test whereWhen does not apply WHERE when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testWhereWhenWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $nameFilter = null;
        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->whereWhen($nameFilter, 'name', '=', $nameFilter)
            ->fetch('rows');

        $this->assertCount(4, $rows);
    }

    /**
     * Test whereUnless applies WHERE when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testWhereUnlessWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $isAdmin = false;
        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->whereUnless($isAdmin, 'id', '>', 2)
            ->fetch('rows');

        $this->assertCount(2, $rows);
    }

    /**
     * Test whereUnless does not apply WHERE when condition is truthy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testWhereUnlessWithTruthyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $isAdmin = true;
        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->whereUnless($isAdmin, 'id', '>', 2)
            ->fetch('rows');

        $this->assertCount(4, $rows);
    }

    /**
     * Test whereInWhen applies WHERE IN when condition is truthy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testWhereInWhenWithTruthyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $ids = [1, 2, 3];
        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->whereInWhen($ids, 'id', $ids)
            ->fetch('rows');

        $this->assertCount(3, $rows);
    }

    /**
     * Test whereInWhen does not apply WHERE IN when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testWhereInWhenWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $ids = [];
        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->whereInWhen($ids, 'id', [1, 2, 3])
            ->fetch('rows');

        $this->assertCount(4, $rows);
    }

    /**
     * Test whereInUnless applies WHERE IN when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testWhereInUnlessWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->whereInUnless(false, 'id', [1, 2])
            ->fetch('rows');

        $this->assertCount(2, $rows);
    }

    /**
     * Test whereNotInWhen applies WHERE NOT IN when condition is truthy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testWhereNotInWhenWithTruthyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $excludeIds = [1];
        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->whereNotInWhen($excludeIds, 'id', $excludeIds)
            ->fetch('rows');

        $this->assertCount(3, $rows);
    }

    /**
     * Test whereNotInWhen does not apply WHERE NOT IN when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testWhereNotInWhenWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->whereNotInWhen(false, 'id', [1])
            ->fetch('rows');

        $this->assertCount(4, $rows);
    }

    /**
     * Test whereNotInUnless applies WHERE NOT IN when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testWhereNotInUnlessWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->whereNotInUnless(false, 'id', [1, 2])
            ->fetch('rows');

        $this->assertCount(2, $rows);
    }

    /**
     * Test havingWhen applies HAVING when condition is truthy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testHavingWhenWithTruthyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $minCount = 1;
        $rows = $query->select('name')
            ->from('conditional_test_users')
            ->group('name')
            ->havingWhen($minCount, 'COUNT(*)', '>', 1)
            ->fetch('rows');

        $this->assertCount(1, $rows);
        $this->assertEquals('Bob', $rows[0]->name);
    }

    /**
     * Test havingWhen does not apply HAVING when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testHavingWhenWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $minCount = 0;
        $rows = $query->select('name')
            ->from('conditional_test_users')
            ->group('name')
            ->havingWhen($minCount, 'COUNT(*)', '>', 1)
            ->fetch('rows');

        $this->assertCount(3, $rows);
    }

    /**
     * Test havingUnless applies HAVING when condition is falsy
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testHavingUnlessWithFalsyCondition(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $rows = $query->select('name')
            ->from('conditional_test_users')
            ->group('name')
            ->havingUnless(false, 'COUNT(*)', '>', 1)
            ->fetch('rows');

        $this->assertCount(1, $rows);
    }

    /**
     * Test method chaining with multiple conditional methods
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testMethodChainingWithMultipleConditionals(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        $filters = [
            'name' => 'Bob',
            'email' => null,
            'sort' => 'id',
            'limit' => 1,
            'page' => 2,
        ];

        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->whereWhen($filters['name'], 'name', '=', $filters['name'])
            ->whereWhen($filters['email'], 'email', '=', $filters['email'])
            ->orderWhen($filters['sort'], $filters['sort'], 'desc')
            ->limitWhen($filters['limit'], $filters['limit'])
            ->startWhen($filters['page'] > 1, ($filters['page'] - 1) * $filters['limit'])
            ->fetch('rows');

        $this->assertCount(1, $rows);
        $this->assertEquals(2, (int) $rows[0]->id);
    }

    /**
     * Test conditional methods return $this for fluent interface
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testConditionalMethodsReturnThis(string $dbName, Driver $driver)
    {
        $query = new Query($driver);

        $result = $query->orderWhen(false, 'id');
        $this->assertSame($query, $result);

        $result = $query->orderUnless(true, 'id');
        $this->assertSame($query, $result);

        $result = $query->limitWhen(false, 10);
        $this->assertSame($query, $result);

        $result = $query->limitUnless(true, 10);
        $this->assertSame($query, $result);

        $result = $query->startWhen(false, 0);
        $this->assertSame($query, $result);

        $result = $query->startUnless(true, 0);
        $this->assertSame($query, $result);

        $result = $query->groupWhen(false, 'id');
        $this->assertSame($query, $result);

        $result = $query->groupUnless(true, 'id');
        $this->assertSame($query, $result);

        $result = $query->joinWhen(false, 'conditional_test_users', 'a.id', 'b.id');
        $this->assertSame($query, $result);

        $result = $query->joinUnless(true, 'conditional_test_users', 'a.id', 'b.id');
        $this->assertSame($query, $result);

        $result = $query->selectWhen(false, ['id']);
        $this->assertSame($query, $result);

        $result = $query->whereWhen(false, 'id', '=', 1);
        $this->assertSame($query, $result);

        $result = $query->whereUnless(true, 'id', '=', 1);
        $this->assertSame($query, $result);

        $result = $query->whereInWhen(false, 'id', [1, 2]);
        $this->assertSame($query, $result);

        $result = $query->whereInUnless(true, 'id', [1, 2]);
        $this->assertSame($query, $result);

        $result = $query->whereNotInWhen(false, 'id', [1, 2]);
        $this->assertSame($query, $result);

        $result = $query->whereNotInUnless(true, 'id', [1, 2]);
        $this->assertSame($query, $result);

        $result = $query->havingWhen(false, 'count', '>', 0);
        $this->assertSame($query, $result);

        $result = $query->havingUnless(true, 'count', '>', 0);
        $this->assertSame($query, $result);
    }

    /**
     * Test conditional methods work with various truthy/falsy values
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testConditionalMethodsWithVariousTruthyFalsyValues(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        // Truthy values: non-empty string, non-zero number, non-empty array, true
        $truthyValues = ['hello', 1, -1, [1], true, new \stdClass()];

        foreach ($truthyValues as $truthy) {
            $testQuery = new Query($driver);
            $rows = $testQuery->select('*')->from('conditional_test_users')->limitWhen($truthy, 2)->fetch('rows');
            $this->assertCount(2, $rows, 'Failed for truthy: ' . var_export($truthy, true));
        }

        // Falsy values: empty string, 0, empty array, null, false
        $falsyValues = ['', 0, [], null, false];

        foreach ($falsyValues as $falsy) {
            $testQuery = new Query($driver);
            $rows = $testQuery->select('*')->from('conditional_test_users')->limitWhen($falsy, 2)->fetch('rows');
            $this->assertCount(4, $rows, 'Failed for falsy: ' . var_export($falsy, true));
        }
    }

    /**
     * Test real-world scenario: dynamic filter building
     */
    #[Test]

    #[DataProvider('databaseProvider')]

    public function testRealWorldDynamicFilterScenario(string $dbName, Driver $driver)
    {
        $this->seedUsers($driver);
        $query = new Query($driver);

        // Simulate a search controller with optional filters
        $search = 'test';
        $emailFilter = null;  // Not filtering by email
        $ids = [1, 2, 3];
        $excludeIds = [];  // No exclusions
        $sortBy = 'name';
        $sortDir = 'asc';
        $page = 1;
        $perPage = 25;

        $rows = $query->select('*')
            ->from('conditional_test_users')
            ->whereWhen($search, 'name', 'LIKE', "%{$search}%")
            ->whereWhen($emailFilter, 'email', '=', $emailFilter)
            ->whereInWhen($ids, 'id', $ids)
            ->whereNotInWhen($excludeIds, 'id', $excludeIds ?: [0])
            ->orderWhen($sortBy, $sortBy, $sortDir)
            ->limitWhen($perPage, $perPage)
            ->startWhen($page > 1, ($page - 1) * $perPage)
            ->fetch('rows');

        $this->assertCount(1, $rows);
        $this->assertEquals(1, (int) $rows[0]->id);
    }
}
