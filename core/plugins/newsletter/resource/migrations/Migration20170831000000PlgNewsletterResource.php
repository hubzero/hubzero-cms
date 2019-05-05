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
 * Migration script for adding Newsletter - Resource plugin
 **/
class Migration20170831000000PlgNewsletterResource extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('newsletter', 'resource');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('newsletter', 'resource');
	}
}
