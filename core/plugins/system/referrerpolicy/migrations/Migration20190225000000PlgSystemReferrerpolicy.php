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
 * Migration script for adding System - Referrer Policy plugin
 **/
class Migration20190225000000PlgSystemReferrerpolicy extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('system', 'referrerpolicy');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('system', 'referrerpolicy');
	}
}
