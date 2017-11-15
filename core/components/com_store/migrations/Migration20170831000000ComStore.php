<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_store
 **/
class Migration20170831000000ComStore extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('store');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('store');
	}
}
