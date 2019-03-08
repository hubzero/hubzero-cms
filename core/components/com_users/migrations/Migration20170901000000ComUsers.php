<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing users tables
 **/
class Migration20170901000000ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__users'))
		{
			$query = "CREATE TABLE `#__users` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NOT NULL DEFAULT '',
			  `givenName` varchar(255) NOT NULL,
			  `middleName` varchar(255) NOT NULL,
			  `surname` varchar(255) NOT NULL,
			  `username` varchar(150) NOT NULL DEFAULT '',
			  `email` varchar(100) NOT NULL DEFAULT '',
			  `password` varchar(127) NOT NULL DEFAULT '',
			  `usertype` varchar(25) NOT NULL DEFAULT '',
			  `block` tinyint(4) NOT NULL DEFAULT '0',
			  `approved` tinyint(4) NOT NULL DEFAULT '2',
			  `sendEmail` tinyint(4) DEFAULT '0',
			  `registerDate` datetime DEFAULT NULL,
			  `registerIP` varchar(40) NOT NULL DEFAULT '',
			  `lastvisitDate` datetime DEFAULT NULL,
			  `activation` int(11) NOT NULL DEFAULT '0',
			  `params` text NOT NULL,
			  `lastResetTime` datetime DEFAULT NULL COMMENT 'Date of last password reset',
			  `resetCount` int(11) NOT NULL DEFAULT '0' COMMENT 'Count of password resets since lastResetTime',
			  `access` int(10) NOT NULL DEFAULT '0',
			  `usageAgreement` tinyint(2) NOT NULL DEFAULT '0',
			  `homeDirectory` varchar(255) NOT NULL,
			  `loginShell` varchar(255) NOT NULL,
			  `ftpShell` varchar(255) NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_username` (`username`),
			  KEY `usertype` (`usertype`),
			  KEY `idx_name` (`name`),
			  KEY `idx_block` (`block`),
			  KEY `username` (`username`),
			  KEY `email` (`email`),
			  FULLTEXT KEY `ftidx_fullname` (`givenName`,`middleName`,`surname`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__usergroups'))
		{
			$query = "CREATE TABLE `#__usergroups` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
			  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Adjacency List Reference Id',
			  `lft` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.',
			  `rgt` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.',
			  `title` varchar(100) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `idx_usergroup_parent_title_lookup` (`parent_id`,`title`),
			  KEY `idx_usergroup_title_lookup` (`title`),
			  KEY `idx_usergroup_adjacency_lookup` (`parent_id`),
			  KEY `idx_usergroup_nested_set_lookup` (`lft`,`rgt`) USING BTREE
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__user_usergroup_map'))
		{
			$query = "CREATE TABLE `#__user_usergroup_map` (
			  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__users.id',
			  `group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__usergroups.id',
			  PRIMARY KEY (`user_id`,`group_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__user_notes'))
		{
			$query = "CREATE TABLE `#__user_notes` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
			  `catid` int(10) unsigned NOT NULL DEFAULT '0',
			  `subject` varchar(100) NOT NULL DEFAULT '',
			  `body` text NOT NULL,
			  `state` tinyint(3) NOT NULL DEFAULT '0',
			  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
			  `checked_out_time` datetime DEFAULT NULL,
			  `created_user_id` int(10) unsigned NOT NULL DEFAULT '0',
			  `created_time` datetime DEFAULT NULL,
			  `modified_user_id` int(10) unsigned NOT NULL,
			  `modified_time` datetime DEFAULT NULL,
			  `review_time` datetime DEFAULT NULL,
			  `publish_up` datetime DEFAULT NULL,
			  `publish_down` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_user_id` (`user_id`),
			  KEY `idx_category_id` (`catid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__user_profiles'))
		{
			$query = "CREATE TABLE `#__user_profiles` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL,
			  `profile_key` varchar(100) NOT NULL,
			  `profile_value` text NOT NULL,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `access` int(10) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Simple user profile storage table';";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__user_profile_fields'))
		{
			$query = "CREATE TABLE `#__user_profile_fields` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `type` varchar(255) NOT NULL,
			  `name` varchar(255) NOT NULL DEFAULT '',
			  `label` varchar(255) NOT NULL DEFAULT '',
			  `placeholder` varchar(255) DEFAULT NULL,
			  `description` mediumtext,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `access` int(10) NOT NULL DEFAULT '0',
			  `option_other` tinyint(2) NOT NULL DEFAULT '0',
			  `option_blank` tinyint(2) NOT NULL DEFAULT '0',
			  `action_create` tinyint(2) NOT NULL DEFAULT '1',
			  `action_update` tinyint(2) NOT NULL DEFAULT '1',
			  `action_edit` tinyint(2) NOT NULL DEFAULT '1',
			  `action_browse` tinyint(2) NOT NULL DEFAULT '0',
			  `min` int(11) NOT NULL DEFAULT '0',
			  `max` int(11) NOT NULL DEFAULT '0',
			  `default_value` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_type` (`type`),
			  KEY `idx_access` (`access`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__user_profile_options'))
		{
			$query = "CREATE TABLE `#__user_profile_options` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `field_id` int(11) NOT NULL DEFAULT '0',
			  `value` varchar(255) NOT NULL DEFAULT '',
			  `label` varchar(255) NOT NULL DEFAULT '',
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `checked` tinyint(2) NOT NULL DEFAULT '0',
			  `dependents` tinytext,
			  PRIMARY KEY (`id`),
			  KEY `idx_field_id` (`field_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__user_reputation'))
		{
			$query = "CREATE TABLE `#__user_reputation` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) DEFAULT NULL,
			  `spam_count` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_user_id` (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__user_roles'))
		{
			$query = "CREATE TABLE `#__user_roles` (
			  `user_id` int(11) NOT NULL,
			  `role` varchar(20) NOT NULL,
			  `group_id` int(11) DEFAULT NULL,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_role_user_id_group_id` (`role`,`user_id`,`group_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__users_password'))
		{
			$query = "CREATE TABLE `#__users_password` (
			  `user_id` int(11) NOT NULL,
			  `passhash` char(127) NOT NULL,
			  `shadowExpire` int(11) DEFAULT NULL,
			  `shadowFlag` int(11) DEFAULT NULL,
			  `shadowInactive` int(11) DEFAULT NULL,
			  `shadowLastChange` int(11) DEFAULT NULL,
			  `shadowMax` int(11) DEFAULT NULL,
			  `shadowMin` int(11) DEFAULT NULL,
			  `shadowWarning` int(11) DEFAULT NULL,
			  PRIMARY KEY (`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__users_password_history'))
		{
			$query = "CREATE TABLE `#__users_password_history` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL,
			  `passhash` char(127) NOT NULL DEFAULT '',
			  `action` int(11) DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) DEFAULT NULL,
			  `invalidated` datetime DEFAULT NULL,
			  `invalidated_by` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_user_id` (`user_id`)
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
		if ($this->db->tableExists('#__users'))
		{
			$query = "DROP TABLE IF EXISTS `#__users`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__usergroups'))
		{
			$query = "DROP TABLE IF EXISTS `#__usergroups`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__user_usergroup_map'))
		{
			$query = "DROP TABLE IF EXISTS `#__user_usergroup_map`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__user_notes'))
		{
			$query = "DROP TABLE IF EXISTS `#__user_notes`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__user_profiles'))
		{
			$query = "DROP TABLE IF EXISTS `#__user_profiles`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__user_profile_fields'))
		{
			$query = "DROP TABLE IF EXISTS `#__user_profile_fields`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__user_profile_options'))
		{
			$query = "DROP TABLE IF EXISTS `#__user_profile_options`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__user_reputation'))
		{
			$query = "DROP TABLE IF EXISTS `#__user_reputation`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__user_roles'))
		{
			$query = "DROP TABLE IF EXISTS `#__user_roles`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__users_password'))
		{
			$query = "DROP TABLE IF EXISTS `#__users_password`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__users_password_history'))
		{
			$query = "DROP TABLE IF EXISTS `#__users_password_history`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
