<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * HUBzero plugin class for system overview
 */
class plgHubzeroSystickets extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return information about this hub
	 *
	 * @param   string  $values
	 * @return  array
	 */
	public function onSystemOverview($values = 'all')
	{
		if ($values != 'all')
		{
			return;
		}

		$response = new stdClass;
		$response->name  = 'tickets';
		$response->label = 'Support Tickets';
		$response->data  = array();

		$database = App::get('db');

		$database->setQuery("SELECT COUNT(*) FROM `#__support_tickets` AS f WHERE f.`type` = '0'");
		$response->data['total'] = $this->_obj('Total', intval($database->loadResult()));

		$database->setQuery("SELECT count(DISTINCT f.id) FROM `#__support_tickets` AS f WHERE f.`open` = '1' AND f.`type` = '0'");
		$response->data['open'] = $this->_obj('Open', intval($database->loadResult()));

		$database->setQuery("SELECT count(DISTINCT f.id) FROM `#__support_tickets` AS f WHERE f.`open` = '1' AND f.`type` = '0' AND f.`status` = '0'");
		$response->data['open_new'] = $this->_obj('(open) New', intval($database->loadResult()));

		$database->setQuery("SELECT count(DISTINCT f.id) FROM `#__support_tickets` AS f WHERE f.`open` = '1' AND f.`type` = '0' AND (f.`owner` = '' OR f.`owner` IS NULL)");
		$response->data['open_unassigned'] = $this->_obj('(open) Unassigned', intval($database->loadResult()));

		$database->setQuery("SELECT count(DISTINCT f.id) FROM `#__support_tickets` AS f WHERE f.`open` = '1' AND f.`type` = '0' AND f.`status` = '1'");
		$response->data['open_waiting'] = $this->_obj('(open) Waiting', intval($database->loadResult()));

		$database->setQuery("SELECT f.`created` FROM `#__support_tickets` AS f WHERE f.`open` = '1' AND f.`type` = '0' ORDER BY f.`created` ASC LIMIT 1");
		$response->data['open_oldest'] = $this->_obj('(open) Oldest', $database->loadResult());

		$database->setQuery("SELECT f.`created` FROM `#__support_tickets` AS f WHERE f.`open` = '1' AND f.`type` = '0' ORDER BY f.`created` DESC LIMIT 1");
		$response->data['open_newest'] = $this->_obj('(open) Newest', $database->loadResult());

		$database->setQuery("SELECT count(DISTINCT f.id) FROM `#__support_tickets` AS f WHERE f.`open` = '0' AND f.`type` = '0'");
		$response->data['closed'] = $this->_obj('Closed', intval($database->loadResult()));

		return $response;
	}

	/**
	 * Assign label and data to an object
	 *
	 * @param   string $label
	 * @param   mixed  $value
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
