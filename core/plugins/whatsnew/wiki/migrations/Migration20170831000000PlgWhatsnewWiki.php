<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Whatsnew - Wiki plugin
 **/
class Migration20170831000000PlgWhatsnewWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('whatsnew', 'wiki');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('whatsnew', 'wiki');
	}
}
