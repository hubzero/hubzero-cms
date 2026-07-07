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
 * Time-series table of mass-mail delivery activity, appended once per cron tick
 * so the dashboard can chart queue depth and send rate over time.
 **/
class Migration20260707000003ComMassmailStats extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__massmail_stats'))
		{
			$this->db->setQuery(
				"CREATE TABLE `#__massmail_stats` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`recorded` datetime DEFAULT NULL,
					`queue_size` int(11) NOT NULL DEFAULT '0',
					`sent_last_hour` int(11) NOT NULL DEFAULT '0',
					`system_sent_last_hour` int(11) NOT NULL DEFAULT '-1',
					`sent_this_run` int(11) NOT NULL DEFAULT '0',
					`pending` int(11) NOT NULL DEFAULT '0',
					`cumulative_sent` int(11) NOT NULL DEFAULT '0',
					PRIMARY KEY (`id`),
					KEY `idx_recorded` (`recorded`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
			);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__massmail_stats'))
		{
			$this->db->setQuery("DROP TABLE `#__massmail_stats`;");
			$this->db->query();
		}
	}
}
