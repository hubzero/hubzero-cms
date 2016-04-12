<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding plugins for Resource metadata
 **/
class Migration20160412173638PlgResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('resources', 'googleschlar');
		$this->addPluginEntry('resources', 'opengraph');
		$this->addPluginEntry('resources', 'dublincore');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('resources', 'googleschlar');
		$this->deletePluginEntry('resources', 'opengraph');
		$this->deletePluginEntry('resources', 'dublincore');
	}
}
