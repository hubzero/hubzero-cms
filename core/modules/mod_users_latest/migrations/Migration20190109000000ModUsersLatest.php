<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing users_latest module
 **/
class Migration20190109000000ModUsersLatest extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_users_latest', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_users_latest');
	}
}
