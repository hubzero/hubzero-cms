<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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