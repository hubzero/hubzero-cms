<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for deleting usage chart plugin
 **/
class Migration20140305081020PlgUsageChart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deletePluginEntry('usage', 'chart');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addPluginEntry('usage', 'chart', 0);
	}
}