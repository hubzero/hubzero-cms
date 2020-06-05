<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for enable or diable plugin DOI for citation component.
 **/
class Migration20190325155621PlgCitationDoi extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('citation', 'doi');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('citation', 'doi');
	}
}
