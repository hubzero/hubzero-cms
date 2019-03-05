<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Latest Groups module
 **/
class Migration20190109000000ModLatestGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_latestgroups');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_latestgroups');
	}
}
