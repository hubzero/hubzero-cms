<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for getting rid of duplicate section date entries
 **/
class Migration20140219221812ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query  = "SELECT count(id) AS num, section_id, scope, scope_id";
		$query .= " FROM `#__courses_offering_section_dates`";
		$query .= " GROUP BY `section_id`, `scope`, `scope_id`";
		$query .= " HAVING num > 1";

		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		if ($results && count($results) > 0)
		{
			foreach ($results as $result)
			{
				$query  = "SELECT * FROM `#__courses_offering_section_dates` WHERE";
				$query .= " section_id = " . $this->db->quote($result->section_id);
				$query .= " AND scope = " . $this->db->quote($result->scope);
				$query .= " AND scope_id = " . $this->db->quote($result->scope_id);

				$this->db->setQuery($query);
				$rows = $this->db->loadObjectList();

				if ($rows && count($rows) > 1)
				{
					// Leave the first one intact
					unset($rows[0]);

					foreach ($rows as $row)
					{
						$query  = "DELETE FROM `#__courses_offering_section_dates`";
						$query .= " WHERE id = " . $this->db->quote($row->id);

						$this->db->setQuery($query);
						$this->db->query();
					}
				}
			}
		}
	}
}