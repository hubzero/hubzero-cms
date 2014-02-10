<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing wrong datatype on column
 **/
class Migration20130524101101PlgCoursesDiscussions extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "ALTER TABLE #__courses_member_notes CHANGE COLUMN `timestamp` `timestamp` time NOT NULL DEFAULT '00:00:00';";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}