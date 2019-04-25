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
 * Migration script for adding indexes on #__cron_jobs
 **/
class Migration20140822155900ComCron extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__cron_jobs'))
		{
			if (!$this->db->tableHasKey('#__cron_jobs', 'idx_state'))
			{
				$query = "ALTER TABLE `#__cron_jobs` ADD INDEX `idx_state` (`state`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__cron_jobs', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__cron_jobs` ADD INDEX `idx_created_by` (`created_by`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__cron_jobs'))
		{
			if ($this->db->tableHasKey('#__cron_jobs', 'idx_state'))
			{
				$query = "ALTER TABLE `#__cron_jobs` DROP INDEX `idx_state`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__cron_jobs', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__cron_jobs` DROP INDEX `idx_created_by`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
