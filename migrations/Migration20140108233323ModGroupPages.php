<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140108233323ModGroupPages extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		self::addModuleEntry('mod_grouppages');
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		self::deleteModuleEntry('mod_grouppages');
	}
}