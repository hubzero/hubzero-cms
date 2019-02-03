<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing stats module
 **/
class Migration20190109000000ModStats extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_stats');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_stats');
	}
}
