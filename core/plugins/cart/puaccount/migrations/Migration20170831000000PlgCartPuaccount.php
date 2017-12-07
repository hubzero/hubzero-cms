<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Cart - Purdue University Account Numbers plugin
 **/
class Migration20170831000000PlgCartPuaccount extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('cart', 'puaccount');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('cart', 'puaccount');
	}
}
