<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2022 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing google gtag module
 **/
class Migration20220109000000ModGooglegtag extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_googlegtag');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_googlegtag');
	}
}
