<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for tracking section enrollment
 **/
class Migration20130401000000ComCourses extends Base
{
	public function up()
	{
		$query = '';

		if (!$this->db->tableHasField('#__courses_offering_sections', 'enrollment'))
		{
			$query .= "ALTER TABLE `#__courses_offering_sections` ADD `enrollment` TINYINT(2)  NOT NULL  DEFAULT '0'  AFTER `created_by`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{
		$query = '';

		if ($this->db->tableHasField('#__courses_offering_sections', 'enrollment'))
		{
			$query .= "ALTER TABLE `#__courses_offering_sections` DROP `enrollment`;";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}