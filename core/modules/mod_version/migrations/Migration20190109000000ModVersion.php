<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing version module
 **/
class Migration20190109000000ModVersion extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_version', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_version');
	}
}
