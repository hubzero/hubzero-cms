<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mycourses module
 **/
class Migration20190109000000ModMyCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_mycourses');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_mycourses');
	}
}
