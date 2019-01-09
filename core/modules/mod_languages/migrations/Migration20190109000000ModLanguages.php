<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Languages module
 **/
class Migration20190109000000ModLanguages extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_languages');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_languages');
	}
}
