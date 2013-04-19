<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130404000000ComCourses extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = '';

		if (!$db->tableHasField('#__courses_announcements', 'publish_up'))
		{
			$query .= "ALTER TABLE `#__courses_announcements` ADD `publish_up` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00';\n";
		}
		if (!$db->tableHasField('#__courses_announcements', 'publish_down'))
		{
			$query .= "ALTER TABLE `#__courses_announcements` ADD `publish_down` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00';\n";
		}
		if (!$db->tableHasField('#__courses_announcements', 'sticky'))
		{
			$query .= "ALTER TABLE `#__courses_announcements` ADD `sticky` TINYINT(2)  NOT NULL  DEFAULT '0';\n";
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

		if ($db->tableHasField('#__courses_announcements', 'publish_up'))
		{
			$query .= "ALTER TABLE `#__courses_announcements` DROP `publish_up`;\n";
		}
		if ($db->tableHasField('#__courses_announcements', 'publish_down'))
		{
			$query .= "ALTER TABLE `#__courses_announcements` DROP `publish_down`;\n";
		}
		if ($db->tableHasField('#__courses_announcements', 'sticky'))
		{
			$query .= "ALTER TABLE `#__courses_announcements` DROP `sticky`;\n";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}