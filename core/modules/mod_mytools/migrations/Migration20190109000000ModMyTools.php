<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mytools module
 **/
class Migration20190109000000ModMyTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_mytools');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_mytools');
	}
}
