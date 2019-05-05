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
 * Migration script for installing default groups cron jobs
 **/
class Migration20170902000000PlgCronGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__cron_jobs'))
		{
			$query = "SELECT `id` FROM `#__cron_jobs` WHERE `plugin`='groups' AND `event`='sendGroupAnnouncements';";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if (!$id)
			{
				$query = "INSERT INTO `#__cron_jobs` (`title`, `state`, `plugin`, `event`, `last_run`, `next_run`, `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`, `params`) VALUES ('Group Announcements', 1, 'groups', 'sendGroupAnnouncements', NULL, NULL, '*/5 * * * *', NULL, 0, NULL, 0, 0, 0, '');";

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
			$query = "DELETE FROM `#__cron_jobs` WHERE `plugin`='groups' AND `event`='sendGroupAnnouncements';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
