<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding tool plugins for session rendering
 **/
class Migration20140818162220PlgTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('tools', 'java', 1);
		$this->addPluginEntry('tools', 'novnc', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('tools', 'java');
		$this->deletePluginEntry('tools', 'novnc');
	}
}