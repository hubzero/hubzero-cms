<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Handlers - Latex plugin
 **/
class Migration20170831000000PlgHandlersLatex extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('handlers', 'latex');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('handlers', 'latex');
	}
}
