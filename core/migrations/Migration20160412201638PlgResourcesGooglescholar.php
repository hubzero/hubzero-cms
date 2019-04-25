<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding plugins for Resource metadata
 **/
class Migration20160412201638PlgResourcesGooglescholar extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deletePluginEntry('resources', 'googleschlar');
		$this->deletePluginEntry('resources', 'googlesochlar');
		$this->addPluginEntry('resources', 'googlescholar');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('resources', 'googlescholar');
		$this->addPluginEntry('resources', 'googleschlar');
	}
}
