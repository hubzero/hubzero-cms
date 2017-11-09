<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding User - LDAP plugin
 **/
class Migration20170831000000PlgUserLdap extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('user', 'ldap');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('user', 'ldap');
	}
}
