<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding User - Middleware plugin
 **/
class Migration20170831000000PlgUserMiddleware extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('user', 'middleware');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('user', 'middleware');
	}
}
