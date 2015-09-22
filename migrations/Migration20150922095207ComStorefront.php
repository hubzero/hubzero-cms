<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script to add com_storefront component again (asset code having been added to 1.2.0 branch)
 **/
class Migration20150922095207ComStorefront extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		self::addComponentEntry('Storefront');
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		self::deleteComponentEntry('Storefront');
	}
}
