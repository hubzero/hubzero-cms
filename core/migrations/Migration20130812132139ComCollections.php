<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indices to pdf2form tables
 *
*/
class Migration20130812132139ComCollections extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__collections')) {
            $schema->createTable('#__collections')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('title', 255)->default('')
                ->string('alias', 255)
                ->integer('object_id')->default(0)
                ->string('object_type', 150)->default('')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->tinyInteger('state')->default(1)
                ->tinyInteger('access')->default(0)
                ->tinyInteger('is_default')->default(0)
                ->mediumText('description')
                ->integer('positive')->default(0)
                ->integer('negative')->default(0)
                ->primaryKey('id')
                ->index('idx_objectified', ['object_type', 'object_id'])
                ->index('idx_state', 'state')
                ->index('idx_access', 'access')
                ->index('idx_createdby', 'created_by')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__collections_assets')) {
            $schema->createTable('#__collections_assets')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('item_id')->default(0)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->string('filename', 255)->default('')
                ->mediumText('description')
                ->tinyInteger('state')->default(0)
                ->string('type', 50)->default('file')
                ->tinyInteger('ordering')->default(0)
                ->primaryKey('id')
                ->index('idx_item_id', 'item_id')
                ->index('idx_created_by', 'created_by')
                ->index('idx_state', 'state')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__collections_following')) {
            $schema->createTable('#__collections_following')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('follower_type', 150)
                ->integer('follower_id')->default(0)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->string('following_type', 150)->default('')
                ->integer('following_id')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__collections_items')) {
            $schema->createTable('#__collections_items')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('title', 255)->default('')
                ->mediumText('description')
                ->string('url', 255)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->integer('modified_by')->default(0)
                ->tinyInteger('state')->default(1)
                ->tinyInteger('access')->default(0)
                ->integer('positive')->default(0)
                ->integer('negative')->default(0)
                ->string('type', 150)->default('')
                ->integer('object_id')->default(0)
                ->primaryKey('id')
                ->index('idx_state', 'state')
                ->index('idx_created_by', 'created_by')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__collections_posts')) {
            $schema->createTable('#__collections_posts')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->integer('collection_id')->default(0)
                ->integer('item_id')->default(0)
                ->mediumText('description')
                ->tinyInteger('original')->default(0)
                ->primaryKey('id')
                ->index('idx_collection_id', 'collection_id')
                ->index('idx_item_id', 'item_id')
                ->index('idx_original', 'original')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__collections_votes')) {
            $schema->createTable('#__collections_votes')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('user_id')->default(0)
                ->integer('item_id')->default(0)
                ->datetime('voted')->default('0000-00-00 00:00:00')
                ->primaryKey('id')
                ->index('idx_item_user', ['item_id', 'user_id'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        $this->addComponentEntry('Collections');
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__collections');
        $schema->dropTable('#__collections_assets');
        $schema->dropTable('#__collections_following');
        $schema->dropTable('#__collections_items');
        $schema->dropTable('#__collections_posts');
        $schema->dropTable('#__collections_votes');

        $this->deleteComponentEntry('Collections');
    }
}
