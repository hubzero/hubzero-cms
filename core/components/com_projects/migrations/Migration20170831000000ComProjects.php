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
 * Migration script for adding component entry for com_projects
 **/
class Migration20170831000000ComProjects extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('projects');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('projects');
	}
}
