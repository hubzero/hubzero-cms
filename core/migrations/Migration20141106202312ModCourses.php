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
 * Migration script for adding a courses module for the admin cpanel
 **/
class Migration20141106202312ModCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_courses', 1, '', 1);
	}

	/**
	 * Up
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_courses');
	}
}
