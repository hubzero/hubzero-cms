<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mygroups module
 **/
class Migration20190305000000ModMyGroupsMini extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_mygroupsmini');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_mygroupsmini');
	}
}
