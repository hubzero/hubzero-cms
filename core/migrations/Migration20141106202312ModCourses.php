<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding a courses module for the admin cpanel
 **/
class Migration20141106202312ModCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_courses', 1, '', 1);
	}

	/**
	 * Up
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_courses');
	}
}