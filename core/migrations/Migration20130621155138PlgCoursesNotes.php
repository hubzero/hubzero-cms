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
class Migration20130621155138PlgCoursesNotes extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_member_notes')
            && !$schema->hasColumn('#__courses_member_notes', 'access')
        ) {
            $schema->addColumn('#__courses_member_notes', 'access')->tinyInteger(2)->notNull()->default(0);
        }
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_member_notes', 'access')) {
            $schema->dropColumn('#__courses_member_notes', 'access');
        }
    }
}
