<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_citations
 **/
class Migration20170831000000ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('citations');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('citations');
	}
}
