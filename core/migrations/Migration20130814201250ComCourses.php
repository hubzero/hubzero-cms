<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing up members badges table
  *
**/
class Migration20130814201250ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->hasColumn('#__courses_member_badges', 'claimed')
            && !$schema->hasColumn('#__courses_member_badges', 'action')
        ) {
            $schema->renameColumn('#__courses_member_badges', 'claimed', 'action')
                ->string(255)
                ->nullable()
                ->default(null)
                ->execute();
        }
        if (
            $schema->hasColumn('#__courses_member_badges', 'claimed_on')
            && !$schema->hasColumn('#__courses_member_badges', 'action_on')
        ) {
            $schema->renameColumn('#__courses_member_badges', 'claimed_on', 'action_on')
                ->datetime()
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

        if (
            !$schema->hasColumn('#__courses_member_badges', 'claimed')
            && $schema->hasColumn('#__courses_member_badges', 'action')
        ) {
            $schema->renameColumn('#__courses_member_badges', 'action', 'claimed')
                ->integer(1)
                ->nullable()
                ->default(null)
                ->execute();
        }
        if (
            !$schema->hasColumn('#__courses_member_badges', 'claimed_on')
            && $schema->hasColumn('#__courses_member_badges', 'action_on')
        ) {
            $schema->renameColumn('#__courses_member_badges', 'action_on', 'claimed_on')
                ->datetime()
                ->nullable()
                ->default(null)
                ->execute();
        }
    }
}
