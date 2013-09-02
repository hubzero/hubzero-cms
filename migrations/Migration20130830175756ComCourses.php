<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for updating form info to correspond to code changes
 **/
class Migration20130830175756ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$queries   = array();
		$queries[] = "UPDATE `#__courses_assets` SET url = SUBSTRING(url, 30, 50) WHERE `type` = 'form' AND `url` LIKE '/courses/form/complete?crumb=%'";
		$queries[] = "UPDATE `#__courses_assets` SET url = SUBSTRING(url, 22) WHERE `type` = 'form' AND `url` LIKE '/courses/form/layout/%'";

		foreach ($queries as $query)
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}