<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding hubpresenter file handler
 **/
class Migration20160108151659PlgHandlersHubpresenter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('handlers', 'hubpresenter');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('handlers', 'hubpresenter');
	}
}
