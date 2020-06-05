<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing incorrect plugin name
 **/
class Migration20160725142238PlgResourcesWatch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deletePluginEntry('resoures', 'watch');
		$this->addPluginEntry('resources', 'watch');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('resources', 'watch');
		$this->addPluginEntry('resources', 'watch');
	}
}
