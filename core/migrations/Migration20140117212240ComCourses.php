<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for adding a column to track whether an asset should have a corresponding gradebook entry or not
  *
**/
class Migration20140117212240ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_assets')
            && $schema->hasColumn('#__courses_assets', 'course_id')
            && !$schema->hasColumn('#__courses_assets', 'graded')
        ) {
            $schema->addColumn('#__courses_assets', 'graded')
                ->tinyInteger(2)
                ->nullable()
                ->default(null)
                ->after('course_id');

            // Mark all assets of type form as graded
            $this->db->getQuery(true)
                ->update('#__courses_assets')
                ->set(['graded' => 1])
                ->where('type', '=', 'form')
                ->execute();
        }

        if (
            $schema->tableExists('#__courses_assets')
            && $schema->hasColumn('#__courses_assets', 'graded')
            && !$schema->hasColumn('#__courses_assets', 'grade_weight')
        ) {
            $schema->addColumn('#__courses_assets', 'grade_weight')
                ->string(255)
                ->notNull()
                ->default('')
                ->after('graded')
                ->execute();

            // Mark all assets of type form as graded
            $this->db->getQuery(true)
                ->update('#__courses_assets')
                ->set(['grade_weight' => Expression::column('subtype')])
                ->where('type', '=', 'form')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__courses_assets') && $schema->hasColumn('#__courses_assets', 'graded')) {
            $schema->dropColumn('#__courses_assets', 'graded');
        }

        if (
            $schema->tableExists('#__courses_assets')
            && $schema->hasColumn('#__courses_assets', 'grade_weight')
        ) {
            $schema->dropColumn('#__courses_assets', 'grade_weight');
        }
    }
}
