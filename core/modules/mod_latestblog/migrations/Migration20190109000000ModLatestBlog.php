<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Latest Blog module
 **/
class Migration20190109000000ModLatestblog extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_latestblog');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_latestblog');
	}
}
