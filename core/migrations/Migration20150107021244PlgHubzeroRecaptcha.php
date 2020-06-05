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
 * Migration script for clearing old recapta keys
 **/
class Migration20150107021244PlgHubzeroRecaptcha extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// delete and then add to clear old keys
		$this->deletePluginEntry('hubzero', 'recaptcha');
		$this->addPluginEntry('hubzero', 'recaptcha');
	}
}
