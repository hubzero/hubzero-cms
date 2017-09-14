<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_storefront
 **/
class Migration20170831000000ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('storefront');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('storefront');
	}
}
