<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Create the "Service accounts" access group, whose members the "Expire
 * inactive accounts" cron job never expires, with the hub's service accounts
 * in it: they use SSH keys only, or no login at all, so they never sign in
 * on the web.
 **/
class Migration20261029000001PlgCronMembersServiceAccounts extends Base
{
	/**
	 * Access group title; the cron job's default "Exempt access group"
	 *
	 * @var  string
	 */
	const TITLE = 'Service accounts';

	/**
	 * Service accounts added when they exist on the hub
	 *
	 * @var  array
	 */
	const USERNAMES = array('apps', 'gridstat', 'hubzerosolrworker');

	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__usergroups') || !$this->db->tableExists('#__user_usergroup_map'))
		{
			return;
		}

		$id = $this->groupId();

		if (!$id)
		{
			// Directly under the root (Public), so the group grants no
			// permissions of its own; it only marks its members.
			$this->db->setQuery("SELECT `id`, `rgt` FROM `#__usergroups` WHERE `parent_id` = 0 ORDER BY `lft` LIMIT 1");
			$root = $this->db->loadObject();

			if (!$root)
			{
				return;
			}

			// Nested set: insert as the root's last child
			$rgt = (int) $root->rgt;

			$this->db->setQuery("UPDATE `#__usergroups` SET `rgt` = `rgt` + 2 WHERE `rgt` >= " . $rgt);
			$this->db->query();
			$this->db->setQuery("UPDATE `#__usergroups` SET `lft` = `lft` + 2 WHERE `lft` > " . $rgt);
			$this->db->query();
			$this->db->setQuery(
				"INSERT INTO `#__usergroups` (`parent_id`, `lft`, `rgt`, `title`) VALUES ("
				. (int) $root->id . ", " . $rgt . ", " . ($rgt + 1) . ", " . $this->db->quote(self::TITLE) . ")"
			);
			$this->db->query();

			$id = $this->groupId();
		}

		if (!$id)
		{
			return;
		}

		$this->db->setQuery(
			"INSERT IGNORE INTO `#__user_usergroup_map` (`user_id`, `group_id`)"
			. " SELECT `id`, " . (int) $id . " FROM `#__users`"
			. " WHERE `username` IN (" . implode(', ', array_map(array($this->db, 'quote'), self::USERNAMES)) . ")"
		);
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__usergroups') || !$this->db->tableExists('#__user_usergroup_map'))
		{
			return;
		}

		$this->db->setQuery("SELECT `id`, `lft`, `rgt` FROM `#__usergroups` WHERE `title` = " . $this->db->quote(self::TITLE) . " LIMIT 1");
		$group = $this->db->loadObject();

		if (!$group)
		{
			return;
		}

		$this->db->setQuery("DELETE FROM `#__user_usergroup_map` WHERE `group_id` = " . (int) $group->id);
		$this->db->query();

		// Only a leaf can be removed without orphaning children
		if ((int) $group->rgt != (int) $group->lft + 1)
		{
			return;
		}

		$this->db->setQuery("DELETE FROM `#__usergroups` WHERE `id` = " . (int) $group->id);
		$this->db->query();
		$this->db->setQuery("UPDATE `#__usergroups` SET `rgt` = `rgt` - 2 WHERE `rgt` > " . (int) $group->rgt);
		$this->db->query();
		$this->db->setQuery("UPDATE `#__usergroups` SET `lft` = `lft` - 2 WHERE `lft` > " . (int) $group->rgt);
		$this->db->query();
	}

	/**
	 * Id of the access group, 0 if it does not exist
	 *
	 * @return  integer
	 */
	protected function groupId()
	{
		$this->db->setQuery("SELECT `id` FROM `#__usergroups` WHERE `title` = " . $this->db->quote(self::TITLE) . " LIMIT 1");

		return (int) $this->db->loadResult();
	}
}
