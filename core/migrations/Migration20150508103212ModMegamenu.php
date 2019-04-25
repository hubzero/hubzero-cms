<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Hubzero!
defined('_HZEXEC_') or die();

/**
 * Migration script for removing unused/deprecated mod_megamenu module
 **/
class Migration20150508103212ModMegamenu extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteModuleEntry('mod_megamenu');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addModuleEntry('mod_megamenu', 1, '');
	}
}
