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
 * Migration script for enabling publications groups plugin
 **/
class Migration20140626100712PlgPublicationsGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('publications', 'groups');
	}

	/**
	 * Up
	 **/
	public function down()
	{
		$this->deletePluginEntry('publications', 'groups');
	}
}
