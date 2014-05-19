<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for deleting groups userenrollment plugin
 **/
class Migration20130401000000ComGroups extends Base
{
	public function up()
	{
		$this->deletePluginEntry('groups', 'userenrollment');
	}
}