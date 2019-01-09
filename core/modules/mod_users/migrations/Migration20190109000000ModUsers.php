<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing users module
 **/
class Migration20190109000000ModUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_users', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_users');
	}
}
