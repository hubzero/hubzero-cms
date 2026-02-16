<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing unused sites table
 *
*/
class Migration20190305000000Sites extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__sites')) {
            $schema->dropTable('#__sites');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__sites')) {
            $schema->createTable('#__sites')
                ->id()
                ->string('title', 100)->nullable()
                ->string('category', 100)->nullable()
                ->string('url', 255)->nullable()
                ->string('image', 255)->nullable()
                ->string('teaser', 255)->nullable()
                ->text('description')->nullable()
                ->text('notes')->nullable()
                ->integer('checked_out')->default(0)
                ->datetime('checked_out_time')->nullable()
                ->tinyInteger('published')->default(0)
                ->datetime('published_date')->nullable()
                ->string('state', 30)->nullable()
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
