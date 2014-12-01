<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding unique constraint to group membership
 **/
class Migration20141201170547ComGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xgroups_members') && !$this->db->tableHasKey('#__xgroups_members', 'idx_gidNumber_uidNumber'))
		{
			$query = "ALTER IGNORE TABLE `#__xgroups_members` ADD UNIQUE KEY idx_gidNumber_uidNumber(`gidNumber`, `uidNumber`)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__xgroups_members') && $this->db->tableHasKey('#__xgroups_members', 'idx_gidNumber_uidNumber'))
		{
			$query = "ALTER TABLE `#__xgroups_members` DROP KEY idx_gidNumber_uidNumber";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}