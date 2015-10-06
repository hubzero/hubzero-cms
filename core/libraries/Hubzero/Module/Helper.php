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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		echo self::renderModule($name,$style);
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

