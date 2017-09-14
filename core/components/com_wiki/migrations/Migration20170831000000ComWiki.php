<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_wiki
 **/
class Migration20170831000000ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('wiki');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('wiki');
	}
}
