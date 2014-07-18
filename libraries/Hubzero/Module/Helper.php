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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	 * @param  string 	$condition	The condition to use
	 * @return integer  Number of modules found
	 */
	public static function countModules($condition)
	{
		jimport('joomla.application.module.helper');

		$words = explode(' ', $condition);
		for ($i = 0; $i < count($words); $i+=2)
		{
			// odd parts (modules)
			$name = strtolower($words[$i]);
			$words[$i] = count(\JModuleHelper::getModules($name));
		}

		$str = 'return ' . implode(' ', $words) . ';';

		return eval($str);
	}

	/**
	 * Render modules for a position
	 * Alias method for renderModules()
	 *
	 * @param      string  $position Position to render modules for
	 * @param      integer $style    Module style (deprecated?)
	 * @return     string HTML
	 */
	public static function displayModules($position, $style=-2)
	{
		echo self::renderModules($position, $style);
	}

	/**
	 * Render a specific module
	 * Alias method for renderModule()
	 *
	 * @param      string  $name  Module name
	 * @param      integer $style Module style (deprecated?)
	 * @return     void
	 */
	public static function displayModule($name, $style=-1)
	{
		echo self::renderModule($name,$style);
	}

	/**
	 * Render a specific module
	 *
	 * @param      string  $name  Module name
	 * @param      integer $style Module style (deprecated?)
	 * @return     string HTML
	 */
	public static function renderModule($name, $style=-1)
	{
		$module = \JModuleHelper::getModule($name);
		$params = array('style' => $style);

		return \JModuleHelper::renderModule($module, $params);
	}

	/**
	 * Render modules for a position
	 *
	 * @param      string  $position Position to render modules for
	 * @param      integer $style    Module style (deprecated?)
	 * @return     string HTML
	 */
	public static function renderModules($position, $style=-2)
	{
		$document = \JFactory::getDocument();
		$renderer = $document->loadRenderer('module');
		$params   = array('style' => $style);

		$contents = '';
		foreach (\JModuleHelper::getModules($position) as $mod)
		{
			if ($mod->showtitle != 0)
			{
				$contents .= '<h3>' . stripslashes($mod->title) . '</h3>';
			}
			$contents .= $renderer->render($mod, $params);
		}

		return $contents;
	}

	/**
	 * Get the parameters for a module
	 *
	 * @param      integer $id Module ID
	 * @return     object
	 */
	public function getParams($id)
	{
		//database object
		$db = \JFactory::getDBO();

		//select module params based on name passed in
		$db->setQuery("SELECT params FROM `#__modules` WHERE id='" . intval($id) . "' AND published=1");
		$params = $db->loadResult();

		//return params
		return new \JRegistry($params);
	}
}

