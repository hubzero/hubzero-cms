<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_cron
 **/
class Migration20170831000000ComCron extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('cron');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('cron');
	}
}
