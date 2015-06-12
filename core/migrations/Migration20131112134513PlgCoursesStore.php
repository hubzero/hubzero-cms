<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for disabling courses store plugin for the time being
 **/
class Migration20131112134513PlgCoursesStore extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->disablePlugin('courses', 'store');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->enablePlugin('courses', 'store');
	}
}