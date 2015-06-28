<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding plugin entry for time summary
 **/
class Migration20140721163818PlgTimeSummary extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('time', 'summary', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('time', 'summary');
	}
}