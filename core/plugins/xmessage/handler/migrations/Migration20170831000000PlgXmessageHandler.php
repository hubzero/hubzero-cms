<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Xmessage - Handler plugin
 **/
class Migration20170831000000PlgXmessageHandler extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('xmessage', 'handler');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('xmessage', 'handler');
	}
}
