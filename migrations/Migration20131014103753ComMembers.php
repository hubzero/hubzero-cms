<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding members quota interface
 **/
class Migration20131014103753ComMembers extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableExists('#__users_quotas'))
		{
			$query = "CREATE TABLE `#__users_quotas` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`user_id` int(11) NOT NULL,
						`class_id` int(11) DEFAULT NULL,
						`hard_files` int(11) NOT NULL,
						`soft_files` int(11) NOT NULL,
						`hard_blocks` int(11) NOT NULL,
						`soft_blocks` int(11) NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$db->setQuery($query);
			$db->query();
		}

		if (!$db->tableExists('#__users_quotas_classes'))
		{
			$query = "CREATE TABLE `#__users_quotas_classes` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`alias` varchar(255) NOT NULL DEFAULT '',
						`hard_files` int(11) NOT NULL,
						`soft_files` int(11) NOT NULL,
						`hard_blocks` int(11) NOT NULL,
						`soft_blocks` int(11) NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$db->setQuery($query);
			$db->query();

			$query = "INSERT INTO `#__users_quotas_classes` (`id`, `alias`, `hard_files`, `soft_files`, `hard_blocks`, `soft_blocks`) VALUES (1, 'default', 0, 0, 1000000, 900000);";
			$db->setQuery($query);
			$db->query();
		}

		if (!$db->tableExists('#__users_quotas_log'))
		{
			$query = "CREATE TABLE `#__users_quotas_log` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`object_type` varchar(255) NOT NULL DEFAULT '',
						`object_id` int(11) NOT NULL,
						`name` varchar(255) NOT NULL DEFAULT '',
						`action` varchar(255) NOT NULL DEFAULT '',
						`actor_id` int(11) NOT NULL,
						`soft_blocks` int(11) NOT NULL,
						`hard_blocks` int(11) NOT NULL,
						`soft_files` int(11) NOT NULL,
						`hard_files` int(11) NOT NULL,
						PRIMARY KEY (`id`)
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
		if ($db->tableExists('#__users_quotas'))
		{
			$query = "DROP TABLE `#__users_quotas`";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableExists('#__users_quotas_classes'))
		{
			$query = "DROP TABLE `#__users_quotas_classes`";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableExists('#__users_quotas_log'))
		{
			$query = "DROP TABLE `#__users_quotas_log`";
			$db->setQuery($query);
			$db->query();
		}
	}
}