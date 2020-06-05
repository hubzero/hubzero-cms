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
 * Migration script for installing developer tables
 **/
class Migration20170901000000ComDeveloper extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__developer_access_tokens'))
		{
			$query = "CREATE TABLE `#__developer_access_tokens` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `application_id` int(11) NOT NULL,
			  `access_token` varchar(40) NOT NULL,
			  `uidNumber` int(11) DEFAULT NULL,
			  `expires` datetime DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  `scope` varchar(2000) DEFAULT NULL,
			  `state` int(11) DEFAULT '1',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__developer_applications'))
		{
			$query = "CREATE TABLE `#__developer_applications` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NOT NULL DEFAULT '',
			  `description` text,
			  `client_id` varchar(80) DEFAULT NULL,
			  `client_secret` varchar(80) NOT NULL,
			  `redirect_uri` varchar(2000) NOT NULL,
			  `grant_types` varchar(80) DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) DEFAULT NULL,
			  `state` int(11) DEFAULT '1',
			  `hub_account` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__developer_application_team_members'))
		{
			$query = "CREATE TABLE `#__developer_application_team_members` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `uidNumber` int(11) DEFAULT NULL,
			  `application_id` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__developer_authorization_codes'))
		{
			$query = "CREATE TABLE `#__developer_authorization_codes` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `application_id` int(11) NOT NULL,
			  `authorization_code` varchar(40) NOT NULL,
			  `uidNumber` int(11) DEFAULT NULL,
			  `redirect_uri` varchar(2000) DEFAULT NULL,
			  `expires` datetime NOT NULL,
			  `scope` varchar(2000) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__developer_rate_limit'))
		{
			$query = "CREATE TABLE `#__developer_rate_limit` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `application_id` int(11) DEFAULT NULL,
			  `uidNumber` int(11) DEFAULT NULL,
			  `ip` varchar(255) DEFAULT NULL,
			  `limit_short` int(11) DEFAULT NULL,
			  `limit_long` int(11) DEFAULT NULL,
			  `count_short` int(11) DEFAULT NULL,
			  `count_long` int(11) DEFAULT NULL,
			  `expires_short` datetime DEFAULT NULL,
			  `expires_long` datetime DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__developer_refresh_tokens'))
		{
			$query = "CREATE TABLE `#__developer_refresh_tokens` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `application_id` int(11) NOT NULL,
			  `refresh_token` varchar(40) NOT NULL,
			  `uidNumber` int(11) DEFAULT NULL,
			  `expires` datetime NOT NULL,
			  `created` datetime DEFAULT NULL,
			  `scope` varchar(2000) DEFAULT NULL,
			  `state` int(11) DEFAULT '1',
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
		if ($this->db->tableExists('#__developer_access_tokens'))
		{
			$query = "DROP TABLE IF EXISTS `#__developer_access_tokens`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__developer_application_team_members'))
		{
			$query = "DROP TABLE IF EXISTS `#__developer_application_team_members`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__developer_applications'))
		{
			$query = "DROP TABLE IF EXISTS `#__developer_applications`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__developer_authorization_codes'))
		{
			$query = "DROP TABLE IF EXISTS `#__developer_authorization_codes`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__developer_rate_limit'))
		{
			$query = "DROP TABLE IF EXISTS `#__developer_rate_limit`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__developer_refresh_tokens'))
		{
			$query = "DROP TABLE IF EXISTS `#__developer_refresh_tokens`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
