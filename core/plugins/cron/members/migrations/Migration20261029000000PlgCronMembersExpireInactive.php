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
 * Seed the (disabled) monthly cron job that expires inactive accounts.
 **/
class Migration20261029000000PlgCronMembersExpireInactive extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__cron_jobs'))
		{
			$this->db->setQuery(
				"SELECT `id` FROM `#__cron_jobs` WHERE `plugin` = 'members' AND `event` = 'expireInactiveAccounts';"
			);

			if (!$this->db->loadResult())
			{
				// Seeded disabled (state = 0). Enable in the Cron admin only
				// after the shadowExpire reset: the job skips accounts whose
				// shadowExpire is already set. 03:00 on the 1st of each month.
				$this->db->setQuery(
					"INSERT INTO `#__cron_jobs`
						(`title`, `state`, `plugin`, `event`, `last_run`, `next_run`, `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`, `params`)
					 VALUES
						('Expire inactive accounts', 0, 'members', 'expireInactiveAccounts', NULL, NULL, '0 3 1 * *', NULL, 1, NULL, 0, 0, 0, '{\"members_expireinactive_months\":\"36\",\"members_expireinactive_exempt_group\":\"Service accounts\"}');"
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
				"DELETE FROM `#__cron_jobs` WHERE `plugin` = 'members' AND `event` = 'expireInactiveAccounts';"
			);
			$this->db->query();
		}
	}
}
