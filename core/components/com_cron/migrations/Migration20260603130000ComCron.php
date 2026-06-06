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
 * Migration adding `active_since` to cron jobs.
 *
 * Records (UTC) when a job's active run was claimed. Used as a bounded
 * fallback: if a job is owned by a host whose process can't be verified
 * locally and it has been active longer than the cutoff, it is assumed dead
 * and reclaimed, so a hostname change/multi-node split can't wedge a job
 * forever. Same-host runs are still judged by live process check.
 **/
class Migration20260603130000ComCron extends Base
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

		if (!$this->db->tableHasField('#__cron_jobs', 'active_since'))
		{
			$query = "ALTER TABLE `#__cron_jobs` ADD `active_since` DATETIME NULL DEFAULT NULL AFTER `pid_host`";
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

		if ($this->db->tableHasField('#__cron_jobs', 'active_since'))
		{
			$query = "ALTER TABLE `#__cron_jobs` DROP COLUMN `active_since`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
