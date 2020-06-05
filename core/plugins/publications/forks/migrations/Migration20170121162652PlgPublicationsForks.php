<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Publications - Forks plugin
 **/
class Migration20170121162652PlgPublicationsForks extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('publications', 'forks');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('publications', 'forks');
	}
}
