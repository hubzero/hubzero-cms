<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding pdf file handler
 **/
class Migration20160108151435PlgHandlersPdf extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('handlers', 'pdf');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('handlers', 'pdf');
	}
}
