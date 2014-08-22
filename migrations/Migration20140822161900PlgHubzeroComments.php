<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding indexes to Hubzero\Item tables
 **/
class Migration20140822161900PlgHubzeroComments extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__item_comment_files'))
		{
			if (!$this->db->tableHasKey('#__item_comment_files', 'idx_comment_id'))
			{
				$query = "ALTER TABLE `#__item_comment_files` ADD INDEX `idx_comment_id` (`comment_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__item_comments'))
		{
			if (!$this->db->tableHasKey('#__item_comments', 'idx_item_type_item_id'))
			{
				$query = "ALTER TABLE `#__item_comments` ADD INDEX `idx_item_type_item_id` (`item_type`, `item_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__item_comments', 'idx_parent'))
			{
				$query = "ALTER TABLE `#__item_comments` ADD INDEX `idx_parent` (`parent`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__item_comments', 'idx_state'))
			{
				$query = "ALTER TABLE `#__item_comments` ADD INDEX `idx_state` (`state`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__item_votes'))
		{
			if (!$this->db->tableHasKey('#__item_votes', 'idx_item_type_item_id'))
			{
				$query = "ALTER TABLE `#__item_votes` ADD INDEX `idx_item_type_item_id` (`item_type`, `item_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__item_votes', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__item_votes` ADD INDEX `idx_created_by` (`created_by`);";
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
		if ($this->db->tableExists('#__item_comment_files'))
		{
			if ($this->db->tableHasKey('#__item_comment_files', 'idx_comment_id'))
			{
				$query = "ALTER TABLE `#__item_comment_files` DROP INDEX `idx_comment_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__item_comments'))
		{
			if ($this->db->tableHasKey('#__item_comments', 'idx_item_type_item_id'))
			{
				$query = "ALTER TABLE `#__item_comments` DROP INDEX `idx_item_type_item_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__item_comments', 'idx_parent'))
			{
				$query = "ALTER TABLE `#__item_comments` DROP INDEX `idx_parent`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__item_comments', 'idx_state'))
			{
				$query = "ALTER TABLE `#__item_comments` DROP INDEX `idx_state`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__item_votes'))
		{
			if ($this->db->tableHasKey('#__item_votes', 'idx_item_type_item_id'))
			{
				$query = "ALTER TABLE `#__item_votes` DROP INDEX `idx_item_type_item_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__item_votes', 'idx_created_by'))
			{
				$query = "ALTER TABLE `#__item_votes` DROP INDEX `idx_created_by`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}