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
class Migration20130531081238PlgCoursesNotes extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_member_notes')
            && !$schema->hasColumn('#__courses_member_notes', 'section_id')
        ) {
            $schema->addColumn('#__courses_member_notes', 'section_id')->integer()->notNull()->default(0)->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_member_notes', 'section_id')) {
            $schema->dropColumn('#__courses_member_notes', 'section_id');
        }
    }
}
