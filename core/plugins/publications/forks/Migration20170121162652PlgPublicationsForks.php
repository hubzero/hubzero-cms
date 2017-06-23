<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Projects - Archive plugin
 **/
class Migration20170121162652PlgProjectsArchive extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('publications', 'forks');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('publications', 'forks');
	}
}
