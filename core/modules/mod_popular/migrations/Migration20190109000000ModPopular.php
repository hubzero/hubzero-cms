<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing popular module
 **/
class Migration20190109000000ModPopular extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_popular', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_popular');
	}
}
