<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add #__tool_version_zone table
 *
*/
class Migration20140421135022ComTools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__tool_version_zone')) {
            $schema->createTable('#__tool_version_zone')
                ->integer('id', ['autoIncrement' => true])
                ->integer('tool_version_id')
                ->integer('zone_id')
                ->datetime('publish_up')->nullable()
                ->datetime('publish_down')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
