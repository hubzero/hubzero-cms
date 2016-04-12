<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding plugins for Resource metadata
 **/
class Migration20160412201638PlgResourcesGooglescholar extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deletePluginEntry('resources', 'googleschlar');
		$this->addPluginEntry('resources', 'googlescholar');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('resources', 'googlesochlar');
		$this->addPluginEntry('resources', 'googleschlar');
	}
}
