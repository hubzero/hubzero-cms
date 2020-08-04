<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Module;

/**
 * Helper Class to render and display modules as needed.
 */
class Helper
{
	/**
	 * Count the modules based on the given condition
	 *
	 * @param   string   $condition  The condition to use
	 * @return  integer  Number of modules found
	 */
	public static function countModules($condition)
	{
		return \Module::count($condition);
	}

	/**
	 * Render modules for a position
	 * Alias method for renderModules()
	 *
	 * @param   string   $position  Position to render modules for
	 * @param   integer  $style     Module style (deprecated?)
	 * @return  string   HTML
	 */
	public static function displayModules($position, $style=-2)
	{
		echo self::renderModules($position, $style);
	}

	/**
	 * Render a specific module
	 * Alias method for renderModule()
	 *
	 * @param   string   $name   Module name
	 * @param   integer  $style  Module style (deprecated?)
	 * @return  void
	 */
	public static function displayModule($name, $style=-1)
	{
		echo self::renderModule($name, $style);
	}

	/**
	 * Render a specific module
	 *
	 * @param   string   $name   Module name
	 * @param   integer  $style  Module style (deprecated?)
	 * @return  string   HTML
	 */
	public static function renderModule($name, $style=-1)
	{
		return \Module::name($name, ($style == -1 ? 'none' : $style));
	}

	/**
	 * Render modules for a position
	 *
	 * @param   string   $position  Position to render modules for
	 * @param   integer  $style     Module style (deprecated?)
	 * @return  string   HTML
	 */
	public static function renderModules($position, $style=-2)
	{
		return \Module::position($position, ($style == -1 ? 'none' : $style));
	}

	/**
	 * Get the parameters for a module
	 *
	 * @param   integer  $id  Module ID
	 * @return  object
	 */
	public function getParams($id)
	{
		return \Module::params($id);
	}
}
