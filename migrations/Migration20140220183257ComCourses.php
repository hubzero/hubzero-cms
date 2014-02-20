<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding some much needed indices to the courses section_dates table
 **/
class Migration20140220183257ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('#__courses_offering_section_dates'))
		{
			if (!$db->tableHasKey('#__courses_offering_section_dates', 'idx_section_id'))
			{
				$query = "CREATE INDEX idx_section_id ON `#__courses_offering_section_dates`(section_id)";
				$db->setQuery($query);
				$db->query();
			}

			if (!$db->tableHasKey('#__courses_offering_section_dates', 'idx_scope_id'))
			{
				$query = "CREATE INDEX idx_scope_id ON `#__courses_offering_section_dates`(scope_id)";
				$db->setQuery($query);
				$db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableExists('#__courses_offering_section_dates'))
		{
			if ($db->tableHasKey('#__courses_offering_section_dates', 'idx_section_id'))
			{
				$query = "DROP INDEX idx_section_id ON `#__courses_offering_section_dates`";
				$db->setQuery($query);
				$db->query();
			}

			if ($db->tableHasKey('#__courses_offering_section_dates', 'idx_scope_id'))
			{
				$query = "DROP INDEX idx_scope_id ON `#__courses_offering_section_dates`";
				$db->setQuery($query);
				$db->query();
			}
		}
	}
}