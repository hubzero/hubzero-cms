<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mytickets module
 **/
class Migration20190109000000ModMyTickets extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_mytickets');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_mytickets');
	}
}
