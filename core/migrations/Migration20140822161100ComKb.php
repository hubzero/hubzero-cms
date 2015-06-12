<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding indexes to com_kb tables
 **/
class Migration20140822161100ComKb extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__faq'))
		{
			if ($this->db->tableHasKey('#__faq', 'jos_faq_title_introtext_fulltext_ftidx'))
			{
				$query = "ALTER TABLE `#__faq` DROP INDEX `jos_faq_title_introtext_fulltext_ftidx`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__faq', 'ftidx_title_fulltxt'))
			{
				$query = "ALTER TABLE `#__faq` ADD FULLTEXT `ftidx_title_fulltxt` (`title`, `fulltxt`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__faq', 'idx_section'))
			{
				$query = "ALTER TABLE `#__faq` ADD INDEX `idx_section` (`section`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__faq', 'idx_category'))
			{
				$query = "ALTER TABLE `#__faq` ADD INDEX `idx_category` (`category`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__faq', 'idx_alias'))
			{
				$query = "ALTER TABLE `#__faq` ADD INDEX `idx_alias` (`alias`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__faq_categories'))
		{
			if (!$this->db->tableHasKey('#__faq_categories', 'idx_alias'))
			{
				$query = "ALTER TABLE `#__faq_categories` ADD INDEX `idx_alias` (`alias`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__faq_categories', 'idx_section'))
			{
				$query = "ALTER TABLE `#__faq_categories` ADD INDEX `idx_section` (`section`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__faq_categories', 'idx_state'))
			{
				$query = "ALTER TABLE `#__faq_categories` ADD INDEX `idx_state` (`state`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__faq_comments'))
		{
			if (!$this->db->tableHasKey('#__faq_comments', 'idx_entry_id'))
			{
				$query = "ALTER TABLE `#__faq_comments` ADD INDEX `idx_entry_id` (`entry_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__faq_comments', 'idx_state'))
			{
				$query = "ALTER TABLE `#__faq_comments` ADD INDEX `idx_state` (`state`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__faq_helpful_log'))
		{
			if (!$this->db->tableHasKey('#__faq_helpful_log', 'idx_type_object_id'))
			{
				$query = "ALTER TABLE `#__faq_helpful_log` ADD INDEX `idx_type_object_id` (`type`, `object_id`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__faq_helpful_log', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__faq_helpful_log` ADD INDEX `idx_user_id` (`user_id`);";
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
		if ($this->db->tableExists('#__faq'))
		{
			if ($this->db->tableHasKey('#__faq', 'ftidx_title_fulltxt'))
			{
				$query = "ALTER TABLE `#__faq` DROP INDEX `ftidx_title_fulltxt`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__faq', 'jos_faq_title_introtext_fulltext_ftidx'))
			{
				$query = "ALTER TABLE `#__faq` ADD FULLTEXT `jos_faq_title_introtext_fulltext_ftidx` (`title`, `params`, `fulltxt`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__faq', 'idx_section'))
			{
				$query = "ALTER TABLE `#__faq` DROP INDEX `idx_section`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__faq', 'idx_category'))
			{
				$query = "ALTER TABLE `#__faq` DROP INDEX `idx_category`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__faq', 'idx_alias'))
			{
				$query = "ALTER TABLE `#__faq` DROP INDEX `idx_alias`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__faq_categories'))
		{
			if ($this->db->tableHasKey('#__faq_categories', 'idx_alias'))
			{
				$query = "ALTER TABLE `#__faq_categories` DROP INDEX `idx_alias`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__faq_categories', 'idx_section'))
			{
				$query = "ALTER TABLE `#__faq_categories` DROP INDEX `idx_section`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__faq_categories', 'idx_state'))
			{
				$query = "ALTER TABLE `#__faq_categories` DROP INDEX `idx_state`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__faq_comments'))
		{
			if ($this->db->tableHasKey('#__faq_comments', 'idx_entry_id'))
			{
				$query = "ALTER TABLE `#__faq_comments` DROP INDEX `idx_entry_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__faq_comments', 'idx_state'))
			{
				$query = "ALTER TABLE `#__faq_comments` DROP INDEX `idx_state`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__faq_helpful_log'))
		{
			if ($this->db->tableHasKey('#__faq_helpful_log', 'idx_type_object_id'))
			{
				$query = "ALTER TABLE `#__faq_helpful_log` DROP INDEX `idx_type_object_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__faq_helpful_log', 'idx_user_id'))
			{
				$query = "ALTER TABLE `#__faq_helpful_log` DROP INDEX `idx_user_id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}