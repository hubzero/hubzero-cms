<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing billboards module
 **/
class Migration20190109000000ModBillboards extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_billboards');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_billboards');
	}
}
