<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for group pages updates
 **/
class Migration20140108233318ComGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// a bunch of name changes
		if (!$this->db->tableHasField('#__xgroups_pages', 'gidNumber'))
		{
			$query = "ALTER TABLE `#__xgroups_pages` CHANGE `gid` `gidNumber` int(11);
			          ALTER TABLE `#__xgroups_pages` CHANGE `url` `alias` varchar(100);
			          ALTER TABLE `#__xgroups_pages` CHANGE `porder` `ordering` int(11);
			          ALTER TABLE `#__xgroups_pages` CHANGE `active` `state` int(11) DEFAULT 1;
			          ALTER TABLE `#__xgroups_pages` ADD COLUMN `home` int(11) DEFAULT 0;
			          ALTER TABLE `#__xgroups_pages` ADD COLUMN `category` int(11) AFTER `gidNumber`;
			          ALTER TABLE `#__xgroups_pages` ADD COLUMN `template` VARCHAR(100) AFTER `category`;
			          ALTER TABLE `#__xgroups_pages_hits` CHANGE `gid` `gidNumber` int(11);
			          ALTER TABLE `#__xgroups_pages_hits` CHANGE `pid` `pageid` int(11);
			          ALTER TABLE `#__xgroups_pages_hits` CHANGE `uid` `userid` int(11);
			          ALTER TABLE `#__xgroups_pages_hits` CHANGE `datetime` `date` datetime;
			          ALTER TABLE `#__xgroups_log` CHANGE `gid` `gidNumber` int(11);
			          ALTER TABLE `#__xgroups_log` CHANGE `uid` `userid` int(11);";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// create page versions table
		if (!$this->db->tableExists('#__xgroups_pages_versions'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__xgroups_pages_versions` (
			             `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			             `pageid` int(11) DEFAULT NULL,
			             `version` int(11) DEFAULT NULL,
			             `content` longtext,
			             `created` datetime DEFAULT NULL,
			             `created_by` int(11) DEFAULT NULL,
			             `approved` int(11) DEFAULT '1',
			             `approved_on` datetime DEFAULT NULL,
			             `approved_by` int(11) DEFAULT NULL,
			             `checked_errors` int(11) DEFAULT 0,
			             `scanned` int(11) DEFAULT 0,
			             PRIMARY KEY (`id`)
			             ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// create page category table
		if (!$this->db->tableExists('#__xgroups_pages_categories'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__xgroups_pages_categories` (
			             `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			              `gidNumber` int(11) DEFAULT NULL,
			              `title` varchar(255) DEFAULT NULL,
			              `color` varchar(6) DEFAULT NULL,
			              PRIMARY KEY (`id`)
			          ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// create page category table
		if (!$this->db->tableExists('#__xgroups_modules'))
		{
			$query = "CREATE TABLE `#__xgroups_modules` (
		                 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		                 `gidNumber` int(11) DEFAULT NULL,
		                 `title` varchar(255) DEFAULT '',
		                 `content` text,
		                 `position` varchar(50) DEFAULT NULL,
		                 `ordering` int(11) DEFAULT NULL,
		                 `state` int(1) DEFAULT NULL,
		                 `created` datetime DEFAULT NULL,
		                 `created_by` int(11) DEFAULT NULL,
		                 `modified` datetime DEFAULT NULL,
		                 `modified_by` int(11) DEFAULT NULL,
		                 `approved` int(11) DEFAULT '1',
		                 `approved_on` datetime DEFAULT NULL,
		                 `approved_by` int(11) DEFAULT NULL,
		                 `checked_errors` int(11) DEFAULT 0,
		                 `scanned` int(11) DEFAULT 0,
		                 PRIMARY KEY (`id`)
		              ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// create page category table
		if (!$this->db->tableExists('#__xgroups_modules_menu'))
		{
			$query = "CREATE TABLE `#__xgroups_modules_menu` (
			             `moduleid` int(11) DEFAULT NULL,
			             `pageid` int(11) DEFAULT NULL
			          ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// remove content field from pages table
		if ($this->db->tableHasField('#__xgroups_pages', 'content'))
		{
			$query  = "INSERT INTO `#__xgroups_pages_versions` (pageid, version, content, created, created_by, approved, approved_on, approved_by) SELECT id as pageid, 1, content, NOW(), 1000, 1, NOW(), 1000 FROM `#__xgroups_pages`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// if the groups table still has the home page overview content
		if ($this->db->tableHasField('#__xgroups', 'overview_type'))
		{
			// get list of groups
			$query = "SELECT `gidNumber`, `overview_type`, `overview_content` FROM `#__xgroups` WHERE `type` IN(1,3)";
			$this->db->setQuery($query);
			$groups = $this->db->loadObjectList();

			// loop through each group
			foreach ($groups as $group)
			{
				// if group has overview content
				if (trim($group->overview_content) != '')
				{
					// create page to store page info
					$query = "INSERT INTO `#__xgroups_pages` (`gidNumber`, `alias`,`title`, `ordering`,`state`, `privacy`, `home`)
					          VALUES(".$this->db->quote($group->gidNumber).",".$this->db->quote('home_page').",".$this->db->quote('Home Page').",0,1,'default',".$this->db->quote($group->overview_type).");";
					$this->db->setQuery($query);
					$this->db->query();

					// create page version to store page content
					$query2 = "INSERT INTO `#__xgroups_pages_versions` (`pageid`,`version`,`content`,`created`,`created_by`,`approved`,`approved_on`,`approved_by`)
					           VALUES(".$this->db->insertid().",1,".$this->db->quote($group->overview_content).",NOW(),1000,1, NOW(), 1000);";
					$this->db->setQuery($query2);
					$this->db->query();
				}
			}

			if ($this->db->tableHasField('#__xgroups_pages', 'content'))
			{
				$query = "ALTER TABLE `#__xgroups_pages` DROP COLUMN `content`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__xgroups', 'overview_type'))
			{
				$query  = "ALTER TABLE `#__xgroups` DROP COLUMN `overview_type`;";
				$query .= "ALTER TABLE `#__xgroups` DROP COLUMN `overview_content`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		//add overview type back
		if (!$this->db->tableHasField('#__xgroups', 'overview_type'))
		{
			$query  = "ALTER TABLE `#__xgroups` ADD COLUMN `overview_type` int(11) AFTER `logo`;";
			$query .= "ALTER TABLE `#__xgroups` ADD COLUMN `overview_content` TEXT AFTER `overview_type`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		//move pages back
		if (!$this->db->tableHasField('#__xgroups_pages', 'content'))
		{
			$query = "ALTER TABLE `#__xgroups_pages` ADD COLUMN `content` TEXT AFTER `title`;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "UPDATE `#__xgroups_pages` AS p SET content=(SELECT content FROM `#__xgroups_pages_versions` as pv WHERE p.id=pv.pageid ORDER BY version DESC LIMIT 1);";
			$this->db->setQuery($query);
			$this->db->query();
		}

		//move home content back to xgroups table and delete those records
		if ($this->db->tableHasField('#__xgroups_pages', 'home'))
		{
			$query = "UPDATE `#__xgroups` as g SET overview_content=(SELECT content FROM `#__xgroups_pages` as p WHERE g.gidNumber=p.gidNumber AND p.alias='home_page'), overview_type=(SELECT home FROM `#__xgroups_pages` as p WHERE g.gidNumber=p.gidNumber AND p.alias='home_page');";
			$this->db->setQuery($query);
			$this->db->query();

			// delete all moved home pages
			$query = "DELETE FROM `#__xgroups_pages` WHERE `alias`='home_page';";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// a bunch of name changes
		if ($this->db->tableHasField('#__xgroups_pages', 'gidNumber'))
		{
			$query = "ALTER TABLE `#__xgroups_pages` CHANGE `gidNumber` `gid` int(11);
			          ALTER TABLE `#__xgroups_pages` CHANGE `alias` `url` varchar(100);
			          ALTER TABLE `#__xgroups_pages` CHANGE `ordering` `porder` int(11);
			          ALTER TABLE `#__xgroups_pages` CHANGE `state` `active` int(11);
			          ALTER TABLE `#__xgroups_pages` DROP COLUMN `home`;
			          ALTER TABLE `#__xgroups_pages` DROP COLUMN `category`;
			          ALTER TABLE `#__xgroups_pages` DROP COLUMN `template`;
			          ALTER TABLE `#__xgroups_pages_hits` CHANGE `gidNumber` `gid` int(11);
			          ALTER TABLE `#__xgroups_pages_hits` CHANGE `pageid` `pid` int(11);
			          ALTER TABLE `#__xgroups_pages_hits` CHANGE `userid` `uid` int(11);
			          ALTER TABLE `#__xgroups_pages_hits` CHANGE `date` `datetime` datetime;
			          ALTER TABLE `#__xgroups_log` CHANGE `gidNumber` `gid` int(11);
			          ALTER TABLE `#__xgroups_log` CHANGE `userid` `uid` int(11);";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// delete page version table
		if ($this->db->tableExists('#__xgroups_pages_versions'))
		{
			$query = "DROP TABLE #__xgroups_pages_versions;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// delete categories table
		if ($this->db->tableExists('#__xgroups_pages_categories'))
		{
			$query = "DROP TABLE #__xgroups_pages_categories;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// delete modules table
		if ($this->db->tableExists('#__xgroups_modules'))
		{
			$query = "DROP TABLE #__xgroups_modules;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// delete  modules menu table
		if ($this->db->tableExists('#__xgroups_modules_menu'))
		{
			$query = "DROP TABLE #__xgroups_modules_menu;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}