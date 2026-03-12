<?php

/**
 * @package    framework
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Relational;
use Hubzero\Database\Tests\TestModels\NonStdPkCountry;
use Hubzero\Database\Tests\TestModels\NonStdPkCity;

/**
 * Regression tests for belongsToOne() with non-standard parent primary keys
 *
 * Verifies that Relational::belongsToOne() defaults $parentKey to the parent
 * model's primary key rather than the child model's primary key. This bug was
 * masked when both models use 'id' as their PK.
 */
class BelongsToOneKeyTest extends AbstractDriverTestCase
{
    protected static $tablesCreated = [];

    protected static function getTestTables(): array
    {
        return ['test_nspk_countries', 'test_nspk_cities'];
    }

    protected static function getTestModelClasses(): array
    {
        return [NonStdPkCountry::class, NonStdPkCity::class];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Tables created on-demand in setupTables()
    }

    protected function setupTables(Driver $driver, string $dbName): void
    {
        if (isset(self::$tablesCreated[$dbName])) {
            $allExist = true;
            foreach (static::getTestTables() as $table) {
                if (!$driver->tableExists($table)) {
                    $allExist = false;
                    break;
                }
            }
            if ($allExist) {
                return;
            }
        }

        $countriesTable = 'test_nspk_countries';
        $citiesTable = 'test_nspk_cities';

        foreach ([$citiesTable, $countriesTable] as $table) {
            $driver->dropTable($table, true);
        }

        $driver->createTable($countriesTable)
            ->string('code', 3)
            ->string('name', 255)
            ->primaryKey('code')
            ->execute();

        $driver->createTable($citiesTable)
            ->id()
            ->string('name', 255)
            ->string('country_code', 3)
            ->execute();

        self::$tablesCreated[$dbName] = true;
    }

    protected function seedTestData(Driver $driver): void
    {
        $countriesTable = 'test_nspk_countries';
        $citiesTable = 'test_nspk_cities';

        $driver->truncateTable($citiesTable);
        $driver->truncateTable($countriesTable);

        $driver->setQuery("INSERT INTO {$countriesTable} (code, name) VALUES ('US', 'United States')");
        $driver->execute();
        $driver->setQuery("INSERT INTO {$countriesTable} (code, name) VALUES ('GB', 'United Kingdom')");
        $driver->execute();

        $driver->setQuery(
            "INSERT INTO {$citiesTable} (id, name, country_code) VALUES (1, 'New York', 'US')"
        );
        $driver->execute();
        $driver->setQuery(
            "INSERT INTO {$citiesTable} (id, name, country_code) VALUES (2, 'London', 'GB')"
        );
        $driver->execute();
        $driver->setQuery(
            "INSERT INTO {$citiesTable} (id, name, country_code) VALUES (3, 'Chicago', 'US')"
        );
        $driver->execute();

        $driver->setAutoIncrement($citiesTable, 4);
    }

    protected function configureModels(): void
    {
        NonStdPkCountry::useTable('test_nspk_countries');
        NonStdPkCity::useTable('test_nspk_cities');
    }

    protected function cleanupTestData(Driver $driver): void
    {
        foreach (['test_nspk_cities', 'test_nspk_countries'] as $table) {
            try {
                $driver->setQuery("DELETE FROM {$table}");
                $driver->execute();
            } catch (\Exception $e) {
                // Ignore cleanup errors
            }
        }
    }

    /**
     * Test that belongsToOne() correctly resolves the parent's primary key
     * when the parent model uses a non-standard PK (e.g. 'code' instead of 'id').
     *
     * Regression: Relational::belongsToOne() previously defaulted $parentKey
     * to $this->getPrimaryKey() (the child's PK 'id') instead of
     * $parent->getPrimaryKey() (the parent's PK 'code').
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testBelongsToOneWithNonStandardParentKey(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        Relational::setDefaultConnection($driver);
        $this->configureModels();
        $this->seedTestData($driver);

        $city = NonStdPkCity::oneOrFail(1);
        $country = $city->country;

        $this->assertNotNull($country, "[$dbName] City should have a country");
        $this->assertEquals('US', $country->get('code'), "[$dbName] Country code should be 'US'");
        $this->assertEquals(
            'United States',
            $country->get('name'),
            "[$dbName] Country name should match"
        );

        // Verify a second city resolves to a different country
        $city2 = NonStdPkCity::oneOrFail(2);
        $country2 = $city2->country;

        $this->assertEquals('GB', $country2->get('code'), "[$dbName] City 2 country should be 'GB'");

        $this->cleanupTestData($driver);
    }

    /**
     * Test eager loading (including) works with non-standard parent primary keys.
     *
     * Exercises the full seedWithRelation() → getRelations() → seed() pipeline
     * with a string PK, verifying the relatedKey is correctly set to the parent's PK.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testEagerLoadingWithNonStandardParentKey(string $dbName, Driver $driver)
    {
        $this->setupTables($driver, $dbName);
        Relational::setDefaultConnection($driver);
        $this->configureModels();
        $this->seedTestData($driver);

        $cities = NonStdPkCity::all()
            ->including('country')
            ->order('id', 'asc')
            ->rows();

        $this->assertCount(3, $cities, "[$dbName] Should have 3 cities");

        // Rows are indexed by PK, so seek by city ID (1, 2, 3)
        $newYork = $cities->seek(1);
        $this->assertNotFalse($newYork, "[$dbName] Should find city with ID 1");
        $this->assertEquals('New York', $newYork->get('name'), "[$dbName] City 1 name");
        $this->assertEquals(
            'United States',
            $newYork->country->get('name'),
            "[$dbName] Eager-loaded country for New York should be United States"
        );

        $london = $cities->seek(2);
        $this->assertNotFalse($london, "[$dbName] Should find city with ID 2");
        $this->assertEquals(
            'United Kingdom',
            $london->country->get('name'),
            "[$dbName] Eager-loaded country for London should be United Kingdom"
        );

        $chicago = $cities->seek(3);
        $this->assertNotFalse($chicago, "[$dbName] Should find city with ID 3");
        $this->assertEquals(
            'United States',
            $chicago->country->get('name'),
            "[$dbName] Eager-loaded country for Chicago should be United States"
        );

        $this->cleanupTestData($driver);
    }
}
