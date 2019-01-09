<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing toptags module
 **/
class Migration20190109000000ModToptags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_toptags');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_toptags');
	}
}
