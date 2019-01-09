<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing supportactivity module
 **/
class Migration20190109000000ModSupportactivity extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_supportactivity', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_supportactivity');
	}
}
