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
use Hubzero\Database\Rows;
use Hubzero\Database\Traits\HasUuid;
use Hubzero\Database\Tests\TestModels\UuidArticle;
use Hubzero\Database\Tests\TestModels\CustomUuidArticle;
use Hubzero\Database\Tests\TestModels\UuidToken;

/**
 * HasUuid trait tests
 *
 * Tests for the HasUuid trait that provides UUID support for models.
 */
class HasUuidTest extends AbstractDriverTestCase
{
    /**
     * Return table names created by this test for automatic cleanup
     *
     * @return array Table names
     */
    protected static function getTestTables(): array
    {
        return ['uuid_articles', 'uuid_custom_articles', 'uuid_tokens'];
    }

    /**
     * Create test tables and insert seed data for a database
     *
     * @param Driver $driver Database driver
     * @return void
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        // Create test table with uuid column
        $driver->dropTable('uuid_articles', true);
        $driver->createTable('uuid_articles')
            ->id()
            ->string('uuid', 36)->nullable()
            ->string('title', 255)
            ->string('content', 500)->nullable()
            ->execute();

        // Create table with custom uuid column name
        $driver->dropTable('uuid_custom_articles', true);
        $driver->createTable('uuid_custom_articles')
            ->id()
            ->string('public_id', 36)->nullable()
            ->string('title', 255)
            ->execute();

        // Create table where UUID is the primary key
        // Note: Schema builder doesn't support string as PK, so uuid is just a regular column
        // The model will treat it as PK via $pk property
        $driver->dropTable('uuid_tokens', true);
        $driver->createTable('uuid_tokens')
            ->string('uuid', 36)
            ->string('name', 255)
            ->string('created_at', 50)->nullable()
            ->execute();
    }

    /**
     * Setup test data before each test
     */
    private function setupTestData(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);

        // Clear booted state
        UuidArticle::clearBootedModels();
        CustomUuidArticle::clearBootedModels();
        UuidToken::clearBootedModels();

        Query::purgeCache();

        // Clear and reset tables
        $driver->truncateTable('uuid_articles');
        $driver->truncateTable('uuid_custom_articles');
        $driver->truncateTable('uuid_tokens');
    }

    // =========================================================================
    // UUID Generation Tests
    // =========================================================================

    /**
     * Test that generateUuid() produces valid UUID v4 format
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGenerateUuidProducesValidFormat(string $dbName, Driver $driver)
    {
        $uuid = UuidArticle::generateUuid();

        // UUID v4 format: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid
        );
    }

    /**
     * Test that generateUuid() produces unique values
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGenerateUuidProducesUniqueValues(string $dbName, Driver $driver)
    {
        $uuids = [];
        for ($i = 0; $i < 100; $i++) {
            $uuids[] = UuidArticle::generateUuid();
        }

        $this->assertCount(100, array_unique($uuids));
    }

    /**
     * Test that isValidUuid() validates correct UUID v4
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testIsValidUuidValidatesCorrectly(string $dbName, Driver $driver)
    {
        // Valid UUID v4
        $this->assertTrue(UuidArticle::isValidUuid('550e8400-e29b-41d4-a716-446655440000'));
        $this->assertTrue(UuidArticle::isValidUuid('6ba7b810-9dad-41d4-8b78-9c2e8e2e8b48'));

        // Invalid - wrong format
        $this->assertFalse(UuidArticle::isValidUuid('not-a-uuid'));
        $this->assertFalse(UuidArticle::isValidUuid(''));
        $this->assertFalse(UuidArticle::isValidUuid('12345'));

        // Invalid - UUID v1 (version digit is 1, not 4)
        $this->assertFalse(UuidArticle::isValidUuid('550e8400-e29b-11d4-a716-446655440000'));
    }

    // =========================================================================
    // Auto-Generation Tests
    // =========================================================================

    /**
     * Test that UUID is auto-generated on save for new records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUuidAutoGeneratedOnSave(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $article = new UuidArticle();
        $article->set('title', 'Test Article');
        $result = $article->save();

        // save() returns insert ID on success, not boolean true
        $this->assertNotFalse($result);
        $this->assertNotNull($article->get('uuid'));
        $this->assertTrue(UuidArticle::isValidUuid($article->get('uuid')));
    }

    /**
     * Test that UUID is not overwritten if already set
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUuidNotOverwrittenIfSet(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $customUuid = '550e8400-e29b-41d4-a716-446655440000';

        $article = new UuidArticle();
        $article->set('title', 'Test Article');
        $article->set('uuid', $customUuid);
        $article->save();

        $this->assertEquals($customUuid, $article->get('uuid'));
    }

    /**
     * Test that UUID is not regenerated on update
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUuidNotRegeneratedOnUpdate(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $article = new UuidArticle();
        $article->set('title', 'Test Article');
        $article->save();

        $originalUuid = $article->get('uuid');

        // Update the article
        $article->set('title', 'Updated Title');
        $article->save();

        $this->assertEquals($originalUuid, $article->get('uuid'));
    }

    // =========================================================================
    // Column Configuration Tests
    // =========================================================================

    /**
     * Test getUuidColumn() returns default column name
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetUuidColumnReturnsDefault(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $article = new UuidArticle();
        $this->assertEquals('uuid', $article->getUuidColumn());
    }

    /**
     * Test getUuidColumn() returns custom column name
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetUuidColumnReturnsCustom(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $article = new CustomUuidArticle();
        $this->assertEquals('public_id', $article->getUuidColumn());
    }

    /**
     * Test getQualifiedUuidColumn() returns table-prefixed column
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetQualifiedUuidColumnReturnsTablePrefixed(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $article = new UuidArticle();
        $this->assertEquals('uuid_articles.uuid', $article->getQualifiedUuidColumn());
    }

    // =========================================================================
    // Accessor Tests
    // =========================================================================

    /**
     * Test getUuid() returns the UUID value
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetUuidReturnsValue(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $query = new Query($driver);
        $query->insertMany('uuid_articles', [['title' => 'Test Article', 'uuid' => $uuid]]);

        $article = UuidArticle::one(1);
        $this->assertEquals($uuid, $article->getUuid());
    }

    /**
     * Test getUuid() returns null when not set
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testGetUuidReturnsNullWhenNotSet(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $query = new Query($driver);
        $query->insertMany('uuid_articles', [['title' => 'Test Article', 'uuid' => null]]);

        $article = UuidArticle::one(1);
        $this->assertNull($article->getUuid());
    }

    /**
     * Test hasUuid() returns correct status
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testHasUuidReturnsCorrectStatus(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $query = new Query($driver);
        $query->insertMany('uuid_articles', [['title' => 'With UUID', 'uuid' => $uuid]]);
        $query = new Query($driver);
        $query->insertMany('uuid_articles', [['title' => 'Without UUID', 'uuid' => null]]);

        $withUuid = UuidArticle::one(1);
        $withoutUuid = UuidArticle::one(2);

        $this->assertTrue($withUuid->hasUuid());
        $this->assertFalse($withoutUuid->hasUuid());
    }

    // =========================================================================
    // Find By UUID Tests
    // =========================================================================

    /**
     * Test findByUuid() returns the correct record
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFindByUuidReturnsRecord(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $query = new Query($driver);
        $query->insertMany('uuid_articles', [['title' => 'Test Article', 'uuid' => $uuid]]);

        $article = UuidArticle::findByUuid($uuid);

        $this->assertNotNull($article);
        $this->assertEquals('Test Article', $article->get('title'));
        $this->assertEquals($uuid, $article->get('uuid'));
    }

    /**
     * Test findByUuid() returns null for non-existent UUID
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFindByUuidReturnsNullForNonExistent(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $result = UuidArticle::findByUuid('non-existent-uuid');

        $this->assertNull($result);
    }

    /**
     * Test findByUuidOrFail() returns record when found
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFindByUuidOrFailReturnsRecord(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $query = new Query($driver);
        $query->insertMany('uuid_articles', [['title' => 'Test Article', 'uuid' => $uuid]]);

        $article = UuidArticle::findByUuidOrFail($uuid);

        $this->assertNotNull($article);
        $this->assertEquals('Test Article', $article->get('title'));
    }

    /**
     * Test findByUuidOrFail() throws exception for non-existent UUID
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFindByUuidOrFailThrowsException(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No model');

        UuidArticle::findByUuidOrFail('non-existent-uuid');
    }

    /**
     * Test findManyByUuid() returns multiple records
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFindManyByUuidReturnsMultipleRecords(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $uuid1 = '550e8400-e29b-41d4-a716-446655440001';
        $uuid2 = '550e8400-e29b-41d4-a716-446655440002';
        $uuid3 = '550e8400-e29b-41d4-a716-446655440003';

        $query = new Query($driver);
        $query->insertMany('uuid_articles', [
            ['title' => 'Article 1', 'uuid' => $uuid1],
            ['title' => 'Article 2', 'uuid' => $uuid2],
            ['title' => 'Article 3', 'uuid' => $uuid3]
        ]);

        $articles = UuidArticle::findManyByUuid([$uuid1, $uuid3]);

        $this->assertInstanceOf(Rows::class, $articles);
        $this->assertCount(2, $articles);
    }

    /**
     * Test findManyByUuid() returns empty Rows for empty array
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testFindManyByUuidReturnsEmptyForEmptyArray(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $articles = UuidArticle::findManyByUuid([]);

        $this->assertInstanceOf(Rows::class, $articles);
        $this->assertCount(0, $articles);
    }

    // =========================================================================
    // Query Scope Tests
    // =========================================================================

    /**
     * Test whereUuid() scope filters correctly
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testWhereUuidScopeFilters(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $query = new Query($driver);
        $query->insertMany('uuid_articles', [['title' => 'Target Article', 'uuid' => $uuid]]);
        $query = new Query($driver);
        $otherUuid = '550e8400-e29b-41d4-a716-446655440001';
        $query->insertMany('uuid_articles', [
            ['title' => 'Other Article', 'uuid' => $otherUuid],
        ]);

        $article = UuidArticle::blank()->whereUuid($uuid)->row();

        $this->assertEquals('Target Article', $article->get('title'));
    }

    /**
     * Test whereUuidIn() scope filters correctly
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testWhereUuidInScopeFilters(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $uuid1 = '550e8400-e29b-41d4-a716-446655440001';
        $uuid2 = '550e8400-e29b-41d4-a716-446655440002';
        $uuid3 = '550e8400-e29b-41d4-a716-446655440003';

        $query = new Query($driver);
        $query->insertMany('uuid_articles', [
            ['title' => 'Article 1', 'uuid' => $uuid1],
            ['title' => 'Article 2', 'uuid' => $uuid2],
            ['title' => 'Article 3', 'uuid' => $uuid3]
        ]);

        $articles = UuidArticle::blank()->whereUuidIn([$uuid1, $uuid2])->rows();

        $this->assertCount(2, $articles);
    }

    /**
     * Test whereUuidNotIn() scope filters correctly
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testWhereUuidNotInScopeFilters(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $uuid1 = '550e8400-e29b-41d4-a716-446655440001';
        $uuid2 = '550e8400-e29b-41d4-a716-446655440002';
        $uuid3 = '550e8400-e29b-41d4-a716-446655440003';

        $query = new Query($driver);
        $query->insertMany('uuid_articles', [
            ['title' => 'Article 1', 'uuid' => $uuid1],
            ['title' => 'Article 2', 'uuid' => $uuid2],
            ['title' => 'Article 3', 'uuid' => $uuid3]
        ]);

        $articles = UuidArticle::blank()->whereUuidNotIn([$uuid1, $uuid2])->rows();

        $this->assertCount(1, $articles);
        foreach ($articles as $article) {
            $this->assertEquals('Article 3', $article->get('title'));
        }
    }

    // =========================================================================
    // Utility Method Tests
    // =========================================================================

    /**
     * Test regenerateUuid() creates new UUID
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testRegenerateUuidCreatesNewUuid(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $query = new Query($driver);
        $query->insertMany('uuid_articles', [['title' => 'Test Article', 'uuid' => $uuid]]);

        $article = UuidArticle::one(1);
        $oldUuid = $article->get('uuid');

        $newUuid = $article->regenerateUuid();

        $this->assertNotEquals($oldUuid, $newUuid);
        $this->assertEquals($newUuid, $article->get('uuid'));
        $this->assertTrue(UuidArticle::isValidUuid($newUuid));
    }

    /**
     * Test uuidExists() returns true for existing UUID
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUuidExistsReturnsTrueForExisting(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $query = new Query($driver);
        $query->insertMany('uuid_articles', [['title' => 'Test Article', 'uuid' => $uuid]]);

        $this->assertTrue(UuidArticle::uuidExists($uuid));
    }

    /**
     * Test uuidExists() returns false for non-existing UUID
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUuidExistsReturnsFalseForNonExisting(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $this->assertFalse(UuidArticle::uuidExists('non-existent-uuid'));
    }

    /**
     * Test uuidExists() with exclude ID
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUuidExistsWithExcludeId(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $query = new Query($driver);
        $query->insertMany('uuid_articles', [['title' => 'Test Article', 'uuid' => $uuid]]);
        $id = 1; // First record gets ID=1 after setAutoIncrement(0)

        // Should return false when excluding the owning record
        $this->assertFalse(UuidArticle::uuidExists($uuid, $id));

        // Should return true when excluding a different record
        $this->assertTrue(UuidArticle::uuidExists($uuid, 999));
    }

    // =========================================================================
    // Custom Column Name Tests
    // =========================================================================

    /**
     * Test custom UUID column name works for auto-generation
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCustomColumnNameAutoGeneration(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $article = new CustomUuidArticle();
        $article->set('title', 'Test Article');
        $article->save();

        $this->assertNotNull($article->get('public_id'));
        $this->assertTrue(UuidArticle::isValidUuid($article->get('public_id')));
    }

    /**
     * Test custom UUID column name works for findByUuid
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testCustomColumnNameFindByUuid(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $publicId = '550e8400-e29b-41d4-a716-446655440000';
        $query = new Query($driver);
        $query->insertMany('uuid_custom_articles', [['title' => 'Test Article', 'public_id' => $publicId]]);

        $article = CustomUuidArticle::findByUuid($publicId);

        $this->assertNotNull($article);
        $this->assertEquals('Test Article', $article->get('title'));
    }

    // =========================================================================
    // UUID as Primary Key Tests
    // =========================================================================

    /**
     * Test usesUuidAsPrimaryKey() returns correct value
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUsesUuidAsPrimaryKeyReturnsCorrect(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $article = new UuidArticle();
        $token = new UuidToken();

        $this->assertFalse($article->usesUuidAsPrimaryKey());
        $this->assertTrue($token->usesUuidAsPrimaryKey());
    }

    /**
     * Test model with UUID as primary key can be saved
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUuidAsPrimaryKeyCanBeSaved(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $token = new UuidToken();
        $token->set('name', 'API Token');
        $result = $token->save();

        // save() returns insert ID on success, not boolean true
        $this->assertNotFalse($result);
        $this->assertNotNull($token->get('uuid'));
        $this->assertTrue(UuidToken::isValidUuid($token->get('uuid')));
    }

    /**
     * Test model with UUID as primary key can be found
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function testUuidAsPrimaryKeyCanBeFound(string $dbName, Driver $driver)
    {
        $this->setupTestData($driver);

        $uuid = '550e8400-e29b-41d4-a716-446655440000';

        // Insert directly to control the UUID
        $query = new Query($driver);
        $query->insertMany('uuid_tokens', [
            ['uuid' => $uuid, 'name' => 'API Token', 'created_at' => '2025-01-15 10:00:00'],
        ]);

        $token = UuidToken::findByUuid($uuid);

        $this->assertNotNull($token);
        $this->assertEquals('API Token', $token->get('name'));
    }
}
