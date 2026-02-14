<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Hubzero\Database\Driver;

/**
 * Cross-driver text field behavior tests.
 *
 * This suite is intentionally focused on practical application behavior:
 * - 32K text payload round-trip
 * - mixed payload sizes across text variants
 * - empty-string semantics by backend
 */
class TextFieldBehaviorTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['text_field_behavior'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('text_field_behavior', true);
    }

    /**
     * Build a deterministic payload of the requested size.
     */
    private function payload(int $length): string
    {
        if ($length <= 0) {
            return '';
        }

        $seed = 'HubzeroTextPayload0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_=+';
        $repeat = (int) ceil($length / strlen($seed));

        return substr(str_repeat($seed, $repeat), 0, $length);
    }

    /**
     * Create baseline table for text behavior tests.
     */
    private function createTextTable(Driver $driver): void
    {
        $driver->dropTable('text_field_behavior', true);
        $driver->createTable('text_field_behavior')
            ->id()
            ->text('body_text')
            ->mediumText('body_medium')
            ->longText('body_long')
            ->execute();
    }

    /**
     * Max payload expected to round-trip for this backend's text mapping.
     */
    private function maxPayloadForDriver(Driver $driver): int
    {
        return 32768;
    }

    /**
     * Retrieve a row property with case-insensitive fallback.
     */
    private function rowValue($row, string $field)
    {
        if (is_object($row)) {
            if (property_exists($row, $field)) {
                return $row->$field;
            }

            foreach (get_object_vars($row) as $key => $value) {
                if (strcasecmp((string) $key, $field) === 0) {
                    return $value;
                }
            }
        }

        return null;
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function textColumnsRoundTrip32kPayload(string $dbName, Driver $driver): void
    {
        $this->createTextTable($driver);

        $max = $this->maxPayloadForDriver($driver);
        $payload = $this->payload($max);

        $driver->getQuery()
            ->insert('text_field_behavior')
            ->values([
                'body_text' => $payload,
                'body_medium' => $payload,
                'body_long' => $payload,
            ])
            ->execute();

        $row = $driver->getQuery()
            ->select('*')
            ->from('text_field_behavior')
            ->whereEquals('id', 1)
            ->fetch('row');

        $text = $this->rowValue($row, 'body_text');
        $medium = $this->rowValue($row, 'body_medium');
        $long = $this->rowValue($row, 'body_long');

        $this->assertSame($payload, $text, "{$dbName}: body_text mismatch at max payload {$max}");
        $this->assertSame($payload, $medium, "{$dbName}: body_medium mismatch at max payload {$max}");
        $this->assertSame($payload, $long, "{$dbName}: body_long mismatch at max payload {$max}");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function textColumnsRoundTripMixedPayloadSizes(string $dbName, Driver $driver): void
    {
        $this->createTextTable($driver);

        $sizes = [1, 255, 8192, 16384, 32768];
        $id = 1;

        foreach ($sizes as $size) {
            $payload = $this->payload($size);
            $driver->getQuery()
                ->insert('text_field_behavior')
                ->values([
                    'id' => $id,
                    'body_text' => $payload,
                    'body_medium' => $payload,
                    'body_long' => $payload,
                ])
                ->execute();

            $row = $driver->getQuery()
                ->select('*')
                ->from('text_field_behavior')
                ->whereEquals('id', $id)
                ->fetch('row');

            $this->assertSame(
                $payload,
                $this->rowValue($row, 'body_text'),
                "{$dbName}: body_text mismatch for size {$size}"
            );
            $this->assertSame(
                $payload,
                $this->rowValue($row, 'body_medium'),
                "{$dbName}: body_medium mismatch for size {$size}"
            );
            $this->assertSame(
                $payload,
                $this->rowValue($row, 'body_long'),
                "{$dbName}: body_long mismatch for size {$size}"
            );

            $id++;
        }
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function textColumnsEmptyStringSemantics(string $dbName, Driver $driver): void
    {
        $this->createTextTable($driver);

        $driver->getQuery()
            ->insert('text_field_behavior')
            ->values([
                'body_text' => '',
                'body_medium' => '',
                'body_long' => '',
            ])
            ->execute();

        $row = $driver->getQuery()
            ->select('*')
            ->from('text_field_behavior')
            ->whereEquals('id', 1)
            ->fetch('row');

        $text = $this->rowValue($row, 'body_text');
        $medium = $this->rowValue($row, 'body_medium');
        $long = $this->rowValue($row, 'body_long');

        $this->assertContains(
            $text,
            ['', null],
            true,
            "{$dbName}: body_text should preserve empty string or normalize it to NULL"
        );
        $this->assertContains(
            $medium,
            ['', null],
            true,
            "{$dbName}: body_medium should preserve empty string or normalize it to NULL"
        );
        $this->assertContains(
            $long,
            ['', null],
            true,
            "{$dbName}: body_long should preserve empty string or normalize it to NULL"
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function textColumnsSqlNullSemanticsDistinguishNullAndEmpty(string $dbName, Driver $driver): void
    {
        $this->createTextTable($driver);

        $driver->getQuery()
            ->insert('text_field_behavior')
            ->values([
                'id' => 1,
                'body_text' => null,
            ])
            ->execute();

        $driver->getQuery()
            ->insert('text_field_behavior')
            ->values([
                'id' => 2,
                'body_text' => '',
            ])
            ->execute();

        $nullCount = (int) $driver->getQuery()
            ->select('COUNT(*)', 'cnt')
            ->from('text_field_behavior')
            ->whereIsNull('body_text')
            ->fetch('row')->cnt;

        $notNullCount = (int) $driver->getQuery()
            ->select('COUNT(*)', 'cnt')
            ->from('text_field_behavior')
            ->whereIsNotNull('body_text')
            ->fetch('row')->cnt;

        $this->assertSame(2, $nullCount + $notNullCount, "{$dbName}: expected exactly two inserted rows");

        $supportsNullEmptyDistinction = ($nullCount === 1 && $notNullCount === 1);
        $normalizesEmptyToNull = ($nullCount === 2 && $notNullCount === 0);

        $this->assertTrue(
            $supportsNullEmptyDistinction || $normalizesEmptyToNull,
            "{$dbName}: expected either distinct NULL/empty"
            . " semantics (1/1) or empty-string normalization"
            . " to NULL (2/0); got {$nullCount}/{$notNullCount}"
        );
    }
}
