<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Files group plugin
 **/
class Migration20151105145500PlgGroupsFiles extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('groups', 'files');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('groups', 'files');
	}
}