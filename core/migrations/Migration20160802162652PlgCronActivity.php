<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Cron - Activity plugin
 **/
class Migration20160802162652PlgCronActivity extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('cron', 'activity');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('cron', 'activity');
	}
}
