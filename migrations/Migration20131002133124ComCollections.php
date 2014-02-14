<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for collection plugin entries
 **/
class Migration20131002133124ComCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('members', 'collections', 0);
		$this->addPluginEntry('groups', 'collections', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('members', 'collections');
		$this->deletePluginEntry('groups', 'collections');
	}
}