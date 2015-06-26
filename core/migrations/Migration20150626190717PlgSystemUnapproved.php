<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for system plugin verifying user approval status
 **/
class Migration20150626190717PlgSystemUnapproved extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('system', 'unapproved');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('system', 'unapproved');
	}
}