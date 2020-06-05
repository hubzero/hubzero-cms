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
 * Migration script for getting rid of duplicate section date entries
 **/
class Migration20140314080012PlgSupportPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('support', 'publications');
	}

	/**
	 * Up
	 **/
	public function down()
	{
		$this->deletePluginEntry('support', 'publications');
	}
}
