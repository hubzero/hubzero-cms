<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for add watching table
**/
class Migration20130430112401ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_member_notes')
            && !$schema->hasColumn('#__courses_member_notes', 'timestamp')
        ) {
            $schema->addColumn('#__courses_member_notes', 'timestamp')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_member_notes', 'timestamp')) {
            $schema->dropColumn('#__courses_member_notes', 'timestamp');
        }
    }
}
