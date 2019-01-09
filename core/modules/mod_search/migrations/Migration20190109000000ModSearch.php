<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing search module
 **/
class Migration20190109000000ModSearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_search');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_search');
	}
}
