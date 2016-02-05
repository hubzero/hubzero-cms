<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for creating default member roles if none exist
 **/
class Migration20130423204715ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "SELECT * FROM `#__courses_roles`";

		$this->db->setQuery($query);

		if (!$this->db->loadResult())
		{
			$query = "INSERT INTO `#__courses_roles` (`offering_id`, `alias`, `title`, `permissions`)
						VALUES
							(0, 'instructor', 'Instructor', ''),
							(0, 'manager', 'Manager', ''),
							(0, 'student', 'Student', '');";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}