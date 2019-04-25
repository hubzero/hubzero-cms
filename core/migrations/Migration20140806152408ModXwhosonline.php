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
 * Migration script for removing old mod_xwhosonline module
 **/
class Migration20140806152408ModXwhosonline extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteModuleEntry('mod_xwhosonline');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addModuleEntry('mod_xwhosonline', 1, '', 1);
	}
}
