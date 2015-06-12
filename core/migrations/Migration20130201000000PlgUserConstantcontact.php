<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
