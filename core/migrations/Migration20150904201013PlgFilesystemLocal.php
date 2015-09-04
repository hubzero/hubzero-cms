<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding the local filesystem plugin
 **/
class Migration20150904201013PlgFilesystemLocal extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('filesystem','local');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('filesystem','local');
	}
}