<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script fixing dashboard migration stuff.
 *
 */
class Migration20141202211549PlgMembersDashboard extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // get dashboard params
        $pluginParams = $this->getParams('plg_members_dashboard');

        if ($schema->tableExists('#__xprofiles_dashboard_preferences')) {
            // delete all null user preferences
            $this->db->getQuery(true)
                ->delete('#__xprofiles_dashboard_preferences')
                ->where('preferences', '=', '[]')
                ->execute();
        }

        // only continue if plugin defaults are NOT set
        $defaults = $pluginParams->get('defaults', '');
        if ($defaults != array()) {
            return;
        }

        // array to hold new defaults
        $defaults = array();

        if ($schema->tableExists('#__modules')) {
            // get top 6 modules
            $modules = $this->db->getQuery(true)
                ->select('id')
                ->from('#__modules')
                ->where('position', '=', 'memberDashboard')
                ->where('published', '=', 1)
                ->where('client_id', '=', 0)
                ->order('ordering', 'ASC')
                ->limit(6)
                ->loadColumn();

            // create default
            $col = 0;
            $row = 1;
            foreach ($modules as $k => $module) {
                $col = $col + 1;
                if ($col > 3) {
                    $col = 1;
                    $row = 3;
                }

                array_push($defaults, array(
                    'module' => $module,
                    'col'    => $col,
                    'row'    => $row,
                    'size_x' => 1,
                    'size_y' => 2
                ));
            }
        }
        // update params & save
        $pluginParams->set('defaults', $defaults);
        $this->savePluginParams('members', 'dashboard', $pluginParams->toArray());
    }
}
