<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding rate limiting table
 *
*/
class Migration20150629100000ComGeosearch extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__geosearch_markers')) {
            $schema->createTable('#__geosearch_markers')
                ->integer('id')->nullable()
                ->string('scope', 255)->nullable()
                ->integer('scope_id')->nullable()
                ->string('addressLatitude', 255)->nullable()
                ->string('addressLongitude', 255)->nullable()
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

        $schema->dropTable('#__geosearch_markers');
    }
}
