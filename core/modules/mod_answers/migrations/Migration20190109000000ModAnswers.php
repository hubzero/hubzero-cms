<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Answers module
 **/
class Migration20190109000000ModAnswers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_answers');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_answers');
	}
}
