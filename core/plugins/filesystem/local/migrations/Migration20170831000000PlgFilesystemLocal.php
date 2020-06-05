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
 * Migration script for adding Filesystem - Local plugin
 **/
class Migration20170831000000PlgFilesystemLocal extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('filesystem', 'local');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('filesystem', 'local');
	}
}
