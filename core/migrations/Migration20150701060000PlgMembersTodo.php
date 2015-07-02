<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding todo member plugin
 **/
class Migration20150701060000PlgMembersTodo extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('members', 'todo', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('members', 'todo');
	}
}