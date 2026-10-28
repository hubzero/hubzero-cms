<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding the apikey column to #__custom_extensions
 *
 * Hubs that had #__custom_extensions before Migration20200731000000ComInstaller
 * kept their older table (token, branch, ...), since that migration only creates
 * the table when it is missing. Without apikey the custom extension form's
 * access token was silently dropped on save.
 **/
class Migration20261012000000ComInstaller extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (
            $this->db->tableExists('#__custom_extensions') &&
            !$this->db->tableHasField('#__custom_extensions', 'apikey')
        ) {
            $query = "ALTER TABLE `#__custom_extensions` ADD COLUMN `apikey` varchar(255) DEFAULT NULL AFTER `url`;";
            $this->db->setQuery($query);
            $this->db->query();

            // Carry over the access token the older table kept in `token`
            if ($this->db->tableHasField('#__custom_extensions', 'token')) {
                $query = "UPDATE `#__custom_extensions` SET `apikey`=`token` WHERE `token` IS NOT NULL AND `token`!='';";
                $this->db->setQuery($query);
                $this->db->query();
            }
        }

        // ...and the branch it kept in `branch`
        if (
            $this->db->tableExists('#__custom_extensions') &&
            $this->db->tableHasField('#__custom_extensions', 'branch') &&
            $this->db->tableHasField('#__custom_extensions', 'git_branch')
        ) {
            $query = "UPDATE `#__custom_extensions` SET `git_branch`=`branch` WHERE (`git_branch` IS NULL OR `git_branch`='') AND `branch` IS NOT NULL AND `branch`!='';";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        // apikey is part of the table Migration20200731000000ComInstaller creates,
        // so leave it in place
    }
}
