<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing submenu module
 **/
class Migration20190109000000ModSubmenu extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_submenu', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_submenu');
	}
}
