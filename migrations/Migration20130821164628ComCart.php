<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130821164628ComCart extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "INSERT INTO `#__components` (`name`, `link`, `menuid`, `parent`, `option`, `ordering`, `iscore`, `params`, `enabled`) 
				SELECT 'Cart','option=com_cart','0','0','com_cart','0','0','storeAdminId=1000\r\n_paymentProvider=PAYPAL STANDARD\r\nPPS_businessName=PayPalStandardBusinesssName\r\nPPS_user=PayPalStandardUser\r\nPPS_password=PayPalStandardPassword\r\nPPS_signature=PayPalStandardSignature\r\npaymentProvider=DUMMY AUTO PAYMENT','0'
				FROM DUAL 
				WHERE NOT EXISTS (SELECT `name` FROM `#__components` WHERE `name` = 'Cart' AND `option` = 'com_cart');";		
		}
		else
		{
			$query = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) 
					SELECT 'com_cart','component','com_cart','','1','0','1','0','','storeAdminId=1000\r\n_paymentProvider=PAYPAL STANDARD\r\nPPS_businessName=PayPalStandardBusinesssName\r\nPPS_user=PayPalStandardUser\r\nPPS_password=PayPalStandardPassword\r\nPPS_signature=PayPalStandardSignature\r\npaymentProvider=DUMMY AUTO PAYMENT','','','0','0000-00-00 00:00:00','0','0' 
					FROM DUAL 
					WHERE NOT EXISTS (SELECT `name` FROM `#__extensions` WHERE `name` = 'com_cart' AND element = 'com_cart');";
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
			$query = "DELETE FROM `#__components` WHERE `name` = 'Cart' AND `option` = 'com_cart';";
		}
		else
		{
			$query = "DELETE FROM `#__extensions` WHERE name = 'com_cart' AND element = 'com_cart';";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}