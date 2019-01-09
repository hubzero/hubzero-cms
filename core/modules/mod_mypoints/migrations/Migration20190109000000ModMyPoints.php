<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mypoints module
 **/
class Migration20190109000000ModMyPoints extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_mypoints');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_mypoints');
	}
}
