<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Cart - UPay plugin
 **/
class Migration20170831000000PlgCartUpay extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('cart', 'upay', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('cart', 'upay');
	}
}
