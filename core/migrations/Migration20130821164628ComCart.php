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
 * Migration script for addind cart component entry
 **/
class Migration20130821164628ComCart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('Cart');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('Cart');
	}
}
