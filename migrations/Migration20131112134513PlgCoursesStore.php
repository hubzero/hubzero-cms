<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for disabling courses store plugin for the time being
 **/
class Migration20131112134513PlgCoursesStore extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		self::disablePlugin('courses', 'store');
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		self::enablePlugin('courses', 'store');
	}
}