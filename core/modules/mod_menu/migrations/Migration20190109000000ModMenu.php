<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Menu module
 **/
class Migration20190109000000ModMenu extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_menu');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_menu');
	}
}
