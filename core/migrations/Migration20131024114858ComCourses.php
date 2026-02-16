<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding first_visit column to courses_members
**/
class Migration20131024114858ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_members')
            && !$schema->hasColumn('#__courses_members', 'first_visit')
        ) {
            $schema->addColumn('#__courses_members', 'first_visit')
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

        if ($schema->hasColumn('#__courses_members', 'first_visit')) {
            $schema->dropColumn('#__courses_members', 'first_visit');
        }
    }
}
