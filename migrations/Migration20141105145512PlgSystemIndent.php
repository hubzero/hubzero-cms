<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for removing the system plugin called 'indent'
 **/
class Migration20141105145512PlgSystemIndent extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deletePluginEntry('system', 'indent');
	}

	/**
	 * Up
	 **/
	public function down()
	{
		$this->addPluginEntry('system', 'indent');
	}
}