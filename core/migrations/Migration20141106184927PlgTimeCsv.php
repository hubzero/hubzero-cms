<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for csv time plugin
 **/
class Migration20141106184927PlgTimeCsv extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('time', 'csv', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('time', 'csv');
	}
}
