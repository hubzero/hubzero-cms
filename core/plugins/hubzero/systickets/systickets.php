<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
