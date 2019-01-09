<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing poll module
 **/
class Migration20190109000000ModPoll extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_poll');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_poll');
	}
}
