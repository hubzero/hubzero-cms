<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for purging courses form start/end times from form deployments table
 **/
class Migration20141113215958ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "UPDATE `#__courses_form_deployments` SET `start_time` = NULL, `end_time` = NULL";
		$this->db->setQuery($query);
		$this->db->query();
	}
}