<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2021 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_installer
 **/
class Migration20210423000000ComInstaller extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__custom_extensions') &&
		    !$this->db->tableHasField('#__custom_extensions', 'git_branch'))
		{
			$query = "ALTER TABLE `#__custom_extensions` ADD COLUMN `git_branch` varchar(255) DEFAULT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__custom_extensions') &&
		    !$this->db->tableHasField('#__custom_extensions', 'git_tag'))
		{
			$query = "ALTER TABLE `#__custom_extensions` ADD COLUMN `git_tag` varchar(255) DEFAULT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__custom_extensions') &&
		    !$this->db->tableHasField('#__custom_extensions', 'previous_version'))
		{
			$query = "ALTER TABLE `#__custom_extensions` ADD COLUMN `previous_version` varchar(255) DEFAULT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__custom_extensions') &&
		    $this->db->tableHasField('#__custom_extensions', 'installed_version'))
		{
			$query = "ALTER TABLE `#__custom_extensions` CHANGE `installed_version` `installed_version` varchar(255) DEFAULT NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__custom_extensions') &&
		    $this->db->tableHasField('#__custom_extensions', 'git_branch'))
		{
			$query = "ALTER TABLE `#__custom_extensions` DROP COLUMN `git_branch`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__custom_extensions') &&
		    $this->db->tableHasField('#__custom_extensions', 'git_tag'))
		{
			$query = "ALTER TABLE `#__custom_extensions` DROP COLUMN `git_tag`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__custom_extensions') &&
		    $this->db->tableHasField('#__custom_extensions', 'previous_version'))
		{
			$query = "ALTER TABLE `#__custom_extensions` DROP COLUMN `previous_version`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
