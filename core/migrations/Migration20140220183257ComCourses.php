<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding some much needed indices to the courses section_dates table
 **/
class Migration20140220183257ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__courses_offering_section_dates'))
		{
			if (!$this->db->tableHasKey('#__courses_offering_section_dates', 'idx_section_id'))
			{
				$query = "CREATE INDEX idx_section_id ON `#__courses_offering_section_dates`(section_id)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__courses_offering_section_dates', 'idx_scope_id'))
			{
				$query = "CREATE INDEX idx_scope_id ON `#__courses_offering_section_dates`(scope_id)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__courses_offering_section_dates'))
		{
			if ($this->db->tableHasKey('#__courses_offering_section_dates', 'idx_section_id'))
			{
				$query = "DROP INDEX idx_section_id ON `#__courses_offering_section_dates`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__courses_offering_section_dates', 'idx_scope_id'))
			{
				$query = "DROP INDEX idx_scope_id ON `#__courses_offering_section_dates`";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}