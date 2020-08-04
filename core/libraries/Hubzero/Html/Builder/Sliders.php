<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Builder;

/**
 * Utility class for Sliders elements
 */
class Sliders
{
	/**
	 * Flag for if a pane is currently open or not
	 *
	 * @var  boolean
	 */
	public static $open = false;

	/**
	 * Creates a panes and loads the javascript behavior for it.
	 *
	 * @param   string  $group   The pane identifier.
	 * @param   array   $params  An array of options.
	 * @return  string
	 */
	public static function start($group = 'sliders', $params = array())
	{
		self::behavior($group, $params);
		self::$open = false;

		return '<div id="' . $group . '" class="pane-sliders">';
	}

	/**
	 * Close the current pane.
	 *
	 * @return  string  hTML to close the pane
	 */
	public static function end()
	{
		$content = '';
		if (self::$open)
		{
			$content .= '</div></div>';
		}
		self::$open = false;
		$content .= '</div>';
		return $content;
	}

	/**
	 * Begins the display of a new panel.
	 *
	 * @param   string  $text  Text to display.
	 * @param   string  $id    Identifier of the panel.
	 * @return  string  HTML to start a panel
	 */
	public static function panel($text, $id)
	{
		$content = '';
		if (self::$open)
		{
			$content .= '</div></div>';
		}
		else
		{
			self::$open = true;
		}
		$content .= '<h3 class="pane-toggler title" id="' . $id . '"><a href="#' . $id . '"><span>' . $text . '</span></a></h3><div class="panel"><div class="pane-slider content">';

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

		if (!array_key_exists($group, $loaded))
		{
			$loaded[$group] = true;

			$display = (isset($params['startOffset']) && isset($params['startTransition']) && $params['startTransition'])
				? (int) $params['startOffset'] : null;
			$show = (isset($params['startOffset']) && !(isset($params['startTransition']) && $params['startTransition']))
				? (int) $params['startOffset'] : null;

			$opt = array();
			$opt['heightStyle'] = "'content'";
			/*$opt['onActive'] = "function(toggler, i) {toggler.addClass('pane-toggler-down');" .
				"toggler.removeClass('pane-toggler');i.addClass('pane-down');i.removeClass('pane-hide');Cookie.write('jpanesliders_" . $group . "',$('div#" . $group . ".pane-sliders > .panel > h3').indexOf(toggler));}";
			$opt['onBackground'] = "function(toggler, i) {toggler.addClass('pane-toggler');" .
				"toggler.removeClass('pane-toggler-down');i.addClass('pane-hide');i.removeClass('pane-down');if($('div#"
				. $group . ".pane-sliders > .panel > h3').length==$('div#" . $group . ".pane-sliders > .panel > h3.pane-toggler').length) Cookie.write('jpanesliders_" . $group . "',-1);}";
			$opt['duration']   = (isset($params['duration'])) ? (int) $params['duration'] : 300;
			$opt['display']    = (isset($params['useCookie']) && $params['useCookie']) ? Request::getInt('jpanesliders_' . $group, $display, 'cookie')
				: $display;
			$opt['show']       = (isset($params['useCookie']) && $params['useCookie']) ? Request::getInt('jpanesliders_' . $group, $show, 'cookie') : $show;
			$opt['opacity']    = (isset($params['opacityTransition']) && ($params['opacityTransition'])) ? 'true' : 'false';
			$opt['alwaysHide'] = (isset($params['allowAllClose']) && (!$params['allowAllClose'])) ? 'false' : 'true';*/

			$options = array();
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
				"jQuery(document).ready(function($){
					$('div#" . $group . "').accordion(" . $options . ");
				});"
			);
		}
	}
}
