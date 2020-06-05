<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding the local filesystem plugin
 **/
class Migration20150904201013PlgFilesystemLocal extends Base
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
