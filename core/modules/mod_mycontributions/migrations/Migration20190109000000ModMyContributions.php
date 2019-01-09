<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mycontributions module
 **/
class Migration20190109000000ModMyContributions extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_mycontributions');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_mycontributions');
	}
}
