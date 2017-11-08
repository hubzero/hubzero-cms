<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Groups - Projects plugin
 **/
class Migration20170831000000PlgGroupsProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('groups', 'projects');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('groups', 'projects');
	}
}
