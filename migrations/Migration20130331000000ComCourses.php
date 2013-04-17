<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130331000000ComCourses extends Hubzero_Migration
{
	protected static function up($db)
	{
		$query = "INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Courses - Course FAQ','faq','courses',0,9,1,0,0,0,'0000-00-00 00:00:00',''
			FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE `name` = 'Courses - Course FAQ');

			INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Courses - Course Related','related','courses',0,10,1,0,0,0,'0000-00-00 00:00:00',''
			FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE `name` = 'Courses - Course Related');

			INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Courses - Store','store','courses',0,12,1,0,0,0,'0000-00-00 00:00:00',''
			FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE `name` = 'Courses - Store');";

		$db->setQuery($query);
		$db->query();
	}

	protected static function down($db)
	{
		$query = "DELETE FROM `#__plugins` WHERE `element` = 'faq' AND `folder`='courses';
			DELETE FROM `#__plugins` WHERE `element` = 'related' AND `folder`='courses';
			DELETE FROM `#__plugins` WHERE `element` = 'store' AND `folder`='courses';";

		$db->setQuery($query);
		$db->query();
	}
}