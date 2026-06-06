<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration creating #__publication_bundle_queue — the queue/state for asynchronous
 * publication bundle building.
 *
 * One row per publication version. status: queued|building|ready|failed.
 * worker_* enable pid/starttime recovery of dead build workers (see
 * Hubzero\Utility\Process); attempts/last_attempt_at drive backoff + dead-
 * lettering so a failing build can't block the queue; source_hash gates
 * staleness (NULL = grandfathered, never auto-rebuilt).
 *
 * Additive and dormant: nothing reads this until the async path is enabled.
 **/
class Migration20260603160000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_bundle_queue'))
		{
			return;
		}

		$query = "CREATE TABLE `#__publication_bundle_queue` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `publication_version_id` int(11) NOT NULL DEFAULT '0',
		  `status` varchar(16) NOT NULL DEFAULT 'queued',
		  `attempts` int(11) NOT NULL DEFAULT '0',
		  `max_attempts` int(11) NOT NULL DEFAULT '3',
		  `source_hash` varchar(64) DEFAULT NULL,
		  `bundle_file` varchar(255) DEFAULT NULL,
		  `bundle_size` bigint(20) unsigned DEFAULT NULL,
		  `last_error` text,
		  `worker_pid` int(11) DEFAULT NULL,
		  `worker_started` bigint(20) unsigned DEFAULT NULL,
		  `worker_host` varchar(255) DEFAULT NULL,
		  `queued_at` datetime DEFAULT NULL,
		  `last_attempt_at` datetime DEFAULT NULL,
		  `built_at` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `uniq_version` (`publication_version_id`),
		  KEY `idx_status` (`status`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__publication_bundle_queue'))
		{
			$this->db->setQuery("DROP TABLE IF EXISTS `#__publication_bundle_queue`;");
			$this->db->query();
		}
	}
}
