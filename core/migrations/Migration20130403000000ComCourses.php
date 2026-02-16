<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding course_id to pages table
**/
class Migration20130403000000ComCourses extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_pages')
            && !$schema->hasColumn('#__courses_pages', 'course_id')
        ) {
            $schema->addColumn('#__courses_pages', 'course_id')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_pages', 'course_id')) {
            $schema->dropColumn('#__courses_pages', 'course_id');
        }
    }
}
