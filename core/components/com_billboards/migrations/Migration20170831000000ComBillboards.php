<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_billboards
 **/
class Migration20170831000000ComBillboards extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('billboards');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('billboards');
	}
}
