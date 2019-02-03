<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing quotes module
 **/
class Migration20190109000000ModQuotes extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_quotes');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_quotes');
	}
}
