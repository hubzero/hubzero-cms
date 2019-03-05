<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Footer module
 **/
class Migration20190109000000ModFooter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_footer');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_footer');
	}
}
