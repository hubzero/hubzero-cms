<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_kb
 **/
class Migration20170831000000ComKb extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('kb');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('kb');
	}
}
