<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Query;
use Hubzero\Database\Nested;
use Hubzero\Database\Relational;
use Hubzero\Database\Tests\TestModels\NestedDiscussion;

/**
 * Nested relational model tests
 *
 * Tests the nested set (modified preorder tree traversal) operations:
 * saveAsChildOf, saveAsFirstChildOf, saveAsRoot, getChildren,
 * getDescendants, and destroy (with cascading delete).
 */
class NestedTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['nested_discussions'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('nested_discussions', true);

        $driver->createTable('nested_discussions')
            ->id()
            ->integer('user_id')
            ->string('title', 255)
            ->string('content', 1000)->nullable()
            ->integer('lft')->default(0)
            ->integer('rgt')->default(0)
            ->integer('parent_id')->default(0)
            ->integer('level')->default(0)
            ->string('scope', 100)->nullable()
            ->integer('scope_id')->default(0)
            ->execute();
    }

    /**
     * Initial seed data for the discussions table
     *
     * Tree structure (scope=group, scope_id=1):
     *   Root (id=1, lft=0, rgt=9)
     *     +-- id=2 "Confused" (lft=1, rgt=2)
     *     +-- id=5 "Another Reply" (lft=3, rgt=4)
     *     +-- id=3 "Testing Stuff" (lft=5, rgt=8)
     *          +-- id=4 "More Testing" (lft=6, rgt=7)
     */
    private static function getSeedData(): array
    {
        return [
            [
                'id' => 1, 'user_id' => 3, 'title' => 'My first discussion',
                'content' => 'Tell me everything!', 'lft' => 0, 'rgt' => 9,
                'parent_id' => 0, 'level' => 0, 'scope' => 'group', 'scope_id' => 1,
            ],
            [
                'id' => 2, 'user_id' => 3, 'title' => 'Confused',
                'content' => 'Is this really a good idea?', 'lft' => 1, 'rgt' => 2,
                'parent_id' => 1, 'level' => 1, 'scope' => 'group', 'scope_id' => 1,
            ],
            [
                'id' => 3, 'user_id' => 3, 'title' => 'Testing Stuff',
                'content' => 'This is a test additional child node', 'lft' => 5, 'rgt' => 8,
                'parent_id' => 1, 'level' => 1, 'scope' => 'group', 'scope_id' => 1,
            ],
            [
                'id' => 4, 'user_id' => 3, 'title' => 'More Testing',
                'content' => 'This is a node added by parent id', 'lft' => 6, 'rgt' => 7,
                'parent_id' => 3, 'level' => 2, 'scope' => 'group', 'scope_id' => 1,
            ],
            [
                'id' => 5, 'user_id' => 3, 'title' => 'Another Reply',
                'content' => 'This is another top-level reply', 'lft' => 3, 'rgt' => 4,
                'parent_id' => 1, 'level' => 1, 'scope' => 'group', 'scope_id' => 1,
            ],
        ];
    }

    private function resetTestState(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);
        NestedDiscussion::clearBootedModels();
        Query::purgeCache();

        // Reset table, seed initial data (IDs 1-5), set next ID to 6
        $driver->truncateTable('nested_discussions');
        $query = new Query($driver);
        $query->insertMany('nested_discussions', self::getSeedData());
        $driver->setAutoIncrement('nested_discussions', 6);
    }

    /**
     * Assert that the discussions table matches the expected state
     *
     * Checks the nested set columns (lft, rgt, parent_id, level) for each row.
     *
     * @param Driver $driver Database driver
     * @param array $expectedRows Array of expected row data keyed by position
     * @param string $dbName Database name for error messages
     */
    private function assertNestedState(Driver $driver, array $expectedRows, string $dbName): void
    {
        $tableName = $driver->quoteName('nested_discussions');
        $idCol = $driver->quoteName('id');
        $result = $driver->setQuery("SELECT * FROM $tableName ORDER BY $idCol")
            ->loadObjectList();

        $this->assertCount(
            count($expectedRows),
            $result,
            "[$dbName] Expected " . count($expectedRows) . " rows, got " . count($result)
        );

        foreach ($expectedRows as $i => $expected) {
            $actual = $result[$i];
            foreach ($expected as $col => $val) {
                $this->assertEquals(
                    $val,
                    $actual->$col,
                    "[$dbName] Row id={$expected['id']}, column '$col': expected '$val', got '{$actual->$col}'"
                );
            }
        }
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }

    // =========================================================================
    // Construction Tests
    // =========================================================================

    /**
     * Tests object construction and variable initialization
     */
    #[DataProvider('databaseProvider')]
    public function testConstruct(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $model = NestedDiscussion::blank();

        $this->assertInstanceOf(
            '\Hubzero\Database\Nested',
            $model,
            "[$dbName] Model is not an instance of \\Hubzero\\Database\\Nested"
        );
        $this->assertEquals(
            'NestedDiscussion',
            $model->getModelName(),
            "[$dbName] Model should have correct model name"
        );
    }

    // =========================================================================
    // Save As Child Tests
    // =========================================================================

    /**
     * Tests adding a new child node from a parent model instance
     *
     * New node (id=6) added as last child of root (id=1)
     */
    #[DataProvider('databaseProvider')]
    public function testCanAddNewChildNodeFromParentModel(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $parent = NestedDiscussion::oneOrFail(1);

        $new = NestedDiscussion::blank()->set([
            'user_id' => 3,
            'title'   => 'Testing Stuff',
            'content' => 'This is a test additional child node'
        ]);

        $new->saveAsChildOf($parent);

        $this->assertNestedState($driver, [
            ['id' => 1, 'lft' => 0, 'rgt' => 11, 'parent_id' => 0, 'level' => 0],
            ['id' => 2, 'lft' => 1, 'rgt' => 2, 'parent_id' => 1, 'level' => 1],
            ['id' => 3, 'lft' => 5, 'rgt' => 8, 'parent_id' => 1, 'level' => 1],
            ['id' => 4, 'lft' => 6, 'rgt' => 7, 'parent_id' => 3, 'level' => 2],
            ['id' => 5, 'lft' => 3, 'rgt' => 4, 'parent_id' => 1, 'level' => 1],
            ['id' => 6, 'lft' => 9, 'rgt' => 10, 'parent_id' => 1, 'level' => 1],
        ], $dbName);
    }

    /**
     * Tests adding a new child node from a parent ID (integer)
     *
     * New node (id=6) added as child of node 3
     */
    #[DataProvider('databaseProvider')]
    public function testCanAddNewChildNodeFromParentId(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $new = NestedDiscussion::blank()->set([
            'user_id' => 3,
            'title'   => 'More Testing',
            'content' => 'This is a node added by parent id'
        ]);

        $new->saveAsChildOf(3);

        $this->assertNestedState($driver, [
            ['id' => 1, 'lft' => 0, 'rgt' => 11, 'parent_id' => 0, 'level' => 0],
            ['id' => 2, 'lft' => 1, 'rgt' => 2, 'parent_id' => 1, 'level' => 1],
            ['id' => 3, 'lft' => 5, 'rgt' => 10, 'parent_id' => 1, 'level' => 1],
            ['id' => 4, 'lft' => 6, 'rgt' => 7, 'parent_id' => 3, 'level' => 2],
            ['id' => 5, 'lft' => 3, 'rgt' => 4, 'parent_id' => 1, 'level' => 1],
            ['id' => 6, 'lft' => 8, 'rgt' => 9, 'parent_id' => 3, 'level' => 2],
        ], $dbName);
    }

    /**
     * Tests adding a new first child node
     *
     * New node (id=6) inserted as first child of root, pushing others right
     */
    #[DataProvider('databaseProvider')]
    public function testCanAddNewFirstChildNode(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $new = NestedDiscussion::blank()->set([
            'user_id' => 3,
            'title'   => 'Left node',
            'content' => 'This should be located as the first child of the parent'
        ]);

        $new->saveAsFirstChildOf(1);

        $this->assertNestedState($driver, [
            ['id' => 1, 'lft' => 0, 'rgt' => 11, 'parent_id' => 0, 'level' => 0],
            ['id' => 2, 'lft' => 3, 'rgt' => 4, 'parent_id' => 1, 'level' => 1],
            ['id' => 3, 'lft' => 7, 'rgt' => 10, 'parent_id' => 1, 'level' => 1],
            ['id' => 4, 'lft' => 8, 'rgt' => 9, 'parent_id' => 3, 'level' => 2],
            ['id' => 5, 'lft' => 5, 'rgt' => 6, 'parent_id' => 1, 'level' => 1],
            ['id' => 6, 'lft' => 1, 'rgt' => 2, 'parent_id' => 1, 'level' => 1],
        ], $dbName);
    }

    // =========================================================================
    // Save As Root Tests
    // =========================================================================

    /**
     * Tests adding a new root node in a different scope
     *
     * New root (id=6) in scope=group, scope_id=2
     */
    #[DataProvider('databaseProvider')]
    public function testCanAddNewRootNode(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        NestedDiscussion::blank()->set([
            'user_id'  => 3,
            'title'    => 'This is a new discussion',
            'content'  => 'Tell me about life',
            'scope'    => 'group',
            'scope_id' => 2
        ])->saveAsRoot();

        $this->assertNestedState($driver, [
            ['id' => 1, 'lft' => 0, 'rgt' => 9, 'parent_id' => 0, 'level' => 0, 'scope' => 'group', 'scope_id' => 1],
            ['id' => 2, 'lft' => 1, 'rgt' => 2, 'parent_id' => 1, 'level' => 1],
            ['id' => 3, 'lft' => 5, 'rgt' => 8, 'parent_id' => 1, 'level' => 1],
            ['id' => 4, 'lft' => 6, 'rgt' => 7, 'parent_id' => 3, 'level' => 2],
            ['id' => 5, 'lft' => 3, 'rgt' => 4, 'parent_id' => 1, 'level' => 1],
            ['id' => 6, 'lft' => 0, 'rgt' => 1, 'parent_id' => 0, 'level' => 0, 'scope' => 'group', 'scope_id' => 2],
        ], $dbName);
    }

    // =========================================================================
    // Get Children / Descendants Tests
    // =========================================================================

    /**
     * Tests getting the children of a given parent
     */
    #[DataProvider('databaseProvider')]
    public function testCanGetChildren(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $discussion = NestedDiscussion::oneOrFail(1);
        $children = $discussion->getChildren()->raw();

        $this->assertCount(3, $children, "[$dbName] Discussion 1 should have had 3 children");

        foreach ([2, 3, 5] as $expected) {
            $this->assertArrayHasKey($expected, $children, "[$dbName] Expected a discussion with id {$expected}");
        }
    }

    /**
     * Tests getting all descendants of a given parent
     */
    #[DataProvider('databaseProvider')]
    public function testCanGetDescendants(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $discussion = NestedDiscussion::oneOrFail(1);
        $descendants = $discussion->getDescendants()->raw();

        $this->assertCount(4, $descendants, "[$dbName] Discussion 1 should have had 4 descendants");

        foreach ([2, 3, 4, 5] as $expected) {
            $this->assertArrayHasKey($expected, $descendants, "[$dbName] Expected a discussion with id {$expected}");
        }
    }

    /**
     * Tests getting a limited set of descendants
     */
    #[DataProvider('databaseProvider')]
    public function testCanGetLimitedDescendants(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        $discussion = NestedDiscussion::oneOrFail(1);
        $descendants = $discussion->descendants()->limit(2)->rows()->raw();

        foreach ([2, 5] as $expected) {
            $this->assertArrayHasKey($expected, $descendants, "[$dbName] Expected a discussion with id {$expected}");
        }
    }

    // =========================================================================
    // Delete Tests
    // =========================================================================

    /**
     * Tests deleting a leaf node (no children)
     *
     * Node 5 removed, lft/rgt values adjusted
     */
    #[DataProvider('databaseProvider')]
    public function testCanDeleteLeafNode(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        NestedDiscussion::oneOrFail(5)->destroy();

        $this->assertNestedState($driver, [
            ['id' => 1, 'lft' => 0, 'rgt' => 7, 'parent_id' => 0, 'level' => 0],
            ['id' => 2, 'lft' => 1, 'rgt' => 2, 'parent_id' => 1, 'level' => 1],
            ['id' => 3, 'lft' => 3, 'rgt' => 6, 'parent_id' => 1, 'level' => 1],
            ['id' => 4, 'lft' => 4, 'rgt' => 5, 'parent_id' => 3, 'level' => 2],
        ], $dbName);
    }

    /**
     * Tests deleting a parent node cascades to children
     *
     * Node 3 and its child node 4 both removed
     */
    #[DataProvider('databaseProvider')]
    public function testCanDeleteParentNode(string $dbName, Driver $driver): void
    {
        $this->resetTestState($driver);

        NestedDiscussion::oneOrFail(3)->destroy();

        $this->assertNestedState($driver, [
            ['id' => 1, 'lft' => 0, 'rgt' => 5, 'parent_id' => 0, 'level' => 0],
            ['id' => 2, 'lft' => 1, 'rgt' => 2, 'parent_id' => 1, 'level' => 1],
            ['id' => 5, 'lft' => 3, 'rgt' => 4, 'parent_id' => 1, 'level' => 1],
        ], $dbName);
    }
}
