<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Permissions model for time component
 */
class TimeModelPermissions extends \Hubzero\Base\Object
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
		$this->juser  = JFactory::getUser();
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
			$this->permissions[$key] = $this->juser->authorise($action, $name);
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
			$config      = \JComponentHelper::getParams('com_time');
			$accessgroup = $config->get('accessgroup', 'time');
			$authorized  = false;

			// Check if they're a member of admin group
			$ugs = \Hubzero\User\Helper::getGroups($this->juser->get('id'));
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