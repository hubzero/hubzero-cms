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
class plgHubzeroSysusers extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return information about this hub
	 * 
	 * @return  array
	 */
	public function onSystemOverview()
	{
		$database = JFactory::getDBO();

		$response = new stdClass;
		$response->name = 'users';
		$response->label = 'Users';
		$response->data = array();

		$database->setQuery("SELECT COUNT(*) FROM `#__users`");
		$response->data['total'] = $this->_obj('Total', $database->loadResult());

		$database->setQuery("SELECT COUNT(*) FROM `#__xprofiles` WHERE `emailConfirmed` < 1");
		$response->data['unconfirmed'] = $this->_obj('Unconfirmed', $database->loadResult());

		$response->data['confirmed'] = $this->_obj('Confirmed', ($response->data['total']->value - $response->data['unconfirmed']->value));

		$database->setQuery("SELECT `lastvisitDate` FROM `#__users` ORDER BY `lastvisitDate` DESC LIMIT 1");
		$response->data['last_visit'] = $this->_obj('Last user login', $database->loadResult());

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
