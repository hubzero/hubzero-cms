<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for deleting com_weblinks
 **/
class Migration20140110132217ComWeblinks extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_weblinks';";

		$this->db->setQuery($query);

		if ($id = $this->db->loadResult())
		{
			$this->deleteComponentEntry('weblinks');

			$this->deletePluginEntry('search', 'weblinks');

			$this->deleteModuleEntry('mod_weblinks');

			$query = "SELECT `id` FROM `#__modules` WHERE `module`='mod_weblinks';";
			$this->db->setQuery($query);
			if ($results = $this->db->loadResultArray())
			{
				$query = "DELETE FROM `#__modules_menu` WHERE `moduleid` IN (" . implode(',', $results) . ");";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "DELETE FROM `#__modules` WHERE `module`='mod_weblinks';";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$query = "DROP TABLE IF EXISTS `#__weblinks`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_weblinks';";

		$this->db->setQuery($query);

		if (!($id = $this->db->loadResult()))
		{
			$this->addComponentEntry('weblinks');

			$this->addPluginEntry('weblinks', 'contacts', 0);

			if (!$this->db->tableExists('#__weblinks'))
			{
				$query = "CREATE TABLE `#__weblinks` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `catid` int(11) NOT NULL DEFAULT '0',
					  `sid` int(11) NOT NULL DEFAULT '0',
					  `title` varchar(250) NOT NULL DEFAULT '',
					  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
					  `url` varchar(250) NOT NULL DEFAULT '',
					  `description` text NOT NULL,
					  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `hits` int(11) NOT NULL DEFAULT '0',
					  `state` tinyint(1) NOT NULL DEFAULT '0',
					  `checked_out` int(11) NOT NULL DEFAULT '0',
					  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `ordering` int(11) NOT NULL DEFAULT '0',
					  `archived` tinyint(1) NOT NULL DEFAULT '0',
					  `approved` tinyint(1) NOT NULL DEFAULT '1',
					  `access` int(11) NOT NULL DEFAULT '1',
					  `params` text NOT NULL,
					  `language` char(7) NOT NULL DEFAULT '',
					  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
					  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
					  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
					  `metakey` text NOT NULL,
					  `metadesc` text NOT NULL,
					  `metadata` text NOT NULL,
					  `featured` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Set if link is featured.',
					  `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
					  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  PRIMARY KEY (`id`),
					  KEY `idx_access` (`access`),
					  KEY `idx_checkout` (`checked_out`),
					  KEY `idx_state` (`state`),
					  KEY `idx_catid` (`catid`),
					  KEY `idx_createdby` (`created_by`),
					  KEY `idx_featured_catid` (`featured`,`catid`),
					  KEY `idx_language` (`language`),
					  KEY `idx_xreference` (`xreference`)
					) ENGINE=MYISAM DEFAULT CHARSET=utf8;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
