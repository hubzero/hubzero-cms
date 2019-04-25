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
 * Migration script for adding help component
 **/
class Migration20130426071658ComHelp extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('Help');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('Help');
	}
}
