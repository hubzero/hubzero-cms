<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
