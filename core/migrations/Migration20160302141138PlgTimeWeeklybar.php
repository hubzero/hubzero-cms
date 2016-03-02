<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding new weekly bar chart plugin (time reports)
 **/
class Migration20160302141138PlgTimeWeeklybar extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('time', 'weeklybar', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('time', 'weeklybar');
	}
}
