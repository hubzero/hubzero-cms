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
 * Migration script registering the group membership cron jobs
 *
 * Both jobs are registered: without the notifier, end dates would be enforced
 * with no warning to the member.
 **/
class Migration20260810000000PlgCronGroups extends Base
{
	/**
	 * event => (title, recurrence)
	 *
	 * @var  array
	 **/
	protected static $jobs = array(
		'expireGroupMemberships' => array(
			'Group Membership Expiration',
			'*/15 * * * *'
		),
		'notifyExpiringMemberships' => array(
			'Group Membership Expiration Warnings',
			'0 7 * * *'
		)
	);

	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__cron_jobs'))
		{
			return;
		}

		foreach (self::$jobs as $event => $job)
		{
			list($title, $recurrence) = $job;

			$query = "SELECT `id` FROM `#__cron_jobs` WHERE `plugin`='groups' AND `event`=" . $this->db->quote($event) . ";";
			$this->db->setQuery($query);

			if ($this->db->loadResult())
			{
				continue;
			}

			$query = "INSERT INTO `#__cron_jobs` (`title`, `state`, `plugin`, `event`, `last_run`, `next_run`, `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`, `params`)"
				. " VALUES (" . $this->db->quote($title) . ", 1, 'groups', " . $this->db->quote($event)
				. ", NULL, NULL, " . $this->db->quote($recurrence) . ", NULL, 0, NULL, 0, 0, 0, '');";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__cron_jobs'))
		{
			return;
		}

		foreach (array_keys(self::$jobs) as $event)
		{
			$query = "DELETE FROM `#__cron_jobs` WHERE `plugin`='groups' AND `event`=" . $this->db->quote($event) . ";";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
