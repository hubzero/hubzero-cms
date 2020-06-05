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
 * Migration script for adding twitter authentication plugin
 **/
class Migration20130529204838PlgAuthenticationTwitter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('authentication', 'twitter', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('authentication', 'twitter');
	}
}
