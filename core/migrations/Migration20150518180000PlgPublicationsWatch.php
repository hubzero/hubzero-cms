<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding a Watch plugin for publications
 **/
class Migration20150518180000PlgPublicationsWatch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('publications', 'watch', 0);
	}

	/**
	 * Up
	 **/
	public function down()
	{
		$this->deletePluginEntry('publications', 'watch');
	}
}