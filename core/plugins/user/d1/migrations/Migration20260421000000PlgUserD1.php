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
 * Migration script for adding User - D1 plugin
 **/
class Migration20260421000000PlgUserD1 extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('user', 'd1');

		if ($this->db->tableExists('#__xgroups'))
		{
			foreach (array('d1_nation', 'd1_override') as $cn)
			{
				$this->db->setQuery(
					"INSERT IGNORE INTO `#__xgroups` (cn, description, published, approved, type, join_policy, discoverability, created)" .
					" VALUES (" . $this->db->quote($cn) . ", " . $this->db->quote($cn) . ", 1, 1, 1, 3, 1, NOW())"
				);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('user', 'd1');

		if ($this->db->tableExists('#__xgroups'))
		{
			$this->db->setQuery(
				"DELETE FROM `#__xgroups` WHERE cn IN ('d1_nation', 'd1_override')"
			);
			$this->db->query();
		}
	}
}
