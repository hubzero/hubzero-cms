<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing supporttickets module
 **/
class Migration20190109000000ModSupporttickets extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_supporttickets', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_supporttickets');
	}
}
