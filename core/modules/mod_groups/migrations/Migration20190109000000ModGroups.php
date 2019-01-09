<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing groups module
 **/
class Migration20190109000000ModGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_groups', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_groups');
	}
}
