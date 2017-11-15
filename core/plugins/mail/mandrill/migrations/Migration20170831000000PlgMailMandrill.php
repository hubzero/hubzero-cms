<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Mail - Mandrill plugin
 **/
class Migration20170831000000PlgMailMandrill extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('mail', 'mandrill');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('mail', 'mandrill');
	}
}
