<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing googleanalytics module
 **/
class Migration20190109000000ModGoogleanalytics extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_googleanalytics');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_googleanalytics');
	}
}
