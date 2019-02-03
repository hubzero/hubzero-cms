<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Latest module
 **/
class Migration20190109000000ModLatest extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_latest');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_latest');
	}
}
