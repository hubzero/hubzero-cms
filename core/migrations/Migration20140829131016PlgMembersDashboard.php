<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing member dashboard module position name
 *
*/
class Migration20140829131016PlgMembersDashboard extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        // set new dashboard mod position
        $params = $this->getParams('plg_members_dashboard');
        if ($params->get('position')) {
            $params->set('position', 'memberDashboard');
            $this->savePluginParams('members', 'dashboard', $params->toArray());
        }

        // update all modules positions currently set to myhub
        $this->db->getQuery(true)
            ->update('#__modules')
            ->set(['position' => 'memberDashboard'])
            ->where('position', '=', 'myhub')
            ->execute();
    }

    /**
     * Down
     **/
    public function down()
    {
        // set new dashboard mod position
        $params = $this->getParams('plg_members_dashboard');
        if ($params->get('position')) {
            $params->set('position', 'myhub');
            $this->savePluginParams('members', 'dashboard', $params->toArray());
        }

        // update all modules positions currently set to myhub
        $this->db->getQuery(true)
            ->update('#__modules')
            ->set(['position' => 'myhub'])
            ->where('position', '=', 'memberDashboard')
            ->execute();
    }
}
