<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Cron - Search plugin
 **/
class Migration20160729201752PlgCronSearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('cron', 'search');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('cron', 'search');
	}
}
