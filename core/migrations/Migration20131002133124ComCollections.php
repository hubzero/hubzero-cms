<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for collection plugin entries
 **/
class Migration20131002133124ComCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('members', 'collections', 0);
		$this->addPluginEntry('groups', 'collections', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('members', 'collections');
		$this->deletePluginEntry('groups', 'collections');
	}
}