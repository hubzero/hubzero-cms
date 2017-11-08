<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Groups - Announcements plugin
 **/
class Migration20170831000000PlgGroupsAnnouncements extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('groups', 'announcements');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('groups', 'announcements');
	}
}
