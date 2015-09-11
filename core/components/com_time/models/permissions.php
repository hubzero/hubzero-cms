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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Time\Models;

use Hubzero\Base\Object;

/**
 * Permissions model for time component
 */
class Permissions extends Object
{
	/**
	 * Option
	 *
	 * @var string
	 **/
	private $option = null;

	/**
	 * Permissions
	 *
	 * @var array
	 **/
	private $permissions = array();

	/**
	 * Constructor
	 *
	 * @param  (string) $option
	 * @return void
	 **/
	public function __construct($option)
	{
		$this->option = $option;
	}

	/**
	 * Check if user can perform a given action
	 *
	 * @param string $action - action to perform
	 * @param string $type   - type of item to check
	 * @param int    $id     - id of item to check
	 *
	 * @return bool
	 */
	public function can($action, $type = 'hubs', $id = 0)
	{
		// Group authorization overrides all (for now)
		if ($this->authorize())
		{
			return true;
		}

		$name = $this->option;

		if ($id)
		{
			$name .= '.' . $type . '.' . (int) $id;
		}

		$key = $name . '.' . $action;

		if (!isset($this->permissions[$key]))
		{
			$this->permissions[$key] = User::authorise($action, $name);
		}

		return $this->permissions[$key];
	}

	/**
	 * Check authorization
	 *
	 * @return bool
	 **/
	private function authorize()
	{
		static $authorized = null;

		if (!isset($authorized))
		{
			$config      = Component::params('com_time');
			$accessgroup = $config->get('accessgroup', 'time');
			$authorized  = false;

			// Check if they're a member of admin group
			$ugs = \Hubzero\User\Helper::getGroups(User::get('id'));
			if ($ugs && count($ugs) > 0)
			{
				foreach ($ugs as $ug)
				{
					if ($ug->cn == $accessgroup)
					{
						$authorized = true;
					}
				}
			}
		}

		return $authorized;
	}
}