<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Cart - Offline plugin
 **/
class Migration20170831000000PlgCartOffline extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('cart', 'offline');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('cart', 'offline');
	}
}
