<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding super group system plugin
 **/
class Migration20140108233322PlgSystemSupergroup extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('system','supergroup');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('system','supergroup');
	}
}