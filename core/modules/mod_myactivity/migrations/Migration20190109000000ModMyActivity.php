<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing myactivity module
 **/
class Migration20190109000000ModMyActivity extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_myactivity');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_myactivity');
	}
}
