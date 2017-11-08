<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Newsletter - Resource plugin
 **/
class Migration20170831000000PlgNewsletterResource extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('newsletter', 'resource');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('newsletter', 'resource');
	}
}
