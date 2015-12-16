<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Support update plugin
 **/
class Migration20151216123223PlgUpdateSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('update', 'support', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('update', 'support');
	}
}