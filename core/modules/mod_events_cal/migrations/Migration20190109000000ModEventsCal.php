<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing events_cal module
 **/
class Migration20190109000000ModEventsCal extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_events_cal');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_events_cal');
	}
}
