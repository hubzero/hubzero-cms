<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing mod_feedaggregator 
 **/
class Migration20160104220531ModFeedaggregator extends Base
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
