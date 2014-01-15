<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140115171149PlgResourcesFindthistext extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		self::addPluginEntry('resources','findthistext');
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		self::deletePluginEntry('resources','findthistext');
	}
}