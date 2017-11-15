<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Citation - Endnote plugin
 **/
class Migration20170831000000PlgCitationEndnote extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('citation', 'endnote');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('citation', 'endnote');
	}
}
