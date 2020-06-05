<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding auth factors authy plugin
 **/
class Migration20150511160714PlgAuthfactorsAuthy extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('authfactors', 'authy', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('authfactors', 'authy');
	}
}
