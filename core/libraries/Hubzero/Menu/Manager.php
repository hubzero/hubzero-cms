<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Menu;

/**
 * Menu manager class
 */
class Manager
{
	/**
	 * The array of created "menus".
	 *
	 * @var  array
	 */
	protected $menus = array();

	/**
	 * Create a new manager instance.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->menus = array();
	}

	/**
	 * Get the default menu name.
	 *
	 * @return  string
	 */
	public function getDefaultMenu()
	{
		return 'base';
	}

	/**
	 * Get a menu instance.
	 *
	 * @param   string  $menu
	 * @return  mixed
	 */
	public function menu($menu = null, $options = array())
	{
		$menu = $menu ?: $this->getDefaultMenu();

		// If the given menu has not been created before, we will create the instances
		// here and cache it so we can return it next time very quickly. If there is
		// already a menu created by this name, we'll just return that instance.
		if (!isset($this->menus[$menu]))
		{
			$this->menus[$menu] = $this->createMenu($menu, $options);
		}

		return $this->menus[$menu];
	}

	/**
	 * Create a new menu instance.
	 *
	 * @param   string  $menu
	 * @return  mixed
	 * @throws  \InvalidArgumentException
	 */
	protected function createMenu($menu, $options = array())
	{
		$cls = __NAMESPACE__ . '\\Type\\' . ucfirst($menu);

		if (class_exists($cls))
		{
			return new $cls($options);
		}

		throw new \InvalidArgumentException("Menu [$menu] not supported.");
	}

	/**
	 * Get all of the created "menus".
	 *
	 * @return  array
	 */
	public function getMenus()
	{
		return $this->menus;
	}

	/**
	 * Dynamically call the default menu instance.
	 *
	 * @param   string  $method
	 * @param   array   $parameters
	 * @return  mixed
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->menu(), $method), $parameters);
	}
}
