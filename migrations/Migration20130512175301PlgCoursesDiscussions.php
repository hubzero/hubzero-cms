<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for add watching table
 **/
class Migration20130512175301PlgCoursesDiscussions extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('#__plugins'))
		{
			$query = "UPDATE `#__plugins` SET `element`='discussions' WHERE `element`='forum' AND `folder`='courses';";
		}
		else
		{
			$query = "UPDATE `#__extensions` SET `element`='discussions' WHERE `element`='forum' AND `folder`='courses';";
		}

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableExists('#__plugins'))
		{
			$query = "UPDATE `#__plugins` SET `element`='forum' WHERE `element`='discussions' AND `folder`='courses';";
		}
		else
		{
			$query = "UPDATE `#__extensions` SET `element`='forum' WHERE `element`='discussions' AND `folder`='courses';";
		}

		$db->setQuery($query);
		$db->query();
	}
}