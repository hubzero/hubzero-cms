<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding pages plugin entry
 **/
class Migration20130423204702PlgCoursesPages extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		self::addPluginEntry('courses', 'pages');
	}

	/**
	 * Down
	 **/
	protected function down($db)
	{
		self::deletePluginEntry('courses', 'pages');
	}
}