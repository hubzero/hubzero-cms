<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding members impact plugin
 **/
class Migration20140529150000PlgMembersImpact extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('members', 'impact');
	}

	/**
	 * Up
	 **/
	public function down()
	{
		$this->deletePluginEntry('members', 'impact');
	}
}