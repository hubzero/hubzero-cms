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
 * Migration script for adding faq, store, and related plugins
 **/
class Migration20130331000000ComCourses extends Base
{
	public function up()
	{
		$this->addPluginEntry('courses', 'faq');
		$this->addPluginEntry('courses', 'related');
		$this->addPluginEntry('courses', 'store');
	}

	public function down()
	{
		$this->deletePluginEntry('courses', 'faq');
		$this->deletePluginEntry('courses', 'related');
		$this->deletePluginEntry('courses', 'store');
	}
}
