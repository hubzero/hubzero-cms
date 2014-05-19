<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for changing citation field data type
 **/
class Migration20131021225942ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query  = "ALTER TABLE `#__citations` MODIFY COLUMN `volume` VARCHAR(11);";
		$query .= "ALTER TABLE `#__citations` MODIFY COLUMN `year` VARCHAR(4);";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query  = "ALTER TABLE `#__citations` MODIFY COLUMN `volume` INT(11);";
		$query .= "ALTER TABLE `#__citations` MODIFY COLUMN `year` INT(4);";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}