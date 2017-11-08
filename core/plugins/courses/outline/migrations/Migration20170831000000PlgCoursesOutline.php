<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Courses - Outline plugin
 **/
class Migration20170831000000PlgCoursesOutline extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('courses', 'outline');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('courses', 'outline');
	}
}
