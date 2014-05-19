<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for deleting com_banners
 **/
class Migration20140110130812ComBanners extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_banners';";

		$this->db->setQuery($query);

		if ($id = $this->db->loadResult())
		{
			$this->deleteComponentEntry('banners');

			$this->deleteModuleEntry('mod_banners');

			$query = "SELECT `id` FROM `#__modules` WHERE `module`='mod_banners';";
			$this->db->setQuery($query);
			if ($results = $this->db->loadResultArray())
			{
				$query = "DELETE FROM `#__modules_menu` WHERE `moduleid` IN (" . implode(',', $results) . ");";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "DELETE FROM `#__modules` WHERE `module`='mod_banners';";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$query = "DROP TABLE IF EXISTS `#__banner_clients`;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__banner_tracks`;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__banners`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_banners';";

		$this->db->setQuery($query);

		if (!($id = $this->db->loadResult()))
		{
			$this->addComponentEntry('banners');

			if (!$this->db->tableExists('#__banner_clients'))
			{
				$query = "CREATE TABLE `#__banner_clients` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `name` varchar(255) NOT NULL DEFAULT '',
					  `contact` varchar(255) NOT NULL DEFAULT '',
					  `email` varchar(255) NOT NULL DEFAULT '',
					  `extrainfo` text NOT NULL,
					  `state` tinyint(3) NOT NULL DEFAULT '0',
					  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
					  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `metakey` text NOT NULL,
					  `own_prefix` tinyint(4) NOT NULL DEFAULT '0',
					  `metakey_prefix` varchar(255) NOT NULL DEFAULT '',
					  `purchase_type` tinyint(4) NOT NULL DEFAULT '-1',
					  `track_clicks` tinyint(4) NOT NULL DEFAULT '-1',
					  `track_impressions` tinyint(4) NOT NULL DEFAULT '-1',
					  PRIMARY KEY (`id`),
					  KEY `idx_own_prefix` (`own_prefix`),
					  KEY `idx_metakey_prefix` (`metakey_prefix`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableExists('#__banner_tracks'))
			{
				$query = "CREATE TABLE `#__banner_tracks` (
					  `track_date` datetime NOT NULL,
					  `track_type` int(10) unsigned NOT NULL,
					  `banner_id` int(10) unsigned NOT NULL,
					  `count` int(10) unsigned NOT NULL DEFAULT '0',
					  PRIMARY KEY (`track_date`,`track_type`,`banner_id`),
					  KEY `idx_track_date` (`track_date`),
					  KEY `idx_track_type` (`track_type`),
					  KEY `idx_banner_id` (`banner_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableExists('#__banners'))
			{
				$query = "CREATE TABLE `#__banners` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `cid` int(11) NOT NULL DEFAULT '0',
					  `type` int(11) NOT NULL DEFAULT '0',
					  `name` varchar(255) NOT NULL DEFAULT '',
					  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
					  `imptotal` int(11) NOT NULL DEFAULT '0',
					  `impmade` int(11) NOT NULL DEFAULT '0',
					  `clicks` int(11) NOT NULL DEFAULT '0',
					  `clickurl` varchar(200) NOT NULL DEFAULT '',
					  `state` tinyint(3) NOT NULL DEFAULT '0',
					  `catid` int(10) unsigned NOT NULL DEFAULT '0',
					  `description` text NOT NULL,
					  `custombannercode` varchar(2048) NOT NULL,
					  `sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
					  `ordering` int(11) NOT NULL DEFAULT '0',
					  `metakey` text NOT NULL,
					  `params` text NOT NULL,
					  `own_prefix` tinyint(1) NOT NULL DEFAULT '0',
					  `metakey_prefix` varchar(255) NOT NULL DEFAULT '',
					  `purchase_type` tinyint(4) NOT NULL DEFAULT '-1',
					  `track_clicks` tinyint(4) NOT NULL DEFAULT '-1',
					  `track_impressions` tinyint(4) NOT NULL DEFAULT '-1',
					  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
					  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `reset` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `language` char(7) NOT NULL DEFAULT '',
					  PRIMARY KEY (`id`),
					  KEY `idx_banner_catid` (`catid`),
					  KEY `idx_own_prefix` (`own_prefix`),
					  KEY `idx_metakey_prefix` (`metakey_prefix`),
					  KEY `idx_language` (`language`),
					  KEY `idx_state` (`state`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}