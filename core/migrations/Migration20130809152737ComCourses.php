<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for including section id on course pages
  *
**/
class Migration20130809152737ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_pages')
            && !$schema->hasColumn('#__courses_pages', 'section_id')
        ) {
            $schema->addColumn('#__courses_pages', 'section_id')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_pages', 'section_id')) {
            $schema->dropColumn('#__courses_pages', 'section_id');
        }
    }
}
