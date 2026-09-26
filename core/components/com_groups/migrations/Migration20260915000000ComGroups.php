<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for manager-ordered group member roles
 *
 * Every existing role starts at 0, so a group that has never been reordered
 * keeps listing its roles by name.
 **/
class Migration20260915000000ComGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xgroups_roles')
		 && !$this->db->tableHasField('#__xgroups_roles', 'ordering'))
		{
			$query = "ALTER TABLE `#__xgroups_roles`
				ADD COLUMN `ordering` int(11) NOT NULL DEFAULT 0 AFTER `name`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__xgroups_roles')
		 && $this->db->tableHasField('#__xgroups_roles', 'ordering'))
		{
			$query = "ALTER TABLE `#__xgroups_roles` DROP COLUMN `ordering`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
