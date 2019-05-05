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
 * Migration script for installing default newsletter cron jobs
 **/
class Migration20170902000000PlgCronNewsletter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__cron_jobs'))
		{
			$query = "SELECT `id` FROM `#__cron_jobs` WHERE `plugin`='newsletter' AND `event`='processMailings';";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if (!$id)
			{
				$query = "INSERT INTO `#__cron_jobs` (`title`, `state`, `plugin`, `event`, `last_run`, `next_run`, `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`, `params`) VALUES ('Process Newsletter Mailings', 0, 'newsletter', 'processMailings', NULL, NULL, '*/5 * * * *', '2013-06-25 08:23:04', 1001, '2013-07-16 17:15:01', 0, 0, 0, '');";

				$this->db->setQuery($query);
				$this->db->query();
			}

			$query = "SELECT `id` FROM `#__cron_jobs` WHERE `plugin`='newsletter' AND `event`='processIps';";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();

			if (!$id)
			{
				$query = "INSERT INTO `#__cron_jobs` (`title`, `state`, `plugin`, `event`, `last_run`, `next_run`, `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`, `params`) VALUES ('Process Newsletter Opens & Click IP Addresses', 0, 'newsletter', 'processIps', NULL, NULL, '*/5 * * * *', '2013-06-25 08:23:04', 1001, '2013-07-16 17:15:01', 0, 0, 0, '');";

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
			$query = "DELETE FROM `#__cron_jobs` WHERE `plugin`='newsletter' AND `event` IN ('processMailings', 'processIps');";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
