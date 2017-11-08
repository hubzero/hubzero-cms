<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Content - Vote plugin
 **/
class Migration20170831000000PlgContentVote extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('content', 'vote');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('content', 'vote');
	}
}
