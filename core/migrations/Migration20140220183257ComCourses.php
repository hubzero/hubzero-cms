<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding some much needed indices to the courses section_dates table
**/
class Migration20140220183257ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__courses_offering_section_dates')) {
            if (!$schema->hasKey('#__courses_offering_section_dates', 'idx_section_id')) {
                $schema->addIndex('#__courses_offering_section_dates', 'idx_section_id', 'section_id');
            }

            if (!$schema->hasKey('#__courses_offering_section_dates', 'idx_scope_id')) {
                $schema->addIndex('#__courses_offering_section_dates', 'idx_scope_id', 'scope_id');
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__courses_offering_section_dates')) {
            if ($schema->hasKey('#__courses_offering_section_dates', 'idx_section_id')) {
                $schema->dropIndex('#__courses_offering_section_dates', 'idx_section_id');
            }

            if ($schema->hasKey('#__courses_offering_section_dates', 'idx_scope_id')) {
                $schema->dropIndex('#__courses_offering_section_dates', 'idx_scope_id');
            }
        }
    }
}
