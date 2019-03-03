<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_oaipmh
 **/
class Migration20170831000000ComOaipmh extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('oaipmh');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('oaipmh');
	}
}
