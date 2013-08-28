<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20130331000000ComCourses extends Hubzero_Migration
{
	protected static function up($db)
	{
		self::addPluginEntry('courses', 'faq');
		self::addPluginEntry('courses', 'related');
		self::addPluginEntry('courses', 'store');
	}

	protected static function down($db)
	{
		self::deletePluginEntry('courses', 'faq');
		self::deletePluginEntry('courses', 'related');
		self::deletePluginEntry('courses', 'store');
	}
}