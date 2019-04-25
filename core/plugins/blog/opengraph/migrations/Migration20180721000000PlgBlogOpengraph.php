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
 * Migration script for adding Blog - Opengraph plugin
 **/
class Migration20180721000000PlgBlogOpengraph extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('blog', 'opengraph');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('blog', 'opengraph');
	}
}
