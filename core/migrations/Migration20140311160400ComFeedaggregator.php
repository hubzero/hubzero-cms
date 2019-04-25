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
 * Migration script for adding feedaggregator component entry
 **/
class Migration20140311160400ComFeedaggregator extends Base
{
	public function up()
	{
		$this->addComponentEntry('Feedaggregator');
	}

	public function down()
	{
		$this->deleteComponentEntry('Feedaggregator');
	}
}
