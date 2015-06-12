<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding is_default column
 **/
class Migration20140217151012ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__courses_offering_sections'))
		{
			if (!$this->db->tableHasField('#__courses_offering_sections', 'is_default'))
			{
				$query = "ALTER TABLE `#__courses_offering_sections` ADD `is_default` TINYINT(2)  NOT NULL  DEFAULT '0' AFTER `offering_id`";

				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__courses_offering_sections` SET `is_default`=1 WHERE `alias`='__default'";

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
		if ($this->db->tableExists('#__courses_offering_sections'))
		{
			if ($this->db->tableHasField('#__courses_offering_sections', 'is_default'))
			{
				$query = "ALTER TABLE `#__courses_offering_sections` DROP `is_default`;";

				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}