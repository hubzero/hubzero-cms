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
 * Migration script for deleting topics component entry
 **/
class Migration20140415105610ComTopics extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteComponentEntry('topics');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addComponentEntry('topics');
	}
}
