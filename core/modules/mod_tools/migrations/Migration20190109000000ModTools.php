<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing tools module
 **/
class Migration20190109000000ModTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_tools', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_tools');
	}
}
