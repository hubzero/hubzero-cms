<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Content - Categories plugin
 **/
class Migration20170831000000PlgContentCategories extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('content', 'categories');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('content', 'categories');
	}
}
