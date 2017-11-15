<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Tools - NoVNC plugin
 **/
class Migration20170831000000PlgToolsNovnc extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('tools', 'novnc');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('tools', 'novnc');
	}
}
