<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding com_dataviewer
 **/
class Migration20130815175354ComDataviewer extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "SELECT `id` FROM `#__components` WHERE `name` = 'Dataviewer'";
			$db->setQuery($query);
			if (!$db->loadResult())
			{
				$query  = "INSERT INTO `#__components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`)";
				$query .= " VALUES ('Dataviewer', 'option=com_dataviewer', 0, 0, 'option=com_dataviewer', 'Dataviewer', 'com_dataviewer', 0, 'js/ThemeOffice/component.png', 0, 'record_display_limit=10\nprocessing_mode_switch=0\nproc_switch_threshold=25000\nacl_users=\nacl_groups=\n\n', 1);";
				$db->setQuery($query);
				$db->query();
			}
		}
		else
		{
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `name` = 'com_dataviewer'";
			$db->setQuery($query);
			if (!$db->loadResult())
			{
				$query  = "INSERT INTO `jos_extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)";
				$query .= " VALUES ('com_dataviewer', 'component', 'com_dataviewer', '', 1, 1, 1, 0, '', '{\"record_display_limit\":\"10\",\"processing_mode_switch\":\"0\",\"proc_switch_threshold\":\"25000\",\"acl_users\":\"\",\"acl_groups\":\"\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0);";
				$db->setQuery($query);
				$db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		self::deleteComponentEntry('Dataviewer');
	}
}