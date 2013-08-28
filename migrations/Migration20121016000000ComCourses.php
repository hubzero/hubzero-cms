<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20121016000000ComCourses extends Hubzero_Migration
{
	protected static function up($db)
	{
		self::addComponentEntry('courses', 'com_courses', 0);
	}
}