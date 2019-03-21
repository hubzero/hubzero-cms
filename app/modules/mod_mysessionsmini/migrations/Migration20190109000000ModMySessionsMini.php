<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mysessions module
 **/
class Migration20190109000000ModMySessionsMini extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_mysessionsmini');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_mysessionsmini');
	}
}
