<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding com_forum component entry if missing, or adding admin_menu_link if missing
 **/
class Migration20130918132946ComForum extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		self::addComponentEntry('Forum');

		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "SELECT * FROM `#__components` WHERE `name` = 'Forum'";
			$db->setQuery($query);
			$result = $db->loadObject();

			if ($result && empty($result->admin_menu_link))
			{
				$query = "UPDATE `#__components` SET `admin_menu_link` = 'option=com_forum' WHERE `id` = '{$result->id}'";
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
		self::deleteComponentEntry('Forum');
	}
}