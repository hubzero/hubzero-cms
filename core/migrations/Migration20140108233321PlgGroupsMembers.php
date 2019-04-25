<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for group roles permission
 **/
class Migration20140108233321PlgGroupsMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		// change role to name
		if (!$this->db->tableHasField('#__xgroups_roles', 'name'))
		{
			$query = "ALTER TABLE `#__xgroups_roles` CHANGE `role` `name` VARCHAR(150);";
		}

		// add permissions field
		if (!$this->db->tableHasField('#__xgroups_roles', 'permissions'))
		{
			$query .= "ALTER TABLE `#__xgroups_roles` ADD COLUMN `permissions` TEXT;";
		}

		// add role to roleid
		if (!$this->db->tableHasField('#__xgroups_member_roles', 'roleid'))
		{
			$query .= "ALTER TABLE `#__xgroups_member_roles` CHANGE `role` `roleid` INT(11);";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "";

		// change role to name
		if ($this->db->tableHasField('#__xgroups_roles', 'name'))
		{
			$query = "ALTER TABLE `#__xgroups_roles` CHANGE `name` `role` VARCHAR(150);";
		}

		// add permissions field
		if ($this->db->tableHasField('#__xgroups_roles', 'permissions'))
		{
			$query .= "ALTER TABLE `#__xgroups_roles` DROP COLUMN `permissions`;";
		}

		// add role to roleid
		if ($this->db->tableHasField('#__xgroups_member_roles', 'roleid'))
		{
			$query .= "ALTER TABLE `#__xgroups_member_roles` CHANGE `roleid` `role` INT(11);";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
