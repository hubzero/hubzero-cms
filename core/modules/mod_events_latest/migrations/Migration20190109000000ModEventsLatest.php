<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing events_latest module
 **/
class Migration20190109000000ModEventsLatest extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_events_latest');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_events_latest');
	}
}
