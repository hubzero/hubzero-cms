<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Application Env module
 **/
class Migration20190109000000ModApplicationEnv extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_application_env');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_application_env');
	}
}
