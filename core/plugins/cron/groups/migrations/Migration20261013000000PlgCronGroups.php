<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration20260810000000 seeded the two membership-expiration jobs with
 * next_run NULL, and the cron runner only picked jobs whose next_run had
 * arrived -- so on every hub that ran it, both jobs were enabled but never
 * fired, and membership terms were not enforced. Stamp next_run on any
 * enabled groups job that still has none so the runner picks it up.
 *
 * Note for hubs that use membership terms: the first run removes members
 * whose term has already lapsed and sends the configured expiry warnings.
 **/
class Migration20261013000000PlgCronGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__cron_jobs'))
		{
			$this->db->setQuery(
				"UPDATE `#__cron_jobs` SET `next_run` = UTC_TIMESTAMP()
				 WHERE `plugin` = 'groups' AND `state` = 1 AND `next_run` IS NULL;"
			);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// Nothing to undo: next_run is runtime state the runner keeps advancing.
	}
}
