<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding tables and data for profile schema
 *
*/
class Migration20161017133711ComMembers extends Base
{
    public function up()
    {
        $this->db->getQuery(true)
            ->update('#__user_profile_fields')
            ->set(['action_edit' => 0])
            ->where('action_create', '=', 0)
            ->where('action_update', '=', 0)
            ->whereIn('name', ['countryresident', 'countryorigin', 'race'])
            ->execute();
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->db->getQuery(true)
            ->update('#__user_profile_fields')
            ->set(['action_edit' => 2])
            ->where('action_create', '=', 0)
            ->where('action_update', '=', 0)
            ->whereIn('name', ['countryresident', 'countryorigin', 'race'])
            ->execute();
    }
}
