<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updates to member dashboard features
 *
*/
class Migration20140508120000PlgMembersDashboard extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__xprofiles_dashboard_preferences') && $schema->tableExists('#__myhub')) {
            $dashboardPlugin = $this->db->getQuery(true)
                ->select(['extension_id', 'params'])
                ->from('#__extensions')
                ->where('folder', '=', 'members')
                ->where('element', '=', 'dashboard')
                ->first();
            $params = json_decode($dashboardPlugin->params);

            $newDefaults = array();
            if (isset($params->defaults)) {
                $oldDefaultCols = array_map("trim", explode(';', $params->defaults));

                foreach ($oldDefaultCols as $col => $oldCol) {
                    $newDefault  = array();
                    $oldDefaults = array_map('trim', explode(',', $oldCol));

                    foreach ($oldDefaults as $row => $pref) {
                        $newDefault['module'] = $pref;
                        $newDefault['col']    = $col + 1;
                        $newDefault['row']    = ($row * 2) + 1;
                        $newDefault['size_x'] = 1;
                        $newDefault['size_y'] = 2;
                        $newDefaults[]        = $newDefault;
                    }
                }
            }

            // make sure we have object
            if (!isset($params) || !is_object($params)) {
                $params = new stdClass();
            }

            $params->defaults = $newDefaults;

            // switch allow customization param to make sense.
            if (isset($params->allow_customization)) {
                if ($params->allow_customization == 1) {
                    $params->allow_customization = 0;
                } else {
                    $params->allow_customization = 1;
                }
            }

            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['params' => json_encode($params)])
                ->where('extension_id', '=', $dashboardPlugin->extension_id)
                ->execute();

            // create dashboard prefs table
            $schema->createTable('#__xprofiles_dashboard_preferences')
                ->unsignedInteger('uidNumber')
                ->text('preferences')->nullable()
                ->datetime('modified')->nullable()
                ->uniqueIndex('uidNumber', 'uidNumber')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            // move over exxisting preferences
            $preferences = $this->db->getQuery(true)
                ->select('*')
                ->from('#__myhub')
                ->group('uid')
                ->loadObjectList();

            $newpreferences = array();
            foreach ($preferences as $preference) {
                $newPrefCols = array();
                $oldPrefCols = array_map("trim", explode(';', $preference->prefs));

                foreach ($oldPrefCols as $col => $oldCol) {
                    $newPref = array();
                    $oldPrefs = array_map('trim', explode(',', $oldCol));

                    foreach ($oldPrefs as $row => $pref) {
                        $newPref['module'] = $pref;
                        $newPref['col']    = $col + 1;
                        $newPref['row']    = ($row * 2) + 1;
                        $newPref['size_x'] = 1;
                        $newPref['size_y'] = 2;

                        $newPrefCols[] = $newPref;
                    }
                }

                $newpreferences[] = array(
                    'uid'      => $preference->uid,
                    'prefs'    => $newPrefCols,
                    'modified' => $preference->modified
                );
            }

            // if we have some prefs to move over
            if (count($newpreferences) > 0) {
                foreach ($newpreferences as $pref) {
                    $this->db->getQuery(true)
                        ->insert('#__xprofiles_dashboard_preferences')
                        ->columns(['uidNumber', 'preferences', 'modified'])
                        ->values([
                            $pref['uid'],
                            json_encode($pref['prefs']),
                            $pref['modified']
                        ])
                        ->execute();
                }
            }

            // drop old myhub tables
            if ($schema->tableExists('#__myhub')) {
                $schema->dropTable('#__myhub');
            }

            if ($schema->tableExists('#__myhub_params')) {
                $schema->dropTable('#__myhub_params');
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // create myhub table
        if (!$schema->tableExists('#__myhub')) {
            $schema->createTable('#__myhub')
                ->integer('uid')
                ->string('prefs', 200)->nullable()
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // create myhub params table
        if (!$schema->tableExists('#__myhub_params')) {
            $schema->createTable('#__myhub_params')
                ->integer('uid')
                ->integer('mid')
                ->text('params')->nullable()
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        // remove new table
        if ($schema->tableExists('#__xprofiles_dashboard_preferences')) {
            $schema->dropTable('#__xprofiles_dashboard_preferences');
        }
    }
}
