<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Admin login module
 **/
class Migration20190109000000ModAdminlogin extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_adminlogin', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_adminlogin');
	}
}
