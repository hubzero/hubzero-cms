<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding indices and setting default field value
 **/
class Migration20131108091700ComBlog extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__blog_entries'))
		{
			$query = "ALTER TABLE `#__blog_entries`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `created_by` `created_by` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `state` `state` TINYINT(2)  NOT NULL  DEFAULT '0',
					CHANGE `publish_up` `publish_up` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00',
					CHANGE `publish_down` `publish_down` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00',
					CHANGE `allow_comments` `allow_comments` TINYINT(2)  NOT NULL  DEFAULT '0',
					CHANGE `hits` `hits` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `group_id` `group_id` INT(11)  NOT NULL  DEFAULT '0',
					CHANGE `params` `params` TINYTEXT  NOT NULL,
					CHANGE `scope` `scope` VARCHAR(100)  NOT NULL  DEFAULT '',
					CHANGE `content` `content` TEXT  NOT NULL,
					CHANGE `alias` `alias` VARCHAR(255)  NOT NULL  DEFAULT '',
					CHANGE `title` `title` VARCHAR(255)  NOT NULL  DEFAULT ''
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__blog_entries', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__blog_entries` ADD INDEX `idx_created_by` (`created_by`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__blog_entries', 'idx_group_id'))
			{
				$query = "ALTER TABLE `#__blog_entries` ADD INDEX `idx_group_id` (`group_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__blog_entries', 'idx_alias'))
			{
				$query = "ALTER TABLE `#__blog_entries` ADD INDEX `idx_alias` (`alias`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__blog_comments'))
		{
			$query = "ALTER TABLE `#__blog_comments`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `parent` `parent` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `created_by` `created_by` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `created` `created` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00',
					CHANGE `entry_id` `entry_id` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `content` `content` TEXT  NOT NULL
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__blog_comments', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__blog_comments` ADD INDEX `idx_created_by` (`created_by`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__blog_comments', 'idx_parent'))
			{
				$query = "ALTER TABLE `#__blog_comments` ADD INDEX `idx_parent` (`parent`);";
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
		if ($this->db->tableExists('#__blog_entries'))
		{
			if ($this->db->tableHasKey('#__blog_entries', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__blog_entries` DROP INDEX `idx_created_by`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__blog_entries', 'idx_group_id'))
			{
				$query = "ALTER TABLE `#__blog_entries` DROP INDEX `idx_group_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__blog_entries', 'idx_alias'))
			{
				$query = "ALTER TABLE `#__blog_entries` DROP INDEX `idx_alias`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__blog_comments'))
		{
			if ($this->db->tableHasKey('#__blog_comments', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__blog_comments` DROP INDEX `idx_created_by`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__blog_comments', 'idx_parent'))
			{
				$query = "ALTER TABLE `#__blog_comments` DROP INDEX `idx_parent`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}