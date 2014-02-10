<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20131111200831ComCourses extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('#__courses_offering_badges') && !$db->tableExists('#__courses_offering_section_badges'))
		{
			$query = "RENAME TABLE `#__courses_offering_badges` TO `#__courses_offering_section_badges`";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableHasField('#__courses_offerings', 'badge_id'))
		{
			$query = "ALTER TABLE `#__courses_offerings` DROP `badge_id`";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableHasField('#__courses_offering_section_badges', 'offering_id') && !$db->tableHasField('#__courses_offering_section_badges', 'section_id'))
		{
			$query = "ALTER TABLE `#__courses_offering_section_badges` CHANGE `offering_id` `section_id` INT(11) NOT NULL";
			$db->setQuery($query);
			$db->query();
		}

		if (!$db->tableHasField('#__courses_offering_section_badges', 'provider_name') && $db->tableHasField('#__courses_offering_section_badges', 'section_id'))
		{
			$query = "ALTER TABLE `#__courses_offering_section_badges` ADD `provider_name` VARCHAR(255) NOT NULL DEFAULT 'passport' AFTER `section_id`";
			$db->setQuery($query);
			$db->query();
		}

		if (!$db->tableHasField('#__courses_offering_section_badges', 'provider_badge_id') && $db->tableHasField('#__courses_offering_section_badges', 'badge_id'))
		{
			$query = "ALTER TABLE `#__courses_offering_section_badges` CHANGE `badge_id` `provider_badge_id` INT(11) NOT NULL";
			$db->setQuery($query);
			$db->query();
		}

		if (!$db->tableHasField('#__courses_offering_section_badges', 'criteria_id') && $db->tableHasField('#__courses_offering_section_badges', 'img_url'))
		{
			$query = "ALTER TABLE `#__courses_offering_section_badges` ADD `criteria_id` INT(11) NOT NULL AFTER `img_url`";
			$db->setQuery($query);
			$db->query();
		}

		if (!$db->tableHasField('#__courses_offering_section_badges', 'published') && $db->tableHasField('#__courses_offering_section_badges', 'section_id'))
		{
			$query = "ALTER TABLE `#__courses_offering_section_badges` ADD `published` INT(1) NOT NULL DEFAULT '0' AFTER `section_id`";
			$db->setQuery($query);
			$db->query();
		}

		if (!$db->tableHasField('#__courses_member_badges', 'section_badge_id') && $db->tableHasField('#__courses_member_badges', 'member_id'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` ADD `section_badge_id` INT(11) NOT NULL AFTER `member_id`";
			$db->setQuery($query);
			$db->query();
		}

		if ($db->tableHasField('#__courses_member_badges', 'claim_url'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` DROP `claim_url`";
			$db->setQuery($query);
			$db->query();
		}

		if (!$db->tableHasField('#__courses_member_badges', 'validation_token') && $db->tableHasField('#__courses_member_badges', 'action_on'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` ADD `validation_token` VARCHAR(20) NULL DEFAULT NULL AFTER `action_on`";
			$db->setQuery($query);
			$db->query();
		}

		if (!$db->tableHasField('#__courses_member_badges', 'criteria_id') && $db->tableHasField('#__courses_member_badges', 'validation_token'))
		{
			$query = "ALTER TABLE `#__courses_member_badges` ADD `criteria_id` INT(11)  NULL  DEFAULT NULL  AFTER `validation_token`";
			$db->setQuery($query);
			$db->query();
		}

		if (!$db->tableExists('#__courses_offering_section_badge_criteria'))
		{
			$query = "CREATE TABLE `#__courses_offering_section_badge_criteria` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`text` text NOT NULL,
						`section_badge_id` int(11) NOT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM;";
			$db->setQuery($query);
			$db->query();
		}
	}
}