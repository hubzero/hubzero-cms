<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Multilangstatus module
 **/
class Migration20190109000000ModMultilangstatus extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_multilangstatus');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_multilangstatus');
	}
}
