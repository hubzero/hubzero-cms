<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Login module
 **/
class Migration20190109000000ModLogin extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_login');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_login');
	}
}
