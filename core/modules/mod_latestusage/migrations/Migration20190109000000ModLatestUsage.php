<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Latest Usage module
 **/
class Migration20190109000000ModLatestUsage extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_latestusage');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_latestusage');
	}
}
