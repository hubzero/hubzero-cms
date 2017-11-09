<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Tools - Java plugin
 **/
class Migration20170831000000PlgToolsJava extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('tools', 'java', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('tools', 'java');
	}
}
