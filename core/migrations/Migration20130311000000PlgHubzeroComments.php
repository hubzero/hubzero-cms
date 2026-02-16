<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating centralized comments table
**/
class Migration20130311000000PlgHubzeroComments extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__item_comments')) {
            $schema->createTable('#__item_comments')
                ->integer('id', ['autoIncrement' => true])
                ->integer('item_id')->default(0)
                ->string('item_type', 150)
                ->text('content')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->integer('modified_by')->default(0)
                ->tinyInteger('anonymous')->default(0)
                ->integer('parent')->default(0)
                ->tinyInteger('notify')->default(0)
                ->tinyInteger('access')->default(0)
                ->tinyInteger('state')->default(0)
                ->integer('positive')->default(0)
                ->integer('negative')->default(0)
                ->integer('rating')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__item_comment_files')) {
            $schema->createTable('#__item_comment_files')
                ->integer('id', ['autoIncrement' => true])
                ->integer('comment_id')->default(0)
                ->string('filename', 100)->nullable()
                ->index('id', 'id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__item_votes')) {
            $schema->createTable('#__item_votes')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('item_id')->default(0)
                ->string('item_type', 255)->nullable()
                ->string('ip', 15)->nullable()
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->tinyInteger('vote')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__item_comments');
        $schema->dropTable('#__item_comment_files');
        $schema->dropTable('#__item_votes');
    }
}
