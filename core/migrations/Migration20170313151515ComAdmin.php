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
 * Migration script for removing unused com_admin component
 **/
class Migration20170313151515ComAdmin extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteComponentEntry('admin');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addComponentEntry('admin');
	}
}
