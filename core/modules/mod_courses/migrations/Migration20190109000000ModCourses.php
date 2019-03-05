<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing courses module
 **/
class Migration20190109000000ModCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_courses');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_courses');
	}
}
