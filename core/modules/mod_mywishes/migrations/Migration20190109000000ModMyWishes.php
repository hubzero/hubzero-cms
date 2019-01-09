<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mywishes module
 **/
class Migration20190109000000ModMywishes extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_mywishes');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_mywishes');
	}
}
