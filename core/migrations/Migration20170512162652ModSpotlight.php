<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for uninstalling mod_spotlight
 **/
class Migration20170512162652ModSpotlight extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteModuleEntry('mod_spotlight');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addModuleEntry('mod_spotlight');
	}
}
