<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing rapid_contact module
 **/
class Migration20190109000000ModRapidContact extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_rapid_contact');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_rapid_contact');
	}
}
