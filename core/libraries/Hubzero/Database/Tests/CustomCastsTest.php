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
use Hubzero\Database\Casts\CastsAttributes;
use Hubzero\Database\Casts\AsJson;
use Hubzero\Database\Casts\AsCollection;
use Hubzero\Database\Casts\AsDateTime;
use Hubzero\Database\Casts\AsEncryptedString;
use Hubzero\Database\Casts\StringEncrypter;
use Hubzero\Database\Relational;
use ArrayObject;
use Hubzero\Database\Tests\TestModels\CastTestModelJson;
use Hubzero\Database\Tests\TestModels\CastTestModelDateTime;
use Hubzero\Database\Tests\TestModels\CastTestModelCollection;
use Hubzero\Database\Tests\TestModels\CastTestModelMixed;
use DateTime;

/**
 * Tests for custom cast classes
 *
 * Functional tests that verify cast behavior with real database operations
 * across all configured backends.
 */
class CustomCastsTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['cast_test'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('cast_test', true);
        $driver->createTable('cast_test')
            ->id()
            ->text('options')->nullable()
            ->string('expires', 50)->nullable()
            ->text('tags')->nullable()
            ->string('secret', 255)->nullable()
            ->execute();
    }

    /**
     * Reset model column cache and set connection
     */
    private function prepareModels(Driver $driver): void
    {
        CastTestModelJson::setDefaultConnection($driver);
        CastTestModelDateTime::setDefaultConnection($driver);
        CastTestModelCollection::setDefaultConnection($driver);
        CastTestModelMixed::setDefaultConnection($driver);
        $driver->flushCachedTableColumns('cast_test');
    }

    // =========================================================================
    // CastsAttributes Interface
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function castClassesImplementInterface(string $dbName, Driver $driver)
    {
        $this->assertInstanceOf(CastsAttributes::class, new AsJson());
        $this->assertInstanceOf(CastsAttributes::class, new AsDateTime());
        $this->assertInstanceOf(CastsAttributes::class, new AsCollection());
        $this->assertInstanceOf(CastsAttributes::class, new AsEncryptedString());
    }

    // =========================================================================
    // Custom Cast Class Detection
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function isCustomCastClassDetection(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);
        $model = CastTestModelMixed::blank();

        $this->assertTrue($model->checkIsCustomCastClass(AsJson::class), "[$dbName]");
        $this->assertFalse($model->checkIsCustomCastClass('string'), "[$dbName]");
        $this->assertFalse($model->checkIsCustomCastClass('integer'), "[$dbName]");
        $this->assertFalse($model->checkIsCustomCastClass('array'), "[$dbName]");
    }

    // =========================================================================
    // AsJson Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function jsonCastStoresAndRetrievesArray(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $testData = ['name' => 'Test', 'count' => 42, 'active' => true];
        $instance = CastTestModelJson::blank();
        $instance->set('options', $testData);
        $instance->save();

        $retrieved = CastTestModelJson::one($instance->get('id'));
        $this->assertIsArray($retrieved->get('options'), "[$dbName]");
        $this->assertEquals('Test', $retrieved->get('options')['name'], "[$dbName]");
        $this->assertEquals(42, $retrieved->get('options')['count'], "[$dbName]");
        $this->assertTrue($retrieved->get('options')['active'], "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function jsonCastHandlesNull(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $instance = CastTestModelJson::blank();
        $instance->set('options', null);
        $instance->save();

        $retrieved = CastTestModelJson::one($instance->get('id'));
        $this->assertNull($retrieved->get('options'), "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function jsonCastPreservesUnicode(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $testData = ['name' => 'Caf\u{e9}', 'city' => 'S\u{e3}o Paulo'];
        $instance = CastTestModelJson::blank();
        $instance->set('options', $testData);
        $instance->save();

        $retrieved = CastTestModelJson::one($instance->get('id'));
        $this->assertEquals('Caf\u{e9}', $retrieved->get('options')['name'], "[$dbName]");
        $this->assertEquals('S\u{e3}o Paulo', $retrieved->get('options')['city'], "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function jsonCastGetReturnsNullForEmptyString(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $cast = new AsJson();
        $model = CastTestModelJson::blank();
        $result = $cast->get($model, 'options', '', []);

        $this->assertNull($result, "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function jsonCastSetEncodesArray(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $cast = new AsJson();
        $model = CastTestModelJson::blank();
        $result = $cast->set($model, 'options', ['key' => 'value', 'count' => 42], []);

        $this->assertIsString($result, "[$dbName]");
        $decoded = json_decode($result, true);
        $this->assertEquals('value', $decoded['key'], "[$dbName]");
        $this->assertEquals(42, $decoded['count'], "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function jsonCastSetReturnsNullForNull(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $cast = new AsJson();
        $model = CastTestModelJson::blank();
        $result = $cast->set($model, 'options', null, []);

        $this->assertNull($result, "[$dbName]");
    }

    // =========================================================================
    // AsDateTime Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function dateTimeCastStoresAndRetrievesDate(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $testDate = new DateTime('2025-06-15 14:30:00');
        $instance = CastTestModelDateTime::blank();
        $instance->set('expires', $testDate);
        $instance->save();

        $retrieved = CastTestModelDateTime::one($instance->get('id'));
        $this->assertInstanceOf(DateTime::class, $retrieved->get('expires'), "[$dbName]");
        $this->assertEquals('2025-06-15', $retrieved->get('expires')->format('Y-m-d'), "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function dateTimeCastHandlesNull(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $instance = CastTestModelDateTime::blank();
        $instance->set('expires', null);
        $instance->save();

        $retrieved = CastTestModelDateTime::one($instance->get('id'));
        $this->assertNull($retrieved->get('expires'), "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function dateTimeCastStoresTimestamp(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $timestamp = 1750000000;
        $instance = CastTestModelDateTime::blank();
        $instance->set('expires', $timestamp);
        $instance->save();

        $retrieved = CastTestModelDateTime::one($instance->get('id'));
        $this->assertInstanceOf(DateTime::class, $retrieved->get('expires'), "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function dateTimeCastStoresStringDate(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $instance = CastTestModelDateTime::blank();
        $instance->set('expires', '2025-12-25 10:00:00');
        $instance->save();

        $retrieved = CastTestModelDateTime::one($instance->get('id'));
        $this->assertInstanceOf(DateTime::class, $retrieved->get('expires'), "[$dbName]");
        $this->assertEquals('2025-12-25', $retrieved->get('expires')->format('Y-m-d'), "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function dateTimeCastGetReturnsNullForEmptyString(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $cast = new AsDateTime();
        $model = CastTestModelDateTime::blank();
        $result = $cast->get($model, 'expires', '', []);

        $this->assertNull($result, "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function dateTimeCastGetReturnsNullForZeroDate(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $cast = new AsDateTime();
        $model = CastTestModelDateTime::blank();
        $result = $cast->get($model, 'expires', '0000-00-00 00:00:00', []);

        $this->assertNull($result, "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function dateTimeCastSetWithDateTimeObject(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $cast = new AsDateTime();
        $model = CastTestModelDateTime::blank();
        $date = new DateTime('2025-06-15 14:30:00');
        $result = $cast->set($model, 'expires', $date, []);

        $this->assertEquals('2025-06-15 14:30:00', $result, "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function dateTimeCastSetReturnsNullForNull(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $cast = new AsDateTime();
        $model = CastTestModelDateTime::blank();
        $result = $cast->set($model, 'expires', null, []);

        $this->assertNull($result, "[$dbName]");
    }

    // =========================================================================
    // AsCollection Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function collectionCastStoresAndRetrievesList(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $testTags = ['php', 'database', 'testing'];
        $instance = CastTestModelCollection::blank();
        $instance->set('tags', $testTags);
        $instance->save();

        $retrieved = CastTestModelCollection::one($instance->get('id'));
        $this->assertInstanceOf(ArrayObject::class, $retrieved->get('tags'), "[$dbName]");
        $this->assertEquals(3, $retrieved->get('tags')->count(), "[$dbName]");
        $this->assertContains('php', iterator_to_array($retrieved->get('tags')), "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function collectionCastHandlesNull(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $instance = CastTestModelCollection::blank();
        $instance->set('tags', null);
        $instance->save();

        // ORM returns null for null columns without passing through cast
        $retrieved = CastTestModelCollection::one($instance->get('id'));
        $this->assertNull($retrieved->get('tags'), "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function collectionCastStoresArrayObject(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $list = new ArrayObject(['a', 'b', 'c']);
        $instance = CastTestModelCollection::blank();
        $instance->set('tags', $list);
        $instance->save();

        $retrieved = CastTestModelCollection::one($instance->get('id'));
        $this->assertInstanceOf(ArrayObject::class, $retrieved->get('tags'), "[$dbName]");
        $this->assertEquals(3, $retrieved->get('tags')->count(), "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function collectionCastGetReturnsEmptyForEmptyString(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $cast = new AsCollection();
        $model = CastTestModelCollection::blank();
        $result = $cast->get($model, 'tags', '', []);

        $this->assertInstanceOf(ArrayObject::class, $result, "[$dbName]");
        $this->assertEquals(0, $result->count(), "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function collectionCastSetEncodesArray(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $cast = new AsCollection();
        $model = CastTestModelCollection::blank();
        $result = $cast->set($model, 'tags', ['one', 'two', 'three'], []);

        $this->assertIsString($result, "[$dbName]");
        $this->assertEquals('["one","two","three"]', $result, "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function collectionCastSetReturnsNullForNull(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $cast = new AsCollection();
        $model = CastTestModelCollection::blank();
        $result = $cast->set($model, 'tags', null, []);

        $this->assertNull($result, "[$dbName]");
    }

    // =========================================================================
    // AsEncryptedString Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function encryptedStringGetReturnsRawWithoutEncrypter(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $cast = new AsEncryptedString();
        $model = CastTestModelMixed::blank();
        $result = $cast->get($model, 'secret', 'some_value', []);

        $this->assertEquals('some_value', $result, "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function encryptedStringSetReturnsRawWithoutEncrypter(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $cast = new AsEncryptedString();
        $model = CastTestModelMixed::blank();
        $result = $cast->set($model, 'secret', 'some_value', []);

        $this->assertEquals('some_value', $result, "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function encryptedStringGetReturnsNullForNull(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $cast = new AsEncryptedString();
        $model = CastTestModelMixed::blank();
        $result = $cast->get($model, 'secret', null, []);

        $this->assertNull($result, "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function encryptedStringGetReturnsNullForEmptyString(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $cast = new AsEncryptedString();
        $model = CastTestModelMixed::blank();
        $result = $cast->get($model, 'secret', '', []);

        $this->assertNull($result, "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function encryptedStringSetReturnsNullForNull(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $cast = new AsEncryptedString();
        $model = CastTestModelMixed::blank();
        $result = $cast->set($model, 'secret', null, []);

        $this->assertNull($result, "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function encryptedStringEncryptsWithInjectedEncrypter(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $encrypter = new class implements StringEncrypter {
            public function encrypt(string $value): string
            {
                return base64_encode($value);
            }
            public function decrypt(string $value): string
            {
                return base64_decode($value);
            }
        };

        AsEncryptedString::setEncrypter($encrypter);

        $cast = new AsEncryptedString();
        $model = CastTestModelMixed::blank();

        $encrypted = $cast->set($model, 'secret', 'my_secret', []);
        $this->assertEquals(base64_encode('my_secret'), $encrypted, "[$dbName]");

        $decrypted = $cast->get($model, 'secret', $encrypted, []);
        $this->assertEquals('my_secret', $decrypted, "[$dbName]");

        AsEncryptedString::flush();
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function encryptedStringFlushClearsEncrypter(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $encrypter = new class implements StringEncrypter {
            public function encrypt(string $value): string
            {
                return 'enc:' . $value;
            }
            public function decrypt(string $value): string
            {
                return substr($value, 4);
            }
        };

        AsEncryptedString::setEncrypter($encrypter);
        $this->assertNotNull(AsEncryptedString::getEncrypter(), "[$dbName]");

        AsEncryptedString::flush();
        $this->assertNull(AsEncryptedString::getEncrypter(), "[$dbName]");

        // Without encrypter, values pass through
        $cast = new AsEncryptedString();
        $model = CastTestModelMixed::blank();
        $this->assertEquals('plain', $cast->set($model, 'secret', 'plain', []), "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function encryptedStringGetReturnsRawOnDecryptFailure(string $dbName, Driver $driver)
    {
        $this->prepareModels($driver);

        $encrypter = new class implements StringEncrypter {
            public function encrypt(string $value): string
            {
                return $value;
            }
            public function decrypt(string $value): string
            {
                throw new \RuntimeException('Decryption failed');
            }
        };

        AsEncryptedString::setEncrypter($encrypter);

        $cast = new AsEncryptedString();
        $model = CastTestModelMixed::blank();
        $result = $cast->get($model, 'secret', 'corrupted_data', []);

        $this->assertEquals('corrupted_data', $result, "[$dbName] Should return raw value on decrypt failure");

        AsEncryptedString::flush();
    }
}
