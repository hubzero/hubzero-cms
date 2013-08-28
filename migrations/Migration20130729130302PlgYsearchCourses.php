<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding pages plugin entry
 **/
class Migration20130729130302PlgYsearchCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		self::addPluginEntry('ysearch', 'courses');
	}

	/**
	 * Down
	 **/
	protected function down($db)
	{
		self::deletePluginEntry('ysearch', 'courses');
	}
}