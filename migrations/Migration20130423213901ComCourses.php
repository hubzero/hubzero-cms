<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for allowing null scores in gradebook for unfinished forms
 **/
class Migration20130423213901ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "ALTER TABLE `#__courses_grade_book` CHANGE `score` `score` DECIMAL(5,2)  NULL;";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}