<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing admin title module
 **/
class Migration20190109000000ModTitle extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_title', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_title');
	}
}
