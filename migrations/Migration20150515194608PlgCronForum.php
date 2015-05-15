<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding new cron plugin for group forum posts email digest
 **/
class Migration20150515194608PlgCronForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('cron', 'forum', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('cron', 'forum');
	}
}