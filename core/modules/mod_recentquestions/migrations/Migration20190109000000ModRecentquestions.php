<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing recentquestions module
 **/
class Migration20190109000000ModRecentquestions extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_recentquestions');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_recentquestions');
	}
}
