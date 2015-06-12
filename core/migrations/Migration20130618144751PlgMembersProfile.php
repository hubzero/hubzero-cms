<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for distinguishing between unanswered and no in profile mail preference column
 **/
class Migration20130618144751PlgMembersProfile extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query  = "ALTER TABLE `#__xprofiles` ALTER COLUMN `mailPreferenceOption` SET DEFAULT -1;";
		$query .= "UPDATE `#__xprofiles` SET `mailPreferenceOption`=1 WHERE `mailPreferenceOption`=2;";

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
		$query = "";

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}