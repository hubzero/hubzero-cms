<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Answers - Tools plugin
 **/
class Migration20170831000000PlgAnswersTools extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('answers', 'tools');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('answers', 'tools');
	}
}
