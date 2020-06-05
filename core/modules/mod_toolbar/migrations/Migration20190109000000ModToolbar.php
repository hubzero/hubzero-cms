<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing toolbar module
 **/
class Migration20190109000000ModToolbar extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_toolbar', 1, '', 1);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_toolbar');
	}
}
