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