<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding course member notes table
 *
*/
class Migration20130329000000ComCourses extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__courses_member_notes')) {
            $schema->createTable('#__courses_member_notes')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('scope', 255)->default('')
                ->integer('scope_id')->default(0)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->mediumText('content')
                ->integer('pos_x')->default(0)
                ->integer('pos_y')->default(0)
                ->integer('width')->default(0)
                ->integer('height')->default(0)
                ->tinyInteger('state')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        $this->addPluginEntry('courses', 'notes');
    }

    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__courses_member_notes');

        $this->deletePluginEntry('courses', 'notes');
    }
}
