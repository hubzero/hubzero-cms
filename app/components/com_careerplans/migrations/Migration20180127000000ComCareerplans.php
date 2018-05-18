<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing career plan tables
 **/
class Migration20180127000000ComCareerplans extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__careerplans'))
		{
			$query = "CREATE TABLE `#__careerplans` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(3) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_user_id` (`user_id`),
			  KEY `idx_state` (`state`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__careerplans_answers'))
		{
			$query = "CREATE TABLE `#__careerplans_answers` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `careerplan_id` int(11) NOT NULL,
			  `field_id` int(11) NOT NULL DEFAULT '0',
			  `value` text NOT NULL,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `idx_careerplan_id` (`careerplan_id`),
			  KEY `idx_field_id` (`field_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__careerplans_goals'))
		{
			$query = "CREATE TABLE `#__careerplans_goals` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `careerplan_id` int(11) NOT NULL,
			  `field_id` int(11) NOT NULL DEFAULT '0',
			  `goal` varchar(255) NOT NULL DEFAULT '',
			  `skills_needed` text NOT NULL,
			  `skills_level` text NOT NULL,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  `state` tinyint(2) NOT NULL DEFAULT '0',
			  `completed` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_careerplan_id` (`careerplan_id`),
			  KEY `idx_field_id` (`field_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__careerplans_goal_strategies'))
		{
			$query = "CREATE TABLE `#__careerplans_goal_strategies` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `goal_id` int(11) NOT NULL DEFAULT '0',
			  `content` text NOT NULL,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  `completed` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_goal_id` (`goal_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__careerplans_fields'))
		{
			$query = "CREATE TABLE `#__careerplans_fields` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `fieldset_id` int(11) NOT NULL DEFAULT '0',
			  `type` varchar(255) NOT NULL,
			  `name` varchar(255) NOT NULL DEFAULT '',
			  `label` varchar(255) NOT NULL DEFAULT '',
			  `placeholder` varchar(255) DEFAULT NULL,
			  `description` mediumtext,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `access` tinyint(3) NOT NULL DEFAULT '0',
			  `option_other` tinyint(2) NOT NULL DEFAULT '0',
			  `option_blank` tinyint(2) NOT NULL DEFAULT '0',
			  `required` tinyint(2) NOT NULL DEFAULT '0',
			  `readonly` tinyint(2) NOT NULL DEFAULT '0',
			  `disabled` tinyint(2) NOT NULL DEFAULT '0',
			  `min` int(11) DEFAULT NULL,
			  `max` int(11) DEFAULT NULL,
			  `rows` tinyint(3) DEFAULT NULL,
			  `cols` tinyint(3) DEFAULT NULL,
			  `default_value` varchar(255) DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  `parent_option` int(11) NOT NULL DEFAULT '0',
			  `validate` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_type` (`type`),
			  KEY `idx_fieldset_id` (`fieldset_id`),
			  KEY `idx_access` (`access`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__careerplans_fieldsets'))
		{
			$query = "CREATE TABLE `#__careerplans_fieldsets` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NOT NULL DEFAULT '',
			  `label` varchar(255) NOT NULL DEFAULT '',
			  `description` mediumtext,
			  `ordering` int(11) NOT NULL DEFAULT '0',
			  `created` datetime DEFAULT NULL,
			  `created_by` int(11) NOT NULL DEFAULT '0',
			  `modified` datetime DEFAULT NULL,
			  `modified_by` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__careerplans_options'))
		{
			$query = "CREATE TABLE `#__careerplans_options` (
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
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__applications'))
		{
			$query = "DROP TABLE IF EXISTS `#__applications`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__applications_answers'))
		{
			$query = "DROP TABLE IF EXISTS `#__applications_answers`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__applications_fields'))
		{
			$query = "DROP TABLE IF EXISTS `#__applications_fields`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__applications_fieldsets'))
		{
			$query = "DROP TABLE IF EXISTS `#__applications_fieldsets`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__applications_options'))
		{
			$query = "DROP TABLE IF EXISTS `#__applications_options`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
