<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing myquestions module
 **/
class Migration20190109000000ModMyQuestions extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_myquestions');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_myquestions');
	}
}
