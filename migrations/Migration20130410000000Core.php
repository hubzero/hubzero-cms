<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130410000000Core extends Hubzero_Migration
{
	protected static function up(&$db)
	{
		$query = "ALTER TABLE `#__forum_sections` ADD INDEX `idx_scoped` (`scope`, `scope_id`);
					ALTER TABLE `#__forum_sections` ADD INDEX `idx_asset_id` (`asset_id`);
					ALTER TABLE `#__forum_sections` ADD INDEX `idx_object_id` (`object_id`);
					ALTER TABLE `#__forum_sections` ADD INDEX `idx_access` (`access`);

					ALTER TABLE `#__forum_categories` ADD INDEX `idx_scoped` (`scope`, `scope_id`);
					ALTER TABLE `#__forum_categories` ADD INDEX `idx_asset_id` (`asset_id`);
					ALTER TABLE `#__forum_categories` ADD INDEX `idx_object_id` (`object_id`);
					ALTER TABLE `#__forum_categories` ADD INDEX `idx_state` (`state`);
					ALTER TABLE `#__forum_categories` ADD INDEX `idx_access` (`access`);
					ALTER TABLE `#__forum_categories` ADD INDEX `idx_section_id` (`section_id`);
					ALTER TABLE `#__forum_categories` ADD INDEX `idx_closed` (`closed`);

					ALTER TABLE `#__forum_posts` ADD INDEX `idx_scoped` (`scope`, `scope_id`);
					ALTER TABLE `#__forum_posts` ADD INDEX `idx_category_id` (`category_id`);
					ALTER TABLE `#__forum_posts` ADD INDEX `idx_access` (`access`);
					ALTER TABLE `#__forum_posts` ADD INDEX `idx_asset_id` (`asset_id`);
					ALTER TABLE `#__forum_posts` ADD INDEX `idx_object_id` (`object_id`);
					ALTER TABLE `#__forum_posts` ADD INDEX `idx_state` (`state`);
					ALTER TABLE `#__forum_posts` ADD INDEX `idx_sticky` (`sticky`);
					ALTER TABLE `#__forum_posts` ADD INDEX `idx_parent` (`parent`);

					ALTER TABLE `#__forum_attachments` ADD INDEX `idx_filename_postid` (`filename`, `post_id`);
					ALTER TABLE `#__forum_attachments` ADD INDEX `idx_parent` (`parent`);

					ALTER TABLE `#__blog_comments` ADD INDEX `idx_entry_id` (`entry_id`);

					ALTER TABLE `#__xmessage_recipient` ADD INDEX `idx_mid` (`mid`);
					ALTER TABLE `#__xmessage_recipient` ADD INDEX `idx_uid` (`uid`);

					ALTER TABLE `#__resource_types` ADD INDEX `idx_category` (`category`);

					DROP TABLE IF EXISTS `#__resource_tags`;
					DROP TABLE IF EXISTS `#__support_tags`;
					DROP TABLE IF EXISTS `#__answers_tags`;";

		$db->setQuery($query);
		$db->query();
	}
}