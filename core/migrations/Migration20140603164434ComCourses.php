<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating table #__courses_progress_factors
 *
*/
class Migration20140603164434ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__courses_progress_factors')) {
            $schema->createTable('#__courses_progress_factors')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('section_id')
                ->integer('asset_id')
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

        $schema->dropTable('#__courses_progress_factors');
    }
}
