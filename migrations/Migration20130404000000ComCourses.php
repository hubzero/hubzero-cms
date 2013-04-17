<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130404000000ComCourses extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__courses_announcements` 
			ADD `publish_up` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00',
			ADD `publish_down` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00',
			ADD `sticky` TINYINT(2)  NOT NULL  DEFAULT '0';";

		$db->setQuery($query);
		$db->query();
	}

	protected static function down($db)
	{
		$query = "ALTER TABLE `#__courses_pages` DROP `publish_up`, DROP `publish_down`, DROP `sticky`;";

		$db->setQuery($query);
		$db->query();
	}
}