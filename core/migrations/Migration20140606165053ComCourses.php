<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating table #__courses_prerequisites
 *
*/
class Migration20140606165053ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__courses_prerequisites')) {
            $schema->createTable('#__courses_prerequisites')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('section_id')->default(0)
                ->string('item_scope', 255)->default('asset')
                ->integer('item_id')->default(0)
                ->string('requisite_scope', 255)->default('asset')
                ->integer('requisite_id')->default(0)
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

        $schema->dropTable('#__courses_prerequisites');
    }
}
