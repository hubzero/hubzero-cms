<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for renaming metrics_author_cluster if it exists, creating it otherwise
 **/
class Migration20130827143717Core extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('metrics_author_cluster') && !$db->tableExists('#__metrics_author_cluster'))
		{
			$query = "RENAME TABLE `metrics_author_cluster` TO `#__metrics_author_cluster`;";
			$db->setQuery($query);
			$db->query();
		}
		else if (!$db->tableExists('metrics_author_cluster') && !$db->tableExists('#__metrics_author_cluster'))
		{
			$query = "CREATE TABLE `#__metrics_author_cluster` (
						`authorid` varchar(60) NOT NULL DEFAULT '0',
						`classes` int(11) DEFAULT '0',
						`users` int(11) DEFAULT '0',
						`schools` int(11) DEFAULT '0',
						PRIMARY KEY (`authorid`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if (!$db->tableExists('metrics_author_cluster') && $db->tableExists('#__metrics_author_cluster'))
		{
			$query = "RENAME TABLE `#__metrics_author_cluster` TO `metrics_author_cluster`;";
			$db->setQuery($query);
			$db->query();
		}
	}
}