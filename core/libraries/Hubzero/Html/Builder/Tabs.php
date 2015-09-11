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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
