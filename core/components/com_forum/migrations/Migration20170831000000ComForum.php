<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_forum
 **/
class Migration20170831000000ComForum extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('forum');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('forum');
	}
}
