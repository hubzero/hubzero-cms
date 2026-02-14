<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Query;
use Hubzero\Database\Relational;
use Hubzero\Database\Tests\TestModels\ThroughUser;
use Hubzero\Database\Tests\TestModels\ThroughProfile;
use Hubzero\Database\Tests\TestModels\ThroughCountry;
use Hubzero\Database\Tests\TestModels\ThroughMechanic;
use Hubzero\Database\Tests\TestModels\ThroughCar;
use Hubzero\Database\Tests\TestModels\ThroughOwner;

/**
 * OneToOneThrough relationship tests
 *
 * Tests for the oneToOneThrough() / hasOneThrough() relationship that
 * retrieves a single related model through an intermediate table.
 *
 * Example use case: User -> Profile -> Country
 * "A user has one country through their profile"
 */
class OneToOneThroughTest extends AbstractDriverTestCase
{
    /**
     * Return table names created by this test for automatic cleanup
     *
     * @return array
     */
    protected static function getTestTables(): array
    {
        return [
            'through_profiles',
            'through_cars',
            'through_users',
            'through_countries',
            'through_mechanics',
            'through_owners',
        ];
    }

    /**
     * Set up test tables using schema builder
     *
     * @param Driver $driver Database driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        // Drop in dependency order
        $driver->dropTable('through_profiles', true);
        $driver->dropTable('through_cars', true);
        $driver->dropTable('through_users', true);
        $driver->dropTable('through_countries', true);
        $driver->dropTable('through_mechanics', true);
        $driver->dropTable('through_owners', true);

        $driver->createTable('through_countries')
            ->id()
            ->string('name', 255)
            ->string('code', 10)
            ->execute();

        $driver->createTable('through_users')
            ->id()
            ->string('name', 255)
            ->string('email', 255)
            ->execute();

        $driver->createTable('through_profiles')
            ->id()
            ->integer('user_id')
            ->integer('country_id')->nullable()
            ->string('bio', 500)->nullable()
            ->execute();

        $driver->createTable('through_owners')
            ->id()
            ->string('name', 255)
            ->execute();

        $driver->createTable('through_mechanics')
            ->id()
            ->string('name', 255)
            ->execute();

        $driver->createTable('through_cars')
            ->id()
            ->integer('mechanic_id')
            ->integer('owner_id')->nullable()
            ->string('model', 255)
            ->execute();
    }

    /**
     * Seed test data into all tables
     *
     * @param Driver $driver Database driver
     * @return void
     */
    private function seedTestData(Driver $driver): void
    {
        $query = new Query($driver);

        // Countries
        $query->insertMany('through_countries', [
            ['name' => 'United States', 'code' => 'US'],
            ['name' => 'Canada', 'code' => 'CA'],
            ['name' => 'United Kingdom', 'code' => 'UK'],
        ]);

        // Users
        $query->insertMany('through_users', [
            ['name' => 'Alice', 'email' => 'alice@example.com'],
            ['name' => 'Bob', 'email' => 'bob@example.com'],
            ['name' => 'Charlie', 'email' => 'charlie@example.com'],
        ]);

        // Profiles (connecting users to countries)
        // Insert rows with non-null country_id via insertMany
        $query->insertMany('through_profiles', [
            ['user_id' => 1, 'country_id' => 1, 'bio' => 'Alice bio'],
            ['user_id' => 2, 'country_id' => 2, 'bio' => 'Bob bio'],
        ]);
        // Insert Charlie's profile with NULL country_id using raw SQL
        $driver->setQuery("INSERT INTO " . $driver->quoteName('through_profiles') . " ("
            . $driver->quoteName('user_id') . ", "
            . $driver->quoteName('country_id') . ", "
            . $driver->quoteName('bio')
            . ") VALUES (3, NULL, 'Charlie bio')");
        $driver->execute();

        // Owners
        $query->insertMany('through_owners', [
            ['name' => 'Owner A'],
            ['name' => 'Owner B'],
        ]);

        // Mechanics
        $query->insertMany('through_mechanics', [
            ['name' => 'Mike the Mechanic'],
            ['name' => 'Sarah the Mechanic'],
        ]);

        // Cars
        $query->insertMany('through_cars', [
            ['mechanic_id' => 1, 'owner_id' => 1, 'model' => 'Honda Civic'],
            ['mechanic_id' => 2, 'owner_id' => 2, 'model' => 'Toyota Camry'],
        ]);
    }

    /**
     * Reset test state: clear models, purge cache, clean tables, re-seed data
     *
     * @param Driver $driver Database driver
     * @return void
     */
    private function resetTestState(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        ThroughUser::clearBootedModels();
        ThroughProfile::clearBootedModels();
        ThroughCountry::clearBootedModels();
        ThroughMechanic::clearBootedModels();
        ThroughCar::clearBootedModels();
        ThroughOwner::clearBootedModels();

        Query::purgeCache();

        // Truncate all tables (delete rows + reset auto-increment)
        $driver->truncateTable('through_profiles');
        $driver->truncateTable('through_cars');
        $driver->truncateTable('through_users');
        $driver->truncateTable('through_countries');
        $driver->truncateTable('through_mechanics');
        $driver->truncateTable('through_owners');

        // Seed fresh data
        $this->seedTestData($driver);
    }

    // =========================================================================
    // Basic Functionality Tests
    // =========================================================================

    /**
     * Test oneToOneThrough method exists on Relational
     */
    #[DataProvider('databaseProvider')]
    public function testOneToOneThroughMethodExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $user = new ThroughUser();
        $this->assertTrue(method_exists($user, 'oneToOneThrough'));
    }

    /**
     * Test hasOneThrough alias exists on Relational
     */
    #[DataProvider('databaseProvider')]
    public function testHasOneThroughAliasExists(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $user = new ThroughUser();
        $this->assertTrue(method_exists($user, 'hasOneThrough'));
    }

    /**
     * Test basic oneToOneThrough retrieval
     */
    #[DataProvider('databaseProvider')]
    public function testBasicOneToOneThroughRetrieval(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $user = ThroughUser::oneOrFail(1); // Alice
        $country = $user->country;

        $this->assertInstanceOf(ThroughCountry::class, $country);
        $this->assertEquals('United States', $country->name);
        $this->assertEquals('US', $country->code);
    }

    /**
     * Test oneToOneThrough returns different result for different models
     */
    #[DataProvider('databaseProvider')]
    public function testOneToOneThroughReturnsDifferentResults(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $alice = ThroughUser::oneOrFail(1);
        $bob = ThroughUser::oneOrFail(2);

        $aliceCountry = $alice->country;
        $bobCountry = $bob->country;

        $this->assertEquals('United States', $aliceCountry->name);
        $this->assertEquals('Canada', $bobCountry->name);
    }

    /**
     * Test oneToOneThrough returns blank model when intermediate has null foreign key
     */
    #[DataProvider('databaseProvider')]
    public function testOneToOneThroughReturnsBlankWhenNullForeignKey(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $charlie = ThroughUser::oneOrFail(3);
        $country = $charlie->country;

        // Should return a blank Country model
        $this->assertInstanceOf(ThroughCountry::class, $country);
        $this->assertNull($country->id);
    }

    /**
     * Test hasOneThrough alias works identically
     */
    #[DataProvider('databaseProvider')]
    public function testHasOneThroughAliasWorks(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $mechanic = ThroughMechanic::oneOrFail(1);
        $owner = $mechanic->carOwner;

        $this->assertInstanceOf(ThroughOwner::class, $owner);
        $this->assertEquals('Owner A', $owner->name);
    }

    // =========================================================================
    // Relationship Method Tests
    // =========================================================================

    /**
     * Test relationship returns correct class type
     */
    #[DataProvider('databaseProvider')]
    public function testRelationshipReturnsCorrectType(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $user = new ThroughUser();
        $relationship = $user->country();

        $this->assertInstanceOf(
            'Hubzero\Database\Relationship\OneToOneThrough',
            $relationship
        );
    }

    /**
     * Test constrain method works
     */
    #[DataProvider('databaseProvider')]
    public function testConstrainMethodWorks(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $user = ThroughUser::oneOrFail(1);
        $query = $user->country()->constrain();

        // Should return the related model instance with constraints applied
        $this->assertInstanceOf(ThroughCountry::class, $query);
    }

    /**
     * Test rows method returns single model
     */
    #[DataProvider('databaseProvider')]
    public function testRowsReturnsSingleModel(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $user = ThroughUser::oneOrFail(1);
        $country = $user->country()->rows();

        $this->assertInstanceOf(ThroughCountry::class, $country);
        $this->assertEquals('United States', $country->name);
    }

    // =========================================================================
    // Eager Loading Tests
    // =========================================================================

    /**
     * Test eager loading with including()
     */
    #[DataProvider('databaseProvider')]
    public function testEagerLoadingWithIncluding(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $users = ThroughUser::all()
            ->including('country')
            ->rows();

        // All users should have country relationship loaded
        foreach ($users as $user) {
            $this->assertTrue($user->hasRelationship('country'));
        }

        // Check specific values
        $alice = $users->seek(1);
        $this->assertEquals('United States', $alice->country->name);

        $bob = $users->seek(2);
        $this->assertEquals('Canada', $bob->country->name);
    }

    /**
     * Test lazy eager loading with load()
     */
    #[DataProvider('databaseProvider')]
    public function testLazyEagerLoadingWithLoad(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $users = ThroughUser::all()->rows();

        // Initially no relationships loaded
        $this->assertFalse($users->first()->hasRelationship('country'));

        // Load relationships
        $users->load('country');

        // Now relationships should be loaded
        $this->assertTrue($users->first()->hasRelationship('country'));
    }

    // =========================================================================
    // Query Method Tests
    // =========================================================================

    /**
     * Test join method works for sorting
     */
    #[DataProvider('databaseProvider')]
    public function testJoinMethodWorks(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $user = new ThroughUser();
        $relationship = $user->country();

        // Should not throw
        $relationship->join();

        $this->assertTrue(true);
    }

    /**
     * Test mediate method works
     */
    #[DataProvider('databaseProvider')]
    public function testMediateMethodWorks(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $user = new ThroughUser();
        $relationship = $user->country();

        // Should not throw
        $relationship->mediate();

        $this->assertTrue(true);
    }

    /**
     * Clean up after all tests
     */
    public static function tearDownAfterClass(): void
    {
        Relational::clearBootedModels();
        Query::purgeCache();
        parent::tearDownAfterClass();
    }
}
