<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing randomquote module
 **/
class Migration20190109000000ModRandomQuote extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_randomquote');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_randomquote');
	}
}
