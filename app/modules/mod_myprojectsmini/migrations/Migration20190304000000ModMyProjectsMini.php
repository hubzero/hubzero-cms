<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing myprojects module
 **/
class Migration20190304000000ModMyProjectsMini extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_myprojectsmini');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_myprojectsmini');
	}
}
