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

use Lang;
use App;

/**
 * Utility class for javascript behaviors
 */
class Behavior
{
	/**
	 * Array containing information for loaded files
	 *
	 * @var  array
	 */
	protected static $loaded = array();

	/**
	 * Method to load the MooTools framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of MooTools is included for easier debugging.
	 *
	 * @param   string   $extras  MooTools file to load
	 * @param   boolean  $debug   Is debugging mode on? [optional]
	 * @return  void
	 */
	public static function framework($extras = false, $debug = null)
	{
		$type = $extras ? 'ui' : 'core';

		if (!empty(self::$loaded[__METHOD__][$type]))
		{
			return;
		}

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$debug = App::get('config')->get('debug');
		}

		if ($type != 'core' && empty(self::$loaded[__METHOD__]['core']))
		{
			self::framework(false, $debug);
		}

		// We need to make sure the framework is first, regardless of where/when
		// Behavior::framework() is called. For instance, if called in the template
		// then the framework needs to be pushed before any custom scripts the component
		// or plugins may have already pushed.
		if ($type == 'core')
		{
			self::_pushScriptTo(0, rtrim(App::get('request')->root(true), '/') . '/core/assets/js/jquery.js');

			if (App::isAdmin())
			{
				Asset::script('assets/core.js', false, true);
			}
		}
		else
		{
			self::_pushScriptTo(1, rtrim(App::get('request')->root(true), '/') . '/core/assets/js/jquery.ui.js');
		}
		self::$loaded[__METHOD__][$type] = true;

		return;
	}

	/**
	 * Push a script to a specific sport int he scripts list
	 *
	 * @param   integer $index
	 * @param   string  $url
	 * @param   string  $type
	 * @param   boolean $defer
	 * @param   boolean $async
	 * @return  void
	 */
	private static function _pushScriptTo($index, $url, $type = 'text/javascript', $defer = false, $async = false)
	{
		if (!App::has('document'))
		{
			return;
		}

		$document = App::get('document')->instance();

		if ($document instanceof \Hubzero\Document\Type\Html)
		{
			$pushed = false;

			// Get the old data
			$data = $document->getHeadData();
			$scripts = $data['scripts'];

			// Reset the scripts data
			// We need a fresh array to reorganize things
			$data['scripts'] = array();

			// We can't reset the script data with $document->setHeadData($data); and then
			// use $document->addScript() because JDocument will ignore the empty array we
			// just set $data['scripts'] to and keep the old data. So, all we'd end up
			// doing is appending items. SO, we populate a new array and set the head data
			// to that.

			// Loop through old data and look for the
			// spot to insert the new data
			$i = 0;
			foreach ($scripts as $key => $foo)
			{
				// Found the spot?
				if ($i == $index)
				{
					$data['scripts'][$url] = array(
						'mime'  => $type,
						'defer' => $defer,
						'async' => $async
					);
					$pushed = true;
				}
				$data['scripts'][$key] = $foo;

				$i++;
			}

			// We didn't find out spot?
			// Append to the end
			if (!$pushed)
			{
				$data['scripts'][$url] = array(
					'mime'  => $type,
					'defer' => $defer,
					'async' => $async
				);
			}

			$document->setHeadData($data);
		}
	}

	/**
	 * Add HUBzero core js resources
	 *
	 * @return  void
	 */
	public static function core()
	{
		if (isset(self::$loaded[__METHOD__]))
		{
			return;
		}

		Asset::script('assets/hubzero.js', true, true);

		self::$loaded[__METHOD__] = true;
	}

	/**
	 * Add unobtrusive javascript support for image captions.
	 *
	 * @param   string  $selector  The selector for which a caption behaviour is to be applied.
	 * @return  void
	 */
	public static function caption($selector = 'img.caption')
	{
		if (isset(self::$loaded[__METHOD__][$selector]))
		{
			return;
		}

		self::framework(true);

		App::get('document')->addScriptDeclaration(
			"jQuery(document).ready(function($){
				$('" . $selector . "').tooltip({
					position: {
						my: 'center bottom',
						at: 'center top'
					},
					create: function(event, ui) {
						var tip = $(this),
							tipText = tip.attr('title');

						if (tipText.indexOf('::') != -1) {
							var parts = tipText.split('::');
							tip.attr('title', parts[1]);
						}
					},
					tooltipClass: 'tooltip'
				});
			});"
		);

		// Set static array
		self::$loaded[__METHOD__][$selector] = true;
	}

	/**
	 * Add unobtrusive javascript support for form validation.
	 *
	 * To enable form validation the form tag must have class="form-validate".
	 * Each field that needs to be validated needs to have class="validate".
	 * Additional handlers can be added to the handler for username, password,
	 * numeric and email. To use these add class="validate-email" and so on.
	 *
	 * @return  void
	 */
	public static function formvalidation()
	{
		if (isset(self::$loaded[__METHOD__]))
		{
			return;
		}

		self::framework();

		Asset::script('assets/validate.js', true, true);

		self::$loaded[__METHOD__] = true;
	}

	/**
	 * Add unobtrusive javascript support for charts
	 *
	 * @return  void
	 */
	public static function chart($type='core')
	{
		if (isset(self::$loaded[__METHOD__][$type]))
		{
			return;
		}

		if ($type != 'core')
		{
			self::chart();
		}

		if ($type == 'core')
		{
			Asset::script('assets/flot/jquery.flot.min.js', true, true);
			Asset::script('assets/flot/jquery.flot.canvas.min.js', true, true);
			Asset::script('assets/flot/jquery.flot.time.min.js', true, true);
		}
		else
		{
			Asset::script('assets/flot/jquery.flot.' . $type . '.min.js', true, true);
		}

		self::$loaded[__METHOD__][$type] = true;

		return;
	}

	/**
	 * Add unobtrusive javascript support for submenu switcher support in
	 * Global Configuration and System Information.
	 *
	 * @return  void
	 */
	public static function switcher($toggler='tabs')
	{
		if (isset(self::$loaded[__METHOD__]))
		{
			return;
		}

		self::framework(true);

		Asset::script('assets/switcher.js', false, true);

		App::get('document')->addScriptDeclaration(
			"jQuery(document).ready(function($){
				$('#" . $toggler . "').switcher();
			});"
		);

		self::$loaded[__METHOD__] = true;
	}

	/**
	 * Add unobtrusive javascript support for a combobox effect.
	 *
	 * Note that this control is only reliable in absolutely positioned elements.
	 * Avoid using a combobox in a slider or dynamic pane.
	 *
	 * @return  void
	 */
	public static function combobox()
	{
		if (isset(self::$loaded[__METHOD__]))
		{
			return;
		}

		self::framework();

		Asset::script('assets/combobox.js', true, true);

		self::$loaded[__METHOD__] = true;
	}

	/**
	 * Add unobtrusive javascript support for a hover tooltips.
	 *
	 * Add a title attribute to any element in the form
	 * title="title::text"
	 *
	 *
	 * Uses the core Tips class in MooTools.
	 *
	 * @param   string  $selector  The class selector for the tooltip.
	 * @param   array   $params    An array of options for the tooltip.
	 *                             Options for the tooltip can be:
	 *                             - maxTitleChars  integer   The maximum number of characters in the tooltip title (defaults to 50).
	 *                             - offsets        object    The distance of your tooltip from the mouse (defaults to {'x': 16, 'y': 16}).
	 *                             - showDelay      integer   The millisecond delay the show event is fired (defaults to 100).
	 *                             - hideDelay      integer   The millisecond delay the hide hide is fired (defaults to 100).
	 *                             - className      string    The className your tooltip container will get.
	 *                             - fixed          boolean   If set to true, the toolTip will not follow the mouse.
	 *                             - onShow         function  The default function for the show event, passes the tip element
	 *                               and the currently hovered element.
	 *                             - onHide         function  The default function for the hide event, passes the currently
	 *                               hovered element.
	 * @return  void
	 */
	public static function tooltip($selector = '.hasTip', $params = array())
	{
		$sig = md5(serialize(array($selector, $params)));

		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		self::framework(true);

		// Setup options object
		/*$opt['maxTitleChars']	= (isset($params['maxTitleChars']) && ($params['maxTitleChars'])) ? (int) $params['maxTitleChars'] : 50;
		// offsets needs an array in the format: array('x'=>20, 'y'=>30)
		$opt['offset']			= (isset($params['offset']) && (is_array($params['offset']))) ? $params['offset'] : null;
		if (!isset($opt['offset']))
		{
			// Supporting offsets parameter which was working in mootools 1.2 (Joomla!1.5)
			$opt['offset']		= (isset($params['offsets']) && (is_array($params['offsets']))) ? $params['offsets'] : null;
		}
		$opt['showDelay']		= (isset($params['showDelay'])) ? (int) $params['showDelay'] : null;
		$opt['hideDelay']		= (isset($params['hideDelay'])) ? (int) $params['hideDelay'] : null;
		$opt['className']		= (isset($params['className'])) ? $params['className'] : null;
		$opt['fixed']			= (isset($params['fixed']) && ($params['fixed'])) ? true : false;
		$opt['onShow']			= (isset($params['onShow'])) ? '\\' . $params['onShow'] : null;
		$opt['onHide']			= (isset($params['onHide'])) ? '\\' . $params['onHide'] : null;

		$options = Behavior::getJSObject($opt);*/

		\App::get('document')->addScriptDeclaration(
			"jQuery(document).ready(function($){
				$('" . $selector . "').tooltip({
					track: true,
					show: false,
					content: function() {
						return $(this).attr('title');
					},
					create: function(event, ui) {
						var tip = $(this),
							tipText = tip.attr('title');

						if (tipText.indexOf('::') != -1) {
							var parts = tipText.split('::');
							tip.attr('title', '<div class=\"tip-title\">' + parts[0] + '</div><div class=\"tip-text\">' + parts[1] + '</div>');
						} else {
							tip.attr('title', '<div class=\"tip-text\">' + tipText + '</div>');
						}
					},
					tooltipClass: 'tool-tip'
				});
			});"
		);

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Add unobtrusive javascript support for modal links.
	 *
	 * @param   string  $selector  The selector for which a modal behaviour is to be applied.
	 * @param   array   $params    An array of parameters for the modal behaviour.
	 *                             Options for the modal behaviour can be:
	 *                            - ajaxOptions
	 *                            - size
	 *                            - shadow
	 *                            - overlay
	 *                            - onOpen
	 *                            - onClose
	 *                            - onUpdate
	 *                            - onResize
	 *                            - onShow
	 *                            - onHide
	 * @return  void
	 */
	public static function modal($selector = 'a.modal', $params = array())
	{
		if (!isset(self::$loaded[__METHOD__]))
		{
			self::framework();

			Asset::script('assets/jquery.fancybox.js', true, true);
		}

		$sig = md5(serialize(array($selector, $params)));
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		// Setup options object
		/*
		$opt['ajaxOptions']		= (isset($params['ajaxOptions']) && (is_array($params['ajaxOptions']))) ? $params['ajaxOptions'] : null;
		$opt['handler']			= (isset($params['handler'])) ? $params['handler'] : null;
		$opt['fullScreen']		= (isset($params['fullScreen'])) ? (bool) $params['fullScreen'] : null;
		$opt['parseSecure']		= (isset($params['parseSecure'])) ? (bool) $params['parseSecure'] : null;
		$opt['closable']		= (isset($params['closable'])) ? (bool) $params['closable'] : null;
		$opt['closeBtn']		= (isset($params['closeBtn'])) ? (bool) $params['closeBtn'] : null;
		$opt['iframePreload']	= (isset($params['iframePreload'])) ? (bool) $params['iframePreload'] : null;
		$opt['iframeOptions']	= (isset($params['iframeOptions']) && (is_array($params['iframeOptions']))) ? $params['iframeOptions'] : null;
		$opt['size']			= (isset($params['size']) && (is_array($params['size']))) ? $params['size'] : null;
		$opt['shadow']			= (isset($params['shadow'])) ? $params['shadow'] : null;
		$opt['overlay']			= (isset($params['overlay'])) ? $params['overlay'] : null;
		$opt['onOpen']			= (isset($params['onOpen'])) ? $params['onOpen'] : null;
		$opt['onClose']			= (isset($params['onClose'])) ? $params['onClose'] : null;
		$opt['onUpdate']		= (isset($params['onUpdate'])) ? $params['onUpdate'] : null;
		$opt['onResize']		= (isset($params['onResize'])) ? $params['onResize'] : null;
		$opt['onMove']			= (isset($params['onMove'])) ? $params['onMove'] : null;
		$opt['onShow']			= (isset($params['onShow'])) ? $params['onShow'] : null;
		$opt['onHide']			= (isset($params['onHide'])) ? $params['onHide'] : null;
		);*/

		if (!empty($params) || \App::isAdmin())
		{
			$opt = array('arrows' => false);
			$opt['ajax']       = (isset($params['ajaxOptions']) && (is_array($params['ajaxOptions']))) ? $params['ajaxOptions'] : null;
			$opt['type']       = (isset($params['handler'])) ? $params['handler'] : 'iframe';
			$opt['modal']      = (isset($params['closable'])) ? (bool) $params['closable'] : null;
			$opt['closeBtn']   = (isset($params['closeBtn'])) ? (bool) $params['closeBtn'] : null;
			$opt['iframe']     = (isset($params['iframeOptions']) && (is_array($params['iframeOptions']))) ? $params['iframeOptions'] : null;
			if (isset($params['size'])
			 && is_array($params['size']))
			{
				if (isset($params['size']['width']))
				{
					$opt['width']  = $params['size']['width'];
				}
				if (isset($params['size']['height']))
				{
					$opt['height'] = $params['size']['height'];
				}
				if (isset($params['size']['minWidth']))
				{
					$opt['minWidth']  = $params['size']['minWidth'];
				}
				if (isset($params['size']['maxWidth']))
				{
					$opt['maxWidth']  = $params['size']['maxWidth'];
				}
				if (isset($params['size']['minHeight']))
				{
					$opt['minHeight'] = $params['size']['minHeight'];
				}
				if (isset($params['size']['maxHeight']))
				{
					$opt['maxHeight'] = $params['size']['maxHeight'];
				}
			}
			$opt['beforeLoad'] = (isset($params['onOpen'])) ? $params['onOpen'] : '\\function(){ var atts = $(this.element).attr("data-rel"); if (atts) { atts = jQuery.parseJSON(atts); console.log(atts); }}';
			$opt['onCancel']   = (isset($params['onClose'])) ? $params['onClose'] : null;
			$opt['onUpdate']   = (isset($params['onUpdate'])) ? $params['onUpdate'] : null;
			$opt['onMove']     = (isset($params['onMove'])) ? $params['onMove'] : null;
			$opt['afterShow']  = (isset($params['onShow'])) ? $params['onShow'] : null;
			$opt['afterClose'] = (isset($params['onHide'])) ? $params['onHide'] : null;
			$opt['tpl']        = (isset($params['tpl'])) ? $params['tpl'] : null;
			$opt['autoSize']   = (isset($params['autoSize'])) ? $params['autoSize'] : false;
			$opt['fitToView']  = (isset($params['fitToView'])) ? $params['fitToView'] : true;

			$options = self::getJSObject($opt);

			App::get('document')->addScriptDeclaration(
				'jQuery(document).ready(function($){
					$("' . $selector . '").fancybox(' . $options . ');
				});'
			);
		}

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * JavaScript behavior to allow shift select in grids
	 *
	 * @param   string  $id  The id of the form for which a multiselect behaviour is to be applied.
	 * @return  void
	 */
	public static function multiselect($id = 'adminForm')
	{
		if (isset(self::$loaded[__METHOD__][$id]))
		{
			return;
		}

		self::framework();

		Asset::script('assets/multiselect.js', true, true);

		// Attach multiselect to document
		App::get('document')->addScriptDeclaration(
			"jQuery(document).ready(function($){
				new Joomla.JMultiSelect('" . $id . "');
			});"
		);

		// Set static array
		self::$loaded[__METHOD__][$id] = true;
		return;
	}

	/**
	 * This method does nothing.
	 *
	 * @return  void
	 */
	public static function uploader()
	{
		return;
	}

	/**
	 * Add unobtrusive javascript support for a collapsible tree.
	 *
	 * @param   string  $id      An index
	 * @param   array   $params  An array of options.
	 * @param   array   $root    The root node
	 * @return  void
	 */
	public static function tree($id, $params = array(), $root = array())
	{
		self::framework();

		/*Asset::script('assets/mootree.js', true, true, false, false);
		Asset::stylesheet('assets/mootree.css', array(), true);

		if (isset(self::$loaded[__METHOD__][$id]))
		{
			return;
		}

		// Setup options object
		$opt['div']		= (array_key_exists('div', $params)) ? $params['div'] : $id . '_tree';
		$opt['mode']	= (array_key_exists('mode', $params)) ? $params['mode'] : 'folders';
		$opt['grid']	= (array_key_exists('grid', $params)) ? '\\' . $params['grid'] : true;
		$opt['theme']	= (array_key_exists('theme', $params)) ? $params['theme'] : Asset::image('assets/mootree.gif', '', array(), true, true);

		// Event handlers
		$opt['onExpand']	= (array_key_exists('onExpand', $params)) ? '\\' . $params['onExpand'] : null;
		$opt['onSelect']	= (array_key_exists('onSelect', $params)) ? '\\' . $params['onSelect'] : null;
		$opt['onClick']		= (array_key_exists('onClick', $params)) ? '\\' . $params['onClick']
		: '\\function(node){  window.open(node.data.url, node.data.target != null ? node.data.target : \'_self\'); }';

		$options = self::getJSObject($opt);

		// Setup root node
		$rt['text']		= (array_key_exists('text', $root)) ? $root['text'] : 'Root';
		$rt['id']		= (array_key_exists('id', $root)) ? $root['id'] : null;
		$rt['color']	= (array_key_exists('color', $root)) ? $root['color'] : null;
		$rt['open']		= (array_key_exists('open', $root)) ? '\\' . $root['open'] : true;
		$rt['icon']		= (array_key_exists('icon', $root)) ? $root['icon'] : null;
		$rt['openicon']	= (array_key_exists('openicon', $root)) ? $root['openicon'] : null;
		$rt['data']		= (array_key_exists('data', $root)) ? $root['data'] : null;
		$rootNode = self::getJSObject($rt);

		$treeName = (array_key_exists('treeName', $params)) ? $params['treeName'] : '';

		$js = '		window.addEvent(\'domready\', function(){
			tree' . $treeName . ' = new MooTreeControl(' . $options . ',' . $rootNode . ');
			tree' . $treeName . '.adopt(\'' . $id . '\');})';

		// Attach tooltips to document
		\App::get('document')->addScriptDeclaration($js);

		// Set static array
		self::$loaded[__METHOD__][$id] = true;*/

		return;
	}

	/**
	 * Add unobtrusive javascript support for a calendar control.
	 *
	 * @return  void
	 */
	public static function calendar()
	{
		if (isset(self::$loaded[__METHOD__]))
		{
			return;
		}

		/*$tag = App::get('language')->getTag();

		$translation = self::_calendartranslation();
		if ($translation)
		{
			App::get('document')->addScriptDeclaration($translation);
		}*/

		Asset::stylesheet('assets/jquery.datepicker.css', array('media' => 'all'), true);
		Asset::stylesheet('assets/jquery.timepicker.css', array('media' => 'all'), true);

		self::framework(true);

		Asset::script('assets/jquery.timepicker.js', false, true);

		self::$loaded[__METHOD__] = true;
	}

	/**
	 * Add unobtrusive javascript support for a color picker.
	 *
	 * @return  void
	 */
	public static function colorpicker()
	{
		if (isset(self::$loaded[__METHOD__]))
		{
			return;
		}

		self::framework(true);

		Asset::stylesheet('assets/jquery.colpick.css', array('media' => 'all'), true);
		Asset::script('assets/jquery.colpick.js', false, true);

		App::get('document')
			->addScriptDeclaration(
			"jQuery(document).ready(function($){
				$('.input-colorpicker').colpick({
					layout:'hex',
					colorScheme:'dark',
					onChange:function(hsb, hex, rgb, el, bySetColor) {
						//$(el).css('border-color','#' + hex);
						// Fill the text box just if the color was set using the picker, and not the colpickSetColor function.
						if (!bySetColor) $(el).val('#' + hex);
					}
				}).keyup(function(){
					$(this).colpickSetColor(this.value);
				});
			});
		");

		self::$loaded[__METHOD__] = true;
	}

	/**
	 * Keep session alive, for example, while editing or creating an article.
	 *
	 * @return  void
	 */
	public static function keepalive()
	{
		if (isset(self::$loaded[__METHOD__]))
		{
			return;
		}

		self::framework();

		$lifetime    = (App::get('config')->get('lifetime') * 60000);
		$refreshTime = ($lifetime <= 60000) ? 30000 : $lifetime - 60000;

		// Refresh time is 1 minute less than the liftime assined in the configuration.php file.
		// the longest refresh period is one hour to prevent integer overflow.
		if ($refreshTime > 3600000 || $refreshTime <= 0)
		{
			$refreshTime = 3600000;
		}

		App::get('document')->addScriptDeclaration('
			jQuery(document).ready(function($){
				(function keepAlive() {
					$.ajax({
						url: "index.php",
						complete: function() {
							setTimeout(keepAlive, ' . $refreshTime . ');
						}
					});
				})();
			});'
		);

		self::$loaded[__METHOD__] = true;

		return;
	}

	/**
	 * Highlight some words via Javascript.
	 *
	 * @param   array   $terms      Array of words that should be highlighted.
	 * @param   string  $start      ID of the element that marks the begin of the section in which words
	 *                              should be highlighted. Note this element will be removed from the DOM.
	 * @param   string  $end        ID of the element that end this section.
	 *                              Note this element will be removed from the DOM.
	 * @param   string  $className  Class name of the element highlights are wrapped in.
	 * @param   string  $tag        Tag that will be used to wrap the highlighted words.
	 * @return  void
	 */
	public static function highlighter(array $terms, $start = 'highlighter-start', $end = 'highlighter-end', $className = 'highlight', $tag = 'span')
	{
		$sig = md5(serialize(array($terms, $start, $end)));
		if (isset(self::$loaded[__METHOD__][$sig]))
		{
			return;
		}

		Asset::script('assets/jquery.highlighter.js', true, true);

		$terms = str_replace('"', '\"', $terms);

		$options = "{
			/*startElement: start,
			endElement: end,*/
			className: '" . $className . "',
			wordsOnly: false,
			element: '" . $tag . "'
		}";

		App::get('document')->addScriptDeclaration("
			jQuery(document).ready(function($){
				$('body').highlight([\"" . implode('","', $terms) . "\"], " . $options . ");
			});
		");

		self::$loaded[__METHOD__][$sig] = true;

		return;
	}

	/**
	 * Break us out of any containing iframes
	 *
	 * @param   string  $location  Location to display in
	 * @return  void
	 */
	public static function noframes($location = 'top.location.href')
	{
		// Only load once
		if (isset(self::$loaded[__METHOD__]))
		{
			return;
		}

		// Include MooTools framework
		self::framework();

		//$js = "window.addEvent('domready', function () {if (top == self) {document.documentElement.style.display = 'block'; }" .
		//	" else {top.location = self.location; }});";
		$js = "jQuery(document).ready(function($){
			if (top == self) {
				document.documentElement.style.display = 'block';
			} else {
				top.location = self.location;
			}
		});";
		$document = App::get('document');
		$document->addStyleDeclaration('html { display:none }');
		$document->addScriptDeclaration($js);

		\JResponse::setHeader('X-Frame-Options', 'SAMEORIGIN');

		self::$loaded[__METHOD__] = true;
	}

	/**
	 * Internal method to get a JavaScript object notation string from an array
	 *
	 * @param   array   $array  The array to convert to JavaScript object notation
	 * @return  string  JavaScript object notation representation of the array
	 */
	protected static function getJSObject($array = array())
	{
		// Initialise variables.
		$object = '{';

		// Iterate over array to build objects
		foreach ((array) $array as $k => $v)
		{
			if (is_null($v))
			{
				continue;
			}

			if (is_bool($v))
			{
				if ($k === 'fullScreen')
				{
					$object .= 'size: { ';
					$object .= 'x: ';
					$object .= 'window.getSize().x-80';
					$object .= ',';
					$object .= 'y: ';
					$object .= 'window.getSize().y-80';
					$object .= ' }';
					$object .= ',';
				}
				else
				{
					$object .= ' ' . $k . ': ';
					$object .= ($v) ? 'true' : 'false';
					$object .= ',';
				}
			}
			elseif (!is_array($v) && !is_object($v))
			{
				$object .= ' ' . $k . ': ';
				$object .= (is_numeric($v) || strpos($v, '\\') === 0) ? (is_numeric($v)) ? $v : substr($v, 1) : "'" . $v . "'";
				$object .= ',';
			}
			else
			{
				$object .= ' ' . $k . ': ' . self::getJSObject($v) . ',';
			}
		}

		if (substr($object, -1) == ',')
		{
			$object = substr($object, 0, -1);
		}

		$object .= '}';

		return $object;
	}

	/**
	 * Internal method to translate the JavaScript Calendar
	 *
	 * @return  string  JavaScript that translates the object
	 */
	protected static function _calendartranslation()
	{
		static $jsscript = 0;

		if ($jsscript == 0)
		{
			$return = 'Calendar._DN = new Array ("' . Lang::txt('SUNDAY', true) . '", "' . Lang::txt('MONDAY', true) . '", "'
				. Lang::txt('TUESDAY', true) . '", "' . Lang::txt('WEDNESDAY', true) . '", "' . Lang::txt('THURSDAY', true) . '", "'
				. Lang::txt('FRIDAY', true) . '", "' . Lang::txt('SATURDAY', true) . '", "' . Lang::txt('SUNDAY', true) . '");'
				. ' Calendar._SDN = new Array ("' . Lang::txt('SUN', true) . '", "' . Lang::txt('MON', true) . '", "' . Lang::txt('TUE', true) . '", "'
				. Lang::txt('WED', true) . '", "' . Lang::txt('THU', true) . '", "' . Lang::txt('FRI', true) . '", "' . Lang::txt('SAT', true) . '", "'
				. Lang::txt('SUN', true) . '");' . ' Calendar._FD = 0;' . ' Calendar._MN = new Array ("' . Lang::txt('JANUARY', true) . '", "'
				. Lang::txt('FEBRUARY', true) . '", "' . Lang::txt('MARCH', true) . '", "' . Lang::txt('APRIL', true) . '", "' . Lang::txt('MAY', true)
				. '", "' . Lang::txt('JUNE', true) . '", "' . Lang::txt('JULY', true) . '", "' . Lang::txt('AUGUST', true) . '", "'
				. Lang::txt('SEPTEMBER', true) . '", "' . Lang::txt('OCTOBER', true) . '", "' . Lang::txt('NOVEMBER', true) . '", "'
				. Lang::txt('DECEMBER', true) . '");' . ' Calendar._SMN = new Array ("' . Lang::txt('JANUARY_SHORT', true) . '", "'
				. Lang::txt('FEBRUARY_SHORT', true) . '", "' . Lang::txt('MARCH_SHORT', true) . '", "' . Lang::txt('APRIL_SHORT', true) . '", "'
				. Lang::txt('MAY_SHORT', true) . '", "' . Lang::txt('JUNE_SHORT', true) . '", "' . Lang::txt('JULY_SHORT', true) . '", "'
				. Lang::txt('AUGUST_SHORT', true) . '", "' . Lang::txt('SEPTEMBER_SHORT', true) . '", "' . Lang::txt('OCTOBER_SHORT', true) . '", "'
				. Lang::txt('NOVEMBER_SHORT', true) . '", "' . Lang::txt('DECEMBER_SHORT', true) . '");'
				. ' Calendar._TT = {};Calendar._TT["INFO"] = "' . Lang::txt('JLIB_HTML_BEHAVIOR_ABOUT_THE_CALENDAR', true) . '";'
				. ' Calendar._TT["ABOUT"] =
 "DHTML Date/Time Selector\n" +
 "(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" +
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"' . Lang::txt('JLIB_HTML_BEHAVIOR_DATE_SELECTION', false, false) . '" +
"' . Lang::txt('JLIB_HTML_BEHAVIOR_YEAR_SELECT', false, false) . '" +
"' . Lang::txt('JLIB_HTML_BEHAVIOR_MONTH_SELECT', false, false) . '" +
"' . Lang::txt('JLIB_HTML_BEHAVIOR_HOLD_MOUSE', false, false)
				. '";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Time selection:\n" +
"- Click on any of the time parts to increase it\n" +
"- or Shift-click to decrease it\n" +
"- or click and drag for faster selection.";

		Calendar._TT["PREV_YEAR"] = "' . Lang::txt('JLIB_HTML_BEHAVIOR_PREV_YEAR_HOLD_FOR_MENU', true) . '";' . ' Calendar._TT["PREV_MONTH"] = "'
				. Lang::txt('JLIB_HTML_BEHAVIOR_PREV_MONTH_HOLD_FOR_MENU', true) . '";' . ' Calendar._TT["GO_TODAY"] = "'
				. Lang::txt('JLIB_HTML_BEHAVIOR_GO_TODAY', true) . '";' . ' Calendar._TT["NEXT_MONTH"] = "'
				. Lang::txt('JLIB_HTML_BEHAVIOR_NEXT_MONTH_HOLD_FOR_MENU', true) . '";' . ' Calendar._TT["NEXT_YEAR"] = "'
				. Lang::txt('JLIB_HTML_BEHAVIOR_NEXT_YEAR_HOLD_FOR_MENU', true) . '";' . ' Calendar._TT["SEL_DATE"] = "'
				. Lang::txt('JLIB_HTML_BEHAVIOR_SELECT_DATE', true) . '";' . ' Calendar._TT["DRAG_TO_MOVE"] = "'
				. Lang::txt('JLIB_HTML_BEHAVIOR_DRAG_TO_MOVE', true) . '";' . ' Calendar._TT["PART_TODAY"] = "'
				. Lang::txt('JLIB_HTML_BEHAVIOR_TODAY', true) . '";' . ' Calendar._TT["DAY_FIRST"] = "'
				. Lang::txt('JLIB_HTML_BEHAVIOR_DISPLAY_S_FIRST', true) . '";' . ' Calendar._TT["WEEKEND"] = "0,6";' . ' Calendar._TT["CLOSE"] = "'
				. Lang::txt('JLIB_HTML_BEHAVIOR_CLOSE', true) . '";' . ' Calendar._TT["TODAY"] = "' . Lang::txt('JLIB_HTML_BEHAVIOR_TODAY', true)
				. '";' . ' Calendar._TT["TIME_PART"] = "' . Lang::txt('JLIB_HTML_BEHAVIOR_SHIFT_CLICK_OR_DRAG_TO_CHANGE_VALUE', true) . '";'
				. ' Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";' . ' Calendar._TT["TT_DATE_FORMAT"] = "'
				. Lang::txt('JLIB_HTML_BEHAVIOR_TT_DATE_FORMAT', true) . '";' . ' Calendar._TT["WK"] = "' . Lang::txt('JLIB_HTML_BEHAVIOR_WK', true) . '";'
				. ' Calendar._TT["TIME"] = "' . Lang::txt('JLIB_HTML_BEHAVIOR_TIME', true) . '";';
			$jsscript = 1;
			return $return;
		}
		else
		{
			return false;
		}
	}
}
