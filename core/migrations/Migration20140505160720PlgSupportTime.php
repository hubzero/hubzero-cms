<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for for adding time plugin for support
 **/
class Migration20140505160720PlgSupportTime extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('support','time', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('support','time');
	}
}