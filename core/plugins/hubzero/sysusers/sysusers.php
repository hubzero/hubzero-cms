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
