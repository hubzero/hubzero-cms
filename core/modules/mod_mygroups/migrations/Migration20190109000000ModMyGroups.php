<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mygroups module
 **/
class Migration20190109000000ModMyGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_mygroups');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_mygroups');
	}
}
