<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating table #__tool_handlers
 *
*/
class Migration20160217014424ComTools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__tool_handlers')) {
            $schema->createTable('#__tool_handlers')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('tool_id')
                ->string('prompt', 255)->default('')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__tool_handler_rules')) {
            $schema->createTable('#__tool_handler_rules')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('handler_id')
                ->string('extension', 10)->default('')
                ->string('quantity', 10)->default('')
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

        $schema->dropTable('#__tool_handlers');
        $schema->dropTable('#__tool_handler_rules');
    }
}
