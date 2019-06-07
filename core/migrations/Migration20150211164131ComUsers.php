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
 * Migration script for adding new pending users module
 **/
class Migration20150211164131ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_users', 0, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_users', 1);
	}
}
