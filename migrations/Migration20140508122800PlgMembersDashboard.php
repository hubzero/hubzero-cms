<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding id column to dashboard preferences
 **/
class Migration20140508122800PlgMembersDashboard extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasKey('#__xprofiles_dashboard_preferences', 'id'))
		{
			$query  = "ALTER TABLE `#__xprofiles_dashboard_preferences` ADD COLUMN `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (id);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// do nothing
	}
}