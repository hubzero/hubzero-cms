<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20121217000000ComForum extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = '';

		if (!$db->tableHasField('#__forum_sections', 'object_id'))
		{
			$query .= "ALTER TABLE `#__forum_sections` ADD `object_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `asset_id`;\n";
		}
		if (!$db->tableHasField('#__forum_categories', 'object_id'))
		{
			$query .= "ALTER TABLE `#__forum_categories` ADD `object_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `asset_id`;\n";
		}
		if (!$db->tableHasField('#__forum_posts', 'object_id'))
		{
			$query .= "ALTER TABLE `#__forum_posts` ADD `object_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `asset_id`;\n";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}

	protected static function down($db)
	{
		$query = '';

		if ($db->tableHasField('#__forum_sections', 'object_id'))
		{
			$query .= "ALTER TABLE `#__forum_sections` DROP `object_id`;\n";
		}
		if ($db->tableHasField('#__forum_categories', 'object_id'))
		{
			$query .= "ALTER TABLE `#__forum_categories` DROP `object_id`;\n";
		}
		if ($db->tableHasField('#__forum_posts', 'object_id'))
		{
			$query .= "ALTER TABLE `#__forum_posts` DROP `object_id`;\n";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}