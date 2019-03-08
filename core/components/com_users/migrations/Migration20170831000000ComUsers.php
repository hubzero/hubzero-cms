<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_users
 **/
class Migration20170831000000ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('users');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('users');
	}
}
