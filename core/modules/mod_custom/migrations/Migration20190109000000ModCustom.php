<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing custom module
 **/
class Migration20190109000000ModCustom extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_custom');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_custom');
	}
}
