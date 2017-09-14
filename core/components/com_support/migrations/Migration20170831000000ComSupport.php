<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_support
 **/
class Migration20170831000000ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('support');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('support');
	}
}
