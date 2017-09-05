<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_tags
 **/
class Migration20170831000000ComTags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('tags');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('tags');
	}
}
