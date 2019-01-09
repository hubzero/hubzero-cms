<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing collect module
 **/
class Migration20190109000000ModCollect extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_collect');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_collect');
	}
}
