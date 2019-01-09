<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Whatsnew module
 **/
class Migration20190109000000ModWhatsnew extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_whatsnew');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_whatsnew');
	}
}
