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
 * Migration script adding process-ownership tracking to cron jobs.
 *
 * When a job is marked active it now records the OS process that owns the
 * run: its pid, the process start time in jiffies (clock ticks since boot,
 * from /proc/<pid>/stat field 22), and the host the process runs on. The
 * runner uses these to detect a job whose process died/was killed (pid gone,
 * or pid reused with a different start time) and reclaim it from the active
 * state so it can run again, instead of staying active forever.
 **/
class Migration20260602000000ComCron extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__cron_jobs'))
		{
			return;
		}

		if (!$this->db->tableHasField('#__cron_jobs', 'pid'))
		{
			$query = "ALTER TABLE `#__cron_jobs` ADD `pid` INT(11) NULL DEFAULT NULL AFTER `active`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__cron_jobs', 'pid_started'))
		{
			$query = "ALTER TABLE `#__cron_jobs` ADD `pid_started` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `pid`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableHasField('#__cron_jobs', 'pid_host'))
		{
			$query = "ALTER TABLE `#__cron_jobs` ADD `pid_host` VARCHAR(255) NULL DEFAULT NULL AFTER `pid_started`";
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

		foreach (array('pid_host', 'pid_started', 'pid') as $field)
		{
			if ($this->db->tableHasField('#__cron_jobs', $field))
			{
				$query = "ALTER TABLE `#__cron_jobs` DROP COLUMN `$field`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
