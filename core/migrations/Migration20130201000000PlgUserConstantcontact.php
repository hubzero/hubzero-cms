<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding constant contact plugin entry
 **/
class Migration20130201000000PlgUserConstantcontact extends Base
{
	public function up()
	{
		$this->addPluginEntry('user', 'constantcontact');
	}

	public function down()
	{
		$this->deletePluginEntry('user', 'constantcontact');
	}
}
