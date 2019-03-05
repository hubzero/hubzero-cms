<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_mailto
 **/
class Migration20170831000000ComMailto extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('mailto');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('mailto');
	}
}
