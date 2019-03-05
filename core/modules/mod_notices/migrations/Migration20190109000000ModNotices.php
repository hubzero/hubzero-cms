<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing notices module
 **/
class Migration20190109000000ModNotices extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_notices');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_notices');
	}
}
