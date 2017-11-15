<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Support - Forum plugin
 **/
class Migration20170831000000PlgSupportForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('support', 'forum');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('support', 'forum');
	}
}
