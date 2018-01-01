<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_publications
 **/
class Migration20170831000000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('publications');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('publications');
	}
}
