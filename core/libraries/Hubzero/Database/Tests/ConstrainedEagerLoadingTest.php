<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Relational;
use Hubzero\Database\Tests\TestModels\EagerPost;
use Hubzero\Database\Tests\TestModels\EagerComment;
use ReflectionClass;
use Closure;

/**
 * Constrained eager loading test
 *
 * Tests the array-based and scope-reference constraints for eager loading
 * which provide a closure-free alternative to constrained eager loading.
 */
class ConstrainedEagerLoadingTest extends AbstractDriverTestCase
{
    // =========================================================================
    // isAssociativeArray Tests
    // =========================================================================

    private function getIsAssociativeArrayMethod()
    {
        $reflection = new ReflectionClass(Relational::class);
        $method = $reflection->getMethod('isAssociativeArray');
        $method->setAccessible(true);
        return $method;
    }

    public function testEmptyArrayIsNotAssociative()
    {
        $model = EagerPost::blank();
        $method = $this->getIsAssociativeArrayMethod();

        $this->assertFalse($method->invoke($model, []));
    }

    public function testIndexedArrayIsNotAssociative()
    {
        $model = EagerPost::blank();
        $method = $this->getIsAssociativeArrayMethod();

        $this->assertFalse($method->invoke($model, ['comments', 'author']));
        $this->assertFalse($method->invoke($model, [0 => 'comments', 1 => 'author']));
    }

    public function testArrayWithStringKeysIsAssociative()
    {
        $model = EagerPost::blank();
        $method = $this->getIsAssociativeArrayMethod();

        $this->assertTrue($method->invoke($model, ['comments' => ['scope' => 'approved']]));
        $this->assertTrue($method->invoke($model, ['comments' => [], 'author']));
    }

    public function testOldFormatArrayIsNotAssociative()
    {
        $model = EagerPost::blank();
        $method = $this->getIsAssociativeArrayMethod();

        $array = ['comments', function ($q) {
        }];
        $this->assertFalse($method->invoke($model, $array));
    }

    // =========================================================================
    // buildEagerConstraint Tests
    // =========================================================================

    private function getBuildEagerConstraintMethod()
    {
        $reflection = new ReflectionClass(Relational::class);
        $method = $reflection->getMethod('buildEagerConstraint');
        $method->setAccessible(true);
        return $method;
    }

    public function testBuildEagerConstraintReturnsClosure()
    {
        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['conditions' => ['approved' => 1]];
        $result = $method->invoke($model, $config);

        $this->assertInstanceOf(Closure::class, $result);
    }

    public function testBuildEagerConstraintWithSingleScope()
    {
        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['scope' => 'approved'];
        $closure = $method->invoke($model, $config);

        $this->assertInstanceOf(Closure::class, $closure);
    }

    public function testBuildEagerConstraintWithMultipleScopes()
    {
        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['scope' => ['approved', 'recent']];
        $closure = $method->invoke($model, $config);

        $this->assertInstanceOf(Closure::class, $closure);
    }

    public function testBuildEagerConstraintWithScopeParameters()
    {
        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['scope' => ['byUser' => [42]]];
        $closure = $method->invoke($model, $config);

        $this->assertInstanceOf(Closure::class, $closure);
    }

    public function testBuildEagerConstraintWithConditions()
    {
        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['conditions' => ['approved' => 1, 'spam' => 0]];
        $closure = $method->invoke($model, $config);

        $this->assertInstanceOf(Closure::class, $closure);
    }

    public function testBuildEagerConstraintWithOrder()
    {
        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['order' => ['created_at' => 'desc', 'id' => 'asc']];
        $closure = $method->invoke($model, $config);

        $this->assertInstanceOf(Closure::class, $closure);
    }

    public function testBuildEagerConstraintWithLimit()
    {
        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['limit' => 5];
        $closure = $method->invoke($model, $config);

        $this->assertInstanceOf(Closure::class, $closure);
    }

    public function testBuildEagerConstraintWithAllOptions()
    {
        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = [
            'scope' => 'approved',
            'conditions' => ['featured' => 1],
            'order' => ['created_at' => 'desc'],
            'limit' => 10
        ];
        $closure = $method->invoke($model, $config);

        $this->assertInstanceOf(Closure::class, $closure);
    }

    // =========================================================================
    // with() Method Acceptance Tests
    // =========================================================================

    public function testIncludingAcceptsSimpleStrings()
    {
        $model = EagerPost::blank();

        $result = $model->with('comments', 'user');

        $this->assertSame($model, $result);
    }

    public function testIncludingAcceptsIndexedArray()
    {
        $model = EagerPost::blank();

        $result = $model->with(['comments', 'user']);

        $this->assertSame($model, $result);
    }

    public function testIncludingAcceptsAssociativeArray()
    {
        $model = EagerPost::blank();

        $result = $model->with([
            'comments' => ['scope' => 'approved'],
            'user'
        ]);

        $this->assertSame($model, $result);
    }

    public function testIncludingAcceptsConditionsArray()
    {
        $model = EagerPost::blank();

        $result = $model->with([
            'comments' => [
                'conditions' => ['approved' => 1, 'spam' => 0]
            ]
        ]);

        $this->assertSame($model, $result);
    }

    public function testIncludingAcceptsComplexConfiguration()
    {
        $model = EagerPost::blank();

        $result = $model->with([
            'comments' => [
                'scope' => ['approved', 'notSpam'],
                'conditions' => ['featured' => 1],
                'order' => ['created_at' => 'desc'],
                'limit' => 5
            ],
            'user' => ['scope' => 'active'],
            'tags'
        ]);

        $this->assertSame($model, $result);
    }

    // =========================================================================
    // Constraint Closure Behavior Tests (functional, against real database)
    // =========================================================================

    #[DataProvider('databaseProvider')]
    public function testConditionsClosureAppliesWhereEquals(string $dbName, Driver $driver)
    {
        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['conditions' => ['approved' => 1]];
        $closure = $method->invoke($model, $config);

        $query = new \Hubzero\Database\Query($driver);
        $query->from('eager_comments')->select('*');
        $closure($query);

        $comments = $query->fetch('rows');

        $this->assertGreaterThan(0, count($comments));
        foreach ($comments as $comment) {
            $this->assertEquals(1, $comment->approved, "[$dbName] whereEquals should filter to approved=1");
        }
    }

    #[DataProvider('databaseProvider')]
    public function testNullConditionAppliesWhereIsNull(string $dbName, Driver $driver)
    {
        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['conditions' => ['deleted_at' => null]];
        $closure = $method->invoke($model, $config);

        $query = new \Hubzero\Database\Query($driver);
        $query->from('eager_comments')->select('*');
        $closure($query);

        $comments = $query->fetch('rows');

        // 4 of 5 comments have NULL deleted_at (the spam comment has '2025-01-01')
        $this->assertEquals(4, count($comments), "[$dbName] whereIsNull should exclude rows with non-null deleted_at");
    }

    #[DataProvider('databaseProvider')]
    public function testOrderClosureAppliesOrder(string $dbName, Driver $driver)
    {
        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['order' => ['rating' => 'desc']];
        $closure = $method->invoke($model, $config);

        $query = new \Hubzero\Database\Query($driver);
        $query->from('eager_comments')->select('*');
        $closure($query);

        $comments = $query->fetch('rows');

        $this->assertGreaterThanOrEqual(2, count($comments));
        for ($i = 1; $i < count($comments); $i++) {
            $this->assertGreaterThanOrEqual(
                $comments[$i]->rating,
                $comments[$i - 1]->rating,
                "[$dbName] order(rating, desc) should sort descending"
            );
        }
    }

    #[DataProvider('databaseProvider')]
    public function testLimitClosureAppliesLimit(string $dbName, Driver $driver)
    {
        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['limit' => 2];
        $closure = $method->invoke($model, $config);

        $query = new \Hubzero\Database\Query($driver);
        $query->from('eager_comments')->select('*');
        $closure($query);

        $comments = $query->fetch('rows');

        $this->assertEquals(2, count($comments), "[$dbName] limit(2) should return exactly 2 rows");
    }

    #[DataProvider('databaseProvider')]
    public function testScopeClosureCallsScopeMethod(string $dbName, Driver $driver)
    {
        Relational::setDefaultConnection($driver);

        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['scope' => 'approved'];
        $closure = $method->invoke($model, $config);

        // Apply closure to a model instance (scopes are resolved via __call)
        $comment = EagerComment::blank();
        $closure($comment);

        $comments = $comment->rows();

        $this->assertGreaterThan(0, count($comments));
        foreach ($comments as $c) {
            $this->assertEquals(1, $c->approved, "[$dbName] approved scope should filter to approved=1");
        }
    }

    #[DataProvider('databaseProvider')]
    public function testScopeClosurePassesParameters(string $dbName, Driver $driver)
    {
        Relational::setDefaultConnection($driver);

        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['scope' => ['byPost' => [1]]];
        $closure = $method->invoke($model, $config);

        $comment = EagerComment::blank();
        $closure($comment);

        $comments = $comment->rows();

        $this->assertGreaterThan(0, count($comments));
        foreach ($comments as $c) {
            $this->assertEquals(1, $c->post_id, "[$dbName] byPost(1) scope should filter to post_id=1");
        }
    }

    // =========================================================================
    // Integration Tests - Real Database Execution
    // =========================================================================

    protected static function getTestTables(): array
    {
        return ['eager_comments', 'eager_posts'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('eager_comments', true);
        $driver->dropTable('eager_posts', true);

        $schema = $driver->schema();
        $schema->createTable('eager_posts')
            ->id()
            ->string('title', 255)
            ->integer('author_id')
            ->integer('status')->default(1)
            ->execute();

        $schema->createTable('eager_comments')
            ->id()
            ->integer('post_id')
            ->string('content', 255)
            ->integer('approved')->default(0)
            ->integer('rating')->default(0)
            ->string('deleted_at', 50)
            ->execute();

        $query = new \Hubzero\Database\Query($driver);
        $query->insertMany('eager_posts', [
            ['title' => 'First Post', 'author_id' => 1, 'status' => 1],
            ['title' => 'Second Post', 'author_id' => 2, 'status' => 1],
            ['title' => 'Draft Post', 'author_id' => 1, 'status' => 0],
        ]);

        $query2 = new \Hubzero\Database\Query($driver);
        $query2->insertMany('eager_comments', [
            ['post_id' => 1, 'content' => 'Great post!', 'approved' => 1, 'rating' => 5],
            ['post_id' => 1, 'content' => 'Spam comment', 'approved' => 0, 'rating' => 1],
            ['post_id' => 1, 'content' => 'Another good one', 'approved' => 1, 'rating' => 4],
            ['post_id' => 2, 'content' => 'Nice work', 'approved' => 1, 'rating' => 5],
            ['post_id' => 2, 'content' => 'Pending comment', 'approved' => 0, 'rating' => 3],
        ]);

        // Set deleted_at on one row so we can test whereIsNull
        $query3 = new \Hubzero\Database\Query($driver);
        $query3->alter('eager_comments', 'content', 'Spam comment', ['deleted_at' => '2025-01-01']);
    }

    #[DataProvider('databaseProvider')]
    public function testEagerLoadingWithConditionsConstraint(string $dbName, Driver $driver)
    {
        $query = new \Hubzero\Database\Query($driver);
        $posts = $query->from('eager_posts')
            ->select('*')
            ->whereEquals('status', 1)
            ->fetch('rows');

        $this->assertGreaterThanOrEqual(2, count($posts));

        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['conditions' => ['approved' => 1]];
        $closure = $method->invoke($model, $config);

        $commentQuery = new \Hubzero\Database\Query($driver);
        $commentQuery->from('eager_comments')->select('*');
        $closure($commentQuery);

        $comments = $commentQuery->fetch('rows');

        $this->assertEquals(3, count($comments));
        foreach ($comments as $comment) {
            $this->assertEquals(1, $comment->approved);
        }
    }

    #[DataProvider('databaseProvider')]
    public function testEagerLoadingWithOrderConstraint(string $dbName, Driver $driver)
    {
        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['order' => ['rating' => 'desc']];
        $closure = $method->invoke($model, $config);

        $query = new \Hubzero\Database\Query($driver);
        $query->from('eager_comments')->select('*');
        $closure($query);

        $comments = $query->fetch('rows');

        $this->assertGreaterThan(0, count($comments));

        if (count($comments) >= 2) {
            $this->assertGreaterThanOrEqual($comments[1]->rating, $comments[0]->rating);
        }
    }

    #[DataProvider('databaseProvider')]
    public function testEagerLoadingWithLimitConstraint(string $dbName, Driver $driver)
    {
        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = ['limit' => 2];
        $closure = $method->invoke($model, $config);

        $query = new \Hubzero\Database\Query($driver);
        $query->from('eager_comments')->select('*');
        $closure($query);

        $comments = $query->fetch('rows');

        $this->assertEquals(2, count($comments));
    }

    #[DataProvider('databaseProvider')]
    public function testEagerLoadingWithMultipleConstraints(string $dbName, Driver $driver)
    {
        $model = EagerPost::blank();
        $method = $this->getBuildEagerConstraintMethod();

        $config = [
            'conditions' => ['approved' => 1],
            'order' => ['rating' => 'desc'],
            'limit' => 2
        ];
        $closure = $method->invoke($model, $config);

        $query = new \Hubzero\Database\Query($driver);
        $query->from('eager_comments')->select('*');
        $closure($query);

        $comments = $query->fetch('rows');

        $this->assertEquals(2, count($comments));

        foreach ($comments as $comment) {
            $this->assertEquals(1, $comment->approved);
        }

        if (count($comments) == 2) {
            $this->assertGreaterThanOrEqual($comments[1]->rating, $comments[0]->rating);
        }
    }
}
