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
 * Migration script for adding entry for Projects - Publications plugin
 **/
class Migration20170831000000PlgProjectsPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('projects', 'publications');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('projects', 'publications');
	}
}
