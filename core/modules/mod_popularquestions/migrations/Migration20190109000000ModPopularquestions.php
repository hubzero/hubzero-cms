<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing popularquestions module
 **/
class Migration20190109000000ModPopularquestions extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_popularquestions');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_popularquestions');
	}
}
