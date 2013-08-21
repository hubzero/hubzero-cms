<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130821164314ComStorefront extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "INSERT INTO `#__components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) 
				SELECT 'Storefront','option=com_storefront','0','0','','','com_storefront','0','','0',' ','0'
				FROM DUAL 
				WHERE NOT EXISTS (SELECT `name` FROM `#__components` WHERE `name` = 'Storefront' AND `option` = 'com_storefront');";		
		}
		else
		{
			$query = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) 
					SELECT 'com_storefront','component','com_storefront','','1','0','1','0','','','','','0','0000-00-00 00:00:00','0','0' 
					FROM DUAL 
					WHERE NOT EXISTS (SELECT `name` FROM `#__extensions` WHERE `name` = 'com_cart' AND element = 'com_storefront');";
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
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "DELETE FROM `#__components` WHERE `name` = 'Storefront' AND `option` = 'com_storefront';";
		}
		else
		{
			$query = "DELETE FROM `#__extensions` WHERE name = 'com_storefront' AND element = 'com_storefront';";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}