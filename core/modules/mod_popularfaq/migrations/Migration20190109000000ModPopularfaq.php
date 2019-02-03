<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing popularfaq module
 **/
class Migration20190109000000ModPopularfaq extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_popularfaq');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_popularfaq');
	}
}
