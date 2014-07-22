<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
		$hubzero     = \JPluginHelper::getPlugin('authentication', 'hubzero');
		$params      = new \JRegistry($hubzero->params);
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
		$hubzero     = \JPluginHelper::getPlugin('authentication', 'hubzero');
		$params      = new \JRegistry($hubzero->params);
		$admin_login = $params->get('admin_login');

		if (isset($admin_login))
		{
			$params = $params->toArray();
			unset($params['admin_login']);
			$this->savePluginParams('authentication', 'hubzero', $params);
		}
	}
}