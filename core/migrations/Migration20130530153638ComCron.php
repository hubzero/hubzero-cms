<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding twitter authentication plugin
 **/
class Migration20130530153638ComCron extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		if (!$this->db->tableHasField('#__cron_jobs', 'params'))
		{
			$query = "ALTER TABLE `#__cron_jobs` ADD `params` TEXT  NOT NULL  AFTER `ordering`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "";

		if ($this->db->tableHasField('#__cron_jobs', 'params'))
		{
			$query .= "ALTER TABLE `#__cron_jobs` DROP `params`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}