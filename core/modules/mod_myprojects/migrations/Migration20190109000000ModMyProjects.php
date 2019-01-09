<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing myprojects module
 **/
class Migration20190109000000ModMyProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_myprojects');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_myprojects');
	}
}
