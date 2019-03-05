<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing quicktips module
 **/
class Migration20190109000000ModQuicktips extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_quicktips');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_quicktips');
	}
}
