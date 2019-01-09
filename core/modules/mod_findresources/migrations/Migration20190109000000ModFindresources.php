<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Findresources module
 **/
class Migration20190109000000ModFindresources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_findresources');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_findresources');
	}
}
