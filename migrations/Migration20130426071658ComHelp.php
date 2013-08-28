<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding help component
 **/
class Migration20130426071658ComHelp extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		self::addComponentEntry('Help');
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		self::deleteComponentEntry('Help');
	}
}