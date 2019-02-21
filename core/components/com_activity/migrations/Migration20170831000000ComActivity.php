<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_activity
 **/
class Migration20170831000000ComActivity extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('activity');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('activity');
	}
}
