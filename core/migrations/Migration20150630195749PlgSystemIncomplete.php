<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding registration completeness plugin check
 **/
class Migration20150630195749PlgSystemIncomplete extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('system', 'incomplete');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('system', 'incomplete');
	}
}
