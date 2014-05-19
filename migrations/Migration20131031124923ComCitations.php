<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for defaulting citations field to 0 rather than null
 **/
class Migration20131031124923ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "ALTER TABLE `#__citations` MODIFY `affiliated` int(11) NOT NULL DEFAULT 0;";

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
		$query = "ALTER TABLE `#__citations` MODIFY `affiliated` int(11) DEFAULT NULL;";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}