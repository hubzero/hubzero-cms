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
 * Migration script for removing Quickicon plugins
 **/
class Migration20171109000000PlgQuickicon extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deletePluginEntry('quickicon', 'extensionupdate');
		$this->deletePluginEntry('quickicon', 'joomlaupdate');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addPluginEntry('quickicon', 'extensionupdate');
		$this->addPluginEntry('quickicon', 'joomlaupdate');
	}
}
