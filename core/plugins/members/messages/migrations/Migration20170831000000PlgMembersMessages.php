<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Members - Messages plugin
 **/
class Migration20170831000000PlgMembersMessages extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('members', 'messages');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('members', 'messages');
	}
}
