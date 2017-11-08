<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Members - Resources plugin
 **/
class Migration20170831000000PlgMembersResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('members', 'resources');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('members', 'resources');
	}
}
