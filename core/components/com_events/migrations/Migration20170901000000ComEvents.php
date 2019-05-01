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
 * Migration script for installing events tables
 **/
class Migration20170901000000ComEvents extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__events'))
		{
			$query = "CREATE TABLE `#__events` (
			  `id` int(12) NOT NULL AUTO_INCREMENT,
			  `catid` int(11) NOT NULL DEFAULT '1',
			  `calendar_id` int(11) DEFAULT NULL,
			  `ical_uid` varchar(255) DEFAULT NULL,
			  `scope` varchar(100) DEFAULT NULL,
			  `scope_id` int(11) DEFAULT NULL,
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `content` longtext NOT NULL,
			  `adresse_info` varchar(120) NOT NULL DEFAULT '',
			  `contact_info` varchar(120) NOT NULL DEFAULT '',
			  `extra_info` varchar(240) NOT NULL DEFAULT '',
			  `state` tinyint(3) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
			  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
			  `checked_out_time` datetime DEFAULT NULL,
			  `publish_up` datetime DEFAULT NULL,
			  `publish_down` datetime DEFAULT NULL,
			  `allday` int(11) DEFAULT '0',
			  `time_zone` varchar(5) DEFAULT NULL,
			  `repeating_rule` varchar(150) DEFAULT NULL,
			  `approved` tinyint(1) NOT NULL DEFAULT '1',
			  `registerby` datetime DEFAULT NULL,
			  `params` text,
			  `restricted` varchar(100) DEFAULT NULL,
			  `email` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  FULLTEXT KEY `ftidx_title` (`title`),
			  FULLTEXT KEY `ftidx_content` (`content`),
			  FULLTEXT KEY `ftidx_title_content` (`title`,`content`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__events_calendars'))
		{
			$query = "CREATE TABLE `#__events_calendars` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `scope` varchar(100) DEFAULT NULL,
			  `scope_id` int(11) DEFAULT NULL,
			  `title` varchar(100) DEFAULT NULL,
			  `color` varchar(100) DEFAULT NULL,
			  `published` int(11) DEFAULT '1',
			  `url` varchar(255) DEFAULT NULL,
			  `readonly` tinyint(4) DEFAULT '0',
			  `last_fetched` datetime DEFAULT NULL,
			  `last_fetched_attempt` datetime DEFAULT NULL,
			  `failed_attempts` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__events_categories'))
		{
			$query = "CREATE TABLE `#__events_categories` (
			  `id` int(12) NOT NULL DEFAULT '0',
			  `color` varchar(8) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__events_config'))
		{
			$query = "CREATE TABLE `#__events_config` (
			  `param` varchar(100) DEFAULT NULL,
			  `value` tinytext
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__event_registration'))
		{
			$query = "CREATE TABLE `#__event_registration` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `event` varchar(100) DEFAULT NULL,
			  `username` varchar(100) DEFAULT NULL,
			  `name` varchar(100) DEFAULT NULL,
			  `email` varchar(100) DEFAULT NULL,
			  `phone` varchar(100) DEFAULT NULL,
			  `institution` varchar(100) DEFAULT NULL,
			  `address` varchar(100) DEFAULT NULL,
			  `city` varchar(100) DEFAULT NULL,
			  `state` varchar(10) DEFAULT NULL,
			  `zip` varchar(10) DEFAULT NULL,
			  `submitted` datetime DEFAULT NULL,
			  `active` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__events_pages'))
		{
			$query = "CREATE TABLE `#__events_pages` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `event_id` int(11) DEFAULT '0',
			  `alias` varchar(100) NOT NULL,
			  `title` varchar(250) NOT NULL,
			  `pagetext` text,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) DEFAULT '0',
			  `ordering` int(2) DEFAULT '0',
			  `params` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__events_respondents'))
		{
			$query = "CREATE TABLE `#__events_respondents` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `event_id` int(11) NOT NULL DEFAULT '0',
			  `registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `first_name` varchar(50) NOT NULL,
			  `last_name` varchar(50) NOT NULL,
			  `affiliation` varchar(50) DEFAULT NULL,
			  `title` varchar(50) DEFAULT NULL,
			  `city` varchar(50) DEFAULT NULL,
			  `state` varchar(20) DEFAULT NULL,
			  `zip` varchar(10) DEFAULT NULL,
			  `country` varchar(20) DEFAULT NULL,
			  `telephone` varchar(20) DEFAULT NULL,
			  `fax` varchar(20) DEFAULT NULL,
			  `email` varchar(255) DEFAULT NULL,
			  `website` varchar(255) DEFAULT NULL,
			  `position_description` varchar(50) DEFAULT NULL,
			  `highest_degree` varchar(10) DEFAULT NULL,
			  `gender` char(1) DEFAULT NULL,
			  `disability_needs` tinyint(4) DEFAULT NULL,
			  `dietary_needs` varchar(500) DEFAULT NULL,
			  `attending_dinner` tinyint(4) DEFAULT NULL,
			  `abstract` text,
			  `comment` text,
			  `arrival` varchar(50) DEFAULT NULL,
			  `departure` varchar(50) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__events_respondent_race_rel'))
		{
			$query = "CREATE TABLE `#__events_respondent_race_rel` (
			  `respondent_id` int(11) DEFAULT NULL,
			  `race` varchar(255) DEFAULT NULL,
			  `tribal_affiliation` varchar(255) DEFAULT NULL
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
		if ($this->db->tableExists('#__events'))
		{
			$query = "DROP TABLE IF EXISTS `#__events`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__events_calendars'))
		{
			$query = "DROP TABLE IF EXISTS `#__events_calendars`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__events_categories'))
		{
			$query = "DROP TABLE IF EXISTS `#__events_categories`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__events_config'))
		{
			$query = "DROP TABLE IF EXISTS `#__events_config`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__events_registration'))
		{
			$query = "DROP TABLE IF EXISTS `#__events_registration`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__events_pages'))
		{
			$query = "DROP TABLE IF EXISTS `#__events_pages`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__events_respondents'))
		{
			$query = "DROP TABLE IF EXISTS `#__events_respondents`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__events_respondent_race_rel'))
		{
			$query = "DROP TABLE IF EXISTS `#__events_respondent_race_rel`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
