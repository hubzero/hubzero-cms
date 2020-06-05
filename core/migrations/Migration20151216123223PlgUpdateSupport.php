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
 * Migration script for adding Support update plugin
 **/
class Migration20151216123223PlgUpdateSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('update', 'support', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('update', 'support');
	}
}
