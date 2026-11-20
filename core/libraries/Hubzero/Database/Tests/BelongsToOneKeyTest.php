<?php

/**
 * @package    framework
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Drivers\Base\BaseSqlDriver;
use Hubzero\Database\Relational;
use Hubzero\Database\Query;
use Hubzero\Database\Tests\TestModels\KeyedNote;
use Hubzero\Database\Tests\TestModels\KeyedOwner;
use Hubzero\Database\Tests\TestModels\PlainNote;
use Hubzero\Database\Tests\TestModels\PlainOwner;

/**
 * Which key of the parent a belongsToOne() finds it by
 *
 * Without a parent key the relationship used the child's own primary key as
 * the parent's: right only when both are 'id'. A message (message_id) looked
 * its sender up by users.message_id, and a group member its group by
 * xgroups.id. The parent's primary key is the default now.
 */
class BelongsToOneKeyTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['plain_owners', 'key_owners', 'key_notes', 'plain_notes'];
    }

    protected static function setUpDatabase(BaseSqlDriver $driver): void
    {
        foreach (self::getTestTables() as $table) {
            $driver->dropTable($table, true);
        }
        $driver->createTable('plain_owners')->id()->string('name', 50)->execute();
        $driver->createTable('key_owners')->id('owner_key')->string('name', 50)->execute();
        $driver->createTable('key_notes')->id('note_id')->integer('owner_id')->execute();
        $driver->createTable('plain_notes')->id()->integer('owner_key')->execute();
    }

    private function setupTestData(BaseSqlDriver $driver): void
    {
        Relational::setDefaultConnection($driver);
        Query::purgeCache();
        Relational::clearTableColumnsCache();

        foreach (self::getTestTables() as $table) {
            $driver->truncateTable($table);
        }
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testChildWithItsOwnKeyFindsItsParentById(string $dbName, BaseSqlDriver $driver): void
    {
        $this->setupTestData($driver);
        PlainOwner::blank()->set('name', 'unused')->save();
        $ada = PlainOwner::blank()->set('name', 'Ada');
        $ada->save();
        KeyedNote::blank()->set('owner_id', $ada->get('id'))->save();

        $note = KeyedNote::all()->row();

        $this->assertSame('id', $note->owner()->getRelatedKey(), $dbName);
        $this->assertSame('Ada', $note->owner->get('name'), $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testParentWithItsOwnKeyIsFoundByThatKey(string $dbName, BaseSqlDriver $driver): void
    {
        $this->setupTestData($driver);
        KeyedOwner::blank()->set('name', 'unused')->save();
        $grace = KeyedOwner::blank()->set('name', 'Grace');
        $grace->save();
        PlainNote::blank()->set('owner_key', $grace->get('owner_key'))->save();

        $note = PlainNote::all()->row();

        $this->assertSame('owner_key', $note->owner()->getRelatedKey(), $dbName);
        $this->assertSame('Grace', $note->owner->get('name'), $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetThroughTheRelationshipReadsTheRelatedRecord(string $dbName, BaseSqlDriver $driver): void
    {
        $this->setupTestData($driver);
        $ada = PlainOwner::blank()->set('name', 'Ada');
        $ada->save();
        KeyedNote::blank()->set('owner_id', $ada->get('id'))->save();

        $note = KeyedNote::all()->row();

        // $note->owner()->get() used to read the unrun query, which had no attributes
        $this->assertSame('Ada', $note->owner()->get('name'), $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetThroughTheRelationshipWithNoRecordGivesTheDefault(
        string $dbName,
        BaseSqlDriver $driver,
    ): void {
        $this->setupTestData($driver);
        KeyedNote::blank()->set('owner_id', 999)->save();

        $note = KeyedNote::all()->row();

        $this->assertSame('nobody', $note->owner()->get('name', 'nobody'), $dbName);
        $this->assertNull($note->owner()->get('name'), $dbName);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testQueryMethodsStillGoToTheQuery(string $dbName, BaseSqlDriver $driver): void
    {
        $this->setupTestData($driver);
        $ada = PlainOwner::blank()->set('name', 'Ada');
        $ada->save();
        KeyedNote::blank()->set('owner_id', $ada->get('id'))->save();

        $note = KeyedNote::all()->row();

        $this->assertSame(1, (int) $note->owner()->total(), $dbName);
        $this->assertSame('Ada', $note->owner()->whereEquals('name', 'Ada')->row()->get('name'), $dbName);
    }
}
