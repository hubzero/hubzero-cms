<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing feedaggregator module
 **/
class Migration20190109000000ModFeedaggregator extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_feedaggregator');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_feedaggregator');
	}
}
