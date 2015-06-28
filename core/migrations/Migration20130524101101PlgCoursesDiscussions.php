<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for fixing wrong datatype on column
 **/
class Migration20130524101101PlgCoursesDiscussions extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "ALTER TABLE #__courses_member_notes CHANGE COLUMN `timestamp` `timestamp` time NOT NULL DEFAULT '00:00:00';";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}