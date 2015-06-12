<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for delete rogue entry to nonexistent plugin
 **/
class Migration20141029174920PlgUserContactcreator extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deletePluginEntry('user', 'contactcreator');
	}
}