<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mymessages module
 **/
class Migration20190904000000ModMyMessagesMini extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_mymessagesmini');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_mymessagesmini');
	}
}
