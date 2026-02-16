<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding an override field to courses grade book
  *
**/
class Migration20130731170234ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_grade_book')
            && !$schema->hasColumn('#__courses_grade_book', 'override')
            && $schema->hasColumn('#__courses_grade_book', 'scope_id')
        ) {
            $schema->addColumn('#__courses_grade_book', 'override')
                ->decimal(5, 2)
                ->nullable()
                ->default(null)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_grade_book', 'override')) {
            $schema->dropColumn('#__courses_grade_book', 'override');
        }
    }
}
