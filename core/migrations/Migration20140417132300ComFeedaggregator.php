<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding feedaggregator entry in disabled state
 **/
class Migration20140417132300ComFeedaggregator extends Base
{
	public function up()
	{
		$this->deleteComponentEntry('feedaggregator');
		$this->addComponentEntry('feedaggregator', null, 1, '', false);
	}

	public function down()
	{
		$this->deleteComponentEntry('feedaggregator');
	}
}
