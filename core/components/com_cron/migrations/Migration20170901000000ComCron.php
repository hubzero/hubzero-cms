<?php

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
			  `last_run` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `next_run` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `recurrence` varchar(50) NOT NULL DEFAULT '',
			  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  `active` tinyint(3) NOT NULL DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `params` text NOT NULL,
			  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
