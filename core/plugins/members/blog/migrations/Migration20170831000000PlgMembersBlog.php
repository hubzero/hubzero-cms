<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Members - Blog plugin
 **/
class Migration20170831000000PlgMembersBlog extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('members', 'blog');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('members', 'blog');
	}
}
