<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing random_image module
 **/
class Migration20190109000000ModRandomImage extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_random_image');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_random_image');
	}
}
