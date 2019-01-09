<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing grouppages module
 **/
class Migration20190109000000ModGrouppages extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_grouppages', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_grouppages');
	}
}
