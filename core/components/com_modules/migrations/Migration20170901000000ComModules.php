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
 * Migration script for installing com_modules tables
 **/
class Migration20170901000000ComModules extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__modules'))
		{
			$query = "CREATE TABLE `#__modules` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `title` varchar(100) NOT NULL DEFAULT '',
			  `note` varchar(255) NOT NULL DEFAULT '',
			  `content` text NOT NULL,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `position` varchar(50) NOT NULL DEFAULT '',
			  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
			  `checked_out_time` datetime DEFAULT NULL,
			  `publish_up` datetime DEFAULT NULL,
			  `publish_down` datetime DEFAULT NULL,
			  `published` tinyint(1) NOT NULL DEFAULT '0',
			  `module` varchar(50) DEFAULT NULL,
			  `access` int(10) unsigned NOT NULL DEFAULT '0',
			  `showtitle` tinyint(3) unsigned NOT NULL DEFAULT '1',
			  `params` text NOT NULL,
			  `client_id` tinyint(4) NOT NULL DEFAULT '0',
			  `language` char(7) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `published` (`published`,`access`),
			  KEY `newsfeeds` (`module`,`published`),
			  KEY `idx_language` (`language`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__modules_menu'))
		{
			$query = "CREATE TABLE `#__modules_menu` (
			  `moduleid` int(11) NOT NULL DEFAULT '0',
			  `menuid` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`moduleid`,`menuid`)
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
		if ($this->db->tableExists('#__modules'))
		{
			$query = "DROP TABLE IF EXISTS `#__modules`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__modules_menu'))
		{
			$query = "DROP TABLE IF EXISTS `#__modules_menu`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
