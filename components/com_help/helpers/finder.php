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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Help\Helpers;

/**
 * Help controller class
 */
class Finder
{
	/**
	 * Help file extension
	 *
	 * @var  string
	 */
	protected static $ext = 'phtml';

	/**
	 * Get path to help page
	 *
	 * @return  void
	 */
	public static function page($component, $extension, $page)
	{
		$name   = str_replace('com_', '', $component);
		$client = \JFactory::getApplication()->isAdmin() ? 'admin' : 'site';
		$tmpl   = \JFactory::getApplication()->getTemplate();
		$lang   = \Lang::getTag();

		$paths = array(
			// Template override help page
			JPATH_BASE . DS . 'templates' . DS . $tmpl . DS .  'html' . DS . 'plg_' . $name . '_' . $page . DS . 'help' . DS . $lang . DS . 'index.' . self::$ext,
			JPATH_BASE . DS . 'templates' . DS . $tmpl . DS .  'html' . DS . $component  . DS . 'help' . DS . $lang . DS . $page . '.' . self::$ext
		);

		// Path to help page
		$paths[] = self::path($component) . DS . 'help' . DS . $lang . DS . $page . '.' . self::$ext;
		$paths[] = PATH_ROOT . DS . 'plugins' . DS . $name . DS . $page . DS . 'help' . DS . $lang . DS . 'index.' . self::$ext;

		// If we have an extension
		if ($extension)
		{
			$paths[2] = PATH_CORE . DS . 'plugins' . DS . $name . DS . $extension . DS . 'help' . DS . $lang . DS . $page . '.' . self::$ext;
			$paths[0] = JPATH_BASE . DS . 'templates' . DS . $tmpl . DS .  'html' . DS . 'plg_' . $name . '_' . $extension . DS . 'help' . DS . $lang . DS . $page . '.' . self::$ext;
		}

		$final = '';

		// Determine path for help page
		foreach ($paths as $path)
		{
			if (file_exists($path))
			{
				$final = $path;
			}
		}

		return $final;
	}

	/**
	 * Get array of help pages for component
	 *
	 * @param   string  $component  Component to get pages for
	 * @return  array
	 */
	private static function path($component)
	{
		$client = \App::isAdmin() ? 'admin' : 'site';
		if (file_exists(PATH_ROOT . DS . 'components' . DS . $component . DS . $client))
		{
			return PATH_ROOT . DS . 'components' . DS . $component . DS . $client;
		}
		else
		{
			return JPATH_BASE . DS . 'components' . DS . $component;
		}
	}

	/**
	 * Get array of help pages for component
	 *
	 * @param   string  $component  Component to get pages for
	 * @return  array
	 */
	public static function pages($component)
	{
		$database = \JFactory::getDBO();

		// Get component name from database
		$database->setQuery(
			"SELECT `name`
			FROM `#__extensions`
			WHERE `type`=" . $database->quote('component') . "
			AND `element`=" . $database->quote($component) . "
			AND `enabled`=1"
		);
		$name = $database->loadResult();

		// Make sure we have a component
		if ($name == '')
		{
			$name = str_replace('com_', '', $component);

			return array(
				'name'   => ucfirst($name),
				'option' => $component,
				'pages'  => array()
			);
		}

		// Path to help pages
		$helpPagesPath = self::path($component) . DS . 'help' . DS . \Lang::getTag();

		// Make sure directory exists
		$pages = array();
		if (is_dir($helpPagesPath))
		{
			// Get help pages for this component
			jimport('joomla.filesystem.folder');
			$pages = \JFolder::files($helpPagesPath , '.' . self::$ext);
		}

		// Return pages
		return array(
			'name'   => $name,
			'option' => $component,
			'pages'  => $pages
		);
	}
}