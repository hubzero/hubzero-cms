<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing resourcemenu module
 **/
class Migration20190109000000ModResourcemenu extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_resourcemenu');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_resourcemenu');
	}
}
