<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Schema\ColumnDefinitionTrait;
use Hubzero\Database\Query;
use Hubzero\Database\Driver;

/**
 * Functional tests for schema builder column types and modifiers
 *
 * Verifies that columns created via the schema builder accept, store,
 * and return data correctly across all database backends.
 */
class ColumnBuilderTest extends AbstractDriverTestCase
{
    /**
     * Table names used by this test class
     */
    protected static function getTestTables(): array
    {
        return [
            'coltest_integers',
            'coltest_strings',
            'coltest_booleans',
            'coltest_decimals',
            'coltest_dates',
            'coltest_special',
            'coltest_notnull',
            'coltest_defaults',
            'coltest_nullable',
            'coltest_unique',
            'coltest_combined',
        ];
    }

    /**
     * Tables are created per-test
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        // No shared setup - tables created and dropped per-test
    }

    /**
     * Test class that uses the trait for isolated unit testing
     */
    protected function createTraitTester()
    {
        return new class {
            use ColumnDefinitionTrait {
                hasAbstractType as public;
                compileAbstractTypeToSql as public;
            }
        };
    }

    // =========================================================================
    // Type Tests - Verify columns accept and return correct data
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function integerTypesStoreAndRetrieveValues(string $dbName, Driver $driver)
    {
        $driver->dropTable('coltest_integers', true);

        $driver->schema()->createTable('coltest_integers')
            ->id()
            ->integer('regular_int')
            ->bigInteger('big_int')
            ->tinyInteger('tiny_int')
            ->execute();

        $query = new Query($driver);
        $query->insertMany('coltest_integers', [
            ['regular_int' => 42, 'big_int' => 2147483648, 'tiny_int' => 7],
        ]);

        $query2 = new Query($driver);
        $row = $query2->from('coltest_integers')->select('*')->fetch('row');

        $this->assertEquals(42, (int) $row->regular_int);
        $this->assertEquals(2147483648, (int) $row->big_int);
        $this->assertEquals(7, (int) $row->tiny_int);

        $driver->dropTable('coltest_integers', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function autoIncrementGeneratesSequentialIds(string $dbName, Driver $driver)
    {
        $driver->dropTable('coltest_integers', true);

        $driver->schema()->createTable('coltest_integers')
            ->id()
            ->string('name', 50)
            ->execute();

        $query = new Query($driver);
        $query->insertMany('coltest_integers', [
            ['name' => 'First'],
            ['name' => 'Second'],
            ['name' => 'Third'],
        ]);

        $query2 = new Query($driver);
        $rows = $query2->from('coltest_integers')
                       ->select('*')
                       ->order('id', 'asc')
                       ->fetch('rows');

        $this->assertCount(3, $rows);
        $this->assertGreaterThan(0, (int) $rows[0]->id);
        $this->assertGreaterThan((int) $rows[0]->id, (int) $rows[1]->id);
        $this->assertGreaterThan((int) $rows[1]->id, (int) $rows[2]->id);

        $driver->dropTable('coltest_integers', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function stringTypesStoreAndRetrieveValues(string $dbName, Driver $driver)
    {
        $driver->dropTable('coltest_strings', true);

        $driver->schema()->createTable('coltest_strings')
            ->id()
            ->string('short_str', 100)
            ->string('default_str')
            ->text('text_col')
            ->longText('long_text_col')
            ->execute();

        $shortVal = 'Hello World';
        $defaultVal = str_repeat('x', 200);
        $textVal = str_repeat('Lorem ipsum.', 100);
        $longTextVal = str_repeat('Long text.', 500);

        $query = new Query($driver);
        $query->insertMany('coltest_strings', [
            [
                'short_str' => $shortVal,
                'default_str' => $defaultVal,
                'text_col' => $textVal,
                'long_text_col' => $longTextVal,
            ],
        ]);

        $query2 = new Query($driver);
        $row = $query2->from('coltest_strings')->select('*')->fetch('row');

        $this->assertEquals($shortVal, $row->short_str);
        $this->assertEquals($defaultVal, $row->default_str);
        $this->assertEquals($textVal, $row->text_col);
        $this->assertEquals($longTextVal, $row->long_text_col);

        $driver->dropTable('coltest_strings', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function booleanTypeStoresAndRetrievesValues(string $dbName, Driver $driver)
    {
        $driver->dropTable('coltest_booleans', true);

        $driver->schema()->createTable('coltest_booleans')
            ->id()
            ->boolean('is_active')
            ->boolean('is_admin')
            ->execute();

        $query = new Query($driver);
        $query->insertMany('coltest_booleans', [
            ['is_active' => 1, 'is_admin' => 0],
        ]);

        $query2 = new Query($driver);
        $row = $query2->from('coltest_booleans')->select('*')->fetch('row');

        $this->assertEquals(1, (int) $row->is_active);
        $this->assertEquals(0, (int) $row->is_admin);

        $driver->dropTable('coltest_booleans', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function decimalTypePreservesPrecision(string $dbName, Driver $driver)
    {
        $driver->dropTable('coltest_decimals', true);

        $driver->schema()->createTable('coltest_decimals')
            ->id()
            ->decimal('price', 8, 4)
            ->decimal('default_decimal')
            ->execute();

        $query = new Query($driver);
        $query->insertMany('coltest_decimals', [
            ['price' => 1234.5678, 'default_decimal' => 99.99],
        ]);

        $query2 = new Query($driver);
        $row = $query2->from('coltest_decimals')->select('*')->fetch('row');

        $this->assertEqualsWithDelta(1234.5678, (float) $row->price, 0.0001);
        $this->assertEqualsWithDelta(99.99, (float) $row->default_decimal, 0.01);

        $driver->dropTable('coltest_decimals', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function dateTimeTypesStoreAndRetrieveValues(string $dbName, Driver $driver)
    {
        $driver->dropTable('coltest_dates', true);

        $driver->schema()->createTable('coltest_dates')
            ->id()
            ->date('birth_date')
            ->datetime('created_at')
            ->execute();

        $query = new Query($driver);
        $query->insertMany('coltest_dates', [
            ['birth_date' => '2000-06-15', 'created_at' => '2024-01-15 14:30:00'],
        ]);

        $query2 = new Query($driver);
        $row = $query2->from('coltest_dates')->select('*')->fetch('row');

        $this->assertStringContainsString('2000-06-15', $row->birth_date);
        $this->assertStringContainsString('2024-01-15', $row->created_at);
        $this->assertStringContainsString('14:30:00', $row->created_at);

        $driver->dropTable('coltest_dates', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function timestampDefaultCurrentTimestamp(string $dbName, Driver $driver)
    {
        $driver->dropTable('coltest_dates', true);

        $driver->schema()->createTable('coltest_dates')
            ->id()
            ->string('label', 50)
            ->timestamp('created_at')->default('CURRENT_TIMESTAMP')
            ->execute();

        $query = new Query($driver);
        $query->insertMany('coltest_dates', [
            ['label' => 'Test'],
        ]);

        $query2 = new Query($driver);
        $row = $query2->from('coltest_dates')->select('*')->fetch('row');

        $this->assertNotNull($row->created_at);
        $this->assertNotEmpty($row->created_at);

        $driver->dropTable('coltest_dates', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function specialTypesStoreAndRetrieveValues(string $dbName, Driver $driver)
    {
        $driver->dropTable('coltest_special', true);

        $driver->schema()->createTable('coltest_special')
            ->id()
            ->uuid('unique_id')
            ->ipAddress('ip')
            ->json('metadata')
            ->execute();

        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $ip = '192.168.1.100';
        $json = '{"key":"value","number":42}';

        $query = new Query($driver);
        $query->push('coltest_special', [
            'unique_id' => $uuid,
            'ip' => $ip,
            'metadata' => $json,
        ]);

        $query2 = new Query($driver);
        $row = $query2->from('coltest_special')->select('*')->fetch('row');

        $this->assertEquals($uuid, $row->unique_id);
        $this->assertEquals($ip, $row->ip);
        // Compare decoded JSON some implementations normalizes whitespace
        $this->assertEquals(json_decode($json, true), json_decode($row->metadata, true));

        $driver->dropTable('coltest_special', true);
    }

    // =========================================================================
    // Modifier Tests - Verify modifiers affect database behavior
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function notNullColumnRejectsNull(string $dbName, Driver $driver)
    {
        $driver->dropTable('coltest_notnull', true);

        $driver->schema()->createTable('coltest_notnull')
            ->id()
            ->string('required_name', 100)->notNull()
            ->execute();

        $threw = false;
        try {
            $query = new Query($driver);
            $query->insertMany('coltest_notnull', [
                ['required_name' => null],
            ]);
        } catch (\Exception $e) {
            $threw = true;
        }

        // Clean up any failed transaction state
        try {
            $conn = $driver->getConnection();
            if ($conn && $conn->inTransaction()) {
                $conn->rollBack();
            }
        } catch (\Exception $ignore) {
        }

        $this->assertTrue($threw, 'Inserting NULL into NOT NULL column should throw an exception');

        $driver->dropTable('coltest_notnull', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function defaultValueAppliedWhenOmitted(string $dbName, Driver $driver)
    {
        $driver->dropTable('coltest_defaults', true);

        $driver->schema()->createTable('coltest_defaults')
            ->id()
            ->string('label', 100)
            ->integer('score')->default(100)
            ->string('status', 20)->default('pending')
            ->execute();

        // Insert only 'label', omitting score and status
        $query = new Query($driver);
        $query->insertMany('coltest_defaults', [
            ['label' => 'Test'],
        ]);

        $query2 = new Query($driver);
        $row = $query2->from('coltest_defaults')->select('*')->fetch('row');

        $this->assertEquals(100, (int) $row->score);
        $this->assertEquals('pending', $row->status);

        $driver->dropTable('coltest_defaults', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function nullableColumnAcceptsNull(string $dbName, Driver $driver)
    {
        $driver->dropTable('coltest_nullable', true);

        $driver->schema()->createTable('coltest_nullable')
            ->id()
            ->string('name', 100)
            ->string('bio', 500)->nullable()
            ->execute();

        $query = new Query($driver);
        $query->insertMany('coltest_nullable', [
            ['name' => 'Alice', 'bio' => null],
        ]);

        $query2 = new Query($driver);
        $row = $query2->from('coltest_nullable')->select('*')->fetch('row');

        $this->assertEquals('Alice', $row->name);
        $this->assertNull($row->bio);

        $driver->dropTable('coltest_nullable', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function uniqueColumnRejectsDuplicates(string $dbName, Driver $driver)
    {
        $driver->dropTable('coltest_unique', true);

        $driver->schema()->createTable('coltest_unique')
            ->id()
            ->string('email', 255)
            ->uniqueIndex('idx_coltest_email', 'email')
            ->execute();

        $query = new Query($driver);
        $query->insertMany('coltest_unique', [
            ['email' => 'test@example.com'],
        ]);

        $threw = false;
        try {
            $query2 = new Query($driver);
            $query2->insertMany('coltest_unique', [
                ['email' => 'test@example.com'],
            ]);
        } catch (\Exception $e) {
            $threw = true;
        }

        // Clean up any failed transaction state
        try {
            $conn = $driver->getConnection();
            if ($conn && $conn->inTransaction()) {
                $conn->rollBack();
            }
        } catch (\Exception $ignore) {
        }

        $this->assertTrue($threw, 'Inserting duplicate into UNIQUE column should throw an exception');

        $driver->dropTable('coltest_unique', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function booleanDefaultValuesWork(string $dbName, Driver $driver)
    {
        $driver->dropTable('coltest_booleans', true);

        $driver->schema()->createTable('coltest_booleans')
            ->id()
            ->string('name', 50)
            ->boolean('is_active')->default(true)
            ->boolean('is_archived')->default(false)
            ->execute();

        // Insert only name, omitting booleans to test defaults
        $query = new Query($driver);
        $query->insertMany('coltest_booleans', [
            ['name' => 'Test'],
        ]);

        $query2 = new Query($driver);
        $row = $query2->from('coltest_booleans')->select('*')->fetch('row');

        $this->assertEquals(1, (int) $row->is_active, 'Boolean default true should be 1');
        $this->assertEquals(0, (int) $row->is_archived, 'Boolean default false should be 0');

        $driver->dropTable('coltest_booleans', true);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function combinedModifiersWork(string $dbName, Driver $driver)
    {
        $driver->dropTable('coltest_combined', true);

        $driver->schema()->createTable('coltest_combined')
            ->id()
            ->string('label', 50)
            ->integer('score')->notNull()->default(0)
            ->string('status', 100)->nullable()->default('pending')
            ->execute();

        // Row 1: Explicit values - null goes into nullable column
        $query = new Query($driver);
        $query->insertMany('coltest_combined', [
            ['label' => 'Row1', 'score' => 50, 'status' => null],
        ]);

        // Row 2: Omit score and status to test defaults
        $query2 = new Query($driver);
        $query2->insertMany('coltest_combined', [
            ['label' => 'Row2'],
        ]);

        // Verify Row 1
        $q1 = new Query($driver);
        $row1 = $q1->from('coltest_combined')
                    ->select('*')
                    ->whereEquals('label', 'Row1')
                    ->fetch('row');
        $this->assertEquals(50, (int) $row1->score);
        $this->assertNull($row1->status);

        // Verify Row 2 (defaults applied)
        $q2 = new Query($driver);
        $row2 = $q2->from('coltest_combined')
                    ->select('*')
                    ->whereEquals('label', 'Row2')
                    ->fetch('row');
        $this->assertEquals(0, (int) $row2->score);
        $this->assertEquals('pending', $row2->status);

        $driver->dropTable('coltest_combined', true);
    }

    // =========================================================================
    // Unit Tests - ColumnDefinitionTrait API contract (no database needed)
    // =========================================================================

    #[Test]
    public function hasAbstractTypeReturnsFalseInitially()
    {
        $tester = $this->createTraitTester();

        $this->assertFalse($tester->hasAbstractType());
    }

    #[Test]
    public function hasAbstractTypeReturnsTrueAfterSettingType()
    {
        $tester = $this->createTraitTester();

        $tester->integer();

        $this->assertTrue($tester->hasAbstractType());
    }

    #[Test]
    public function typeMethodsReturnSelf()
    {
        $tester = $this->createTraitTester();

        $result = $tester->integer();
        $this->assertSame($tester, $result);

        $tester = $this->createTraitTester();
        $result = $tester->string(100);
        $this->assertSame($tester, $result);
    }

    #[Test]
    public function modifierMethodsReturnSelf()
    {
        $tester = $this->createTraitTester();

        $result = $tester->integer()->nullable();
        $this->assertSame($tester, $result);

        $result = $result->default(0);
        $this->assertSame($tester, $result);

        $result = $result->unsigned();
        $this->assertSame($tester, $result);
    }
}
