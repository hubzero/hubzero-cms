<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * HUBzero plugin class for system overview
 */
class plgHubzeroSysusers extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return information about this hub
	 *
	 * @param   string  $values
	 * @return  array
	 */
	public function onSystemOverview($values = 'all')
	{
		$database = App::get('db');

		$response = new stdClass;
		$response->name  = 'users';
		$response->label = 'Users';
		$response->data  = array();

		if ($values == 'all')
		{
			$database->setQuery("SELECT COUNT(*) FROM `#__users`");
			$response->data['total'] = $this->_obj('Total', $database->loadResult());

			$database->setQuery("SELECT COUNT(*) FROM `#__users` WHERE `activation` < 1");
			$response->data['unconfirmed'] = $this->_obj('Unconfirmed', $database->loadResult());

			$response->data['confirmed'] = $this->_obj('Confirmed', ($response->data['total']->value - $response->data['unconfirmed']->value));

			$database->setQuery("SELECT `lastvisitDate` FROM `#__users` ORDER BY `lastvisitDate` DESC LIMIT 1");
			$response->data['last_visit'] = $this->_obj('Last user login', $database->loadResult());
		}

		if ($values == 'all' || $values == 'short')
		{
			$database->setQuery("SELECT COUNT(*) FROM `#__session` WHERE `guest`=0 AND `time` >= UNIX_TIMESTAMP(NOW() - INTERVAL 15 MINUTE) AND `client_id`=0;");
			$response->data['site'] = $this->_obj('Active (site)', $database->loadResult());

			$database->setQuery("SELECT COUNT(*) FROM `#__session` WHERE `guest`=0 AND `time` >= UNIX_TIMESTAMP(NOW() - INTERVAL 15 MINUTE) AND `client_id`=1;");
			$response->data['admin'] = $this->_obj('Active (admin)', $database->loadResult());
		}

		return $response;
	}

	/**
	 * Assign label and data to an object
	 *
	 * @param   string  $label
	 * @param   mixed   $value
	 * @return  object
	 */
	private function _obj($label, $value)
	{
		$obj = new stdClass;
		$obj->label = $label;
		$obj->value = $value;

		return $obj;
	}
}
