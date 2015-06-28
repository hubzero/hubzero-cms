<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for cleaning up some old component entries
 **/
class Migration20140428183704Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteComponentEntry('apc');
		$this->deleteComponentEntry('geodb');
		$this->deleteComponentEntry('ldap');
		$this->deleteComponentEntry('myhub');
		$this->deleteComponentEntry('xflash');
		$this->deleteComponentEntry('xpoll');
	}
}