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
 * Migration script for adding a Watch plugin for publications
 **/
class Migration20150518180000PlgPublicationsWatch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('publications', 'watch', 0);
	}

	/**
	 * Up
	 **/
	public function down()
	{
		$this->deletePluginEntry('publications', 'watch');
	}
}
