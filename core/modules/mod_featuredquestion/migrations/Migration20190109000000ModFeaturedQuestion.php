<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing featuredquestion module
 **/
class Migration20190109000000ModFeaturedQuestion extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_featuredquestion');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_featuredquestion');
	}
}
