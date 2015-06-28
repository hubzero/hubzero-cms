<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for allowing null scores in gradebook for unfinished forms
 **/
class Migration20130423213901ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "ALTER TABLE `#__courses_grade_book` CHANGE `score` `score` DECIMAL(5,2)  NULL;";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}