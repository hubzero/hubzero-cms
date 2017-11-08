<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Members - Projects plugin
 **/
class Migration20170831000000PlgMembersProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('members', 'projects');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('members', 'projects');
	}
}
