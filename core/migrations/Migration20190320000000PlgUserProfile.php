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
 * Migration script for removing User - Profile plugin
 **/
class Migration20190320000000PlgUserProfile extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deletePluginEntry('user', 'profile');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addPluginEntry('user', 'profile', 0);
	}
}
