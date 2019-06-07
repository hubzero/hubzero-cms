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
 * Migration script for installing cron tables
 **/
class Migration20170901000000ComCron extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__cron_jobs'))
		{
			$query = "CREATE TABLE `#__cron_jobs` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `state` tinyint(3) NOT NULL DEFAULT '0',
			  `plugin` varchar(255) NOT NULL DEFAULT '',
			  `event` varchar(255) NOT NULL DEFAULT '',
			  `last_run` datetime DEFAULT NULL,
			  `next_run` datetime DEFAULT NULL,
			  `recurrence` varchar(50) NOT NULL DEFAULT '',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  `active` tinyint(3) NOT NULL DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `params` text NOT NULL,
			  `publish_up` datetime DEFAULT NULL,
			  `publish_down` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_state` (`state`),
			  KEY `idx_created_by` (`created_by`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__cron_jobs'))
		{
			$query = "DROP TABLE IF EXISTS `#__cron_jobs`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
