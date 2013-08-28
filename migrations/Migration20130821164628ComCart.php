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
		self::addComponentEntry('Cart');
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		self::deleteComponentEntry('Cart');
	}
}