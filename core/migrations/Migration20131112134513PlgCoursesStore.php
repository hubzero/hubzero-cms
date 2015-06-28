<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

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