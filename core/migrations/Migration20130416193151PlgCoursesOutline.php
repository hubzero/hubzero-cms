<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for inserting courses outline plugin
 **/
class Migration20130416193151PlgCoursesOutline extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('courses', 'outline');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('courses', 'outline');
	}
}
