<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding varioud activity log plugins
 **/
class Migration20160415110900PlgActivity extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('system', 'activity');
		$this->addPluginEntry('members', 'activity');
		$this->addPluginEntry('groups', 'activity');
		$this->addPluginEntry('resoures', 'watch');
		$this->addModuleEntry('mod_myactivity');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('system', 'activity');
		$this->deletePluginEntry('members', 'activity');
		$this->deletePluginEntry('groups', 'activity');
		$this->deletePluginEntry('resoures', 'watch');
		$this->deleteModuleEntry('mod_myactivity');
	}
}