<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for unregistering plg_cron_search
 **/
class Migration20170602135253PlgSearchCron extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deletePluginEntry('cron', 'search');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addPluginEntry('cron', 'search');
	}
}
