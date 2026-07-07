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
 * Register the Cron - Mass Mail plugin and seed its (disabled) cron job.
 **/
class Migration20260707000001PlgCronMassmail extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('cron', 'massmail');

		if ($this->db->tableExists('#__cron_jobs'))
		{
			$this->db->setQuery(
				"SELECT `id` FROM `#__cron_jobs` WHERE `plugin` = 'massmail' AND `event` = 'processMassmailQueue';"
			);

			if (!$this->db->loadResult())
			{
				// Seeded disabled (state = 0). Enable in the Cron admin once
				// mass mail is configured and sending is turned on.
				$this->db->setQuery(
					"INSERT INTO `#__cron_jobs`
						(`title`, `state`, `plugin`, `event`, `last_run`, `next_run`, `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`, `params`)
					 VALUES
						('Process Mass Mail Queue', 0, 'massmail', 'processMassmailQueue', NULL, NULL, '*/5 * * * *', NULL, 1, NULL, 0, 0, 0, '');"
				);
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
			$this->db->setQuery(
				"DELETE FROM `#__cron_jobs` WHERE `plugin` = 'massmail' AND `event` = 'processMassmailQueue';"
			);
			$this->db->query();
		}

		$this->deletePluginEntry('cron', 'massmail');
	}
}
