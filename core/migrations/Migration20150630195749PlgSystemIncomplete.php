<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding registration completeness plugin check
 **/
class Migration20150630195749PlgSystemIncomplete extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('system', 'incomplete');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('system', 'incomplete');
	}
}