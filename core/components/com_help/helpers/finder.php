<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$client = \App::isAdmin() ? 'admin' : 'site';
		$tmpl   = \App::get('template')->path;
		$lang   = \Lang::getLanguage();

		$paths = array(
			// Template override help page
			PATH_APP . DS . 'templates' . DS . $tmpl . DS .  'html' . DS . 'plg_' . $name . '_' . $page . DS . 'help' . DS . $lang . DS . 'index.' . self::$ext,
			PATH_APP . DS . 'templates' . DS . $tmpl . DS .  'html' . DS . $component  . DS . 'help' . DS . $lang . DS . $page . '.' . self::$ext,

			PATH_CORE . DS . 'templates' . DS . $tmpl . DS .  'html' . DS . 'plg_' . $name . '_' . $page . DS . 'help' . DS . $lang . DS . 'index.' . self::$ext,
			PATH_CORE . DS . 'templates' . DS . $tmpl . DS .  'html' . DS . $component  . DS . 'help' . DS . $lang . DS . $page . '.' . self::$ext,

			$tmpl . DS .  'html' . DS . 'plg_' . $name . '_' . $page . DS . 'help' . DS . $lang . DS . 'index.' . self::$ext,
			$tmpl . DS .  'html' . DS . $component  . DS . 'help' . DS . $lang . DS . $page . '.' . self::$ext
		);

		// Path to help page
		$paths[] = self::path($component) . DS . 'help' . DS . $lang . DS . $page . '.' . self::$ext;
		$paths[] = PATH_CORE . DS . 'plugins' . DS . $name . DS . $page . DS . 'help' . DS . $lang . DS . 'index.' . self::$ext;

		// If we have an extension
		if ($extension)
		{
			$paths[2] = PATH_CORE . DS . 'plugins' . DS . $name . DS . $extension . DS . 'help' . DS . $lang . DS . $page . '.' . self::$ext;
			$paths[0] = $tmpl . DS .  'html' . DS . 'plg_' . $name . '_' . $extension . DS . 'help' . DS . $lang . DS . $page . '.' . self::$ext;
		}

		$final = '';

		// Determine path for help page
		foreach ($paths as $path)
		{
			if (file_exists($path))
			{
				$final = $path;
				break;
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

		return \App::get('component')->path($component) . DS . $client;

		/*if (file_exists(PATH_CORE . DS . 'components' . DS . $component . DS . $client))
		{
			return PATH_CORE . DS . 'components' . DS . $component . DS . $client;
		}
		else
		{
			return PATH_APP . DS . 'components' . DS . $component;
		}*/
	}

	/**
	 * Get array of help pages for component
	 *
	 * @param   string  $component  Component to get pages for
	 * @return  array
	 */
	public static function pages($component)
	{
		$database = \App::get('db');

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
			$pages = \Filesystem::files($helpPagesPath, '.' . self::$ext);
		}

		$pages = array_map(function($file)
		{
			return ltrim($file, DS);
		}, $pages);

		// Return pages
		return array(
			'name'   => $name,
			'option' => $component,
			'pages'  => $pages
		);
	}
}
