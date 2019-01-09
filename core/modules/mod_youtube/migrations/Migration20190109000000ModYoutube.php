<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing youtube module
 **/
class Migration20190109000000ModYoutube extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_youtube');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_youtube');
	}
}
