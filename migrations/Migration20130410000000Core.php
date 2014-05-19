<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for cleaning up indices
 **/
class Migration20130410000000Core extends Base
{
	public function up()
	{
		$query = '';

		// Forum section indices
		if (!$this->db->tableHasKey('#__forum_sections', 'idx_scoped'))
		{
			$query .= "ALTER TABLE `#__forum_sections` ADD INDEX `idx_scoped` (`scope`, `scope_id`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_sections', 'idx_asset_id'))
		{
			$query .= "ALTER TABLE `#__forum_sections` ADD INDEX `idx_asset_id` (`asset_id`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_sections', 'idx_object_id'))
		{
			$query .= "ALTER TABLE `#__forum_sections` ADD INDEX `idx_object_id` (`object_id`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_sections', 'idx_access'))
		{
			$query .= "ALTER TABLE `#__forum_sections` ADD INDEX `idx_access` (`access`);\n";
		}

		// Forum categories indices
		if (!$this->db->tableHasKey('#__forum_categories', 'idx_scoped'))
		{
			$query .= "ALTER TABLE `#__forum_categories` ADD INDEX `idx_scoped` (`scope`, `scope_id`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_categories', 'idx_asset_id'))
		{
			$query .= "ALTER TABLE `#__forum_categories` ADD INDEX `idx_asset_id` (`asset_id`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_categories', 'idx_object_id'))
		{
			$query .= "ALTER TABLE `#__forum_categories` ADD INDEX `idx_object_id` (`object_id`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_categories', 'idx_state'))
		{
			$query .= "ALTER TABLE `#__forum_categories` ADD INDEX `idx_state` (`state`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_categories', 'idx_access'))
		{
			$query .= "ALTER TABLE `#__forum_categories` ADD INDEX `idx_access` (`access`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_categories', 'idx_section_id'))
		{
			$query .= "ALTER TABLE `#__forum_categories` ADD INDEX `idx_section_id` (`section_id`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_categories', 'idx_closed'))
		{
			$query .= "ALTER TABLE `#__forum_categories` ADD INDEX `idx_closed` (`closed`);\n";
		}

		// Forum post indices
		if (!$this->db->tableHasKey('#__forum_posts', 'idx_scoped'))
		{
			$query .= "ALTER TABLE `#__forum_posts` ADD INDEX `idx_scoped` (`scope`, `scope_id`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_posts', 'idx_category_id'))
		{
			$query .= "ALTER TABLE `#__forum_posts` ADD INDEX `idx_category_id` (`category_id`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_posts', 'idx_access'))
		{
			$query .= "ALTER TABLE `#__forum_posts` ADD INDEX `idx_access` (`access`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_posts', 'idx_asset_id'))
		{
			$query .= "ALTER TABLE `#__forum_posts` ADD INDEX `idx_asset_id` (`asset_id`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_posts', 'idx_object_id'))
		{
			$query .= "ALTER TABLE `#__forum_posts` ADD INDEX `idx_object_id` (`object_id`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_posts', 'idx_state'))
		{
			$query .= "ALTER TABLE `#__forum_posts` ADD INDEX `idx_state` (`state`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_posts', 'idx_sticky'))
		{
			$query .= "ALTER TABLE `#__forum_posts` ADD INDEX `idx_sticky` (`sticky`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_posts', 'idx_parent'))
		{
			$query .= "ALTER TABLE `#__forum_posts` ADD INDEX `idx_parent` (`parent`);\n";
		}

		// Forum attachment indices
		if (!$this->db->tableHasKey('#__forum_attachments', 'idx_filename_postid'))
		{
			$query .= "ALTER TABLE `#__forum_attachments` ADD INDEX `idx_filename_postid` (`filename`, `post_id`);\n";
		}
		if (!$this->db->tableHasKey('#__forum_attachments', 'idx_parent'))
		{
			$query .= "ALTER TABLE `#__forum_attachments` ADD INDEX `idx_parent` (`parent`);\n";
		}

		// Blog comments index
		if (!$this->db->tableHasKey('#__blog_comments', 'idx_entry_id'))
		{
			$query .= "ALTER TABLE `#__blog_comments` ADD INDEX `idx_entry_id` (`entry_id`);\n";
		}

		// Xmessage recipient
		if (!$this->db->tableHasKey('#__xmessage_recipient', 'idx_mid'))
		{
			$query .= "ALTER TABLE `#__xmessage_recipient` ADD INDEX `idx_mid` (`mid`);\n";
		}
		if (!$this->db->tableHasKey('#__xmessage_recipient', 'idx_uid'))
		{
			$query .= "ALTER TABLE `#__xmessage_recipient` ADD INDEX `idx_uid` (`uid`);\n";
		}

		// Xmessage recipient
		if (!$this->db->tableHasKey('#__resource_types', 'idx_category'))
		{
			$query .= "ALTER TABLE `#__resource_types` ADD INDEX `idx_category` (`category`);\n";
		}

		$query .= "DROP TABLE IF EXISTS `#__resource_tags`;\n
					DROP TABLE IF EXISTS `#__support_tags`;\n
					DROP TABLE IF EXISTS `#__answers_tags`;";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}