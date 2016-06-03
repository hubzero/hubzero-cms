<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding project description tables
 **/
class Migration20160603173202ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Create the project_descriptions table
		if (!$this->db->tableExists('#__project_descriptions'))
		{
			$createQuery = "CREATE TABLE `#__project_descriptions` (
  			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  			`project_id` int(11) NOT NULL,
  			`description_key` varchar(100) NOT NULL DEFAULT '',
  			`description_value` text NOT NULL,
  			`ordering` int(11) DEFAULT NULL,
  			PRIMARY KEY (`id`),
  			KEY `idx_user_id` (`project_id`)
				) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='Simple user profile storage table';";
			$this->db->setQuery($createQuery);
			$this->db->query();
		}

		// Create the project_description_options table
		if (!$this->db->tableExists('#__project_description_options'))
		{
			$createQuery = "CREATE TABLE `#__project_description_options` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `field_id` int(11) NOT NULL DEFAULT '0',
		  `value` varchar(255) NOT NULL DEFAULT '',
		  `label` varchar(255) NOT NULL DEFAULT '',
		  `ordering` int(11) NOT NULL DEFAULT '0',
		  `checked` tinyint(2) NOT NULL DEFAULT '0',
		  `dependents` tinytext,
		  PRIMARY KEY (`id`),
		  KEY `idx_field_id` (`field_id`)
			) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;";

			$this->db->setQuery($createQuery);
			$this->db->query();
		}

		// Create the project_description_options table
		if (!$this->db->tableExists('#__project_description_fields'))
		{
			$createQuery = "CREATE TABLE `#__project_description_fields` (
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
  			PRIMARY KEY (`id`),
  			KEY `idx_type` (`type`),
  			KEY `idx_access` (`access`)
				) 			ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;";
			$this->db->setQuery($createQuery);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// Drop project descriptions table
		if ($this->db->tableExists('#__project_descriptions'))
		{
			$dropQuery = "DROP TABLE `#__project_descriptions`;";
			$this->db->setQuery($dropQuery);
			$this->db->query();
		}

		// Drop the project_description_options table
		if ($this->db->tableExists('#__project_description_options'))
		{
			$dropQuery = "DROP TABLE `#__project_description_options`;";
			$this->db->setQuery($dropQuery);
			$this->db->query();
		}

		// Drop the project_description_options table
		if ($this->db->tableExists('#__project_description_fields'))
		{
			$dropQuery = "DROP TABLE `#__project_description_fields`;";
			$this->db->setQuery($dropQuery);
			$this->db->query();
		}
	}
}
