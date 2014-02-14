<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding com cron component
 **/
class Migration20130426072033ComCron extends Base
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
						PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->addComponentEntry('Cron');
		$this->addPluginEntry('cron', 'support');
		$this->addPluginEntry('cron', 'members');
		$this->addPluginEntry('cron', 'cache');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__cron_jobs'))
		{
			$query = "DROP TABLE `#__cron_jobs`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->deleteComponentEntry('Cron');
		$this->deletePluginEntry('cron');
	}
}