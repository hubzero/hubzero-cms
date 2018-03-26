<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_newsletter
 **/
class Migration20170831000000ComNewsletter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('newsletter');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('newsletter');
	}
}
