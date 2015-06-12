<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding wikitoolbar editor
 **/
class Migration20140211083020PlgEditorsWikitoolbar extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('editors','wikitoolbar');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('editors','wikitoolbar');
	}
}