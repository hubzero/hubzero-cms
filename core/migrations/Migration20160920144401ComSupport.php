<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to drop deprecated support sections table
 *
*/
class Migration20160920144401ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__support_sections');
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__support_sections')) {
            $schema->createTable('#__support_sections')
                ->integer('id', ['autoIncrement' => true])
                ->string('section', 50)->nullable()
                ->primaryKey('id')
                ->engine('InnoDB')
                ->charset('utf8')
                ->execute();
        }
    }
}
