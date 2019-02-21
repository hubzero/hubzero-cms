<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_oauth
 **/
class Migration20170831000000ComUsage extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('oauth');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('oauth');
	}
}
