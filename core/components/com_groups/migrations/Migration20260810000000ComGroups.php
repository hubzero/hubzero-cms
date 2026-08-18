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
 * Migration script for optional time-limited group membership
 *
 * Adds the term columns to the membership table and an archive of lapsed
 * memberships. Every existing row keeps `expires` NULL, which means
 * "never expires" - the behaviour groups have today.
 **/
class Migration20260810000000ComGroups extends Base
{
	/**
	 * Is an index present on a table?
	 *
	 * The driver exposes tableExists()/tableHasField() but no key equivalent,
	 * so this goes through getTableKeys(), which returns the SHOW KEYS rows
	 * indexed by Key_name.
	 *
	 * @param   string   $table
	 * @param   string   $key
	 * @return  boolean
	 **/
	protected function hasKey($table, $key)
	{
		$keys = $this->db->getTableKeys($table);

		return is_array($keys) && array_key_exists($key, $keys);
	}

	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__xgroups_members'))
		{
			if (!$this->db->tableHasField('#__xgroups_members', 'expires'))
			{
				$query = "ALTER TABLE `#__xgroups_members`
					ADD COLUMN `expires` datetime DEFAULT NULL AFTER `uidNumber`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__xgroups_members', 'expires_set_by'))
			{
				$query = "ALTER TABLE `#__xgroups_members`
					ADD COLUMN `expires_set_by` int(11) DEFAULT NULL AFTER `expires`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__xgroups_members', 'expires_notified'))
			{
				$query = "ALTER TABLE `#__xgroups_members`
					ADD COLUMN `expires_notified` datetime DEFAULT NULL AFTER `expires_set_by`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->hasKey('#__xgroups_members', 'idx_expires'))
			{
				$query = "ALTER TABLE `#__xgroups_members` ADD KEY `idx_expires` (`expires`)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		// The reaper's heartbeat lives in the group log, which is queried by
		// action on every admin membership page view and pruned on every run.
		// That table carries only a primary key, so both are full scans
		// without this - measurably ~14ms per view on a 200k-row log.
		if ($this->db->tableExists('#__xgroups_log')
		 && !$this->hasKey('#__xgroups_log', 'idx_action_timestamp'))
		{
			$query = "ALTER TABLE `#__xgroups_log` ADD KEY `idx_action_timestamp` (`action`,`timestamp`)";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__xgroups_member_history'))
		{
			$query = "CREATE TABLE `#__xgroups_member_history` (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`gidNumber` int(11) NOT NULL,
				`uidNumber` int(11) NOT NULL,
				`expires` datetime DEFAULT NULL,
				`revoked` datetime DEFAULT NULL,
				`reason` varchar(32) DEFAULT NULL,
				`was_manager` tinyint(1) DEFAULT 0,
				`actor` int(11) DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `idx_gidNumber_uidNumber` (`gidNumber`,`uidNumber`),
				KEY `idx_revoked` (`revoked`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__xgroups_members'))
		{
			if ($this->hasKey('#__xgroups_members', 'idx_expires'))
			{
				$query = "ALTER TABLE `#__xgroups_members` DROP KEY `idx_expires`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			foreach (array('expires_notified', 'expires_set_by', 'expires') as $field)
			{
				if ($this->db->tableHasField('#__xgroups_members', $field))
				{
					$query = "ALTER TABLE `#__xgroups_members` DROP COLUMN `$field`";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}

		if ($this->db->tableExists('#__xgroups_log')
		 && $this->hasKey('#__xgroups_log', 'idx_action_timestamp'))
		{
			$query = "ALTER TABLE `#__xgroups_log` DROP KEY `idx_action_timestamp`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__xgroups_member_history'))
		{
			$query = "DROP TABLE IF EXISTS `#__xgroups_member_history`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
