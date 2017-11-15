<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_feedaggregator
 **/
class Migration20170831000000ComFeedaggregator extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('feedaggregator');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('feedaggregator');
	}
}
