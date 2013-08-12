<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding indices to pdf2form tables
 **/
class Migration20130812132139ComCollections extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "CREATE TABLE IF NOT EXISTS `#__collections` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `title` varchar(255) NOT NULL DEFAULT '',
				  `alias` varchar(255) NOT NULL,
				  `object_id` int(11) NOT NULL DEFAULT '0',
				  `object_type` varchar(150) NOT NULL DEFAULT '',
				  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `created_by` int(11) NOT NULL DEFAULT '0',
				  `state` tinyint(3) NOT NULL DEFAULT '1',
				  `access` tinyint(3) NOT NULL DEFAULT '0',
				  `is_default` tinyint(2) NOT NULL DEFAULT '0',
				  `description` mediumtext NOT NULL,
				  `positive` int(11) NOT NULL DEFAULT '0',
				  `negative` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  KEY `idx_objectified` (`object_type`,`object_id`),
				  KEY `idx_state` (`state`),
				  KEY `idx_access` (`access`),
				  KEY `idx_createdby` (`created_by`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `#__collections_assets` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `item_id` int(11) NOT NULL DEFAULT '0',
				  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `created_by` int(11) NOT NULL DEFAULT '0',
				  `filename` varchar(255) NOT NULL DEFAULT '',
				  `description` mediumtext NOT NULL,
				  `state` tinyint(2) NOT NULL DEFAULT '0',
				  `type` varchar(50) NOT NULL DEFAULT 'file',
				  `ordering` tinyint(3) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  KEY `idx_item_id` (`item_id`),
				  KEY `idx_created_by` (`created_by`),
				  KEY `idx_state` (`state`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `#__collections_following` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `follower_type` varchar(150) NOT NULL,
				  `follower_id` int(11) NOT NULL DEFAULT '0',
				  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `following_type` varchar(150) NOT NULL DEFAULT '',
				  `following_id` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `#__collections_items` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `title` varchar(255) NOT NULL DEFAULT '',
				  `description` mediumtext NOT NULL,
				  `url` varchar(255) NOT NULL,
				  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `created_by` int(11) NOT NULL DEFAULT '0',
				  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `modified_by` int(11) NOT NULL DEFAULT '0',
				  `state` tinyint(3) NOT NULL DEFAULT '1',
				  `access` tinyint(2) NOT NULL DEFAULT '0',
				  `positive` int(11) NOT NULL DEFAULT '0',
				  `negative` int(11) NOT NULL DEFAULT '0',
				  `type` varchar(150) NOT NULL DEFAULT '',
				  `object_id` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  KEY `idx_state` (`state`),
				  KEY `idx_created_by` (`created_by`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `#__collections_posts` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  `created_by` int(11) NOT NULL DEFAULT '0',
				  `collection_id` int(11) NOT NULL DEFAULT '0',
				  `item_id` int(11) NOT NULL DEFAULT '0',
				  `description` mediumtext NOT NULL,
				  `original` tinyint(2) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  KEY `idx_collection_id` (`collection_id`),
				  KEY `idx_item_id` (`item_id`),
				  KEY `idx_original` (`original`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `#__collections_votes` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `user_id` int(11) NOT NULL DEFAULT '0',
				  `item_id` int(11) NOT NULL DEFAULT '0',
				  `voted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  PRIMARY KEY (`id`),
				  KEY `idx_item_user` (`item_id`,`user_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

		$db->setQuery($query);
		$db->query();

		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "INSERT INTO `#__components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`)
					SELECT 'Collections', 'option=com_collections', 0, 0, 'option=com_collections', 'Collections', 'com_collections', 0, 'js/ThemeOffice/component.png', 0, '', 1
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__components` WHERE name = 'Collections');";
		}
		else
		{
			$query = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)
					SELECT 'Collections', 'component', 'com_collections', '', 0, 1, 1, 0, null, null, null, null, 0, '0000-00-00 00:00:00', 0, 0
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__extensions` WHERE name = 'Collections');";
		}
		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "DROP TABLE IF EXISTS `#__collections`;
				DROP TABLE IF EXISTS `#__collections_assets`;
				DROP TABLE IF EXISTS `#__collections_following`;
				DROP TABLE IF EXISTS `#__collections_items`;
				DROP TABLE IF EXISTS `#__collections_posts`;
				DROP TABLE IF EXISTS `#__collections_votes`;";
		$db->setQuery($query);
		$db->query();

		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "DELETE FROM `#__components` WHERE `option`='com_collections';";
		}
		else
		{
			$query = "DELETE FROM `#__extensions` WHERE `type`='component' AND `element`='com_collections';";
		}
		$db->setQuery($query);
		$db->query();
	}
}
