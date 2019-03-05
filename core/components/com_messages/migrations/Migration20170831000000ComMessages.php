<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_messages
 **/
class Migration20170831000000ComMessages extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('messages');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('messages');
	}
}
