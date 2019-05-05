<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding new weekly bar chart plugin (time reports)
 **/
class Migration20160302141138PlgTimeWeeklybar extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('time', 'weeklybar', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('time', 'weeklybar');
	}
}
