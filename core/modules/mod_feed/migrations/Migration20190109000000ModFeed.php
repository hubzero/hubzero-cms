<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing feed module
 **/
class Migration20190109000000ModFeed extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_feed');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_feed');
	}
}
