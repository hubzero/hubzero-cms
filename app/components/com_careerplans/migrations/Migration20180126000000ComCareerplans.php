<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding component entry for com_careerplans
 **/
class Migration20180126000000ComCareerplans extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('careerplans');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('careerplans');
	}
}
