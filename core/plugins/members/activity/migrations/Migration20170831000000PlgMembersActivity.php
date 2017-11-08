<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Members - Activity plugin
 **/
class Migration20170831000000PlgMembersActivity extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('members', 'activity');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('members', 'activity');
	}
}
