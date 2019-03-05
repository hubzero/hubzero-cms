<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Newsletter module
 **/
class Migration20190109000000ModNewsletter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_newsletter');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_newsletter');
	}
}
