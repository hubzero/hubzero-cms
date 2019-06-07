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
 * Migration script for disabling new user admin notifications by default
 **/
class Migration20150210234536ComMembers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$params = $this->getParams('com_users');
		$params->set('mail_to_admin', '0');

		$this->saveParams('com_users', $params);
	}
}
