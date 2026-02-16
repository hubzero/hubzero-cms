<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding offering_id to notes
 *
*/
class Migration20130703075132ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_pages', 'porder')) {
            $schema->alterTable('#__courses_pages')->renameColumn('porder', 'ordering')
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

        if ($schema->hasColumn('#__courses_pages', 'ordering')) {
            $schema->renameColumn('#__courses_pages', 'ordering', 'porder')
                ->integer()
                ->notNull()
                ->default(0)
                ->execute();
        }
    }
}
