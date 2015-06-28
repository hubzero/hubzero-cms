<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

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