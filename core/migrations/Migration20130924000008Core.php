<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for languages table addition
 **/
class Migration20130924000008Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__languages'))
		{
			$query = "CREATE TABLE `#__languages` (
							`lang_id` int(11) unsigned NOT NULL auto_increment,
							`lang_code` char(7) NOT NULL,
							`title` varchar(50) NOT NULL,
							`title_native` varchar(50) NOT NULL,
							`sef` varchar(50) NOT NULL,
							`image` varchar(50) NOT NULL,
							`description` varchar(512) NOT NULL,
							`metakey` text NOT NULL,
							`metadesc` text NOT NULL,
							`sitename` VARCHAR(1024) NOT NULL DEFAULT '',
							`published` int(11) NOT NULL default '0',
							`access` INT(10) UNSIGNED NOT NULL DEFAULT '0',
							`ordering` int(11) NOT NULL DEFAULT 0,
							PRIMARY KEY  (`lang_id`),
							UNIQUE `idx_sef` (`sef`),
							UNIQUE INDEX `idx_image` (`image` ASC),
							UNIQUE INDEX `idx_langcode` (`lang_code` ASC),
							INDEX `idx_access` (`access` ASC),
							INDEX `idx_ordering` (`ordering`)
						)  DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();

			$query  = "INSERT INTO `#__languages` (`lang_id`,`lang_code`,`title`,`title_native`,`sef`,`image`,`description`,`metakey`,`metadesc`, `published`, `access`, `ordering`)";
			$query .= " VALUES ";
			$query .= "(1, 'en-GB', 'English (UK)', 'English (UK)', 'en', 'en', '', '', '', 1, 1, 1);";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
