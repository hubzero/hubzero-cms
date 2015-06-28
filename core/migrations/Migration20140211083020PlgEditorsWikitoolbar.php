<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

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