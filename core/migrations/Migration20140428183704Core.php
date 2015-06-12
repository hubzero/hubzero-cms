<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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