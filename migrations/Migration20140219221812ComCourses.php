<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for getting rid of duplicate section date entries
 **/
class Migration20140219221812ComCourses extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query  = "SELECT count(id) AS num, section_id, scope, scope_id";
		$query .= " FROM `#__courses_offering_section_dates`";
		$query .= " GROUP BY `section_id`, `scope`, `scope_id`";
		$query .= " HAVING num > 1";

		$db->setQuery($query);
		$results = $db->loadObjectList();

		if ($results && count($results) > 0)
		{
			foreach ($results as $result)
			{
				$query  = "SELECT * FROM `#__courses_offering_section_dates` WHERE";
				$query .= " section_id = " . $db->quote($result->section_id);
				$query .= " AND scope = " . $db->quote($result->scope);
				$query .= " AND scope_id = " . $db->quote($result->scope_id);

				$db->setQuery($query);
				$rows = $db->loadObjectList();

				if ($rows && count($rows) > 1)
				{
					// Leave the first one intact
					unset($rows[0]);

					foreach ($rows as $row)
					{
						$query  = "DELETE FROM `#__courses_offering_section_dates`";
						$query .= " WHERE id = " . $db->quote($row->id);

						$db->setQuery($query);
						$db->query();
					}
				}
			}
		}
	}
}
