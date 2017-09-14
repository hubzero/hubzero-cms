<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_poll
 **/
class Migration20170831000000ComPoll extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('poll');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('poll');
	}
}
