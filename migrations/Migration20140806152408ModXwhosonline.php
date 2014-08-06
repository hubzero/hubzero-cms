<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140806152408ModXwhosonline extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteModuleEntry('mod_xwhosonline');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addModuleEntry('mod_xwhosonline', 1, '', 1);
	}
}