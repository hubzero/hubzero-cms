<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_cart
 **/
class Migration20170831000000ComCart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('cart');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('cart');
	}
}
