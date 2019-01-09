<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mytodos module
 **/
class Migration20190109000000ModMyTodos extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_mytodos');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_mytodos');
	}
}
