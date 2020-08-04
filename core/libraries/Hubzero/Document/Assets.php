<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document;

use Hubzero\Document\Asset\Javascript;
use Hubzero\Document\Asset\Stylesheet;
use Exception;
use Request;
use lessc;

/**
 * Class for adding stylesheets from components, modules, and plugins to the document
 */
class Assets
{
	/**
	 * Get an item from the applcation
	 *
	 * @param   string  $key
	 * @return  mixed
	 */
	protected static function app($key)
	{
		if (\App::has($key))
		{
			return \App::get($key);
		}
		return null;
	}

	/**
	 * Get the base path
	 *
	 * @return  string
	 */
	public static function base()
	{
		return PATH_APP;
	}

	/**
	 * Check if a filename is a supported image type
	 *
	 * @param   string   $image  Filename
	 * @return  boolean
	 */
	public static function isImage($image)
	{
		if (!trim($image))
		{
			return false;
		}

		$dot = strrpos($image, '.') + 1;

		$ext = substr($image, $dot);
		$ext = strtolower($ext);
		if (!in_array($ext, array('gif', 'jpg', 'jpe', 'jpeg', 'png', 'bmp')))
		{
			return false;
		}

		return true;
	}

	/**
	 * Adds a linked stylesheet to the page
	 *
	 * @param   string  $stylesheet  Stylesheet name (optional, uses component name if left blank)
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

		if ($document = self::app('document'))
		{
			$document->addStyleSheet(rtrim(Request::base(true), '/') . $stylesheet . '?v=' . filemtime($root . $stylesheet));
		}
	}

	/**
	 * Adds a linked script to the page
	 *
	 * @param   string  $script  Script name (optional, uses module name if left blank)
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

		if ($document = self::app('document'))
		{
			$document->addScript(rtrim(Request::base(true), '/') . $script . '?v=' . filemtime($root . $script));
		}
	}

	/**
	 * Adds a linked stylesheet from a component to the page
	 *
	 * @param   string  $component   Component name
	 * @param   string  $stylesheet  Stylesheet name (optional, uses component name if left blank)
	 * @param   string  $dir         Asset directory to look in
	 * @return  void
	 */
	public static function addComponentStylesheet($component, $stylesheet = '', $dir = 'css')
	{
		if ($dir != 'css')
		{
			$stylesheet = $dir . '/' . $stylesheet;
		}

		$asset = new Stylesheet($component, $stylesheet);

		if (defined('JPATH_GROUPCOMPONENT'))
		{
			$base = substr(JPATH_GROUPCOMPONENT, strlen(PATH_ROOT));

			$asset->setPath('source', $base . DS . 'assets' . DS . 'css' . DS . $asset->file());
			$asset->setPath('override', $base . DS . 'assets' . DS . 'css' . DS . $asset->file());
		}

		if ($asset->exists())
		{
			if ($document = self::app('document'))
			{
				$document->addStyleSheet($asset->link());
			}
		}
	}

	/**
	 * Adds a linked script from a component to the page
	 *
	 * @param   string  $component  URL to the linked script
	 * @param   string  $script     Script name (optional, uses module name if left blank)
	 * @param   string  $dir        Asset directory to look in
	 * @return  void
	 */
	public static function addComponentScript($component, $script = '', $dir = 'js')
	{
		if ($dir != 'js')
		{
			$script = $dir . '/' . $script;
		}

		$asset = new Javascript($component, $script);

		if (defined('JPATH_GROUPCOMPONENT'))
		{
			$base = substr(JPATH_GROUPCOMPONENT, strlen(PATH_ROOT));

			$asset->setPath('source', $base . DS . 'assets' . DS . ($dir ? DS . $dir : '') . DS . $asset->file());
			$asset->setPath('override', $base . DS . 'assets' . DS . ($dir ? DS . $dir : '') . DS . $asset->file());
		}

		if ($asset->exists())
		{
			if ($document = self::app('document'))
			{
				$document->addScript($asset->link());
			}
		}
	}

	/**
	 * Adds a linked stylesheet from the system to the page
	 *
	 * @param   string  $stylesheet  Stylesheet name
	 * @param   string  $dir         Asset directory to look in
	 * @return  void
	 */
	public static function addSystemStylesheet($stylesheet, $dir = 'css')
	{
		if ($dir != 'css')
		{
			$stylesheet = $dir . '/' . $stylesheet;
		}

		$asset = new Stylesheet('system', $stylesheet);

		if ($asset->exists())
		{
			if ($document = self::app('document'))
			{
				$document->addStyleSheet($asset->link());
			}
		}
	}

	/**
	 * Adds a linked script from the system to the page
	 *
	 * @param   string  $script  Script name (optional, uses module name if left blank)
	 * @param   string  $dir     Asset directory to look in
	 * @return  void
	 */
	public static function addSystemScript($script, $dir = 'js')
	{
		if ($dir != 'js')
		{
			$script = $dir . '/' . $script;
		}

		$asset = new Javascript('system', $script);

		if ($asset->exists())
		{
			if ($document = self::app('document'))
			{
				$document->addScript($asset->link());
			}
		}
	}

	/**
	 * Gets the path to a component image
	 * checks template overrides first, then component
	 *
	 * @param   string  $component  Component name
	 * @param   string  $image      Image to look for
	 * @param   string  $dir        Asset directory to look in
	 * @return  string	Path to an image file
	 */
	public static function getComponentImage($component, $image, $dir = 'img')
	{
		$image = ltrim($image, DS);

		if (!self::isImage($image))
		{
			return $image;
		}

		$template = 'system';
		if ($t = self::app('template'))
		{
			$template = self::app('template')->template;
		}

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
				return rtrim(Request::base(true), '/') . $path;
			}
		}
	}

	/**
	 * Gets the path to a component stylesheet
	 * checks template overrides first, then component
	 *
	 * @param   string  $component   Component name
	 * @param   string  $stylesheet  Stylesheet to look for
	 * @param   string  $dir         Asset directory to look in
	 * @return  string  Path to a stylesheet
	 */
	public static function getComponentStylesheet($component, $stylesheet, $dir = 'css')
	{
		$template = 'system';
		if ($t = self::app('template'))
		{
			$template = self::app('template')->template;
		}

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
				return rtrim(Request::base(true), '/') . $path;
			}
		}
	}

	/**
	 * Gets the path to a module image
	 * checks template overrides first, then module
	 *
	 * @param   string  $module  Module name
	 * @param   string  $image   Image to look for
	 * @param   string  $dir     Asset directory to look in
	 * @return  string  Path to an image file
	 */
	public static function getModuleImage($module, $image, $dir = 'img')
	{
		$image = ltrim($image, DS);

		if (!self::isImage($image))
		{
			return $image;
		}

		$template = 'system';
		if ($t = self::app('template'))
		{
			$template = self::app('template')->template;
		}

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
				return rtrim(Request::base(true), '/') . $path;
			}
		}
	}

	/**
	 * Adds a linked stylesheet from a module to the page
	 *
	 * @param   string  $module      Module name
	 * @param   string  $stylesheet  Stylesheet name (optional, uses module name if left blank)
	 * @param   string  $dir         Asset directory to look in
	 * @return  void
	 */
	public static function addModuleStyleSheet($module, $stylesheet = '', $dir = 'css')
	{
		if ($dir != 'css')
		{
			$stylesheet = $dir . '/' . $stylesheet;
		}

		$asset = new Stylesheet($module, $stylesheet);

		if ($asset->exists())
		{
			if ($document = self::app('document'))
			{
				$document->addStyleSheet($asset->link());
			}
		}
	}

	/**
	 * Adds a linked script to the page
	 *
	 * @param   string  $module  URL to the linked script
	 * @param   string  $script  Script name (optional, uses module name if left blank)
	 * @param   string  $dir     Asset directory to look in
	 * @return  void
	 */
	public static function addModuleScript($module, $script = '', $dir = 'js')
	{
		if ($dir != 'js')
		{
			$script = $dir . '/' . $script;
		}

		$asset = new Javascript($module, $script);

		if ($asset->exists())
		{
			if ($document = self::app('document'))
			{
				$document->addScript($asset->link());
			}
		}
	}

	/**
	 * Gets the path to a plugin image
	 * checks template overrides first, then plugin folder
	 *
	 * @param   string  $folder  Plugin folder name
	 * @param   string  $plugin  Plugin name
	 * @param   string  $image   Image to look for
	 * @param   string  $dir     Asset directory to look in
	 * @return  string	Path to an image file
	 */
	public static function getPluginImage($folder, $plugin, $image, $dir = 'img')
	{
		$image = ltrim($image, DS);

		if (!self::isImage($image))
		{
			return $image;
		}

		$template = 'system';
		if ($t = self::app('template'))
		{
			$template = self::app('template')->template;
		}

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
					$b = rtrim(Request::base(true), DS);
				}
				else
				{
					$b = str_replace('/administrator', '', rtrim(Request::base(true), DS));
				}
				// Push script to the document
				return $b . $path;
			}
		}
	}

	/**
	 * Adds a linked stylesheet from a plugin to the page
	 *
	 * @param   string  $folder      Plugin folder name
	 * @param   string  $plugin      Plugin name
	 * @param   string  $stylesheet  Stylesheet name (optional, uses module name if left blank)
	 * @param   string  $dir         Asset directory to look in
	 * @return  void
	 */
	public static function addPluginStyleSheet($folder, $plugin, $stylesheet = '', $dir = 'css')
	{
		if ($dir != 'css')
		{
			$stylesheet = $dir . '/' . $stylesheet;
		}

		$asset = new Stylesheet('plg_' . $folder . '_' . $plugin, $stylesheet);

		if ($asset->exists())
		{
			if ($document = self::app('document'))
			{
				$document->addStyleSheet($asset->link());
			}
		}
	}

	/**
	 * Adds a linked script to the page
	 *
	 * @param   string  $folder  Plugin folder name
	 * @param   string  $plugin  Plugin name
	 * @param   string  $script  Script name (optional, uses module name if left blank)
	 * @param   string  $dir     Asset directory to look in
	 * @return  void
	 */
	public static function addPluginScript($folder, $plugin, $script = '', $dir = 'js')
	{
		if ($dir != 'js')
		{
			$script = $dir . '/' . $script;
		}

		$asset = new Javascript('plg_' . $folder . '_' . $plugin, $script);

		if ($asset->exists())
		{
			if ($document = self::app('document'))
			{
				$document->addScript($asset->link());
			}
		}
	}

	/**
	 * Gets the path to a system image
	 *
	 * @param   string  $image  Image to look for
	 * @param   string  $dir    Asset directory to look in
	 * @return  string  Path to an image file
	 */
	public static function getSystemImage($image, $dir = 'images')
	{
		$image = ltrim($image, DS);

		if (!self::isImage($image))
		{
			return $image;
		}

		$template = DS . 'core' . DS . 'templates' . DS . 'system';
		if ($t = self::app('template'))
		{
			$template = substr($t->path, strlen(PATH_ROOT));
		}

		$paths = array();
		$paths[] = $template . DS . 'html' . DS . 'system' . ($dir ? DS . $dir : '') . DS . $image;
		$paths[] = DS . 'core' . DS . 'assets' . DS . $dir . DS . $image;

		// Run through each path until we find one that works
		foreach ($paths as $path)
		{
			if (file_exists(PATH_ROOT . $path))
			{
				// Push script to the document
				return str_replace('/administrator', '', rtrim(Request::base(true), '/')) . $path;
			}
		}
	}

	/**
	 * Returns the path to a system stylesheet
	 * Accepts either an array or string of comma-separated file names
	 * If more than one stylesheet is called for, it will combine, compress, return path to cached file
	 *
	 * @param   mixed  $elements  An array or string of comma-separated file names
	 * @return  string
	 */
	public static function getSystemStylesheet($elements = null)
	{
		// Path to system cache
		$client   = (isset(\App::get('client')->alias) ? \App::get('client')->alias : \App::get('client')->name);

		$cachedir = PATH_APP . DS . 'cache' . DS . $client;
		if (!self::app('filesystem')->exists(PATH_APP . DS . 'cache' . DS . $client))
		{
			if (!self::app('filesystem')->makeDirectory(PATH_APP . DS . 'cache' . DS . $client))
			{
				return '';
			}
		}

		// Path to system CSS
		$thispath = PATH_CORE . DS . 'assets' . DS . 'css';

		$env = self::app('config')->get('application_env', 'production');

		try
		{
			// Primary build file
			$primary   = 'site';

			// Cache vars
			$output    = $cachedir . DS . $primary . '.css';

			// If debugging is turned off and a cache file exist
			if ($env == 'production' && file_exists($output))
			{
				$output = rtrim(Request::root(true), '/') . '/app/cache/' . $client . '/' . $primary . '.css?v=' . filemtime($output);
			}
			else
			{
				$lesspath = PATH_CORE . DS . 'assets' . DS . 'less';

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
				$template  = self::app('template')->path . DS . 'less'; // . 'bootstrap.less';
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
				if (!$cache || (is_array($cache) && isset($cache['root']) && $cache['root'] != $input))
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
					$newCache['compiled'] = str_replace(array("'/media/system/", "'/core/assets/"), "'" . rtrim(Request::root(true), '/') . '/core/assets/', $newCache['compiled']);
					file_put_contents($output, $newCache['compiled']);    // Update the compiled LESS
				}
				$output = rtrim(Request::root(true), '/') . '/app/cache/' . $client . '/' . $primary . '.css?v=' . $newCache['updated'];
			}
		}
		catch (Exception $e)
		{
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
				$contents = str_replace(array("url('/media/system/", "url('/core/assets/"), "url('" . rtrim(Request::root(true), '/') . "/core/assets/", $contents);

				if ($fp = fopen($cachedir . DS . $cachefile, 'wb'))
				{
					fwrite($fp, $contents);
					fclose($fp);
				}
			}

			$output = rtrim(Request::base(true), '/') . '/app/cache/' . $client . '/' . $cachefile;
		}

		return $output;
	}
}
