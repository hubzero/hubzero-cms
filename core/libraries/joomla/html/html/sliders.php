<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class for Sliders elements
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
abstract class JHtmlSliders
{
	public static $open = false;

	/**
	 * Creates a panes and loads the javascript behavior for it.
	 *
	 * @param   string  $group   The pane identifier.
	 * @param   array   $params  An array of options.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public static function start($group = 'sliders', $params = array())
	{
		self::_loadBehavior($group, $params);
		self::$open = false;

		return '<div id="' . $group . '" class="pane-sliders">';
	}

	/**
	 * Close the current pane.
	 *
	 * @return  string  hTML to close the pane
	 *
	 * @since   11.1
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
	 *
	 * @return  string  HTML to start a panel
	 *
	 * @since   11.1
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
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected static function _loadBehavior($group, $params = array())
	{
		static $loaded = array();
		if (!array_key_exists($group, $loaded))
		{
			$loaded[$group] = true;
			// Include mootools framework.
			JHtml::_('behavior.framework', true);

			$document = JFactory::getDocument();

			$display = (isset($params['startOffset']) && isset($params['startTransition']) && $params['startTransition'])
				? (int) $params['startOffset'] : null;
			$show = (isset($params['startOffset']) && !(isset($params['startTransition']) && $params['startTransition']))
				? (int) $params['startOffset'] : null;

			$options = '{';
			$opt = array();
			$opt['heightStyle'] = "'content'";
			/*$opt['onActive'] = "function(toggler, i) {toggler.addClass('pane-toggler-down');" .
				"toggler.removeClass('pane-toggler');i.addClass('pane-down');i.removeClass('pane-hide');Cookie.write('jpanesliders_" . $group . "',$('div#" . $group . ".pane-sliders > .panel > h3').indexOf(toggler));}";
			$opt['onBackground'] = "function(toggler, i) {toggler.addClass('pane-toggler');" .
				"toggler.removeClass('pane-toggler-down');i.addClass('pane-hide');i.removeClass('pane-down');if($('div#"
				. $group . ".pane-sliders > .panel > h3').length==$('div#" . $group . ".pane-sliders > .panel > h3.pane-toggler').length) Cookie.write('jpanesliders_" . $group . "',-1);}";
			$opt['duration'] = (isset($params['duration'])) ? (int) $params['duration'] : 300;
			$opt['display'] = (isset($params['useCookie']) && $params['useCookie']) ? JRequest::getInt('jpanesliders_' . $group, $display, 'cookie')
				: $display;
			$opt['show'] = (isset($params['useCookie']) && $params['useCookie']) ? JRequest::getInt('jpanesliders_' . $group, $show, 'cookie') : $show;
			$opt['opacity'] = (isset($params['opacityTransition']) && ($params['opacityTransition'])) ? 'true' : 'false';
			$opt['alwaysHide'] = (isset($params['allowAllClose']) && (!$params['allowAllClose'])) ? 'false' : 'true';*/
			foreach ($opt as $k => $v)
			{
				if ($v)
				{
					$options .= $k . ': ' . $v . ',';
				}
			}
			if (substr($options, -1) == ',')
			{
				$options = substr($options, 0, -1);
			}
			$options .= '}';

			$js = "jQuery(document).ready(function($){
				$('div#" . $group . "').accordion(" . $options . ");
			});";

			$document->addScriptDeclaration($js);
		}
	}
}
