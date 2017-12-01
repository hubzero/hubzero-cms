<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing Antispam - Mollom plugin
 **/
class Migration20171127000000PlgAntispamMollom extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deletePluginEntry('antispam', 'mollom');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addPluginEntry('antispam', 'mollom');
	}
}
