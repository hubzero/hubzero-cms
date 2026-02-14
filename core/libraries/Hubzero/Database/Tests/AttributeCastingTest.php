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
use Hubzero\Database\Relational;
use Hubzero\Database\Query;
use Hubzero\Database\Tests\TestModels\CastTestItem;
use Hubzero\Database\Tests\TestModels\NoCastItem;

/**
 * Multi-database Attribute Casting tests
 *
 * Tests automatic type casting of model attributes across multiple database backends.
 */
class AttributeCastingTest extends AbstractDriverTestCase
{
    /**
     * Test table name
     *
     * @var string
     */
    private static $testTable = 'cast_test_items';

    /**
     * Return table names created by this test for automatic cleanup
     *
     * @return array Table names
     */
    protected static function getTestTables(): array
    {
        return [self::$testTable];
    }

    /**
     * Create test tables and insert seed data for a database
     *
     * @param Driver $driver Database driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        // Drop existing table
        try {
            $driver->dropTable(self::$testTable, true);
        } catch (\Exception $e) {
            // Table doesn't exist, which is fine
        }

        // Create table using schema builder for database-agnostic DDL
        $schema = $driver->schema();
        $schema->createTable(self::$testTable)
            ->id()
            ->string('title', 255)
            ->integer('is_active')->default(1)
            ->integer('view_count')->default(0)
            ->decimal('price', 10, 2)->default(0.0)
            ->string('amount', 20)->nullable()
            ->string('tax_rate', 20)->nullable()
            ->text('settings')->nullable()
            ->text('metadata')->nullable()
            ->text('tags')->nullable()
            ->datetime('published_at')->nullable()
            ->date('created_date')->nullable()
            ->datetime('expires_at')->nullable()
            ->execute();
    }

    /**
     * Set up before each test
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Clear booted models cache
        CastTestItem::clearBootedModels();
        NoCastItem::clearBootedModels();

        // Purge query cache
        Query::purgeCache();
    }

    /**
     * Clean up test data before each test
     *
     * @param string $dbName Database name
     * @param Driver $driver Database driver
     * @return void
     */
    private function cleanupData(string $dbName, Driver $driver): void
    {
        try {
            $driver->exec("DELETE FROM " . $driver->quoteName(self::$testTable));

            // Commit after DELETE to ensure transaction doesn't block subsequent operations
            $connection = $driver->getConnection();
            if ($connection && $connection->inTransaction()) {
                $connection->commit();
            }
        } catch (\Exception $e) {
            // Ignore cleanup errors
        }
    }

    /**
     * Helper to insert a test record
     *
     * @param Driver $driver Database driver
     * @param array $data Record data
     * @return int Insert ID
     */
    protected function insertTestRecord(Driver $driver, array $data = []): int
    {
        $defaults = [
            'title' => 'Test Item',
            'is_active' => 1,
            'view_count' => 100,
            'price' => 19.99,
            'settings' => '{"theme":"dark","notifications":true}',
            'metadata' => '{"author":"John","version":1}',
            'tags' => '["php","database","testing"]',
            'published_at' => '2024-06-15 14:30:00',
            'created_date' => '2024-06-15',
            'expires_at' => '2025-12-31 23:59:59',
        ];

        $data = array_merge($defaults, $data);

        $query = new Query($driver);
        return $query->push(self::$testTable, $data);
    }

    // =========================================================================
    // Integer Casting Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testIntegerCastingOnGet(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['view_count' => 42]);

        $item = CastTestItem::one($id);

        $this->assertSame(42, $item->get('view_count'));
        $this->assertIsInt($item->get('view_count'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testIntegerCastingOnSet(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $item = new CastTestItem();
        $item->set('title', 'Test');
        $item->set('view_count', '123');

        // The raw value should be cast for storage
        $this->assertSame(123, $item->getRaw('view_count'));
    }

    // =========================================================================
    // Float Casting Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFloatCastingOnGet(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['price' => 29.99]);

        $item = CastTestItem::one($id);

        $this->assertSame(29.99, $item->get('price'));
        $this->assertIsFloat($item->get('price'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFloatCastingOnSet(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $item = new CastTestItem();
        $item->set('title', 'Test');
        $item->set('price', '45.50');

        $this->assertSame(45.50, $item->getRaw('price'));
    }

    // =========================================================================
    // Decimal Casting Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDecimalCastingWithPrecision2(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['amount' => '19.99']);

        $item = CastTestItem::one($id);

        $amount = $item->get('amount');
        $this->assertIsString($amount);
        $this->assertEquals('19.99', $amount);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDecimalCastingRoundsToPrecision(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['amount' => '19.999']);

        $item = CastTestItem::one($id);

        // Should round to 2 decimal places
        $amount = $item->get('amount');
        $this->assertEquals('20.00', $amount);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDecimalCastingPadsZeros(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['amount' => '19']);

        $item = CastTestItem::one($id);

        // Should add decimal places
        $this->assertEquals('19.00', $item->get('amount'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDecimalCastingWithPrecision4(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['tax_rate' => '0.0825']);

        $item = CastTestItem::one($id);

        $taxRate = $item->get('tax_rate');
        $this->assertEquals('0.0825', $taxRate);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDecimalCastingOnSet(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $item = new CastTestItem();
        $item->set('title', 'Test');
        $item->set('amount', 123.456);

        // Should be stored with 2 decimal places
        $raw = $item->getRaw('amount');
        $this->assertEquals('123.46', $raw);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDecimalAvoidsFloatingPointIssues(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        // Classic floating point issue: 0.1 + 0.2 != 0.3
        $item = new CastTestItem();
        $item->set('title', 'Test');
        $item->set('amount', 0.1 + 0.2);

        // With decimal casting, we get exact string representation
        $this->assertEquals('0.30', $item->get('amount'));
    }

    // =========================================================================
    // Boolean Casting Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testBooleanCastingFromIntegerOne(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['is_active' => 1]);

        $item = CastTestItem::one($id);

        $this->assertTrue($item->get('is_active'));
        $this->assertIsBool($item->get('is_active'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testBooleanCastingFromIntegerZero(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['is_active' => 0]);

        $item = CastTestItem::one($id);

        $this->assertFalse($item->get('is_active'));
        $this->assertIsBool($item->get('is_active'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testBooleanCastingOnSetConvertsToInteger(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $item = new CastTestItem();
        $item->set('title', 'Test');
        $item->set('is_active', true);

        $this->assertSame(1, $item->getRaw('is_active'));

        $item->set('is_active', false);
        $this->assertSame(0, $item->getRaw('is_active'));
    }

    // =========================================================================
    // Array (JSON) Casting Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testArrayCastingFromJson(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['settings' => '{"theme":"light","volume":80}']);

        $item = CastTestItem::one($id);

        $settings = $item->get('settings');
        $this->assertIsArray($settings);
        $this->assertEquals('light', $settings['theme']);
        $this->assertEquals(80, $settings['volume']);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testArrayCastingOnSetEncodesToJson(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $item = new CastTestItem();
        $item->set('title', 'Test');
        $item->set('settings', ['theme' => 'dark', 'volume' => 50]);

        $raw = $item->getRaw('settings');
        $this->assertIsString($raw);
        $this->assertJson($raw);

        $decoded = json_decode($raw, true);
        $this->assertEquals('dark', $decoded['theme']);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testArrayCastingWithEmptyJson(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['settings' => '[]']);

        $item = CastTestItem::one($id);

        $this->assertIsArray($item->get('settings'));
        $this->assertEmpty($item->get('settings'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testArrayCastingWithInvalidJson(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['settings' => 'not valid json']);

        $item = CastTestItem::one($id);

        $this->assertIsArray($item->get('settings'));
        $this->assertEmpty($item->get('settings'));
    }

    // =========================================================================
    // Object (JSON) Casting Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testObjectCastingFromJson(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['metadata' => '{"author":"Jane","version":2}']);

        $item = CastTestItem::one($id);

        $metadata = $item->get('metadata');
        $this->assertIsObject($metadata);
        $this->assertEquals('Jane', $metadata->author);
        $this->assertEquals(2, $metadata->version);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testObjectCastingOnSetEncodesToJson(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $item = new CastTestItem();
        $item->set('title', 'Test');

        $obj = new \stdClass();
        $obj->author = 'Bob';
        $obj->version = 3;
        $item->set('metadata', $obj);

        $raw = $item->getRaw('metadata');
        $this->assertIsString($raw);
        $this->assertJson($raw);
    }

    // =========================================================================
    // Collection Casting Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCollectionCastingFromJson(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['tags' => '["laravel","php","testing"]']);

        $item = CastTestItem::one($id);

        $tags = $item->get('tags');
        $this->assertInstanceOf(\ArrayObject::class, $tags);
        $this->assertCount(3, $tags);
        $this->assertEquals('laravel', $tags[0]);
    }

    // =========================================================================
    // DateTime Casting Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDatetimeCasting(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['published_at' => '2024-03-15 10:30:00']);

        $item = CastTestItem::one($id);

        $publishedAt = $item->get('published_at');
        $this->assertInstanceOf(\DateTime::class, $publishedAt);
        $this->assertEquals('2024', $publishedAt->format('Y'));
        $this->assertEquals('03', $publishedAt->format('m'));
        $this->assertEquals('15', $publishedAt->format('d'));
        $this->assertEquals('10', $publishedAt->format('H'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDatetimeCastingOnSet(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $item = new CastTestItem();
        $item->set('title', 'Test');
        $item->set('published_at', new \DateTime('2024-07-20 15:45:00'));

        $raw = $item->getRaw('published_at');
        $this->assertIsString($raw);
        $this->assertEquals('2024-07-20 15:45:00', $raw);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDatetimeCastingWithNull(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['published_at' => null]);

        $item = CastTestItem::one($id);

        $this->assertNull($item->get('published_at'));
    }

    // =========================================================================
    // Date Casting Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDateCastingSetsTimeToMidnight(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['created_date' => '2024-05-20']);

        $item = CastTestItem::one($id);

        $createdDate = $item->get('created_date');
        $this->assertInstanceOf(\DateTime::class, $createdDate);
        $this->assertEquals('00', $createdDate->format('H'));
        $this->assertEquals('00', $createdDate->format('i'));
        $this->assertEquals('00', $createdDate->format('s'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDateCastingOnSet(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $item = new CastTestItem();
        $item->set('title', 'Test');
        $item->set('created_date', new \DateTime('2024-08-25 14:30:00'));

        $raw = $item->getRaw('created_date');
        $this->assertEquals('2024-08-25', $raw);
    }

    // =========================================================================
    // Timestamp Casting Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTimestampCasting(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['expires_at' => '2024-01-01 00:00:00']);

        $item = CastTestItem::one($id);

        $expiresAt = $item->get('expires_at');
        $this->assertIsInt($expiresAt);

        // Verify it's a valid timestamp
        $dt = (new \DateTime())->setTimestamp($expiresAt);
        $this->assertEquals('2024', $dt->format('Y'));
    }

    // =========================================================================
    // Null Value Handling
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testNullValuesStayNull(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, [
            'is_active' => null,
            'view_count' => null,
            'price' => null,
            'settings' => null,
            'metadata' => null,
            'published_at' => null,
        ]);

        $item = CastTestItem::one($id);

        $this->assertNull($item->get('is_active'));
        $this->assertNull($item->get('view_count'));
        $this->assertNull($item->get('price'));
        $this->assertNull($item->get('settings'));
        $this->assertNull($item->get('metadata'));
        $this->assertNull($item->get('published_at'));
    }

    // =========================================================================
    // Cast Cache Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCastCachePreventsRepeatedCasting(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['settings' => '{"key":"value"}']);

        $item = CastTestItem::one($id);

        // First get - casts and caches
        $first = $item->get('settings');
        // Second get - returns from cache
        $second = $item->get('settings');

        // Should be the exact same array instance
        $this->assertSame($first, $second);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSettingValueClearsCastCache(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['settings' => '{"key":"original"}']);

        $item = CastTestItem::one($id);

        // Get to populate cache
        $original = $item->get('settings');
        $this->assertEquals('original', $original['key']);

        // Set new value (should clear cache)
        $item->set('settings', ['key' => 'updated']);

        // Get should return new value
        $updated = $item->get('settings');
        $this->assertEquals('updated', $updated['key']);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testClearCastCacheMethod(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['settings' => '{"key":"value"}']);

        $item = CastTestItem::one($id);

        // Populate cache
        $item->get('settings');

        // Clear specific cache
        $item->clearCastCache('settings');

        // This is mainly testing the method doesn't error
        $this->assertIsArray($item->get('settings'));
    }

    // =========================================================================
    // Helper Method Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testHasCast(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $item = new CastTestItem();

        $this->assertTrue($item->hasCast('is_active'));
        $this->assertTrue($item->hasCast('view_count'));
        $this->assertTrue($item->hasCast('settings'));
        $this->assertFalse($item->hasCast('title')); // Not in casts
        $this->assertFalse($item->hasCast('nonexistent'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testHasCastWithTypeFilter(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $item = new CastTestItem();

        $this->assertTrue($item->hasCast('is_active', 'boolean'));
        $this->assertTrue($item->hasCast('view_count', ['integer', 'int']));
        $this->assertFalse($item->hasCast('is_active', 'string'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetCasts(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $item = new CastTestItem();
        $casts = $item->getCasts();

        $this->assertArrayHasKey('is_active', $casts);
        $this->assertArrayHasKey('view_count', $casts);
        $this->assertArrayHasKey('settings', $casts);
        $this->assertEquals('boolean', $casts['is_active']);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetRawReturnsUncastedValue(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['is_active' => 1]);

        $item = CastTestItem::one($id);

        // get() returns boolean
        $this->assertIsBool($item->get('is_active'));

        // getRaw() returns raw database value (may be int or string depending on driver)
        $raw = $item->getRaw('is_active');
        $this->assertEquals(1, $raw);
    }

    // =========================================================================
    // Model Without Casts Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testModelWithoutCastsReturnsRawValues(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver, ['is_active' => 1, 'settings' => '{"key":"value"}']);

        $item = NoCastItem::one($id);

        // Should return raw database values (string/int depending on driver)
        $this->assertNotInstanceOf(\DateTime::class, $item->get('published_at'));
        $this->assertIsString($item->get('settings'));
    }

    // =========================================================================
    // Edge Cases
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCastingWithDefaultValue(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $item = new CastTestItem();

        // Non-existent key should return default
        $this->assertEquals('default', $item->get('nonexistent', 'default'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testMultipleGetsOfDifferentCastTypes(string $dbName, Driver $driver): void
    {
        $this->cleanupData($dbName, $driver);
        Relational::setDefaultConnection($driver);

        $id = $this->insertTestRecord($driver);

        $item = CastTestItem::one($id);

        $this->assertIsBool($item->get('is_active'));
        $this->assertIsInt($item->get('view_count'));
        $this->assertIsFloat($item->get('price'));
        $this->assertIsArray($item->get('settings'));
        $this->assertIsObject($item->get('metadata'));
        $this->assertInstanceOf(\DateTime::class, $item->get('published_at'));
    }
}
