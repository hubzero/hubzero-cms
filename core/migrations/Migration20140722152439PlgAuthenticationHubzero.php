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
 * Migration script for enabling admin login on hubzero auth plugin
 **/
class Migration20140722152439PlgAuthenticationHubzero extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$params      = $this->getParams('plg_authentication_hubzero');
		$admin_login = $params->get('admin_login');

		if (is_null($admin_login))
		{
			$params->set('admin_login', '1');
			$params = $params->toArray();
			$this->savePluginParams('authentication', 'hubzero', $params);
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$params      = $this->getParams('plg_authentication_hubzero');
		$admin_login = $params->get('admin_login');

		if (isset($admin_login))
		{
			$params = $params->toArray();
			unset($params['admin_login']);
			$this->savePluginParams('authentication', 'hubzero', $params);
		}
	}
}
