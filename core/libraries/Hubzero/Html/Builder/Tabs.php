<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Builder;

/**
 * Utility class for Tabs elements.
 */
class Tabs
{
	/**
	 * Flag for if a pane is currently open
	 *
	 * @var  boolean
	 */
	public static $open = false;

	/**
	 * Creates a panes and creates the JavaScript object for it.
	 *
	 * @param   string  $group   The pane identifier.
	 * @param   array   $params  An array of option.
	 * @return  string
	 */
	public static function start($group = 'tabs', $params = array())
	{
		self::behavior($group, $params);
		self::$open = false;

		return '<dl class="tabs" id="' . $group . '">';
	}

	/**
	 * Close the current pane
	 *
	 * @return  string  HTML to close the pane
	 */
	public static function end()
	{
		self::$open = false;

		return '</dd></dl>';
	}

	/**
	 * Begins the display of a new panel.
	 *
	 * @param   string  $text  Text to display.
	 * @param   string  $id    Identifier of the panel.
	 * @return  string  HTML to start a new panel
	 */
	public static function panel($text, $id)
	{
		$content = '';

		if (self::$open)
		{
			$content .= '</dd>';
		}
		else
		{
			self::$open = true;
		}
		$content .= '<dt id="tab' . $id . '"><a href="#tab' . $id . '">' . $text . '</a></dt><dd>';

		return $content;
	}

	/**
	 * Load the JavaScript behavior.
	 *
	 * @param   string  $group   The pane identifier.
	 * @param   array   $params  Array of options.
	 * @return  void
	 */
	protected static function behavior($group, $params = array())
	{
		static $loaded = array();

		if (!array_key_exists((string) $group, $loaded))
		{
			$options = array();

			$opt['onActive']            = (isset($params['onActive'])) ? $params['onActive'] : null;
			$opt['onBackground']        = (isset($params['onBackground'])) ? $params['onBackground'] : null;
			$opt['display']             = (isset($params['startOffset'])) ? (int) $params['startOffset'] : null;
			$opt['useStorage']          = (isset($params['useCookie']) && $params['useCookie']) ? 'true' : 'false';
			$opt['titleSelector']       = "'dt.tabs'";
			$opt['descriptionSelector'] = "'dd.tabs'";

			foreach ($opt as $k => $v)
			{
				if ($v)
				{
					$options[] = $k . ': ' . $v;
				}
			}

			$options = '{' . implode(',', $options) . '}';

			Behavior::framework(true);

			\App::get('document')->addScriptDeclaration(
				'jQuery(document).ready(function($){
					$("dl#' . $group . '.tabs").tabs();
				});'
			);

			Asset::script('system/jquery.tabs.js', false, true);

			$loaded[(string) $group] = true;
		}
	}
}
