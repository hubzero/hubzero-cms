<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_media
 **/
class Migration20170831000000ComMedia extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('media');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('media');
	}
}
