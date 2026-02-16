<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for distinguishing between unanswered and no in profile mail preference column
**/
class Migration20130618144751PlgMembersProfile extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__xprofiles')
            && $schema->hasColumn('#__xprofiles', 'mailPreferenceOption')
        ) {
            // Change default value for column
            $schema->modifyColumn('#__xprofiles', 'mailPreferenceOption')->tinyInteger(4)->default(-1);

            // Update existing data
            $this->db->getQuery(true)
                ->update('#__xprofiles')
                ->set(['mailPreferenceOption' => 1])
                ->where('mailPreferenceOption', '=', 2)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        // No action needed for down migration
    }
}
