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
 * Migration script for adding system auth factors plugin
 **/
class Migration20150421213243PlgSystemAuthfactors extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('system', 'authfactors', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('system', 'authfactors');
	}
}
