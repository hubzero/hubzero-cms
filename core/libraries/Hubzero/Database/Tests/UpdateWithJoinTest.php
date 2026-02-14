<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Query;
use Hubzero\Database\Driver;

/**
 * Tests for UPDATE with JOIN queries
 */
class UpdateWithJoinTest extends AbstractDriverTestCase
{
    /**
     * Return table names created by this test for automatic cleanup
     *
     * @return array
     */
    protected static function getTestTables(): array
    {
        return ['update_test_users', 'update_test_profiles', 'update_test_groups'];
    }

    /**
     * Set up test tables using schema builder
     *
     * Tables are created per-test, so this is a no-op.
     *
     * @param Driver $driver Database driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        // Tables are created/dropped per-test in setupTestTables() and setupGroupsTable()
    }

    /**
     * Test UPDATE with INNER JOIN updates matching records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function updateWithInnerJoinUpdatesMatchingRecords(string $dbName, Driver $driver)
    {
        // Setup tables and data
        $this->setupTestTables($driver);
        $this->insertTestData($driver);

        // Execute UPDATE with INNER JOIN
        // Update user access to 2 where profile is public
        $query = new Query($driver);
        $query->update('update_test_users')
              ->innerJoin('update_test_profiles', 'update_test_users.id', 'update_test_profiles.user_id')
              ->set(['update_test_users.access' => 2])
              ->whereEquals('update_test_profiles.public', 1);

        $affected = $query->execute();

        // Verify records were updated
        $verifyQuery = new Query($driver);
        $updated = $verifyQuery->from('update_test_users')
                              ->select('*')
                              ->whereEquals('access', 2)
                              ->fetch('rows');

        // Alice (id=1) and Charlie (id=3) have public profiles
        $this->assertCount(2, $updated, "Should update 2 users with public profiles");

        // Verify Bob (private profile) was not updated
        $bobQuery = new Query($driver);
        $bob = $bobQuery->from('update_test_users')
                      ->select('access')
                      ->whereEquals('id', 2)
                      ->fetch('row');
        $this->assertEquals(1, $bob->access, "Bob should still have access=1 (private profile)");

        // Cleanup
        $driver->dropTable('update_test_users', true);
        $driver->dropTable('update_test_profiles', true);
    }

    /**
     * Test UPDATE with LEFT JOIN and multiple conditions
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function updateWithLeftJoinAndMultipleConditions(string $dbName, Driver $driver)
    {
        // Setup tables and data
        $this->setupTestTables($driver);
        $this->insertTestData($driver);

        // Execute UPDATE with LEFT JOIN and multiple WHERE conditions
        // Update verified=1 for active users with public profiles
        $query = new Query($driver);
        $query->update('update_test_users')
              ->leftJoin('update_test_profiles', 'update_test_users.id', 'update_test_profiles.user_id')
              ->set(['update_test_users.verified' => 1])
              ->whereEquals('update_test_users.status', 'active')
              ->whereEquals('update_test_profiles.public', 1);

        $query->execute();

        // Verify only Alice was updated (active + public profile)
        $verifyQuery = new Query($driver);
        $verified = $verifyQuery->from('update_test_users')
                               ->select('*')
                               ->whereEquals('verified', 1)
                               ->fetch('rows');

        $this->assertCount(1, $verified, "Only Alice should be verified (active + public)");
        $this->assertEquals('Alice', $verified[0]->name);

        // Cleanup
        $driver->dropTable('update_test_users', true);
        $driver->dropTable('update_test_profiles', true);
    }

    /**
     * Test UPDATE with multiple JOINs
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function updateWithMultipleJoins(string $dbName, Driver $driver)
    {
        // Setup all tables
        $this->setupTestTables($driver);
        $this->insertTestData($driver);
        $this->setupGroupsTable($driver);
        $this->insertGroupsData($driver);

        // Update access for users in admin group with public profiles
        $query = new Query($driver);
        $query->update('update_test_users')
              ->innerJoin('update_test_profiles', 'update_test_users.id', 'update_test_profiles.user_id')
              ->innerJoin('update_test_groups', 'update_test_users.group_id', 'update_test_groups.id')
              ->set(['update_test_users.access' => 3])
              ->whereEquals('update_test_groups.type', 'admin')
              ->whereEquals('update_test_profiles.public', 1);

        $query->execute();

        // Verify Alice was updated (admin group + public profile)
        $verifyQuery = new Query($driver);
        $updated = $verifyQuery->from('update_test_users')
                              ->select('*')
                              ->whereEquals('access', 3)
                              ->fetch('rows');

        $this->assertCount(1, $updated, "Alice should be updated (admin + public)");
        $this->assertEquals('Alice', $updated[0]->name);

        // Cleanup
        $driver->dropTable('update_test_users', true);
        $driver->dropTable('update_test_profiles', true);
        $driver->dropTable('update_test_groups', true);
    }

    /**
     * Test UPDATE with JOIN sets multiple columns
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function updateWithJoinSetsMultipleColumns(string $dbName, Driver $driver)
    {
        // Setup tables and data
        $this->setupTestTables($driver);
        $this->insertTestData($driver);

        // Update both access and verified for users with public profiles
        $query = new Query($driver);
        $query->update('update_test_users')
              ->innerJoin('update_test_profiles', 'update_test_users.id', 'update_test_profiles.user_id')
              ->set([
                  'update_test_users.access' => 2,
                  'update_test_users.verified' => 1
              ])
              ->whereEquals('update_test_profiles.public', 1);

        $query->execute();

        // Verify both columns were updated
        $verifyQuery = new Query($driver);
        $updated = $verifyQuery->from('update_test_users')
                              ->select('*')
                              ->whereEquals('access', 2)
                              ->whereEquals('verified', 1)
                              ->fetch('rows');

        $this->assertCount(2, $updated, "2 users should have both columns updated");

        // Verify Bob was not updated
        $bobQuery = new Query($driver);
        $bob = $bobQuery->from('update_test_users')
                      ->select('*')
                      ->whereEquals('id', 2)
                      ->fetch('row');
        $this->assertEquals(1, $bob->access, "Bob's access should still be 1");
        $this->assertEquals(0, $bob->verified, "Bob should not be verified");

        // Cleanup
        $driver->dropTable('update_test_users', true);
        $driver->dropTable('update_test_profiles', true);
    }

    /**
     * Test UPDATE with JOIN doesn't affect unmatched rows
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function updateWithJoinDoesNotAffectUnmatchedRows(string $dbName, Driver $driver)
    {
        // Setup tables and data
        $this->setupTestTables($driver);
        $this->insertTestData($driver);

        // Get original values
        $beforeQuery = new Query($driver);
        $before = $beforeQuery->from('update_test_users')
                             ->select('*')
                             ->whereEquals('id', 2)
                             ->fetch('row');

        // Execute UPDATE that shouldn't match Bob
        $query = new Query($driver);
        $query->update('update_test_users')
              ->innerJoin('update_test_profiles', 'update_test_users.id', 'update_test_profiles.user_id')
              ->set(['update_test_users.access' => 2])
              ->whereEquals('update_test_profiles.public', 1);

        $query->execute();

        // Verify Bob's row is unchanged (has private profile)
        $afterQuery = new Query($driver);
        $after = $afterQuery->from('update_test_users')
                          ->select('*')
                          ->whereEquals('id', 2)
                          ->fetch('row');

        $this->assertEquals($before->access, $after->access, "Bob's access should be unchanged");
        $this->assertEquals($before->verified, $after->verified, "Bob's verified should be unchanged");

        // Cleanup
        $driver->dropTable('update_test_users', true);
        $driver->dropTable('update_test_profiles', true);
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Setup test tables
     */
    private function setupTestTables(Driver $driver): void
    {
        // Drop existing tables
        $driver->dropTable('update_test_users', true);
        $driver->dropTable('update_test_profiles', true);

        // Create users table
        $schema = $driver->schema();
        $schema->createTable('update_test_users')
            ->id()
            ->string('name', 100)
            ->string('status', 20)->default('active')
            ->integer('access')->default(1)
            ->integer('verified')->default(0)
            ->integer('group_id')->default(1)
            ->execute();

        // Create profiles table
        $schema->createTable('update_test_profiles')
            ->id()
            ->integer('user_id')
            ->string('bio', 500)->nullable()
            ->integer('public')->default(1)
            ->execute();
    }

    /**
     * Setup groups table
     */
    private function setupGroupsTable(Driver $driver): void
    {
        $driver->dropTable('update_test_groups', true);

        $schema = $driver->schema();
        $schema->createTable('update_test_groups')
            ->id()
            ->string('name', 100)
            ->string('type', 20)
            ->execute();
    }

    /**
     * Insert test data
     */
    private function insertTestData(Driver $driver): void
    {
        // Insert users
        $query = new Query($driver);
        $query->insertMany('update_test_users', [
            ['name' => 'Alice', 'status' => 'active', 'access' => 1, 'verified' => 0, 'group_id' => 1],
            ['name' => 'Bob', 'status' => 'active', 'access' => 1, 'verified' => 0, 'group_id' => 2],
            ['name' => 'Charlie', 'status' => 'inactive', 'access' => 1, 'verified' => 0, 'group_id' => 2],
        ]);

        // Insert profiles
        $query = new Query($driver);
        $query->insertMany('update_test_profiles', [
            ['user_id' => 1, 'bio' => 'Alice bio', 'public' => 1],
            ['user_id' => 2, 'bio' => 'Bob bio', 'public' => 0],
            ['user_id' => 3, 'bio' => 'Charlie bio', 'public' => 1],
        ]);
    }

    /**
     * Insert groups data
     */
    private function insertGroupsData(Driver $driver): void
    {
        $query = new Query($driver);
        $query->insertMany('update_test_groups', [
            ['name' => 'Administrators', 'type' => 'admin'],
            ['name' => 'Members', 'type' => 'member'],
        ]);
    }
}
