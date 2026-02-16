<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing #__auth_link_data table
 *
*/
class Migration20180419000000Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__auth_link_data')) {
            $schema->createTable('#__auth_link_data')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('link_id')->default(0)
                ->datetime('modified')->nullable()
                ->string('domain_key', 255)->nullable()
                ->text('domain_value')->nullable()
                ->primaryKey('id')
                ->index('idx_link_id', 'link_id')
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

        if ($schema->tableExists('#__auth_link_data')) {
            $schema->dropTable('#__auth_link_data');
        }
    }
}
