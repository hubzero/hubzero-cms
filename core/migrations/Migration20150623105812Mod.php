<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Hubzero!
/**
 * Migration script for renaming admin modules to avoid conflict with site modules
**/
class Migration20150623105812Mod extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['element' => 'mod_adminmenu'])
                ->where('client_id', '=', 1)
                ->where('element', '=', 'mod_menu')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['element' => 'mod_adminlogin'])
                ->where('client_id', '=', 1)
                ->where('element', '=', 'mod_login')
                ->execute();
        }

        if ($schema->tableExists('#__modules')) {
            $this->db->getQuery(true)
                ->update('#__modules')
                ->set(['module' => 'mod_adminmenu'])
                ->where('client_id', '=', 1)
                ->where('module', '=', 'mod_menu')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__modules')
                ->set(['module' => 'mod_adminlogin'])
                ->where('client_id', '=', 1)
                ->where('module', '=', 'mod_login')
                ->execute();
        }

        $this->deleteModuleEntry('mod_dashboard', 1);
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['element' => 'mod_menu'])
                ->where('client_id', '=', 1)
                ->where('element', '=', 'mod_adminmenu')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['element' => 'mod_login'])
                ->where('client_id', '=', 1)
                ->where('element', '=', 'mod_adminlogin')
                ->execute();
        }

        if ($schema->tableExists('#__modules')) {
            $this->db->getQuery(true)
                ->update('#__modules')
                ->set(['module' => 'mod_menu'])
                ->where('client_id', '=', 1)
                ->where('module', '=', 'mod_adminmenu')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__modules')
                ->set(['module' => 'mod_login'])
                ->where('client_id', '=', 1)
                ->where('module', '=', 'mod_adminlogin')
                ->execute();
        }

        $this->addModuleEntry('mod_dashboard', 1, '', 1);
    }
}
