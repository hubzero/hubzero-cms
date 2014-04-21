<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * HUBzero plugin class for system overview
 */
class plgHubzeroSystickets extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return information about this hub
	 * 
	 * @return  array
	 */
	public function onSystemOverview()
	{
		$response = new stdClass;
		$response->name  = 'tickets';
		$response->label = 'Support Tickets';
		$response->data  = array();

		$database = JFactory::getDBO();

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
