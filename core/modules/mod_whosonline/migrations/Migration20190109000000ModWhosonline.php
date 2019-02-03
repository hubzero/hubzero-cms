<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing whosonline module
 **/
class Migration20190109000000ModWhosonline extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_whosonline');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_whosonline');
	}
}
