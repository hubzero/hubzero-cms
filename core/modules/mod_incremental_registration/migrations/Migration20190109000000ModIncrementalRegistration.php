<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing incremental_registration module
 **/
class Migration20190109000000ModIncrementalRegistration extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_incremental_registration');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_incremental_registration');
	}
}
