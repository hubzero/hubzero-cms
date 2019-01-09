<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Logjserrors module
 **/
class Migration20190109000000ModLogjserrors extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_logjserrors');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_logjserrors');
	}
}
