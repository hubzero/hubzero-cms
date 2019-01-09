<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mycuration module
 **/
class Migration20190109000000ModMyCuration extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_mycuration');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_mycuration');
	}
}
