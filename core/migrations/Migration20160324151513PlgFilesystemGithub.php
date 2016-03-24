<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding the github filesystem plugin
 **/
class Migration20160324151513PlgFilesystemGithub extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('filesystem','github', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('filesystem','github');
	}
}
