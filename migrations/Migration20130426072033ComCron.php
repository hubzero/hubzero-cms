<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding com cron component
 **/
class Migration20130426072033ComCron extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "";

		if (!$db->tableExists('#__cron_jobs'))
		{
			$query .= "CREATE TABLE `#__cron_jobs` (
							`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
							`title` varchar(255) NOT NULL DEFAULT '',
							`state` tinyint(3) NOT NULL DEFAULT '0',
							`plugin` varchar(255) NOT NULL DEFAULT '',
							`event` varchar(255) NOT NULL DEFAULT '',
							`last_run` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`next_run` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`recurrence` varchar(50) NOT NULL DEFAULT '',
							`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`created_by` int(11) NOT NULL DEFAULT '0',
							`modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`modified_by` int(11) NOT NULL DEFAULT '0',
							`active` tinyint(3) NOT NULL DEFAULT '0',
							`ordering` int(11) NOT NULL DEFAULT '0',
							PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;\n";
		}

		$query .= "INSERT INTO `#__components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`)
					SELECT 'Cron', 'option=com_cron', 0, 0, 'option=com_cron', 'Cron', 'com_cron', 0, '', 0, ' ', 1
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__components` WHERE `name` = 'Cron');\n";

		$query .= "INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
					SELECT 'Cron - Support', 'support', 'cron', 0, 1, 1, 0, 0, 0, '0000-00-00 00:00:00', ''
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE `name` = 'Cron - Support');\n";

		$query .= "INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
					SELECT 'Cron - Members', 'members', 'cron', 0, 2, 1, 0, 0, 0, '0000-00-00 00:00:00', ''
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE `name` = 'Cron - Members');\n";

		$query .= "INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
					SELECT 'Cron - Cache', 'cache', 'cron', 0, 3, 1, 0, 0, 0, '0000-00-00 00:00:00', ''
					FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__plugins` WHERE `name` = 'Cron - Cache');\n";

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

		if ($db->tableExists('#__cron_jobs'))
		{
			$query .= "DROP TABLE `#__cron_jobs`;";

			$query .= "DELETE FROM `#__components` WHERE `name` = 'Cron';";

			$query .= "DELETE FROM `#__plugins` WHERE `folder` = 'cron';";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}