<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130618144751PlgMembersProfile extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query  = "ALTER TABLE `#__xprofiles` ALTER COLUMN `mailPreferenceOption` SET DEFAULT -1;";
		$query .= "UPDATE `#__xprofiles` SET `mailPreferenceOption`=1 WHERE `mailPreferenceOption`=2;";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}