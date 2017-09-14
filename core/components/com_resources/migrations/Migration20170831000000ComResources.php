<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_resources
 **/
class Migration20170831000000ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('resources');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('resources');
	}
}
