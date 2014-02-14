<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for for adding wikiwyg plugin
 **/
class Migration20140211083120PlgEditorsWikiwyg extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('editors','wikiwyg');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('editors','wikiwyg');
	}
}