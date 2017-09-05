<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_jobs
 **/
class Migration20170831000000ComJobs extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('jobs');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('jobs');
	}
}
