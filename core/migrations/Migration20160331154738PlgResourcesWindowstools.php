<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding plugin for handling (resources) Windows Tools
 **/
class Migration20160331154738PlgResourcesWindowstools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('resources', 'windowstools');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('resources', 'windowstools');
	}
}
