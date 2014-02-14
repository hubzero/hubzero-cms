<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for add watching table
 **/
class Migration20130512175301PlgCoursesDiscussions extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__plugins'))
		{
			$query = "UPDATE `#__plugins` SET `element`='discussions' WHERE `element`='forum' AND `folder`='courses';";
		}
		else
		{
			$query = "UPDATE `#__extensions` SET `element`='discussions' WHERE `element`='forum' AND `folder`='courses';";
		}

		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__plugins'))
		{
			$query = "UPDATE `#__plugins` SET `element`='forum' WHERE `element`='discussions' AND `folder`='courses';";
		}
		else
		{
			$query = "UPDATE `#__extensions` SET `element`='forum' WHERE `element`='discussions' AND `folder`='courses';";
		}

		$this->db->setQuery($query);
		$this->db->query();
	}
}