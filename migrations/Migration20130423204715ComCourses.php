<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
			$query = "INSERT INTO `jos_courses_roles` (`offering_id`, `alias`, `title`, `permissions`)
						VALUES
							(0, 'instructor', 'Instructor', ''),
							(0, 'manager', 'Manager', ''),
							(0, 'student', 'Student', '');";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}