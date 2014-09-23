<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Document;

use JFactory;
use JURI;
use Exception;
use lessc;

/**
 * Class for adding stylesheets from components, modules, and plugins to the document
 */
class Assets
{
	/**
	 * Get the base path
	 *
	 * @return  string
	 */
	public static function base()
	{
		$base = JPATH_SITE;
		if (JFactory::getApplication()->isAdmin())
		{
			$base = JPATH_ADMINISTRATOR;
		}
		return $base;
	}

	/**
	 * Check if a filename is a supported image type
	 *
	 * @param   string $image Filename
	 * @return  boolean
	 */
	public static function isImage($image)
	{
		if (!trim($image))
		{
			return false;
		}

		jimport('joomla.filesystem.file');
		$ext = strtolower(\JFile::getExt($image));
		if (!in_array($ext, array('gif', 'jpg', 'jpe', 'jpeg', 'png', 'bmp')))
		{
			return false;
		}

		return true;
	}

	/**
	 * Adds a linked stylesheet to the page
	 *
	 * @param	string  $stylesheet Stylesheet name (optional, uses component name if left blank)
	 * @return  void
	 */
	public static function addStylesheet($stylesheet)
	{
		if (!$stylesheet)
		{
			return;
		}

		if (substr($stylesheet, -4) != '.css')
		{
			$stylesheet .= '.css';
		}

		$root = self::base();

		JFactory::getDocument()->addStyleSheet(rtrim(JURI::base(true), DS) . $stylesheet . '?v=' . filemtime($root . $stylesheet));
	}

	/**
	 * Adds a linked script to the page
	 *
	 * @param	string  $script Script name (optional, uses module name if left blank)
	 * @return  void
	 */
	public static function addScript($script)
	{
		if (!$script)
		{
			return;
		}

		if (substr($script, -3) != '.js')
		{
			$script .= '.js';
		}

		$root = self::base();

		JFactory::getDocument()->addScript(rtrim(JURI::base(true), DS) . $script . '?v=' . filemtime($root . $script));
	}

	/**
	 * Adds a linked stylesheet from a component to the page
	 *
	 * @param	string  $component  Component name
	 * @param	string  $stylesheet Stylesheet name (optional, uses component name if left blank)
	 * @param	string  $dir        Asset directory to look in
	 * @return  void
	 */
	public static function addComponentStylesheet($component, $stylesheet = '', $dir = 'css')
	{
		$app = JFactory::getApplication();
		$template = $app->getTemplate();

		$root = self::base();

		if (empty($stylesheet))
		{
			$stylesheet = substr($component, 4) . '.css';
		}
		if (substr(strtolower($stylesheet), -4) != '.css')
		{
			$stylesheet .= '.css';
		}

		// Build a list of possible paths
		$paths = array();

		if (defined('JPATH_GROUPCOMPONENT'))
		{
			$base = substr(JPATH_GROUPCOMPONENT, strlen(JPATH_ROOT));

			$paths[] = $base . DS . 'assets' . DS . 'css' . DS . $stylesheet;
			$paths[] = $base . DS . $stylesheet;
		}
		else
		{
			$paths[] = DS . 'templates' . DS . $template . DS . 'html' . DS . $component . DS . $stylesheet;
			$paths[] = DS . 'components' . DS . $component . DS . 'assets' . ($dir ? DS . $dir : '') . DS . $stylesheet;
			$paths[] = DS . 'components' . DS . $component . DS . $stylesheet;
		}

		// Run through each path until we find one that works
		foreach ($paths as $path)
		{
			if (file_exists($root . $path))
			{
				// Push script to the document
				$jdocument = JFactory::getDocument();
				$jdocument->addStyleSheet(rtrim(JURI::base(true), DS) . $path . '?v=' . filemtime($root . $path));
				break;
			}
		}
	}

	/**
	 * Adds a linked script from a component to the page
	 *
	 * @param   string  $component  URL to the linked script
	 * @param	string  $script     Script name (optional, uses module name if left blank)
	 * @param	string  $dir        Asset directory to look in
	 * @return  void
	 */
	public static function addComponentScript($component, $script = '', $dir = 'js')
	{
		if (empty($script))
		{
			$script = substr($component, 4);
		}

		// We need to momentarily strip the file extension
		if (substr(strtolower($script), -3) == '.js')
		{
			$script = substr($script, 0, -3);
		}

		$base = DS . 'components' . DS . $component;
		if (defined('JPATH_GROUPCOMPONENT'))
		{
			$base = substr(JPATH_GROUPCOMPONENT, strlen(JPATH_ROOT));
		}

		// Build a list of possible paths
		$paths = array();

		if (\JPluginHelper::isEnabled('system', 'jquery'))
		{
			$paths[] = $base . DS . 'assets' . ($dir ? DS . $dir : '') . DS . $script . '.jquery.js';
			$paths[] = $base . DS . $script . '.jquery.js';
		}

		$paths[] = $base . DS . 'assets' . ($dir ? DS . $dir : '') . DS . $script . '.js';
		$paths[] = $base . DS . $script . '.js';

		$root = self::base();

		// Run through each path until we find one that works
		foreach ($paths as $path)
		{
			if (file_exists($root . $path))
			{
				// Push script to the document
				$jdocument = JFactory::getDocument();
				$jdocument->addScript(rtrim(JURI::base(true), DS) . $path . '?v=' . filemtime($root . $path));
				break;
			}
		}
	}

	/**
	 * Adds a linked stylesheet from the system to the page
	 *
	 * @param	string  $stylesheet Stylesheet name
	 * @param	string  $dir        Asset directory to look in
	 * @return  void
	 */
	public static function addSystemStylesheet($stylesheet, $dir = 'css')
	{
		if (!$stylesheet)
		{
			return;
		}

		if (substr(strtolower($stylesheet), -4) != '.css')
		{
			$stylesheet .= '.css';
		}

		$template  = JFactory::getApplication()->getTemplate();

		// Build a list of possible paths
		$paths = array();

		$paths[] = DS . 'templates' . DS . $template . DS . 'html' . DS . 'system' . ($dir ? DS . $dir : '') . DS . $stylesheet;
		$paths[] = DS . 'media' . DS . 'system' . ($dir ? DS . $dir : '') . DS . $stylesheet;

		// Run through each path until we find one that works
		foreach ($paths as $i => $path)
		{
			$base = JPATH_ROOT;
			$b = str_replace('/administrator', '', rtrim(JURI::base(true), DS));
			if ($i == 0)
			{
				$base = (JFactory::getApplication()->isAdmin() ? JPATH_ADMINISTRATOR : JPATH_SITE);
				$b = rtrim(JURI::base(true), DS);
			}
			if (file_exists($base . $path))
			{
				// Push script to the document
				$jdocument = JFactory::getDocument();
				$jdocument->addStyleSheet($b . $path . '?v=' . filemtime($base . $path));
				break;
			}
		}
	}

	/**
	 * Adds a linked script from the system to the page
	 *
	 * @param	string  $script     Script name (optional, uses module name if left blank)
	 * @param	string  $dir        Asset directory to look in
	 * @return  void
	 */
	public static function addSystemScript($script, $dir = 'js')
	{
		if (!$script)
		{
			return;
		}

		// We need to momentarily strip the file extension
		if (substr(strtolower($script), -3) == '.js')
		{
			$script = substr($script, 0, -3);
		}

		$base = DS . 'media' . DS . 'system' . ($dir ? DS . $dir : '');

		// Build a list of possible paths
		$paths = array();
		if (\JPluginHelper::isEnabled('system', 'jquery'))
		{
			$paths[] = $base . DS . $script . '.jquery.js';
		}
		$paths[] = $base . DS . $script . '.js';

		// Run through each path until we find one that works
		foreach ($paths as $path)
		{
			if (file_exists(JPATH_ROOT . $path))
			{
				// Push script to the document
				$jdocument = JFactory::getDocument();
				$jdocument->addScript(str_replace('/administrator', '', rtrim(JURI::base(true), DS)) . $path . '?v=' . filemtime(JPATH_ROOT . $path));
				break;
			}
		}
	}

	/**
	 * Gets the path to a component image
	 * checks template overrides first, then component
	 *
	 * @param	string  $component	Component name
	 * @param	string  $image		Image to look for
	 * @param	string  $dir        Asset directory to look in
	 * @return  string	Path to an image file
	 */
	public static function getComponentImage($component, $image, $dir = 'img')
	{
		$image = ltrim($image, DS);

		if (!self::isImage($image))
		{
			return $image;
		}

		$template  = JFactory::getApplication()->getTemplate();

		$paths = array();
		$paths[] = DS . 'templates' . DS . $template . DS . 'html' . DS . $component . DS . 'images' . DS . $image;
		$paths[] = DS . 'components' . DS . $component . DS . 'assets' . ($dir ? DS . $dir : '') . DS . $image;
		$paths[] = DS . 'components' . DS . $component . DS . 'images' . DS . $image;

		$root = self::base();

		// Run through each path until we find one that works
		foreach ($paths as $path)
		{
			if (file_exists($root . $path))
			{
				// Push script to the document
				return rtrim(JURI::base(true), DS) . $path;
			}
		}
	}

	/**
	 * Gets the path to a component stylesheet
	 * checks template overrides first, then component
	 *
	 * @param	string  $component	Component name
	 * @param	string  $stylesheet	Stylesheet to look for
	 * @param	string  $dir        Asset directory to look in
	 * @return  string	Path to a stylesheet
	 */
	public static function getComponentStylesheet($component, $stylesheet, $dir = 'css')
	{
		$template  = JFactory::getApplication()->getTemplate();

		$paths = array();
		$paths[] = DS . 'templates' . DS . $template . DS . 'html' . DS . $component . DS . $stylesheet;
		$paths[] = DS . 'components' . DS . $component . DS . 'assets' . ($dir ? DS . $dir : '') . DS . $stylesheet;
		$paths[] = DS . 'components' . DS . $component . DS . $folder . DS . $stylesheet;

		$root = self::base();

		// Run through each path until we find one that works
		foreach ($paths as $path)
		{
			if (file_exists($root . $path))
			{
				// Push script to the document
				return rtrim(JURI::base(true), DS) . $path;
			}
		}
	}

	/**
	 * Gets the path to a module image
	 * checks template overrides first, then module
	 *
	 * @param	string  $module	Module name
	 * @param	string  $image	Image to look for
	 * @param	string  $dir        Asset directory to look in
	 * @return  string	Path to an image file
	 */
	public static function getModuleImage($module, $image, $dir = 'img')
	{
		$image = ltrim($image, DS);

		if (!self::isImage($image))
		{
			return $image;
		}

		$template  = JFactory::getApplication()->getTemplate();

		$paths = array();
		$paths[] = DS . 'templates' . DS . $template . DS . 'html' . DS . $module . DS . 'images' . DS . $image;
		$paths[] = DS . 'modules' . DS . $module . DS . 'assets' . ($dir ? DS . $dir : '') . DS . $image;
		$paths[] = DS . 'modules' . DS . $module . DS . 'images' . DS . $image;

		$root = self::base();

		// Run through each path until we find one that works
		foreach ($paths as $path)
		{
			if (file_exists($root . $path))
			{
				// Push script to the document
				return rtrim(JURI::base(true), DS) . $path;
			}
		}
	}

	/**
	 * Adds a linked stylesheet from a module to the page
	 *
	 * @param	string  $module		Module name
	 * @param	string  $stylesheet	Stylesheet name (optional, uses module name if left blank)
	 * @param	string  $dir        Asset directory to look in
	 * @return  void
	 */
	public static function addModuleStyleSheet($module, $stylesheet = '', $dir = 'css')
	{
		$template = JFactory::getApplication()->getTemplate();

		if (empty($stylesheet))
		{
			$stylesheet = $module . '.css';
		}
		if (!$stylesheet)
		{
			return;
		}

		if (substr(strtolower($stylesheet), -4) != '.css')
		{
			$stylesheet .= '.css';
		}

		$root = self::base();

		// Build a list of possible paths
		$paths = array();

		$paths[] = DS . 'templates' . DS . $template . DS . 'html' . DS . $module . DS . $stylesheet;
		$paths[] = DS . 'modules' . DS . $module . DS . 'assets' . ($dir ? DS . $dir : '') . DS . $stylesheet;
		$paths[] = DS . 'modules' . DS . $module . DS . $stylesheet;

		// Run through each path until we find one that works
		foreach ($paths as $path)
		{
			if (file_exists($root . $path))
			{
				// Push script to the document
				$jdocument = JFactory::getDocument();
				$jdocument->addStyleSheet(rtrim(JURI::base(true), DS) . $path . '?v=' . filemtime($root . $path));
				break;
			}
		}
	}

	/**
	 * Adds a linked script to the page
	 *
	 * @param   string  $module  	URL to the linked script
	 * @param	string  $script  	Script name (optional, uses module name if left blank)
	 * @param	string  $dir        Asset directory to look in
	 * @return  void
	 */
	public static function addModuleScript($module, $script = '', $dir = 'js')
	{
		$template = JFactory::getApplication()->getTemplate();

		if (empty($script))
		{
			$script = $module;
		}

		if (!$script)
		{
			return;
		}

		// We need to momentarily strip the file extension
		if (substr(strtolower($script), -3) == '.js')
		{
			$script = substr($script, 0, -3);
		}

		$root = self::base();

		// Build a list of possible paths
		$paths = array();

		if (\JPluginHelper::isEnabled('system', 'jquery'))
		{
			$paths[] = DS . 'templates' . DS . $template . DS . 'html' . DS . $module . DS . $script . '.jquery.js';
			$paths[] = DS . 'modules' . DS . $module . DS . 'assets' . ($dir ? DS . $dir : '') . DS . $script . '.jquery.js';
			$paths[] = DS . 'modules' . DS . $module . DS . $script . '.jquery.js';
		}

		$paths[] = DS . 'templates' . DS . $template . DS . 'html' . DS . $module . DS . $script . '.js';
		$paths[] = DS . 'modules' . DS . $module . DS . 'assets' . ($dir ? DS . $dir : '') . DS . $script . '.js';
		$paths[] = DS . 'modules' . DS . $module . DS . $script . '.js';

		// Run through each path until we find one that works
		foreach ($paths as $path)
		{
			if (file_exists($root . $path))
			{
				// Push script to the document
				$jdocument = JFactory::getDocument();
				$jdocument->addScript(rtrim(JURI::base(true), DS) . $path . '?v=' . filemtime($root . $path));
				break;
			}
		}
	}

	/**
	 * Gets the path to a plugin image
	 * checks template overrides first, then plugin folder
	 *
	 * @param	string  $folder		Plugin folder name
	 * @param	string  $plugin		Plugin name
	 * @param	string  $image		Image to look for
	 * @param	string  $dir        Asset directory to look in
	 * @return  string	Path to an image file
	 */
	public static function getPluginImage($folder, $plugin, $image, $dir = 'img')
	{
		$image = ltrim($image, DS);

		if (!self::isImage($image))
		{
			return $image;
		}

		$template  = JFactory::getApplication()->getTemplate();

		$paths = array();
		$paths[] = DS . 'templates' . DS . $template . DS . 'html' . DS . 'plg_' . $folder . '_' . $plugin . DS . 'images' . DS . $image;
		$paths[] = DS . 'plugins' . DS . $folder . DS . $plugin . DS . 'assets' . ($dir ? DS . $dir : '') . DS . $image;
		$paths[] = DS . 'plugins' . DS . $folder . DS . $plugin . DS . 'images' . DS . $image;

		// Run through each path until we find one that works
		foreach ($paths as $i => $path)
		{
			$root = JPATH_SITE;
			if ($i == 0)
			{
				$root = JPATH_ADMINISTRATOR;
			}

			if (file_exists($root . $path))
			{
				if ($i == 0)
				{
					$b = rtrim(JURI::base(true), DS);
				}
				else
				{
					$b = str_replace('/administrator', '', rtrim(JURI::base(true), DS));
				}
				// Push script to the document
				return $b . $path;
			}
		}
	}

	/**
	 * Adds a linked stylesheet from a plugin to the page
	 *
	 * @param	string  $folder		Plugin folder name
	 * @param	string  $plugin		Plugin name
	 * @param	string  $stylesheet	Stylesheet name (optional, uses module name if left blank)
	 * @param	string  $dir        Asset directory to look in
	 * @return  void
	 */
	public static function addPluginStyleSheet($folder, $plugin, $stylesheet = '', $dir = 'css')
	{
		$template = JFactory::getApplication()->getTemplate();

		if (empty($stylesheet))
		{
			if (!$plugin)
			{
				return;
			}
			$stylesheet = $plugin . '.css';
		}

		if (substr(strtolower($stylesheet), -4) != '.css')
		{
			$stylesheet .= '.css';
		}

		// Build a list of possible paths
		$paths = array();

		$paths[] = DS . 'templates' . DS . $template . DS . 'html' . DS . 'plg_' . $folder . '_' . $plugin . DS . $stylesheet;
		$paths[] = DS . 'plugins' . DS . $folder . DS . $plugin . DS . 'assets' . ($dir ? DS . $dir : '') . DS . $stylesheet;
		$paths[] = DS . 'plugins' . DS . $folder . DS . $plugin . DS . $stylesheet;

		// Run through each path until we find one that works
		foreach ($paths as $i => $path)
		{
			$root = JPATH_SITE;
			if ($i == 0)
			{
				$root = self::base();
			}

			if (file_exists($root . $path))
			{
				if ($i == 0)
				{
					$b = rtrim(JURI::base(true), DS);
				}
				else
				{
					$b = str_replace('/administrator', '', rtrim(JURI::base(true), DS));
				}
				// Push script to the document
				$jdocument = JFactory::getDocument();
				$jdocument->addStyleSheet($b . $path . '?v=' . filemtime($root . $path));
				break;
			}
		}
	}

	/**
	 * Adds a linked script to the page
	 *
	 * @param	string  $folder		Plugin folder name
	 * @param	string  $plugin		Plugin name
	 * @param	string  $script  	Script name (optional, uses module name if left blank)
	 * @param	string  $dir        Asset directory to look in
	 * @return  void
	 */
	public static function addPluginScript($folder, $plugin, $script = '', $dir = 'js')
	{
		$template = JFactory::getApplication()->getTemplate();

		if (empty($script))
		{
			$script = $plugin;
		}

		if (!$script)
		{
			return;
		}

		// We need to momentarily strip the file extension
		if (substr(strtolower($script), -3) == '.js')
		{
			$script = substr($script, 0, -3);
		}

		// Build a list of possible paths
		$paths = array();

		if (\JPluginHelper::isEnabled('system', 'jquery'))
		{
			$paths[] = DS . 'templates' . DS . $template . DS . 'html' . DS . 'plg_' . $folder . '_' . $plugin . DS . $script . '.jquery.js';
			$paths[] = DS . 'plugins' . DS . $folder . DS . $plugin . DS . 'assets' . ($dir ? DS . $dir : '') . DS . $script . '.jquery.js';
			$paths[] = DS . 'plugins' . DS . $folder . DS . $plugin . DS . $script . '.jquery.js';
		}

		$paths[] = DS . 'templates' . DS . $template . DS . 'html' . DS . 'plg_' . $folder . '_' . $plugin . DS . $script . '.js';
		$paths[] = DS . 'plugins' . DS . $folder . DS . $plugin . DS . 'assets' . ($dir ? DS . $dir : '') . DS . $script . '.js';
		$paths[] = DS . 'plugins' . DS . $folder . DS . $plugin . DS . $script . '.js';

		// Run through each path until we find one that works
		foreach ($paths as $i => $path)
		{
			$root = JPATH_SITE;
			if ($i == 0 || $i == 3)
			{
				$root = self::base();
			}

			if (file_exists($root . $path))
			{
				if ($i == 0 || $i == 3)
				{
					$b = rtrim(JURI::base(true), DS);
				}
				else
				{
					$b = str_replace('/administrator', '', rtrim(JURI::base(true), DS));
				}
				// Push script to the document
				$jdocument = JFactory::getDocument();
				$jdocument->addScript($b . $path . '?v=' . filemtime($root . $path));
				break;
			}
		}
	}

	/**
	 * Gets the path to a system image
	 *
	 * @param	string  $image		Image to look for
	 * @param	string  $dir        Asset directory to look in
	 * @return  string	Path to an image file
	 */
	public static function getSystemImage($image, $dir = 'images')
	{
		$image = ltrim($image, DS);

		if (!self::isImage($image))
		{
			return $image;
		}

		$template  = JFactory::getApplication()->getTemplate();

		$paths = array();
		$paths[] = DS . 'templates' . DS . $template . DS . 'html' . DS . 'system' . ($dir ? DS . $dir : '') . DS . $image;
		$paths[] = DS . 'media' . DS . 'system' . DS . $dir . DS . $image;

		// Run through each path until we find one that works
		foreach ($paths as $path)
		{
			if (file_exists(JPATH_ROOT . $path))
			{
				// Push script to the document
				return str_replace('/administrator', '', rtrim(JURI::base(true), DS)) . $path;
			}
		}
	}

	/**
	 * Returns the path to a system stylesheet
	 * Accepts either an array or string of comma-separated file names
	 * If more than one stylesheet is called for, it will combine, compress, return path to cached file
	 *
	 * @param	mixed  $elements An array or string of comma-separated file names
	 * @return  string
	 */
	public static function getSystemStylesheet($elements = null)
	{
		// Path to system cache
		$cachedir = JPATH_ROOT . DS . 'cache';
		// Path to system CSS
		$thispath = JPATH_ROOT . DS . 'media' . DS . 'system' . DS . 'css';

		$env = JFactory::getConfig()->getValue('config.application_env', 'production');

		try {
			// Primary build file
			$primary   = 'site';

			// Cache vars
			$output    = $cachedir . DS . $primary . '.css';

			// If debugging is turned off and a cache file exist
			//if (!JDEBUG && file_exists($output))
			if ($env == 'production' && file_exists($output))
			{
				$output =  DS . 'cache' . DS . $primary. '.css?v=' . filemtime($output);
			}
			else
			{
				$lesspath = JPATH_ROOT . DS . 'media' . DS . 'system' . DS . 'less';

				if (!class_exists('lessc'))
				{
					throw new Exception('LESS parser not found.');
				}

				// Try to compile LESS files
				$less = new lessc;
				if ($env != 'development')
				{
					$less->setFormatter('compressed');
				}

				// Are there any template overrides?
				$template  = JPATH_ROOT . DS . 'templates' . DS . JFactory::getApplication()->getTemplate() . DS . 'less'; // . 'bootstrap.less';
				$input     = $lesspath . DS . $primary . '.less';

				if (file_exists($template . DS . $primary . '.less'))
				{
					// Reset the path to the primary build file
					$input = $template . DS . $primary . '.less';
				}

				// Add the template path to the import list
				$less->setImportDir(array(
					$template . DS,
					$lesspath . DS
				));

				$cacheFile = $cachedir . DS . $primary . '.less.cache';
				$cache     = null;

				if (file_exists($cacheFile))
				{
					$cache = unserialize(file_get_contents($cacheFile));
				}

				if ($cache && is_array($cache['files']))
				{
					foreach ($cache['files'] as $fname => $ftime)
					{
						$path = explode('/', $fname);
						$file = array_pop($path);

						if (file_exists($template . '/' . $file))
						{
							$nname = $template . '/' . $file;
						}
						else
						{
							$nname = $lesspath . '/' . $file;
						}

						if ($fname != $nname or !file_exists($nname) or filemtime($nname) > $ftime)
						{
							// One of the files we knew about previously has changed
							// so we should look at our incoming root again.
							$cache = $input;
							break;
						}
					}
				}

				// If no cache file or the root build file is different
				if (!$cache || ($cache['root'] != $input))
				{
					$cache = $input;
				}

				// create a new cache object, and compile
				/*
				array(
					'files'    => list of files imported,
					'root'     => root file (bootstrap.less)
					'updated'  => timestamp,
					'compiled' => compiled LESS
				)
				*/

				if (is_string($cache))
				{
					$newCache = $less->cachedCompile($cache);
				}
				else
				{
					$newCache = $cache;
				}

				// Did the cache change?
				if (!is_array($cache) || $newCache['updated'] > $cache['updated'])
				{
					file_put_contents($cacheFile, serialize($newCache));  // Update the compiled LESS timestamp
					$newCache['compiled'] = str_replace("'/media/system/", "'" . rtrim(JURI::base(true), DS) . '/media/system/', $newCache['compiled']);
					file_put_contents($output, $newCache['compiled']);    // Update the compiled LESS
				}
				$output =  rtrim(JURI::root(true), '/') . DS . 'cache' . DS . $primary . '.css?v=' . $newCache['updated'];
			}
		}
		catch (Exception $e)
		{
			//echo "fatal error: " . $e->getMessage(); die();

			// Anything passed?
			if (!$elements)
			{
				return '';
			}
			// Is it a string?
			if (is_string($elements))
			{
				$elements = explode(',', $elements);
			}
			if (count($elements) <= 0)
			{
				return '';
			}
			// Trim items
			$elements = array_map('trim', $elements);

			// Determine last modification date of the files
			$lastmodified = 0;

			foreach ($elements as $k => $element)
			{
				if (!$element)
				{
					$elements[$k] = false;
					continue;
				}

				// Strip file extension to normalize data
				$element = basename($element, '.css');

				$elements[$k] = $element;

				// Check if the file exists
				$path = $thispath . DS . $element . '.css';

				if (!file_exists($path))
				{
					$elements[$k] = false;
					continue;
				}

				// Get the last modified time
				// We take the max time so $lastmodified should be different if any of the files have changed.
				$lastmodified += filemtime($path);
			}

			// Remove any empty items
			$elements = array_filter($elements);

			// Build hash
			$hash = $lastmodified; // . '-' . md5(implode(',', $elements));

			// Only one stylesheet called for so return it as is
			if (count($elements) == 1)
			{
				return $thispath . DS . $elements[0] . '.css';
			}

			// Try the cache first to see if the combined files were already generated
			$cachefile = 'system-' . $hash . '.css';

			if (!file_exists($cachedir . DS . $cachefile))
			{
				$contents = '';
				reset($elements);

				foreach ($elements as $k => $element)
				{
					$contents .= "\n\n" . file_get_contents($thispath . DS . $element . '.css');
				}
				$patterns = array(
					'!/\*[^*]*\*+([^/][^*]*\*+)*/!',  /* remove comments */
					'/[\n\r \t]/',                    /* remove tabs, spaces, newlines, etc. */
					'/ +/'                           /* collapse multiple spaces to a single space */
					/* '/ ?([,:;{}]) ?/'                 remove space before and after , : ; { }     [!] apparently, IE 7 doesn't like this and won't process the stylesheet */
				);
				$replacements = array(
					'',
					' ',
					' '/*,
					'$1'*/
				);
				$contents = preg_replace($patterns, $replacements, $contents);
				$contents = str_replace("url('/media/system/", "url('" . rtrim(JURI::base(true), DS) . "/media/system/", $contents);

				if ($fp = fopen($cachedir . DS . $cachefile, 'wb'))
				{
					fwrite($fp, $contents);
					fclose($fp);
				}
			}

			$output = rtrim(JURI::base(true), DS) . DS . 'cache' . DS . $cachefile;
		}

		return $output;
	}
}
