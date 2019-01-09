<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Announcements module
 **/
class Migration20190109000000ModAnnouncements extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_announcements');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_announcements');
	}
}
