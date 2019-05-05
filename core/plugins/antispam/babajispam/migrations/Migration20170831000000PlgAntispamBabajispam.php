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
 * Migration script for adding Antispam - Babajispam plugin
 **/
class Migration20170831000000PlgAntispamBabajispam extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('antispam', 'babajispam');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('antispam', 'babajispam');
	}
}
