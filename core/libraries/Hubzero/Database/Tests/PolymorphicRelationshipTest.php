<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Relational;
use Hubzero\Database\Query;
use Hubzero\Database\Relationship\MorphTo;
use Hubzero\Database\Relationship\MorphOne;
use Hubzero\Database\Relationship\MorphMany;
use Hubzero\Database\Relationship\MorphToMany;
use Hubzero\Database\Tests\TestModels\PolyImage;
use Hubzero\Database\Tests\TestModels\PolyVideo;
use Hubzero\Database\Tests\TestModels\PolyActivityLog;
use Hubzero\Database\Tests\TestModels\PolyTagModel;
use Hubzero\Database\Tests\TestModels\PolyPostModel;

/**
 * Polymorphic relationship tests
 *
 * Tests the structure and behavior of polymorphic relationship classes
 * with real database tables and data.
 */
#[CoversClass(MorphTo::class)]
#[CoversClass(MorphOne::class)]
#[CoversClass(MorphMany::class)]
#[CoversClass(MorphToMany::class)]
class PolymorphicRelationshipTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['poly_taggables', 'poly_activity_logs', 'poly_images', 'poly_tags', 'poly_videos', 'poly_posts'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        $driver->dropTable('poly_taggables', true);
        $driver->dropTable('poly_activity_logs', true);
        $driver->dropTable('poly_images', true);
        $driver->dropTable('poly_tags', true);
        $driver->dropTable('poly_videos', true);
        $driver->dropTable('poly_posts', true);

        $schema = $driver->schema();

        $schema->createTable('poly_videos')
            ->id()
            ->string('title', 255)
            ->execute();

        $schema->createTable('poly_posts')
            ->id()
            ->string('title', 255)
            ->execute();

        $schema->createTable('poly_images')
            ->id()
            ->string('imageable_type', 255)
            ->integer('imageable_id')
            ->string('filename', 255)
            ->execute();

        $schema->createTable('poly_activity_logs')
            ->id()
            ->string('loggable_type', 255)
            ->integer('loggable_id')
            ->string('action', 255)
            ->execute();

        $schema->createTable('poly_tags')
            ->id()
            ->string('name', 255)
            ->execute();

        $schema->createTable('poly_taggables')
            ->id()
            ->integer('tag_id')
            ->string('taggable_type', 255)
            ->integer('taggable_id')
            ->execute();
    }

    private function prepareModels(Driver $driver): void
    {
        Relational::setDefaultConnection($driver);
        PolyVideo::clearBootedModels();
        PolyPostModel::clearBootedModels();
        PolyImage::clearBootedModels();
        PolyActivityLog::clearBootedModels();
        PolyTagModel::clearBootedModels();
        Query::purgeCache();

        Relational::morphMap([
            'video' => PolyVideo::class,
            'post'  => PolyPostModel::class,
        ]);
    }

    private function cleanAllData(Driver $driver): void
    {
        $driver->truncateTable('poly_taggables');
        $driver->truncateTable('poly_activity_logs');
        $driver->truncateTable('poly_images');
        $driver->truncateTable('poly_tags');
        $driver->truncateTable('poly_videos');
        $driver->truncateTable('poly_posts');
    }

    protected function tearDown(): void
    {
        Relational::clearMorphMap();
        parent::tearDown();
    }

    // =========================================================================
    // Class Structure Tests
    // =========================================================================

    #[Test]
    public function morphToExtendsRelationship(): void
    {
        $reflection = new \ReflectionClass(MorphTo::class);
        $this->assertTrue(
            $reflection->isSubclassOf('Hubzero\Database\Relationship\Relationship'),
            'MorphTo should extend Relationship'
        );
    }

    #[Test]
    public function morphOneExtendsRelationship(): void
    {
        $reflection = new \ReflectionClass(MorphOne::class);
        $this->assertTrue(
            $reflection->isSubclassOf('Hubzero\Database\Relationship\Relationship'),
            'MorphOne should extend Relationship'
        );
    }

    #[Test]
    public function morphManyExtendsMorphOne(): void
    {
        $reflection = new \ReflectionClass(MorphMany::class);
        $this->assertTrue(
            $reflection->isSubclassOf(MorphOne::class),
            'MorphMany should extend MorphOne'
        );
    }

    #[Test]
    public function morphToManyExtendsOneToManyThrough(): void
    {
        $reflection = new \ReflectionClass(MorphToMany::class);
        $this->assertTrue(
            $reflection->isSubclassOf('Hubzero\Database\Relationship\OneToManyThrough'),
            'MorphToMany should extend OneToManyThrough'
        );
    }

    // =========================================================================
    // MorphTo Constructor Tests
    // =========================================================================

    #[Test]
    public function morphToConstructorSetsColumnNames(): void
    {
        $model = PolyImage::blank();
        $morphTo = new MorphTo($model, 'commentable');

        $this->assertEquals('commentable_type', $morphTo->getMorphType());
        $this->assertEquals('commentable_id', $morphTo->getMorphId());
        $this->assertEquals('commentable', $morphTo->getMorphName());
    }

    #[Test]
    public function morphToAcceptsCustomColumnNames(): void
    {
        $model = PolyImage::blank();
        $morphTo = new MorphTo($model, 'parent', 'parent_class', 'parent_pk');

        $this->assertEquals('parent_class', $morphTo->getMorphType());
        $this->assertEquals('parent_pk', $morphTo->getMorphId());
    }

    #[Test]
    public function morphToJoinThrowsException(): void
    {
        $model = PolyImage::blank();
        $morphTo = new MorphTo($model, 'commentable');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Join is not supported for polymorphic MorphTo relationships');

        $morphTo->join();
    }

    // =========================================================================
    // Morph Map Tests
    // =========================================================================

    #[Test]
    public function morphMapCanRegisterMappings(): void
    {
        Relational::morphMap([
            'post'  => 'App\Models\Post',
            'video' => 'App\Models\Video',
        ]);

        $map = Relational::getMorphMap();

        $this->assertArrayHasKey('post', $map);
        $this->assertArrayHasKey('video', $map);
        $this->assertEquals('App\Models\Post', $map['post']);
        $this->assertEquals('App\Models\Video', $map['video']);
    }

    #[Test]
    public function morphMapMergesByDefault(): void
    {
        Relational::morphMap(['post' => 'App\Models\Post']);
        Relational::morphMap(['video' => 'App\Models\Video']);

        $map = Relational::getMorphMap();

        $this->assertCount(2, $map);
        $this->assertArrayHasKey('post', $map);
        $this->assertArrayHasKey('video', $map);
    }

    #[Test]
    public function morphMapCanReplaceInsteadOfMerge(): void
    {
        Relational::morphMap(['post' => 'App\Models\Post']);
        Relational::morphMap(['video' => 'App\Models\Video'], false);

        $map = Relational::getMorphMap();

        $this->assertCount(1, $map);
        $this->assertArrayNotHasKey('post', $map);
        $this->assertArrayHasKey('video', $map);
    }

    #[Test]
    public function clearMorphMapRemovesAllMappings(): void
    {
        Relational::morphMap([
            'post'  => 'App\Models\Post',
            'video' => 'App\Models\Video',
        ]);

        Relational::clearMorphMap();
        $this->assertEmpty(Relational::getMorphMap());
    }

    // =========================================================================
    // Relational Method Existence Tests
    // =========================================================================

    #[Test]
    public function relationalHasPolymorphicMethods(): void
    {
        $reflection = new \ReflectionClass(Relational::class);

        $methods = [
            'morphTo', 'morphOne', 'morphMany', 'morphToMany',
            'morphedByMany', 'morphMap', 'getMorphMap', 'clearMorphMap',
        ];
        foreach ($methods as $method) {
            $this->assertTrue($reflection->hasMethod($method), "Relational should have method: {$method}");
        }
    }

    // =========================================================================
    // Import/Namespace Tests
    // =========================================================================

    #[Test]
    public function morphRelationshipClassesAreImportedInRelational(): void
    {
        $filePath = dirname(__DIR__) . '/Relational.php';
        $content = file_get_contents($filePath);

        $imports = [
            'use Hubzero\Database\Relationship\MorphTo;',
            'use Hubzero\Database\Relationship\MorphOne;',
            'use Hubzero\Database\Relationship\MorphMany;',
            'use Hubzero\Database\Relationship\MorphToMany;',
        ];

        foreach ($imports as $import) {
            $this->assertStringContainsString($import, $content, "{$import} should be in Relational.php");
        }
    }

    // =========================================================================
    // Cascade & OnCondition Inheritance Tests
    // =========================================================================

    #[Test]
    public function morphOneInheritsCascadeAndConditionMethods(): void
    {
        $reflection = new \ReflectionClass(MorphOne::class);

        $methods = [
            'cascadeOnDelete', 'cascadeOnSave', 'orphanRemoval',
            'onCondition', 'orOnCondition', 'onConditionIn',
            'onConditionNotIn', 'onConditionNull', 'onConditionNotNull',
        ];
        foreach ($methods as $method) {
            $this->assertTrue($reflection->hasMethod($method), "MorphOne should have method: {$method}");
        }
    }

    #[Test]
    public function morphManyInheritsCascadeAndConditionMethods(): void
    {
        $reflection = new \ReflectionClass(MorphMany::class);

        $methods = ['cascadeOnDelete', 'cascadeOnSave', 'orphanRemoval', 'onCondition', 'onConditionIn'];
        foreach ($methods as $method) {
            $this->assertTrue($reflection->hasMethod($method), "MorphMany should have method: {$method}");
        }
    }

    // =========================================================================
    // Functional: MorphOne (Video has one Image)
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function morphOneReturnsRelatedModel(string $dbName, Driver $driver): void
    {
        $this->prepareModels($driver);
        $this->cleanAllData($driver);

        // Create a video
        $video = PolyVideo::blank();
        $video->set('title', 'Test Video');
        $video->save();
        $videoId = $video->get('id');

        // Create an image belonging to this video
        $image = PolyImage::blank();
        $image->set('filename', 'thumb.jpg');
        $image->set('imageable_type', 'video');
        $image->set('imageable_id', $videoId);
        $image->save();

        // Load the video and access its image
        PolyVideo::clearBootedModels();
        $loaded = PolyVideo::one($videoId);
        $this->assertNotNull($loaded, "[$dbName] Video should be found");

        $relatedImage = $loaded->image;
        $this->assertInstanceOf(PolyImage::class, $relatedImage, "[$dbName] Should return PolyImage");
        $this->assertEquals('thumb.jpg', $relatedImage->get('filename'), "[$dbName]");
    }

    // =========================================================================
    // Functional: MorphMany (Video has many ActivityLogs)
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function morphManyReturnsRelatedCollection(string $dbName, Driver $driver): void
    {
        $this->prepareModels($driver);
        $this->cleanAllData($driver);

        // Create a video
        $video = PolyVideo::blank();
        $video->set('title', 'Activity Video');
        $video->save();
        $videoId = $video->get('id');

        // Create activity logs for the video
        $query = new Query($driver);
        $query->insertMany('poly_activity_logs', [
            ['loggable_type' => 'video', 'loggable_id' => $videoId, 'action' => 'viewed'],
            ['loggable_type' => 'video', 'loggable_id' => $videoId, 'action' => 'shared'],
            ['loggable_type' => 'post',  'loggable_id' => 999,      'action' => 'edited'],
        ]);

        // Load the video and access its activities
        PolyVideo::clearBootedModels();
        $loaded = PolyVideo::one($videoId);
        $activities = $loaded->activities;

        $this->assertCount(2, $activities, "[$dbName] Should only get activities for this video");

        $actions = [];
        foreach ($activities as $activity) {
            $actions[] = $activity->get('action');
        }
        $this->assertContains('viewed', $actions, "[$dbName]");
        $this->assertContains('shared', $actions, "[$dbName]");
    }

    // =========================================================================
    // Functional: MorphTo (Image belongs to Video or Post)
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function morphToResolvesVideoParent(string $dbName, Driver $driver): void
    {
        $this->prepareModels($driver);
        $this->cleanAllData($driver);

        // Create a video
        $video = PolyVideo::blank();
        $video->set('title', 'Parent Video');
        $video->save();

        // Create an image for the video
        $image = PolyImage::blank();
        $image->set('filename', 'video_thumb.jpg');
        $image->set('imageable_type', 'video');
        $image->set('imageable_id', $video->get('id'));
        $image->save();
        $imageId = $image->get('id');

        // Load the image and resolve its parent
        PolyImage::clearBootedModels();
        $loaded = PolyImage::one($imageId);
        $parent = $loaded->imageable;

        $this->assertInstanceOf(PolyVideo::class, $parent, "[$dbName] Should resolve to PolyVideo");
        $this->assertEquals('Parent Video', $parent->get('title'), "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function morphToResolvesPostParent(string $dbName, Driver $driver): void
    {
        $this->prepareModels($driver);
        $this->cleanAllData($driver);

        // Create a post
        $post = PolyPostModel::blank();
        $post->set('title', 'Parent Post');
        $post->save();

        // Create an image for the post
        $image = PolyImage::blank();
        $image->set('filename', 'post_thumb.jpg');
        $image->set('imageable_type', 'post');
        $image->set('imageable_id', $post->get('id'));
        $image->save();
        $imageId = $image->get('id');

        // Load the image and resolve its parent
        PolyImage::clearBootedModels();
        $loaded = PolyImage::one($imageId);
        $parent = $loaded->imageable;

        $this->assertInstanceOf(PolyPostModel::class, $parent, "[$dbName] Should resolve to PolyPostModel");
        $this->assertEquals('Parent Post', $parent->get('title'), "[$dbName]");
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function morphToReturnsNullForMissingParent(string $dbName, Driver $driver): void
    {
        $this->prepareModels($driver);
        $this->cleanAllData($driver);

        // Create an image with no parent
        $image = PolyImage::blank();
        $image->set('filename', 'orphan.jpg');
        $image->set('imageable_type', '');
        $image->set('imageable_id', 0);
        $image->save();
        $imageId = $image->get('id');

        PolyImage::clearBootedModels();
        $loaded = PolyImage::one($imageId);
        $parent = $loaded->imageable;

        $this->assertNull($parent, "[$dbName] Should return null for empty morph columns");
    }

    // =========================================================================
    // Functional: MorphToMany (Video has many Tags through pivot)
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function morphToManyReturnsTags(string $dbName, Driver $driver): void
    {
        $this->prepareModels($driver);
        $this->cleanAllData($driver);

        // Create a video
        $video = PolyVideo::blank();
        $video->set('title', 'Tagged Video');
        $video->save();
        $videoId = $video->get('id');

        // Create tags
        $query = new Query($driver);
        $query->insertMany('poly_tags', [
            ['name' => 'php'],
            ['name' => 'testing'],
            ['name' => 'unrelated'],
        ]);

        // Connect tags to video via pivot table
        $query2 = new Query($driver);
        $query2->insertMany('poly_taggables', [
            ['tag_id' => 1, 'taggable_type' => 'video', 'taggable_id' => $videoId],
            ['tag_id' => 2, 'taggable_type' => 'video', 'taggable_id' => $videoId],
        ]);

        // Load the video and its tags
        PolyVideo::clearBootedModels();
        $loaded = PolyVideo::one($videoId);
        $tags = $loaded->tags;

        $this->assertCount(2, $tags, "[$dbName] Should get 2 tags for this video");

        $tagNames = [];
        foreach ($tags as $tag) {
            $tagNames[] = $tag->get('name');
        }
        $this->assertContains('php', $tagNames, "[$dbName]");
        $this->assertContains('testing', $tagNames, "[$dbName]");
        $this->assertNotContains('unrelated', $tagNames, "[$dbName]");
    }

    // =========================================================================
    // Functional: MorphedByMany (Tag has many Videos through pivot)
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function morphedByManyReturnsVideos(string $dbName, Driver $driver): void
    {
        $this->prepareModels($driver);
        $this->cleanAllData($driver);

        // Create videos
        $query = new Query($driver);
        $query->insertMany('poly_videos', [
            ['title' => 'Video A'],
            ['title' => 'Video B'],
            ['title' => 'Video C'],
        ]);

        // Create a tag
        $tag = PolyTagModel::blank();
        $tag->set('name', 'popular');
        $tag->save();
        $tagId = $tag->get('id');

        // Connect videos to tag via pivot
        $query2 = new Query($driver);
        $query2->insertMany('poly_taggables', [
            ['tag_id' => $tagId, 'taggable_type' => 'video', 'taggable_id' => 1],
            ['tag_id' => $tagId, 'taggable_type' => 'video', 'taggable_id' => 2],
        ]);

        // Load the tag and its videos
        PolyTagModel::clearBootedModels();
        $loaded = PolyTagModel::one($tagId);
        $videos = $loaded->videos;

        $this->assertCount(2, $videos, "[$dbName] Should get 2 videos for this tag");
    }

    // =========================================================================
    // Functional: Polymorphic isolation (different types share pivot)
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function morphToManyIsolatesByType(string $dbName, Driver $driver): void
    {
        $this->prepareModels($driver);
        $this->cleanAllData($driver);

        // Create a video and a post
        $video = PolyVideo::blank();
        $video->set('title', 'Shared Tag Video');
        $video->save();

        $post = PolyPostModel::blank();
        $post->set('title', 'Shared Tag Post');
        $post->save();

        // Create a tag
        $tag = PolyTagModel::blank();
        $tag->set('name', 'shared');
        $tag->save();

        // Tag both the video and the post
        $query = new Query($driver);
        $query->insertMany('poly_taggables', [
            ['tag_id' => $tag->get('id'), 'taggable_type' => 'video', 'taggable_id' => $video->get('id')],
            ['tag_id' => $tag->get('id'), 'taggable_type' => 'post',  'taggable_id' => $post->get('id')],
        ]);

        // Load the tag - videos() should only return the video, not the post
        PolyTagModel::clearBootedModels();
        $loaded = PolyTagModel::one($tag->get('id'));

        $videos = $loaded->videos;
        $this->assertCount(1, $videos, "[$dbName] Should only get videos, not posts");
        $first = $videos->first();
        $this->assertEquals('Shared Tag Video', $first->get('title'), "[$dbName]");

        $posts = $loaded->posts;
        $this->assertCount(1, $posts, "[$dbName] Should only get posts, not videos");
        $first = $posts->first();
        $this->assertEquals('Shared Tag Post', $first->get('title'), "[$dbName]");
    }

    // =========================================================================
    // Functional: MorphOne associate
    // =========================================================================

    #[Test]
    #[DataProvider('databaseProvider')]
    public function morphOneAssociateSetsCorrectColumns(string $dbName, Driver $driver): void
    {
        $this->prepareModels($driver);
        $this->cleanAllData($driver);

        // Create a video
        $video = PolyVideo::blank();
        $video->set('title', 'Associate Video');
        $video->save();

        // Use associate to link an image via the relationship method
        $image = PolyImage::blank();
        $image->set('filename', 'associated.jpg');
        $video->image()->associate($image);

        $this->assertEquals($video->get('id'), $image->get('imageable_id'), "[$dbName] Should set imageable_id");
        $this->assertEquals(
            'video',
            $image->get('imageable_type'),
            "[$dbName] Should set imageable_type to morph map alias"
        );
    }
}
