<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding section grade policy id field
 **/
class Migration20130412000000ComCourses extends Base
{
	public function up()
	{
		if (!$this->db->tableHasField('#__courses_offering_sections', 'grade_policy_id'))
		{
			$query = "ALTER TABLE `#__courses_offering_sections` ADD `grade_policy_id` INT(11)  NOT NULL  DEFAULT '1'  AFTER `enrollment`;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		if ($this->db->tableHasField('#__courses_offering_sections', 'grade_policy_id'))
		{
			$query = "ALTER TABLE `#__courses_offering_sections` DROP `grade_policy_id`;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}