<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

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