<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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