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
 * Migration script for tracking group announcement emails
 **/
class Migration20131016184016PlgGroupsAnnouncements extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "";

		// add email column
		if (!$this->db->tableHasField('#__announcements', 'email'))
		{
			$query .= "ALTER TABLE `#__announcements` ADD COLUMN `email` TINYINT(4) DEFAULT 0;";
		}

		// add sent column
		if (!$this->db->tableHasField('#__announcements', 'sent'))
		{
			$query .= "ALTER TABLE `#__announcements` ADD COLUMN `sent` TINYINT(4) DEFAULT 0;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}

		$query = "SELECT title FROM `#__cron_jobs` WHERE title='Group Announcements';";

		$this->db->setQuery($query);

		if ($this->db->loadResult() != "Group Announcements")
		{
			// add group announcements cron
			$query = "INSERT INTO `#__cron_jobs` (`title`, `state`, `plugin`, `event`, `recurrence`)
				   VALUES ('Group Announcements', 1, 'groups', 'sendGroupAnnouncements', '*/5 * * * *');";

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

		// add email column
		if ($this->db->tableHasField('#__announcements', 'email'))
		{
			$query .= "ALTER TABLE `#__announcements` DROP COLUMN `email`;";
		}

		// add sent column
		if ($this->db->tableHasField('#__announcements', 'sent'))
		{
			$query .= "ALTER TABLE `#__announcements` DROP COLUMN `sent`;";
		}

		// remove announcements cron
		$query .= "DELETE FROM `#__cron_jobs` WHERE event='sendGroupAnnouncements'";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
