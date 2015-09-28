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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
