<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing slideshow module
 **/
class Migration20190109000000ModSlideshow extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_slideshow');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_slideshow');
	}
}
