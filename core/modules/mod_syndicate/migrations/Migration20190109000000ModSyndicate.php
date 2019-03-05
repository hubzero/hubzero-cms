<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing syndicate module
 **/
class Migration20190109000000ModSyndicate extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_syndicate');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_syndicate');
	}
}
