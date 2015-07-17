<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding a Watch plugin for projects
 **/
class Migration20150717100000PlgProjectsWatch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('projects', 'watch', 0);
	}

	/**
	 * Up
	 **/
	public function down()
	{
		$this->deletePluginEntry('projects', 'watch');
	}
}