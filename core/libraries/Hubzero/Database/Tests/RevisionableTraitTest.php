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
use Hubzero\Database\Tests\TestModels\RevisionableArticle;
use Hubzero\Database\Tests\TestModels\CustomRevisionablePost;

/**
 * Revisionable trait tests
 *
 * Tests for the Revisionable trait that provides version history
 * functionality for models.
 */
class RevisionableTraitTest extends AbstractDriverTestCase
{
    /**
     * Return table names created by this test for automatic cleanup
     *
     * @return array
     */
    protected static function getTestTables(): array
    {
        return [
            'revisionable_articles',
            'revisionable_articles_revisions',
            'revisionable_posts',
            'post_history',
        ];
    }

    /**
     * Set up test tables
     *
     * @param Driver $driver Database driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        // Create revisionable_articles table
        $driver->dropTable('revisionable_articles', true);
        $driver->createTable('revisionable_articles')
            ->id()
            ->string('title', 255)
            ->string('content', 2048)
            ->integer('state')->default(1)
            ->execute();

        // Create revisionable_articles_revisions table
        $driver->dropTable('revisionable_articles_revisions', true);
        $driver->createTable('revisionable_articles_revisions')
            ->id()
            ->integer('article_id')
            ->integer('revision_number')
            ->string('data', 2048)
            ->string('created', 50)
            ->integer('created_by')
            ->string('log', 1024, true) // nullable
            ->execute();

        // Create revisionable_posts table
        $driver->dropTable('revisionable_posts', true);
        $driver->createTable('revisionable_posts')
            ->id()
            ->string('title', 255)
            ->string('body', 2048)
            ->integer('author_id', true) // nullable
            ->integer('status')->default(1)
            ->execute();

        // Create post_history table
        $driver->dropTable('post_history', true);
        $driver->createTable('post_history')
            ->id()
            ->integer('post_id')
            ->integer('revision_number')
            ->string('data', 2048)
            ->string('created', 50)
            ->integer('created_by')
            ->string('log', 1024, true) // nullable
            ->execute();
    }

    private function setupTestData(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);
        RevisionableArticle::clearBootedModels();
        CustomRevisionablePost::clearBootedModels();
        Query::purgeCache();

        $driver->truncateTable('revisionable_articles');
        $driver->truncateTable('revisionable_articles_revisions');
        $driver->truncateTable('revisionable_posts');
        $driver->truncateTable('post_history');
    }

    // =========================================================================
    // Basic Create Revision Tests
    // =========================================================================

    /**
     * Test createRevision() creates a revision
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function createRevisionCreatesRevision(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Original content', 'state' => 1],
        ]);
        $id = 1;

        $article = RevisionableArticle::one($id);
        $result = $article->createRevision();

        $this->assertTrue($result);
        $this->assertEquals(1, $article->getRevisionCount());

        // Verify revision data
        $revision = $article->findRevision(1);
        $this->assertNotNull($revision);
        $this->assertEquals('Test Article', $revision->data['title']);
        $this->assertEquals(
            'Original content',
            $revision->data['content']
        );
    }

    /**
     * Test createRevision() with log message
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function createRevisionWithLogMessage(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $article = RevisionableArticle::one($id);
        $result = $article->createRevision('Updated the title');

        $this->assertTrue($result);

        $revision = $article->findRevision(1);
        $this->assertEquals('Updated the title', $revision->log);
    }

    /**
     * Test createRevision() fails for unsaved record
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function createRevisionFailsForUnsavedRecord(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $article = new RevisionableArticle();
        $article->set('title', 'New Article');

        $result = $article->createRevision();

        $this->assertFalse($result);
    }

    /**
     * Test multiple revisions increment revision number
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function multipleRevisionsIncrementNumber(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content v1', 'state' => 1],
        ]);
        $id = 1;

        $article = RevisionableArticle::one($id);
        $article->createRevision('Version 1');

        $article->set('content', 'Content v2');
        $article->save();
        $article->createRevision('Version 2');

        $article->set('content', 'Content v3');
        $article->save();
        $article->createRevision('Version 3');

        $this->assertEquals(3, $article->getRevisionCount());

        $revisions = $article->revisions('asc');
        $this->assertEquals(1, $revisions[0]->revision_number);
        $this->assertEquals(2, $revisions[1]->revision_number);
        $this->assertEquals(3, $revisions[2]->revision_number);
    }

    // =========================================================================
    // Get Revision Tests
    // =========================================================================

    /**
     * Test revisions() returns all revisions
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function revisionsReturnsAllRevisions(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $query = new Query($driver);
        $query->insertMany('revisionable_articles_revisions', [
            [
                'article_id' => 1,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'Test v1', 'content' => 'C1']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 2,
                'data' => json_encode(['title' => 'Test v2', 'content' => 'C2']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 3,
                'data' => json_encode(['title' => 'Test v3', 'content' => 'C3']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ]
        ]);

        $article = RevisionableArticle::one($id);
        $revisions = $article->revisions();

        $this->assertCount(3, $revisions);

        // Default order is desc
        $this->assertEquals(3, $revisions[0]->revision_number);
        $this->assertEquals(2, $revisions[1]->revision_number);
        $this->assertEquals(1, $revisions[2]->revision_number);
    }

    /**
     * Test revisions() with asc order
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function revisionsWithAscOrder(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $query = new Query($driver);
        $query->insertMany('revisionable_articles_revisions', [
            [
                'article_id' => 1,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'V1']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 2,
                'data' => json_encode(['title' => 'V2']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ]
        ]);

        $article = RevisionableArticle::one($id);
        $revisions = $article->revisions('asc');

        $this->assertEquals(1, $revisions[0]->revision_number);
        $this->assertEquals(2, $revisions[1]->revision_number);
    }

    /**
     * Test findRevision() returns specific revision
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function findRevisionReturnsSpecificRevision(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $query = new Query($driver);
        $query->insertMany('revisionable_articles_revisions', [
            [
                'article_id' => 1,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'Version 1', 'content' => 'C1']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 2,
                'data' => json_encode(['title' => 'Version 2', 'content' => 'C2']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ]
        ]);

        $article = RevisionableArticle::one($id);
        $revision = $article->findRevision(1);

        $this->assertNotNull($revision);
        $this->assertEquals(1, $revision->revision_number);
        $this->assertEquals('Version 1', $revision->data['title']);
    }

    /**
     * Test findRevision() returns null for non-existent revision
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function findRevisionReturnsNullForNonExistent(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $article = RevisionableArticle::one($id);
        $revision = $article->findRevision(999);

        $this->assertNull($revision);
    }

    /**
     * Test latestRevision() returns most recent
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function latestRevisionReturnsMostRecent(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $query = new Query($driver);
        $query->insertMany('revisionable_articles_revisions', [
            [
                'article_id' => 1,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'V1']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 2,
                'data' => json_encode(['title' => 'V2']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 3,
                'data' => json_encode(['title' => 'V3']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ]
        ]);

        $article = RevisionableArticle::one($id);
        $latest = $article->latestRevision();

        $this->assertNotNull($latest);
        $this->assertEquals(3, $latest->revision_number);
        $this->assertEquals('V3', $latest->data['title']);
    }

    /**
     * Test latestRevision() returns null when no revisions exist
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function latestRevisionReturnsNullWhenNoRevisions(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $article = RevisionableArticle::one($id);
        $latest = $article->latestRevision();

        $this->assertNull($latest);
    }

    /**
     * Test getRevisionCount() returns correct count
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getRevisionCountReturnsCorrectCount(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $query = new Query($driver);
        $query->insertMany('revisionable_articles_revisions', [
            [
                'article_id' => 1,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'V1']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 2,
                'data' => json_encode(['title' => 'V2']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ]
        ]);

        $article = RevisionableArticle::one($id);

        $this->assertEquals(2, $article->getRevisionCount());
    }

    /**
     * Test getRevisionCount() returns 0 for no revisions
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getRevisionCountReturnsZeroForNoRevisions(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $article = RevisionableArticle::one($id);

        $this->assertEquals(0, $article->getRevisionCount());
    }

    // =========================================================================
    // Restore Revision Tests
    // =========================================================================

    /**
     * Test restoreRevision() restores data
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function restoreRevisionRestoresData(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Current Title', 'content' => 'Current Content', 'state' => 1],
        ]);
        $id = 1;

        $query = new Query($driver);
        $query->insertMany('revisionable_articles_revisions', [
            [
                'article_id' => 1,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'Old Title', 'content' => 'Old Content', 'state' => 1]),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ]
        ]);

        $article = RevisionableArticle::one($id);
        $result = $article->restoreRevision(1);

        $this->assertTrue($result);

        // Reload and verify
        $article = RevisionableArticle::one($id);
        $this->assertEquals('Old Title', $article->get('title'));
        $this->assertEquals('Old Content', $article->get('content'));
    }

    /**
     * Test restoreRevision() creates new revision by default
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function restoreRevisionCreatesNewRevision(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Current Title', 'content' => 'Current Content', 'state' => 1],
        ]);
        $id = 1;

        $query = new Query($driver);
        $query->insertMany('revisionable_articles_revisions', [
            [
                'article_id' => 1,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'Old Title', 'content' => 'Old Content', 'state' => 1]),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ]
        ]);

        $article = RevisionableArticle::one($id);
        $article->restoreRevision(1, true);

        // Should now have 2 revisions (original + pre-restore snapshot)
        $this->assertEquals(2, $article->getRevisionCount());
    }

    /**
     * Test restoreRevision() without creating new revision
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function restoreRevisionWithoutNewRevision(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Current Title', 'content' => 'Current Content', 'state' => 1],
        ]);
        $id = 1;

        $query = new Query($driver);
        $query->insertMany('revisionable_articles_revisions', [
            [
                'article_id' => 1,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'Old Title', 'content' => 'Old Content', 'state' => 1]),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ]
        ]);

        $article = RevisionableArticle::one($id);
        $article->restoreRevision(1, false);

        // Should still have just 1 revision
        $this->assertEquals(1, $article->getRevisionCount());
    }

    /**
     * Test restoreRevision() fails for non-existent revision
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function restoreRevisionFailsForNonExistent(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $article = RevisionableArticle::one($id);
        $result = $article->restoreRevision(999);

        $this->assertFalse($result);
    }

    // =========================================================================
    // Diff Tests
    // =========================================================================

    /**
     * Test diffRevisions() shows changes between revisions
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function diffRevisionsShowsChanges(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $query = new Query($driver);
        $query->insertMany('revisionable_articles_revisions', [
            [
                'article_id' => 1,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'Title v1', 'content' => 'Same content', 'state' => 1]),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 2,
                'data' => json_encode(['title' => 'Title v2', 'content' => 'Same content', 'state' => 0]),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ]
        ]);

        $article = RevisionableArticle::one($id);
        $diff = $article->diffRevisions(1, 2);

        $this->assertArrayHasKey('title', $diff);
        $this->assertEquals('Title v1', $diff['title']['old']);
        $this->assertEquals('Title v2', $diff['title']['new']);

        $this->assertArrayHasKey('state', $diff);
        $this->assertEquals(1, $diff['state']['old']);
        $this->assertEquals(0, $diff['state']['new']);

        // content didn't change, should not be in diff
        $this->assertArrayNotHasKey('content', $diff);
    }

    /**
     * Test diffRevisions() returns empty for non-existent revisions
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function diffRevisionsReturnsEmptyForNonExistent(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $article = RevisionableArticle::one($id);
        $diff = $article->diffRevisions(1, 2);

        $this->assertEmpty($diff);
    }

    /**
     * Test diffFromRevision() compares revision to current state
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function diffFromRevisionComparesToCurrentState(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'New Title', 'content' => 'New Content', 'state' => 1],
        ]);
        $id = 1;

        $query = new Query($driver);
        $query->insertMany('revisionable_articles_revisions', [
            [
                'article_id' => 1,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'Old Title', 'content' => 'New Content', 'state' => 1]),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ]
        ]);

        $article = RevisionableArticle::one($id);
        $diff = $article->diffFromRevision(1);

        $this->assertArrayHasKey('title', $diff);
        $this->assertEquals('Old Title', $diff['title']['old']);
        $this->assertEquals('New Title', $diff['title']['new']);

        // content is the same
        $this->assertArrayNotHasKey('content', $diff);
    }

    /**
     * Test hasChangedSinceLastRevision() detects changes
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function hasChangedSinceLastRevisionDetectsChanges(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Original Title', 'content' => 'Original Content', 'state' => 1],
        ]);
        $id = 1;

        $query = new Query($driver);
        $query->insertMany('revisionable_articles_revisions', [
            [
                'article_id' => 1,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'Original Title', 'content' => 'Original Content', 'state' => 1]),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ]
        ]);

        $article = RevisionableArticle::one($id);

        // No changes yet
        $this->assertFalse($article->hasChangedSinceLastRevision());

        // Make a change
        $article->set('title', 'Modified Title');

        $this->assertTrue($article->hasChangedSinceLastRevision());
    }

    /**
     * Test hasChangedSinceLastRevision() returns true when no revisions exist
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function hasChangedSinceLastRevisionReturnsTrueWhenNoRevisions(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $article = RevisionableArticle::one($id);

        $this->assertTrue($article->hasChangedSinceLastRevision());
    }

    // =========================================================================
    // Delete/Prune Tests
    // =========================================================================

    /**
     * Test deleteRevisions() removes all revisions
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function deleteRevisionsRemovesAll(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $query = new Query($driver);
        $query->insertMany('revisionable_articles_revisions', [
            [
                'article_id' => 1,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'V1']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 2,
                'data' => json_encode(['title' => 'V2']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 3,
                'data' => json_encode(['title' => 'V3']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ]
        ]);

        $article = RevisionableArticle::one($id);
        $result = $article->deleteRevisions();

        $this->assertTrue($result);
        $this->assertEquals(0, $article->getRevisionCount());
    }

    /**
     * Test pruneOldRevisions() keeps only recent revisions
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function pruneOldRevisionsKeepsRecent(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $query = new Query($driver);
        $query->insertMany('revisionable_articles_revisions', [
            [
                'article_id' => 1,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'V1']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 2,
                'data' => json_encode(['title' => 'V2']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 3,
                'data' => json_encode(['title' => 'V3']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 4,
                'data' => json_encode(['title' => 'V4']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 5,
                'data' => json_encode(['title' => 'V5']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ]
        ]);

        $article = RevisionableArticle::one($id);
        $deleted = $article->pruneOldRevisions(2);

        $this->assertEquals(3, $deleted);
        $this->assertEquals(2, $article->getRevisionCount());

        // Check that newest revisions remain
        $remaining = $article->revisions('desc');
        $this->assertEquals(5, $remaining[0]->revision_number);
        $this->assertEquals(4, $remaining[1]->revision_number);
    }

    /**
     * Test pruneOldRevisions() does nothing when under limit
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function pruneOldRevisionsDoesNothingWhenUnderLimit(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Test Article', 'content' => 'Content', 'state' => 1],
        ]);
        $id = 1;

        $query = new Query($driver);
        $query->insertMany('revisionable_articles_revisions', [
            [
                'article_id' => 1,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'V1']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 2,
                'data' => json_encode(['title' => 'V2']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ]
        ]);

        $article = RevisionableArticle::one($id);
        $deleted = $article->pruneOldRevisions(5);

        $this->assertEquals(0, $deleted);
        $this->assertEquals(2, $article->getRevisionCount());
    }

    /**
     * Test static pruneRevisions() prunes across all records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function staticPruneRevisionsAcrossAllRecords(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        // Create two articles with revisions
        $query = new Query($driver);
        $query->insertMany('revisionable_articles', [
            ['title' => 'Article 1', 'content' => 'Content', 'state' => 1],
            ['title' => 'Article 2', 'content' => 'Content', 'state' => 1]
        ]);
        $id1 = 1;
        $id2 = 2;

        $query = new Query($driver);
        $query->insertMany('revisionable_articles_revisions', [
            [
                'article_id' => 1,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'A1-V1']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 2,
                'data' => json_encode(['title' => 'A1-V2']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 1,
                'revision_number' => 3,
                'data' => json_encode(['title' => 'A1-V3']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 2,
                'revision_number' => 1,
                'data' => json_encode(['title' => 'A2-V1']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 2,
                'revision_number' => 2,
                'data' => json_encode(['title' => 'A2-V2']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 2,
                'revision_number' => 3,
                'data' => json_encode(['title' => 'A2-V3']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ],
            [
                'article_id' => 2,
                'revision_number' => 4,
                'data' => json_encode(['title' => 'A2-V4']),
                'created' => '2024-01-15 10:00:00',
                'created_by' => 1,
                'log' => null
            ]
        ]);

        // Prune to keep 2 per record
        $deleted = RevisionableArticle::pruneRevisions(2);

        // Article 1: 3 - 2 = 1 deleted
        // Article 2: 4 - 2 = 2 deleted
        $this->assertEquals(3, $deleted);

        // Verify counts
        $article1 = RevisionableArticle::one($id1);
        $article2 = RevisionableArticle::one($id2);

        $this->assertEquals(2, $article1->getRevisionCount());
        $this->assertEquals(2, $article2->getRevisionCount());
    }

    // =========================================================================
    // Configuration Tests
    // =========================================================================

    /**
     * Test getRevisionTable() returns correct table name
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getRevisionTableReturnsCorrectTableName(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $article = new RevisionableArticle();
        $this->assertEquals('revisionable_articles_revisions', $article->getRevisionTable());

        $post = new CustomRevisionablePost();
        $this->assertEquals('post_history', $post->getRevisionTable());
    }

    /**
     * Test getRevisionForeignKey() returns correct key
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getRevisionForeignKeyReturnsCorrectKey(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $article = new RevisionableArticle();
        $this->assertEquals('article_id', $article->getRevisionForeignKey());

        $post = new CustomRevisionablePost();
        $this->assertEquals('post_id', $post->getRevisionForeignKey());
    }

    /**
     * Test getRevisionableFields() returns configured fields
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getRevisionableFieldsReturnsConfiguredFields(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $post = new CustomRevisionablePost();

        // CustomRevisionablePost limits to title and body
        $fields = $post->getRevisionableFields();

        $this->assertContains('title', $fields);
        $this->assertContains('body', $fields);
        $this->assertNotContains('author_id', $fields);
        $this->assertNotContains('status', $fields);
    }

    /**
     * Test custom column configuration works
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function customConfigurationWorks(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        // Insert a post
        $query = new Query($driver);
        $query->insertMany('revisionable_posts', [
            ['title' => 'Post Title', 'body' => 'Post Body', 'author_id' => 1, 'status' => 1],
        ]);
        $id = 1;

        $post = CustomRevisionablePost::one($id);
        $result = $post->createRevision('Initial version');

        $this->assertTrue($result);

        // Verify revision was created in custom table
        $this->assertEquals(1, $post->getRevisionCount());

        $revision = $post->findRevision(1);
        $this->assertNotNull($revision);
        $this->assertEquals(1, $revision->revision_number);

        // Should only contain title and body (configured revisionable fields)
        $this->assertArrayHasKey('title', $revision->data);
        $this->assertArrayHasKey('body', $revision->data);
        $this->assertArrayNotHasKey('author_id', $revision->data);
        $this->assertArrayNotHasKey('status', $revision->data);
    }

    /**
     * Test getMaxRevisions() returns configured value
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getMaxRevisionsReturnsConfiguredValue(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        $article = new RevisionableArticle();
        $this->assertEquals(0, $article->getMaxRevisions()); // Default unlimited

        $post = new CustomRevisionablePost();
        $this->assertEquals(5, $post->getMaxRevisions());
    }

    /**
     * Test auto-prune when maxRevisions is set
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function autoPruneWhenMaxRevisionsSet(string $dbName, Driver $driver): void
    {
        $this->setupTestData($driver);

        // Insert a post
        $query = new Query($driver);
        $query->insertMany('revisionable_posts', [
            ['title' => 'Post', 'body' => 'Body', 'author_id' => null, 'status' => 1],
        ]);
        $id = 1;

        $post = CustomRevisionablePost::one($id);

        // Create 7 revisions (max is 5)
        for ($i = 1; $i <= 7; $i++) {
            $post->set('title', "Title v$i");
            $post->createRevision("Version $i");
        }

        // Get the actual count - with auto-prune after each creation
        // and maxRevisions=5, we should have at most 5 revisions
        $count = $post->getRevisionCount();

        // The count should be <= maxRevisions (5)
        // Note: Due to auto-prune being called after each createRevision,
        // older revisions are pruned to maintain the max limit
        $this->assertLessThanOrEqual(5, $count);
        $this->assertGreaterThan(0, $count);

        // If we have revisions, the latest should be revision #7
        if ($count > 0) {
            $latest = $post->latestRevision();
            if ($latest) {
                $this->assertEquals(7, $latest->revision_number);
            }
        }
    }

    /**
     * Clean up after all tests
     */
    public static function tearDownAfterClass(): void
    {
        Relational::clearBootedModels();
        Query::purgeCache();
        parent::tearDownAfterClass();
    }
}
