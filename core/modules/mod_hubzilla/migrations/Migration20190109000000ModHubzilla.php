<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing hubzilla module
 **/
class Migration20190109000000ModHubzilla extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_hubzilla');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_hubzilla');
	}
}
