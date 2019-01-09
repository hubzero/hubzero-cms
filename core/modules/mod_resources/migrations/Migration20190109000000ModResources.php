<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing resources module
 **/
class Migration20190109000000ModResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_resources', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_resources');
	}
}
