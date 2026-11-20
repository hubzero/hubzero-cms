<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Drivers\Base\BaseSqlDriver;
use Hubzero\Database\Relational;
use Hubzero\Database\Query;
use Hubzero\Database\Tests\TestModels\FitPost;

/**
 * What a form posts against what the columns hold
 *
 * A blank field arrives as '', and a typo as text; strict SQL refused both
 * for date and numeric columns, as it refuses text too long for a varchar,
 * and the refusal was an uncaught query error. A blank is now no value where
 * the column allows none, and the rest are validation errors.
 */
class ColumnFitTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['fit_posts'];
    }

    protected static function setUpDatabase(BaseSqlDriver $driver): void
    {
        $driver->dropTable('fit_posts', true);
        $driver
            ->createTable('fit_posts')
            ->id()
            ->string('title', 20)
            ->integer('count')->default(0)
            ->integer('score')->nullable()
            ->dateTime('published_at')->nullable()
            ->execute();
    }

    private function setupTestData(BaseSqlDriver $driver): void
    {
        Relational::setDefaultConnection($driver);
        Query::purgeCache();
        Relational::clearTableColumnsCache();

        $driver->truncateTable('fit_posts');
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testBlankDateAndNumberAreSavedAsNone(string $dbName, BaseSqlDriver $driver): void
    {
        $this->setupTestData($driver);

        $post = FitPost::blank()->set([
            'title'        => 'Blank fields',
            'count'        => '3',
            'score'        => '',
            'published_at' => '',
        ]);

        $this->assertNotFalse($post->save(), implode('; ', $post->getErrors()));

        $saved = FitPost::oneOrFail($post->get('id'));
        $this->assertNull($saved->get('score'));
        $this->assertNull($saved->get('published_at'));
        $this->assertEquals(3, (int) $saved->get('count'));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTextInANumberIsAnError(string $dbName, BaseSqlDriver $driver): void
    {
        $this->setupTestData($driver);

        $post = FitPost::blank()->set(['title' => 'Typo', 'count' => 'three']);

        $this->assertFalse($post->save());
        $this->assertContains('count must be a number', $post->getErrors());
        $this->assertEquals(0, FitPost::all()->total());
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testTextTooLongIsAnError(string $dbName, BaseSqlDriver $driver): void
    {
        $this->setupTestData($driver);

        $post = FitPost::blank()->set(['title' => str_repeat('a', 21), 'count' => 1]);

        $this->assertFalse($post->save());
        $this->assertContains('title can be at most 20 characters long', $post->getErrors());

        $post->set('title', str_repeat('é', 20));
        $this->assertNotFalse($post->save(), implode('; ', $post->getErrors()));
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testOnlyChangedValuesAreCheckedOnUpdate(string $dbName, BaseSqlDriver $driver): void
    {
        $this->setupTestData($driver);

        $post = FitPost::blank()->set(['title' => 'Kept', 'count' => 1]);
        $this->assertNotFalse($post->save(), implode('; ', $post->getErrors()));

        $post = FitPost::oneOrFail($post->get('id'));
        $post->set('score', '7');
        $this->assertNotFalse($post->save(), implode('; ', $post->getErrors()));
        $this->assertEquals(7, (int) FitPost::oneOrFail($post->get('id'))->get('score'));
    }
}
