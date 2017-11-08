<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Groups - Files plugin
 **/
class Migration20170831000000PlgGroupsFiles extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('groups', 'files');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('groups', 'files');
	}
}
