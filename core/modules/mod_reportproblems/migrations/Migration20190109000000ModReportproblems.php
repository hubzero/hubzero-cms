<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing reportproblems module
 **/
class Migration20190109000000ModReportproblems extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_reportproblems');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_reportproblems');
	}
}
