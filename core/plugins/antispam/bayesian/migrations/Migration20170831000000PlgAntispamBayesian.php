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
 * Migration script for adding Antispam - Bayesian plugin
 **/
class Migration20170831000000PlgAntispamBayesian extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('antispam', 'bayesian');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('antispam', 'bayesian');
	}
}
