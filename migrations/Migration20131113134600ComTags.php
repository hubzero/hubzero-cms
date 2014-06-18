<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding indices and setting default field value
 **/
class Migration20131113134600ComTags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__tags'))
		{
			$query = "ALTER TABLE `#__tags`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `raw_tag` `raw_tag` VARCHAR(100)  NOT NULL  DEFAULT '',
					CHANGE `description` `description` TEXT  NOT NULL,
					CHANGE `admin` `admin` TINYINT(3)  UNSIGNED  NOT NULL  DEFAULT '0';
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__tags', 'idx_tag'))
			{
				$query = "ALTER TABLE `#__tags` ADD INDEX `idx_tag` (`tag`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__tags_object'))
		{
			$query = "ALTER TABLE `#__tags_object`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `objectid` `objectid` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `tagid` `tagid` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `strength` `strength` TINYINT(3)  NOT NULL  DEFAULT '0',
					CHANGE `taggerid` `taggerid` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `taggedon` `taggedon` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00',
					CHANGE `tbl` `tbl` VARCHAR(255)  NOT NULL  DEFAULT '',
					CHANGE `label` `label` VARCHAR(30)  NOT NULL  DEFAULT ''
			;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__tags_group'))
		{
			$query = "ALTER TABLE `#__tags_group`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `groupid` `groupid` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `tagid` `tagid` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `priority` `priority` INT(11)  NOT NULL  DEFAULT '0'
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__tags_group', 'idx_tagid'))
			{
				$query = "ALTER TABLE `#__tags_group` ADD INDEX `idx_tagid` (`tagid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__tags_group', 'idx_groupid'))
			{
				$query = "ALTER TABLE `#__tags_group` ADD INDEX `idx_groupid` (`groupid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__tags_log'))
		{
			$query = "ALTER TABLE `#__tags_log`
					CHANGE `id` `id` INT(11)  UNSIGNED  NOT NULL  AUTO_INCREMENT,
					CHANGE `tag_id` `tag_id` INT(11)  UNSIGNED  NOT NULL DEFAULT '0',
					CHANGE `action` `action` VARCHAR(50)  NOT NULL  DEFAULT '',
					CHANGE `comments` `comments` TEXT  NOT NULL,
					CHANGE `user_id` `user_id` INT(11)  UNSIGNED  NOT NULL DEFAULT '0',
					CHANGE `actorid` `actorid` INT(11)  UNSIGNED  NOT NULL DEFAULT '0'
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__tags_log', 'idx_tag_id'))
			{
				$query = "ALTER TABLE `#__tags_log` ADD INDEX `idx_tag_id` (`tag_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__tags_log', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__tags_log` ADD INDEX `idx_user_id` (`user_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__tags_substitute'))
		{
			$query = "ALTER TABLE `#__tags_substitute`
					CHANGE `tag_id` `tag_id` INT(11)  UNSIGNED  NOT NULL DEFAULT '0',
					CHANGE `created_by` `created_by` INT(11)  UNSIGNED  NOT NULL  DEFAULT '0',
					CHANGE `raw_tag` `raw_tag` VARCHAR(100)  NOT NULL  DEFAULT '',
					CHANGE `tag` `tag` VARCHAR(100)  NOT NULL  DEFAULT ''
			;";
			$this->db->setQuery($query);
			$this->db->query();

			if (!$this->db->tableHasKey('#__tags_substitute', 'idx_tag_id'))
			{
				$query = "ALTER TABLE `#__tags_substitute` ADD INDEX `idx_tag_id` (`tag_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__tags_substitute', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__tags_substitute` ADD INDEX `idx_created_by` (`created_by`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__tags_substitute', 'idx_tag'))
			{
				$query = "ALTER TABLE `#__tags_substitute` ADD INDEX `idx_tag` (`tag`);";
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
		if ($this->db->tableExists('#__tags'))
		{
			if ($this->db->tableHasKey('#__tags', 'idx_tag'))
			{
				$query = "ALTER TABLE `#__tags` DROP INDEX `idx_tag`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__tags_group'))
		{
			if ($this->db->tableHasKey('#__tags_group', 'idx_tagid'))
			{
				$query = "ALTER TABLE `#__tags_group` DROP INDEX `idx_tagid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__tags_group', 'idx_groupid'))
			{
				$query = "ALTER TABLE `#__tags_group` DROP INDEX `idx_groupid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__tags_log'))
		{
			if ($this->db->tableHasKey('#__tags_log', 'idx_tag_id'))
			{
				$query = "ALTER TABLE `#__tags_log` DROP INDEX `idx_tag_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__tags_log', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__tags_log` DROP INDEX `idx_user_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__tags_substitute'))
		{
			if ($this->db->tableHasKey('#__tags_substitute', 'idx_tag_id'))
			{
				$query = "ALTER TABLE `#__tags_substitute` DROP INDEX `idx_tag_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__tags_substitute', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__tags_substitute` DROP INDEX `idx_created_by`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__tags_substitute', 'idx_tag'))
			{
				$query = "ALTER TABLE `#__tags_substitute` DROP INDEX `idx_tag`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}