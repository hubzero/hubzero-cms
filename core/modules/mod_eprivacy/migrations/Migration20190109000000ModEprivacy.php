<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing eprivacy module
 **/
class Migration20190109000000ModEprivacy extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_eprivacy');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_eprivacy');
	}
}
