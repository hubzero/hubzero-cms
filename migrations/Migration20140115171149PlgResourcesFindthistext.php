<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding resources findthistext plugin
 **/
class Migration20140115171149PlgResourcesFindthistext extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('resources','findthistext');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('resources','findthistext');
	}
}