<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Xmessage - Internal plugin
 **/
class Migration20170831000000PlgXmessageInternal extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('xmessage', 'internal');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('xmessage', 'internal');
	}
}
