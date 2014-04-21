<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding publications tags plugin
 **/
class Migration20140421080012PlgTagsPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('tags', 'publications');
	}

	/**
	 * Up
	 **/
	public function down()
	{
		$this->deletePluginEntry('tags', 'publications');
	}
}