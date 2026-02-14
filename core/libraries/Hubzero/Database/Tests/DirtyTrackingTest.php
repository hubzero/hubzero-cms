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
use Hubzero\Database\Tests\TestModels\DirtyPost;

/**
 * Dirty tracking test
 *
 * Tests the dirty attribute tracking feature (isDirty, getDirty, getOriginal, wasChanged, etc.)
 * which allows models to track which attributes have been modified since loading from the database.
 */
class DirtyTrackingTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['posts'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('posts', true);
        $driver->createTable('posts')
            ->id()
            ->string('title', 255)
            ->text('content')
            ->integer('count')->default(0)
            ->integer('user_id')->default(0)
            ->string('deleted_at', 50)->nullable()
            ->execute();
    }

    private function setupTestData(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);
        DirtyPost::clearBootedModels();
        Query::purgeCache();

        $driver->truncateTable('posts');
    }

    // =========================================================================
    // isDirty Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testBlankModelIsNotDirty(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::blank();

        $this->assertFalse($model->isDirty(), 'A blank model with no attributes should not be dirty');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSettingAttributeOnBlankModelMakesDirty(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::blank();
        $model->set('title', 'Test Title');

        $this->assertTrue($model->isDirty(), 'Model should be dirty after setting an attribute');
        $this->assertTrue($model->isDirty('title'), 'Title attribute should be dirty');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testModelFromResultsIsNotDirty(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1,
            'title' => 'Original Title',
            'content' => 'Original Content'
        ]);

        $this->assertFalse($model->isDirty(), 'Model loaded from results should not be dirty');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testModifyingLoadedModelMakesDirty(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1,
            'title' => 'Original Title',
            'content' => 'Original Content'
        ]);

        $model->set('title', 'New Title');

        $this->assertTrue($model->isDirty(), 'Model should be dirty after modification');
        $this->assertTrue($model->isDirty('title'), 'Modified attribute should be dirty');
        $this->assertFalse($model->isDirty('content'), 'Unmodified attribute should not be dirty');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testIsDirtyWithArrayOfAttributes(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1,
            'title' => 'Original Title',
            'content' => 'Original Content'
        ]);

        $model->set('title', 'New Title');

        $this->assertTrue(
            $model->isDirty(['title', 'content']),
            'Should be dirty if any specified attribute is dirty'
        );
        $this->assertFalse(
            $model->isDirty(['content', 'id']),
            'Should not be dirty if no specified attributes are dirty'
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSettingSameValueDoesNotMakeDirty(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1,
            'title' => 'Same Title'
        ]);

        $model->set('title', 'Same Title');

        $this->assertFalse($model->isDirty('title'), 'Setting same value should not make attribute dirty');
    }

    // =========================================================================
    // getDirty Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetDirtyReturnsChangedAttributes(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1,
            'title' => 'Original Title',
            'content' => 'Original Content'
        ]);

        $model->set('title', 'New Title');
        $model->set('content', 'New Content');

        $dirty = $model->getDirty();

        $this->assertCount(2, $dirty);
        $this->assertEquals('New Title', $dirty['title']);
        $this->assertEquals('New Content', $dirty['content']);
        $this->assertArrayNotHasKey('id', $dirty, 'Unchanged attributes should not be in dirty');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetDirtyOnBlankModel(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::blank();
        $model->set('title', 'Test');

        $dirty = $model->getDirty();

        $this->assertCount(1, $dirty);
        $this->assertEquals('Test', $dirty['title']);
    }

    // =========================================================================
    // getOriginal Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetOriginalReturnsAllOriginalValues(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1,
            'title' => 'Original Title'
        ]);

        $model->set('title', 'New Title');

        $original = $model->getOriginal();

        $this->assertEquals(1, $original['id']);
        $this->assertEquals('Original Title', $original['title']);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetOriginalReturnsSpecificValue(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1,
            'title' => 'Original Title'
        ]);

        $model->set('title', 'New Title');

        $this->assertEquals('Original Title', $model->getOriginal('title'));
        $this->assertEquals('New Title', $model->get('title'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetOriginalReturnsDefaultForMissingKey(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1
        ]);

        $this->assertNull($model->getOriginal('nonexistent'));
        $this->assertEquals('default', $model->getOriginal('nonexistent', 'default'));
    }

    // =========================================================================
    // syncOriginal Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSyncOriginalClearsDirtyState(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1,
            'title' => 'Original Title'
        ]);

        $model->set('title', 'New Title');
        $this->assertTrue($model->isDirty());

        $model->syncOriginal();

        $this->assertFalse($model->isDirty(), 'Model should not be dirty after syncOriginal');
        $this->assertEquals('New Title', $model->getOriginal('title'), 'Original should be updated to new value');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSyncOriginalAttributeSyncsOnlyOneAttribute(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1,
            'title' => 'Original Title',
            'content' => 'Original Content'
        ]);

        $model->set('title', 'New Title');
        $model->set('content', 'New Content');

        $model->syncOriginalAttribute('title');

        $this->assertFalse($model->isDirty('title'), 'Synced attribute should not be dirty');
        $this->assertTrue($model->isDirty('content'), 'Non-synced attribute should still be dirty');
    }

    // =========================================================================
    // wasChanged / getChanges Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testWasChangedIsFalseBeforeSave(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1,
            'title' => 'Original Title'
        ]);

        $model->set('title', 'New Title');

        $this->assertFalse($model->wasChanged(), 'wasChanged should be false before save');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetChangesIsEmptyBeforeSave(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1,
            'title' => 'Original Title'
        ]);

        $model->set('title', 'New Title');

        $this->assertEmpty($model->getChanges(), 'getChanges should be empty before save');
    }

    // =========================================================================
    // discardChanges Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDiscardChangesRevertsAllChanges(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1,
            'title' => 'Original Title',
            'content' => 'Original Content'
        ]);

        $model->set('title', 'New Title');
        $model->set('content', 'New Content');

        $model->discardChanges();

        $this->assertEquals('Original Title', $model->get('title'));
        $this->assertEquals('Original Content', $model->get('content'));
        $this->assertFalse($model->isDirty());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDiscardChangesRevertsSpecificAttribute(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1,
            'title' => 'Original Title',
            'content' => 'Original Content'
        ]);

        $model->set('title', 'New Title');
        $model->set('content', 'New Content');

        $model->discardChanges('title');

        $this->assertEquals('Original Title', $model->get('title'), 'Title should be reverted');
        $this->assertEquals('New Content', $model->get('content'), 'Content should remain changed');
        $this->assertFalse($model->isDirty('title'));
        $this->assertTrue($model->isDirty('content'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDiscardChangesRemovesNewAttributes(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1,
            'title' => 'Original Title'
        ]);

        $model->set('new_field', 'new value');

        $model->discardChanges('new_field');

        $this->assertFalse($model->hasAttribute('new_field'), 'New attribute should be removed');
    }

    // =========================================================================
    // Type Coercion Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testNumericStringComparisonHandled(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => '1',  // String from DB
            'count' => '42'  // String from DB
        ]);

        $model->set('id', 1);
        $model->set('count', 42);

        $this->assertFalse($model->isDirty('id'), 'Integer 1 should equal string "1"');
        $this->assertFalse($model->isDirty('count'), 'Integer 42 should equal string "42"');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testNullHandling(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::newFromResults((object)[
            'id' => 1,
            'deleted_at' => null
        ]);

        $model->set('deleted_at', null);
        $this->assertFalse($model->isDirty('deleted_at'), 'Setting null to null should not be dirty');

        $model->set('deleted_at', '2024-01-01');
        $this->assertTrue($model->isDirty('deleted_at'), 'Setting null to value should be dirty');
    }

    // =========================================================================
    // Integration with wasChanged Tests
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testWasChangedWithArrayOfAttributes(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $model = DirtyPost::blank();

        $reflection = new \ReflectionClass(Relational::class);
        $property = $reflection->getProperty('changes');
        $property->setAccessible(true);
        $property->setValue($model, ['title' => 'New Title']);

        $this->assertTrue(
            $model->wasChanged(['title', 'content']),
            'Should return true if any specified attribute was changed'
        );
        $this->assertFalse(
            $model->wasChanged(['content', 'author']),
            'Should return false if no specified attributes were changed'
        );
    }
}
