<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding publish_up and publish_down fields to cron table
 **/
class Migration20140305090721ComCron extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// add publish_up
		if (!$this->db->tableHasField('#__cron_jobs', 'publish_up'))
		{
			$query = "ALTER TABLE `#__cron_jobs` ADD `publish_up` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00';";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// add publish_down
		if (!$this->db->tableHasField('#__cron_jobs', 'publish_down'))
		{
			$query = "ALTER TABLE `#__cron_jobs` ADD `publish_down` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// drop publish_up
		if ($this->db->tableHasField('#__cron_jobs', 'publish_up'))
		{
			$query = "ALTER TABLE `#__cron_jobs` DROP COLUMN `publish_up`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// drop publish_down
		if ($this->db->tableHasField('#__cron_jobs', 'publish_down'))
		{
			$query = "ALTER TABLE `#__cron_jobs` DROP COLUMN `publish_down`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}