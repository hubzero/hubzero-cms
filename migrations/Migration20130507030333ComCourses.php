<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding courses badge implementation
 **/
class Migration20130507030333ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";

		if (!$db->tableExists('#__courses_offering_badges'))
		{
			$query .= "CREATE TABLE `#__courses_offering_badges` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`offering_id` int(11) NOT NULL,
						`badge_id` int(11) NOT NULL,
						`img_url` varchar(255) NOT NULL DEFAULT '',
						PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
		}

		if (!$db->tableHasField('#__courses_offerings', 'badge_id'))
		{
			$query .= "ALTER TABLE `#__courses_offerings` ADD `badge_id` INT(11)  NULL  DEFAULT NULL  AFTER `state`;\n";
		}

		if (!$db->tableExists('#__courses_member_badges'))
		{
			$query .= "CREATE TABLE `#__courses_member_badges` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`member_id` int(11) NOT NULL,
						`earned` int(1) DEFAULT NULL,
						`earned_on` datetime DEFAULT NULL,
						`claim_url` varchar(255) DEFAULT NULL,
						`claimed` int(1) DEFAULT NULL,
						`claimed_on` datetime DEFAULT NULL,
						PRIMARY KEY (`id`),
						UNIQUE KEY `member_id` (`member_id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "";

		if ($db->tableExists('#__courses_offering_badges'))
		{
			$query .= "DROP TABLE `#__courses_offering_badges`;\n";
		}

		if ($db->tableHasField('#__courses_offerings', 'badge_id'))
		{
			$query .= "ALTER TABLE `#__courses_offerings` DROP `badge_id`;\n";
		}

		if ($db->tableExists('#__courses_member_badges'))
		{
			$query .= "DROP TABLE `#__courses_member_badges`;";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}