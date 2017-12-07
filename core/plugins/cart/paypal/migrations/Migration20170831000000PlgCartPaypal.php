<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Cart - Paypal plugin
 **/
class Migration20170831000000PlgCartPaypal extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('cart', 'paypal', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('cart', 'paypal');
	}
}
