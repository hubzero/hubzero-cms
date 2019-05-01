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
 * Migration script for installing menu tables
 **/
class Migration20170901000000ComMenus extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__menu'))
		{
			$query = "CREATE TABLE `#__menu` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `menutype` varchar(24) NOT NULL COMMENT 'The type of menu this item belongs to. FK to #__menu_types.menutype',
			  `title` varchar(255) NOT NULL COMMENT 'The display title of the menu item.',
			  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'The SEF alias of the menu item.',
			  `note` varchar(255) NOT NULL DEFAULT '',
			  `path` varchar(1024) NOT NULL COMMENT 'The computed path of the menu item based on the alias field.',
			  `link` varchar(1024) NOT NULL COMMENT 'The actually link the menu item refers to.',
			  `type` varchar(16) NOT NULL COMMENT 'The type of link: Component, URL, Alias, Separator',
			  `published` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'The published state of the menu link.',
			  `parent_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'The parent menu item in the menu tree.',
			  `level` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The relative level in the tree.',
			  `component_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to #__extensions.id',
			  `ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'The relative ordering of the menu item in the tree.',
			  `checked_out` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to #__users.id',
			  `checked_out_time` timestamp DEFAULT NULL COMMENT 'The time the menu item was checked out.',
			  `browserNav` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'The click behaviour of the link.',
			  `access` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The access level required to view the menu item.',
			  `img` varchar(255) NOT NULL COMMENT 'The image of the menu item.',
			  `template_style_id` int(10) unsigned NOT NULL DEFAULT '0',
			  `params` text NOT NULL COMMENT 'JSON encoded data for the menu item.',
			  `lft` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.',
			  `rgt` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.',
			  `home` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Indicates if this menu item is the home or default page.',
			  `language` char(7) NOT NULL DEFAULT '',
			  `client_id` tinyint(4) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_componentid` (`component_id`,`menutype`,`published`,`access`),
			  KEY `idx_menutype` (`menutype`),
			  KEY `idx_left_right` (`lft`,`rgt`),
			  KEY `idx_alias` (`alias`),
			  KEY `idx_path` (`path`(333)),
			  KEY `idx_language` (`language`),
			  KEY `idx_client_id_parent_id_alias_language` (`client_id`,`parent_id`,`alias`,`language`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();

			$query = "INSERT INTO `#__menu` VALUES (1,'','Menu_Item_Root','root','','','','',1,0,0,0,0,0,NULL,0,0,'',0,'',0,1,0,'*',0);";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__menu_types'))
		{
			$query = "CREATE TABLE `#__menu_types` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `menutype` varchar(24) NOT NULL,
			  `title` varchar(48) NOT NULL,
			  `description` varchar(255) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_menutype` (`menutype`)
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
		if ($this->db->tableExists('#__menu'))
		{
			$query = "DROP TABLE IF EXISTS `#__menu`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__menu_types'))
		{
			$query = "DROP TABLE IF EXISTS `#__menu_types`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
