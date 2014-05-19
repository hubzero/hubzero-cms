<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for deleting com_contact
 **/
class Migration20140110132511ComContact extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_contact';";

		$this->db->setQuery($query);

		if ($id = $this->db->loadResult())
		{
			$this->deleteComponentEntry('contact');

			$this->deletePluginEntry('search', 'contacts');
			$this->deletePluginEntry('user', 'contactcreator');

			$query = "DROP TABLE IF EXISTS `#__contact_details`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_contact';";

		$this->db->setQuery($query);

		if (!($id = $this->db->loadResult()))
		{
			$this->addComponentEntry('contact');

			$this->addPluginEntry('search', 'contacts', 0);
			$this->addPluginEntry('user', 'contactcreator');

			if (!$this->db->tableExists('#__contact_details'))
			{
				$query = "CREATE TABLE `#__contact_details` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `name` varchar(255) NOT NULL DEFAULT '',
					  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
					  `con_position` varchar(255) DEFAULT NULL,
					  `address` text,
					  `suburb` varchar(100) DEFAULT NULL,
					  `state` varchar(100) DEFAULT NULL,
					  `country` varchar(100) DEFAULT NULL,
					  `postcode` varchar(100) DEFAULT NULL,
					  `telephone` varchar(255) DEFAULT NULL,
					  `fax` varchar(255) DEFAULT NULL,
					  `misc` mediumtext,
					  `image` varchar(255) DEFAULT NULL,
					  `imagepos` varchar(20) DEFAULT NULL,
					  `email_to` varchar(255) DEFAULT NULL,
					  `default_con` tinyint(1) unsigned NOT NULL DEFAULT '0',
					  `published` tinyint(1) NOT NULL DEFAULT '0',
					  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
					  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `ordering` int(11) NOT NULL DEFAULT '0',
					  `params` text NOT NULL,
					  `user_id` int(11) NOT NULL DEFAULT '0',
					  `catid` int(11) NOT NULL DEFAULT '0',
					  `access` int(10) unsigned NOT NULL DEFAULT '0',
					  `mobile` varchar(255) NOT NULL DEFAULT '',
					  `webpage` varchar(255) NOT NULL DEFAULT '',
					  `sortname1` varchar(255) NOT NULL,
					  `sortname2` varchar(255) NOT NULL,
					  `sortname3` varchar(255) NOT NULL,
					  `language` char(7) NOT NULL,
					  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
					  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
					  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
					  `metakey` text NOT NULL,
					  `metadesc` text NOT NULL,
					  `metadata` text NOT NULL,
					  `featured` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Set if article is featured.',
					  `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
					  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  PRIMARY KEY (`id`),
					  KEY `idx_access` (`access`),
					  KEY `idx_checkout` (`checked_out`),
					  KEY `idx_state` (`published`),
					  KEY `idx_catid` (`catid`),
					  KEY `idx_createdby` (`created_by`),
					  KEY `idx_featured_catid` (`featured`,`catid`),
					  KEY `idx_language` (`language`),
					  KEY `idx_xreference` (`xreference`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}