<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mysubmissions module
 **/
class Migration20190109000000ModMySubmissions extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_mysubmissions');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_mysubmissions');
	}
}
