<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing myresources module
 **/
class Migration20190109000000ModMyResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_myresources');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_myresources');
	}
}
