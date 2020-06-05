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
 * Migration script for adding component entry for com_languages
 **/
class Migration20170901000000ComLanguages extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__languages'))
		{
			$query = "CREATE TABLE `#__languages` (
			  `lang_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `lang_code` char(7) NOT NULL,
			  `title` varchar(50) NOT NULL,
			  `title_native` varchar(50) NOT NULL,
			  `sef` varchar(50) NOT NULL,
			  `image` varchar(50) NOT NULL,
			  `description` varchar(512) NOT NULL,
			  `metakey` text NOT NULL,
			  `metadesc` text NOT NULL,
			  `sitename` varchar(1024) NOT NULL DEFAULT '',
			  `published` int(11) NOT NULL DEFAULT '0',
			  `access` int(10) unsigned NOT NULL DEFAULT '0',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`lang_id`),
			  UNIQUE KEY `idx_sef` (`sef`),
			  UNIQUE KEY `idx_image` (`image`),
			  UNIQUE KEY `idx_langcode` (`lang_code`),
			  KEY `idx_access` (`access`),
			  KEY `idx_ordering` (`ordering`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();

			$query = "INSERT INTO `#__languages` VALUES (1,'en-GB','English (UK)','English (UK)','en','en','','','','',1,1,1)";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__overrider'))
		{
			$query = "CREATE TABLE `#__overrider` (
			  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
			  `constant` varchar(255) NOT NULL,
			  `string` text NOT NULL,
			  `file` varchar(255) NOT NULL,
			  PRIMARY KEY (`id`)
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
		if ($this->db->tableExists('#__languages'))
		{
			$query = "DROP TABLE IF EXISTS `#__languages`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__overrider'))
		{
			$query = "DROP TABLE IF EXISTS `#__overrider`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
