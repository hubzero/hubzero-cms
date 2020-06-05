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
 * Migration script for removing old mod_status module.
 **/
class Migration20140806144415ModStatus extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteModuleEntry('mod_status');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addModuleEntry('mod_status', 1, '', 1);
	}
}
