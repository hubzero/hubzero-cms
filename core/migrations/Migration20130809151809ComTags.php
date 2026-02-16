<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding missing tags_log table
 *
*/
class Migration20130809151809ComTags extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__tags_log')) {
            $schema->createTable('#__tags_log')
                ->integer('id', ['autoIncrement' => true])
                ->integer('tag_id')->default(0)
                ->datetime('timestamp')->default('0000-00-00 00:00:00')
                ->integer('user_id')->default(0)
                ->string('action', 50)->nullable()
                ->text('comments')->nullable()
                ->integer('actorid')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__tags_log');
    }
}
