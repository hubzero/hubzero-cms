<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Newsletter - Event plugin
 **/
class Migration20170831000000PlgNewsletterEvent extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('newsletter', 'event');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('newsletter', 'event');
	}
}
