<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding tables for developer component
 **/
class Migration20150610123746ComDeveloper extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// add applications table
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
						) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// add application team members table
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

		// add access token table
		if (!$this->db->tableExists('#__developer_access_tokens'))
		{
			$query = "CREATE TABLE `#__developer_access_tokens` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `application_id` int(11) NOT NULL,
						  `access_token` varchar(40) NOT NULL,
						  `uidNumber` int(11) DEFAULT NULL,
						  `expires` datetime NOT NULL,
						  `created` datetime DEFAULT NULL,
						  `scope` varchar(2000) DEFAULT NULL,
						  `state` int(11) DEFAULT '1',
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// add authorization codes table
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
						) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// add refresh tokens table
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
						) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// remove applications table
		if ($this->db->tableExists('#__developer_applications'))
		{
			$query = 'DROP TABLE `#__developer_applications`';
			$this->db->setQuery($query);
			$this->db->query();
		}

		// remove application team members table
		if ($this->db->tableExists('#__developer_application_team_members'))
		{
			$query = "DROP TABLE `#__developer_application_team_members`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// remove access token table
		if ($this->db->tableExists('#__developer_access_tokens'))
		{
			$query = "DROP TABLE `#__developer_access_tokens`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// remove authorization codes table
		if ($this->db->tableExists('#__developer_authorization_codes'))
		{
			$query = "DROP TABLE `#__developer_authorization_codes`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// remove refresh tokens table
		if ($this->db->tableExists('#__developer_refresh_tokens'))
		{
			$query = "DROP TABLE `#__developer_refresh_tokens`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}