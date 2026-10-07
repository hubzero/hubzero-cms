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
	 * Get the path to a help page
	 *
	 * Candidates are checked in order: template overrides first, then the
	 * plugin and component pages. When an extension is given the plugin was
	 * asked for by name, so its page outranks the component's page of the
	 * same name; otherwise the component's page comes first.
	 *
	 * @param   string  $component  Component option, e.g. com_groups
	 * @param   string  $extension  Plugin of the component to look in, or ''
	 * @param   string  $page       Page name
	 * @return  string  Path to the page, or '' if none exists
	 */
	public static function page($component, $extension, $page)
	{
		$name = str_replace('com_', '', $component);
		$tmpl = \App::get('template')->path;
		$lang = \Lang::getTag();

		// A plugin's help page is <extension>/help/<lang>/<page>.phtml when the
		// plugin is named by $extension, else <page>/help/<lang>/index.phtml
		$plugin = $extension ?: $page;
		$file   = ($extension ? $page : 'index') . '.' . self::$ext;

		$componentPage = self::path($component) . DS . 'help' . DS . $lang . DS . $page . '.' . self::$ext;

		// \Plugin::path() is '' when no such plugin exists in app/ or core/
		$pluginPath = \Plugin::path($name, $plugin);
		$pluginPage = $pluginPath ? $pluginPath . DS . 'help' . DS . $lang . DS . $file : '';

		$paths = array(
			// Template overrides
			$tmpl . DS . 'html' . DS . 'plg_' . $name . '_' . $plugin . DS . 'help' . DS . $lang . DS . $file,
			$tmpl . DS . 'html' . DS . $component . DS . 'help' . DS . $lang . DS . $page . '.' . self::$ext
		);
		$paths = array_merge($paths, $extension
			? array($pluginPage, $componentPage)
			: array($componentPage, $pluginPage)
		);

		foreach ($paths as $path)
		{
			if ($path && file_exists($path))
			{
				return $path;
			}
		}

		return '';
	}

	/**
	 * Get the component's client directory (site or admin)
	 *
	 * @param   string  $component  Component option
	 * @return  string
	 */
	private static function path($component)
	{
		$client = \App::isAdmin() ? 'admin' : 'site';

		return \App::get('component')->path($component) . DS . $client;
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
