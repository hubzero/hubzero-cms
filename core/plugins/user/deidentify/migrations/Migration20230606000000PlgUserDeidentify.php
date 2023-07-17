<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2023 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for deidentifying members
 **/
class Migration20230606000000PlgUserDeidentify extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('user', 'deidentify');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('user', 'deidentify');
	}
}
