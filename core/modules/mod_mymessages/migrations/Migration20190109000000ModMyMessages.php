<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mymessages module
 **/
class Migration20190109000000ModMyMessages extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_mymessages');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_mymessages');
	}
}
