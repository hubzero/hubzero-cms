<?php
/**
* @version		$Id: behavior.php 18130 2010-07-14 11:21:35Z louis $
* @package		Joomla.Framework
* @subpackage	HTML
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('JPATH_BASE') or die();

/**
 * JHTML helper class for loading JavaScript behaviors into the document head.  This version is
 * designed to load MooTools version 1.2 plus the 1.1 compatibility layer.
 *
 * @package     Joomla.Framework
 * @subpackage  HTML
 *
 * @since       1.5.19
 * @static
 */
class JHTMLBehavior
{
	/**
	 * Method to load the mootools framework and compatibility layer into the document head.  If the
	 * optional debug flag is set then a uncompressed version of the files will be loaded.
	 *
	 * @param   boolean  $debug  True to enable debugging mode.  If no value is set the value will
	 *                           be taken from the application configuration settings.
	 *
	 * @return  void
	 *
	 * @since   1.5.19
	 * @static
	 */
	function mootools($debug = null)
	{
		// Check to see if it has already been loaded.
		static $loaded;
		if (!empty($loaded)) {
			return;
		}

		// If no debugging value is set, use the setting from  the application configuration.
		if ($debug === null) {
			$debug = JFactory::getConfig()->getValue('config.debug');
		}

		/*
		 * Note: Konqueror browser check.
		 *  - If they fix thier issue with compressed javascript we can remove this.
		 */
		$kcheck = isset($_SERVER['HTTP_USER_AGENT']) ? strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'konqueror') : null;

		// If the debugging flag is set or the browser is Konqueror use the uncompressed file.
		if ($debug || $kcheck) {
			JHTML::script('mootools-uncompressed.js', 'plugins/system/mtupgrade/', false);
		}
		else {
			JHTML::script('mootools.js', 'plugins/system/mtupgrade/', false);
		}

		// Set the MooTools version string in the application object.
		JFactory::getApplication()->set('MooToolsVersion', '1.2.4 +Compat');

		// Ensure the files aren't loaded more than once.
		$loaded = true;
	}

	/**
	 * Method to load the system caption behavior.
	 *
	 * @return  void
	 *
	 * @since   1.5.19
	 * @static
	 */
	function caption()
	{
		JHTML::script('caption.js');
	}

	/**
	 * Method to load the system form validation behavior.
	 *
	 * @return  void
	 *
	 * @since   1.5.19
	 * @static
	 */
	function formvalidation()
	{
		JHTML::script('validate.js');
	}

	/**
	 * Method to load the system container switcher behavior.
	 *
	 * @return  void
	 *
	 * @since   1.5.19
	 * @static
	 */
	function switcher()
	{
		JHTML::script('switcher.js');
	}

	/**
	 * Method to load the system combobox behavior.
	 *
	 * @return  void
	 *
	 * @since   1.5.19
	 * @static
	 */
	function combobox()
	{
		JHTML::script('combobox.js');
	}

	/**
	 * Method to load the system tooltips behavior.  Because the tooltips class and interface has
	 * changed between Mootools 1.2 and 1.1, we are including our 1.2 version and making it to work
	 * in the same way as the old one.
	 *
	 * @param   string  $selector  The CSS selector for elements to apply the behavior.
	 * @param   array   $params    The array of options to use for the behavior.
	 *
	 * @return  void
	 *
	 * @since   1.5.19
	 * @static
	 */
	function tooltip($selector='.hasTip', $params = array())
	{
		// Setup the static array of tooltip instance options.
		static $instances;
		if (!isset($instances)) {
			$instances = array();
		}

		// Generate the behavior/option signature and check to see if it has already been loaded.
		$sig = md5(serialize(array($selector,$params)));
		if (!empty($instances[$sig])) {
			return;
		}

		// Load the Mootools framework.
		JHTMLBehavior::mootools();

		// Setup the options object.
		$opt['maxTitleChars']	= (isset($params['maxTitleChars']) && ($params['maxTitleChars'])) ? (int)$params['maxTitleChars'] : 50 ;
		$opt['showDelay']		= (isset($params['showDelay'])) ? (int)$params['showDelay'] : null;
		$opt['hideDelay']		= (isset($params['hideDelay'])) ? (int)$params['hideDelay'] : null;
		$opt['className']		= (isset($params['className'])) ? $params['className'] : null;
		$opt['fixed']			= (isset($params['fixed']) && ($params['fixed'])) ? '\\true' : '\\false';

		// Optional event handler methods.
		$opt['onShow']			= (isset($params['onShow'])) ? '\\'.$params['onShow'] : null;
		$opt['onHide']			= (isset($params['onHide'])) ? '\\'.$params['onHide'] : null;

		// Offsets needs an array in the format: array('x'=>20, 'y'=>30).
		$opt['offsets']			= (isset($params['offsets']) && (is_array($params['offsets']))) ? $params['offsets'] : null;

		// Build the script.
		$script = array(
			'window.addEvent("domready", function() {',
			'	var JTooltips = new Tips($$("'.$selector.'"), '.JHTMLBehavior::_getJSObject($opt).');',
			'});'
		);

		// Load the script into the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Ensure the same instance isn't loaded more than once.
		$instances[$sig] = true;
	}

	/**
	 * Method to load the system modal behavior.
	 *
	 * @param   string  $selector  The CSS selector for elements to apply the behavior.
	 * @param   array   $params    The array of options to use for the behavior.
	 *
	 * @return  void
	 *
	 * @since   1.5.19
	 * @static
	 */
	function modal($selector='a.modal', $params = array())
	{
		// Setup the static array of modal instance options.
		static $instances;
		if (!isset($instances)) {
			$instances = array();
		}

		// Generate the behavior/option signature and check to see if it has already been loaded.
		$sig = md5(serialize(array($selector,$params)));
		if (!empty($instances[$sig])) {
			return;
		}

		// Load the behavior and stylesheet files into the document head.
		JHTML::script('modal.js');
		JHTML::stylesheet('modal.css');

		// Setup the options object.
		$opt['ajaxOptions']	= (isset($params['ajaxOptions']) && (is_array($params['ajaxOptions']))) ? $params['ajaxOptions'] : null;
		$opt['size']		= (isset($params['size']) && (is_array($params['size']))) ? $params['size'] : null;

		// Optional event handler methods.
		$opt['onOpen']		= (isset($params['onOpen'])) ? $params['onOpen'] : null;
		$opt['onClose']		= (isset($params['onClose'])) ? $params['onClose'] : null;
		$opt['onUpdate']	= (isset($params['onUpdate'])) ? $params['onUpdate'] : null;
		$opt['onResize']	= (isset($params['onResize'])) ? $params['onResize'] : null;
		$opt['onMove']		= (isset($params['onMove'])) ? $params['onMove'] : null;
		$opt['onShow']		= (isset($params['onShow'])) ? $params['onShow'] : null;
		$opt['onHide']		= (isset($params['onHide'])) ? $params['onHide'] : null;

		// Build the script.
		$script = array(
			'window.addEvent("domready", function() {',
			'	SqueezeBox.initialize('.JHTMLBehavior::_getJSObject($opt).');',
			'	$$("'.$selector.'").each(function(el) {',
			'		el.addEvent("click", function(e) {',
			'			new Event(e).stop();',
			'			SqueezeBox.fromElement(el);',
			'		});',
			'	});',
			'});'
		);

		// Load the script into the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Ensure the same instance isn't loaded more than once.
		$instances[$sig] = true;
	}

	/**
	 * Method to load the system file uploader behavior.
	 *
	 * @param   string  $id      The DOM node id for the element to apply the behavior.
	 * @param   array   $params  The array of options to use for the behavior.
	 *
	 * @return  void
	 *
	 * @since   1.5.19
	 * @static
	 */
	function uploader($id='file-upload', $params = array())
	{
		// Setup the static array of behavior instances.
		static $instances;
		if (!isset($instances)) {
			$instances = array();
		}

		// Check to see if it has already been loaded.
		if (!empty($instances[$id])) {
			return;
		}

		// Load the behavior files into the document head.
		JHTML::script('swf.js');
		JHTML::script('uploader.js');

		// Setup the options object.
		$opt['url']					= (isset($params['targetURL'])) ? $params['targetURL'] : null ;
		$opt['swf']					= (isset($params['swf'])) ? $params['swf'] : JURI::root(true).'/media/system/swf/uploader.swf';
		$opt['multiple']			= (isset($params['multiple']) && !($params['multiple'])) ? '\\false' : '\\true';
		$opt['queued']				= (isset($params['queued']) && !($params['queued'])) ? '\\false' : '\\true';
		$opt['queueList']			= (isset($params['queueList'])) ? $params['queueList'] : 'upload-queue';
		$opt['instantStart']		= (isset($params['instantStart']) && ($params['instantStart'])) ? '\\true' : '\\false';
		$opt['allowDuplicates']		= (isset($params['allowDuplicates']) && !($params['allowDuplicates'])) ? '\\false' : '\\true';
		$opt['limitSize']			= (isset($params['limitSize']) && ($params['limitSize'])) ? (int)$params['limitSize'] : null;
		$opt['limitFiles']			= (isset($params['limitFiles']) && ($params['limitFiles'])) ? (int)$params['limitFiles'] : null;
		$opt['optionFxDuration']	= (isset($params['optionFxDuration'])) ? (int)$params['optionFxDuration'] : null;
		$opt['container']			= (isset($params['container'])) ? '\\$('.$params['container'].')' : '\\$(\''.$id.'\').getParent()';

		// JSON object with ('description': 'extension') pairs. eg. (default: Images (*.jpg; *.jpeg; *.gif; *.png));
		$opt['types']				= (isset($params['types'])) ?'\\'.$params['types'] : '\\{\'All Files (*.*)\': \'*.*\'}';

		// Optional functions.
		$opt['createReplacement']	= (isset($params['createReplacement'])) ? '\\'.$params['createReplacement'] : null;
		$opt['onComplete']			= (isset($params['onComplete'])) ? '\\'.$params['onComplete'] : null;
		$opt['onAllComplete']		= (isset($params['onAllComplete'])) ? '\\'.$params['onAllComplete'] : null;

		// Build the script.
		$script = array(
			'sBrowseCaption="'.JText::_('Browse Files', true).'";',
			'sRemoveToolTip="'.JText::_('Remove from queue', true).'";',
			'',
			'window.addEvent("load", function() {',
			'	var Uploader = new FancyUpload($("'.$id.'"), '.JHTMLBehavior::_getJSObject($opt).');',
			'	$("upload-clear").adopt(new Element("input", {type: "button", events: { click: Uploader.clearList.bind(Uploader, [false])}, value: "'.JText::_('Clear Completed').'" }));',
			'});'
		);

		// Load the script into the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Ensure the same instance isn't loaded more than once.
		$instances[$id] = true;
	}

	/**
	 * Method to load the system tree behavior.
	 *
	 * @param   string  $id      The DOM node id for the element to apply the behavior.
	 * @param   array   $params  The array of options to use for the behavior.
	 * @param   array   $root    The array of root node options.
	 *
	 * @return  void
	 *
	 * @since   1.5.19
	 * @static
	 */
	function tree($id, $params = array(), $root = array())
	{
		// Setup the static array of behavior instances.
		static $instances;
		if (!isset($instances)) {
			$instances = array();
		}

		// Check to see if it has already been loaded.
		if (!empty($instances[$id])) {
			return;
		}

		// Load the behavior and stylesheet files into the document head.
		JHTML::script('mootree.js');
		JHTML::stylesheet('mootree.css');

		// Setup the options object.
		$opt['div']		= (isset($params['div'])) ? $params['div'] : $id.'_tree';
		$opt['mode']	= (isset($params['mode'])) ? $params['mode'] : 'folders';
		$opt['grid']	= (isset($params['grid'])) ? '\\'.$params['grid'] : '\\true';
		$opt['theme']	= (isset($params['theme'])) ? $params['theme'] : JURI::root(true).'/media/system/images/mootree.gif';

		// Optional event handler methods.
		$opt['onExpand']	= (isset($params['onExpand'])) ? '\\'.$params['onExpand'] : null;
		$opt['onSelect']	= (isset($params['onSelect'])) ? '\\'.$params['onSelect'] : null;
		$opt['onClick']		= (isset($params['onClick'])) ? '\\'.$params['onClick'] : '\\function(node){  window.open(node.data.url, $chk(node.data.target) ? node.data.target : \'_self\'); }';

		// Setup the root node options.
		$rt['text']		= (isset($root['text'])) ? $root['text'] : 'Root';
		$rt['id']		= (isset($root['id'])) ? $root['id'] : null;
		$rt['color']	= (isset($root['color'])) ? $root['color'] : null;
		$rt['open']		= (isset($root['open'])) ? '\\'.$root['open'] : '\\true';
		$rt['icon']		= (isset($root['icon'])) ? $root['icon'] : null;
		$rt['openicon']	= (isset($root['openicon'])) ? $root['openicon'] : null;
		$rt['data']		= (isset($root['data'])) ? $root['data'] : null;

		// Get the optional name of the tree.
		$name = (isset($params['treeName'])) ? $params['treeName'] : '';

		// Build the script.
		$script = array(
			'window.addEvent(\'domready\', function(){',
			'	tree'.$name.' = new MooTreeControl('.JHTMLBehavior::_getJSObject($opt).','.JHTMLBehavior::_getJSObject($rt).');',
			'	tree'.$name.'.adopt(\''.$id.'\');',
			'})'
		);

		// Load the script into the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Ensure the same instance isn't loaded more than once.
		$instances[$id] = true;
	}

	/**
	 * Method to load the system calendar behavior.
	 *
	 * @return  void
	 *
	 * @since   1.5.19
	 * @static
	 */
	function calendar()
	{
		// Load the behavior and stylesheet files into the document head.
		JHTML::stylesheet('calendar-jos.css', 'media/system/css/', array(' title' => JText::_('green') ,' media' => 'all'));
		JHTML::script('calendar.js');
		JHTML::script('calendar-setup.js');

		// Get and load the calendar translation string into the document head.
		if ($translation = JHTMLBehavior::_calendartranslation()) {
			JFactory::getDocument()->addScriptDeclaration($translation);
		}
	}

	/**
	 * Method to load the system keepalive behavior.  This will send an ascynchronous request to the
	 * server via AJAX on an interval just under the session expiration lifetime so that the session
	 * does not expire.
	 *
	 * @return  void
	 *
	 * @since   1.5.19
	 * @static
	 */
	function keepalive()
	{
		// Load the behavior framework into the document head.
		JHTMLBehavior::mootools();

		// Get the session lifetime in microseconds.
		$lifetime = (JFactory::getConfig()->getValue('lifetime', 900) * 60000);

		// Set the session refresh period to one minute less than the session lifetime.
		$refreshTime = (int) ($lifetime <= 60000) ? 30000 : $lifetime - 60000;

		// Build the keepalive script.
		$script = array(
			'function keepAlive() { new Ajax("index.php", {method: "get"}).request(); }',
			'window.addEvent("domready", function() { keepAlive.periodical('.$refreshTime.'); });'
		);

		// Load the keepalive script into the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
	}

	/**
	 * Method to get a simple JavaScript Object Notation (JSON) representation of an input
	 * associative array.
	 *
	 * @param   array   $array  The array to convert to JavaScript Object Notation.
	 *
	 * @return  string  JavaScript Object Notation representation of the array.
	 *
	 * @access  protected
	 * @since   1.5.19
	 * @static
	 */
	function _getJSObject($array = array())
	{
		// Initialize variables
		$json = '{';

		// Iterate over array to build objects
		foreach ((array)$array as $k => $v)
		{
			if (is_null($v)) {
				continue;
			}

			if (!is_array($v) && !is_object($v)) {
				$json .= ' '.$k.': ';
				$json .= (is_numeric($v) || strpos($v, '\\') === 0) ? (is_numeric($v)) ? $v : substr($v, 1) : "'".$v."'";
				$json .= ',';
			}
			else {
				$json .= ' '.$k.': '.JHTMLBehavior::_getJSObject($v).',';
			}
		}

		if (substr($json, -1) == ',') {
			$json = substr($json, 0, -1);
		}

		$json .= '}';

		return $json;
	}

	/**
	 * Method to get the internationalisation script/settings for the JavaScript Calendar behavior.
	 *
	 * @return  mixed  JavaScript calendar internationalisation settings script or null if already loaded.
	 *
	 * @access  protected
	 * @since   1.5.19
	 * @static
	 */
	function _calendartranslation()
	{
		// Check to see if it has already been loaded.
		static $loaded;
		if (!empty($loaded)) {
			return;
		}

		// Build the day names array.
		$dayNames = array(
			'"'.JText::_('Sunday').'"',
			'"'.JText::_('Monday').'"',
			'"'.JText::_('Tuesday').'"',
			'"'.JText::_('Wednesday').'"',
			'"'.JText::_('Thursday').'"',
			'"'.JText::_('Friday').'"',
			'"'.JText::_('Saturday').'"',
			'"'.JText::_('Sunday').'"'
		);

		// Build the short day names array.
		$shortDayNames = array(
			'"'.JText::_('Sun').'"',
			'"'.JText::_('Mon').'"',
			'"'.JText::_('Tue').'"',
			'"'.JText::_('Wed').'"',
			'"'.JText::_('Thu').'"',
			'"'.JText::_('Fri').'"',
			'"'.JText::_('Sat').'"',
			'"'.JText::_('Sun').'"'
		);

		// Build the month names array.
		$monthNames = array(
			'"'.JText::_('January').'"',
			'"'.JText::_('February').'"',
			'"'.JText::_('March').'"',
			'"'.JText::_('April').'"',
			'"'.JText::_('May').'"',
			'"'.JText::_('June').'"',
			'"'.JText::_('July').'"',
			'"'.JText::_('August').'"',
			'"'.JText::_('September').'"',
			'"'.JText::_('October').'"',
			'"'.JText::_('November').'"',
			'"'.JText::_('December').'"'
		);

		// Build the short month names array.
		$shortMonthNames = array(
			'"'.JText::_('January_short').'"',
			'"'.JText::_('February_short').'"',
			'"'.JText::_('March_short').'"',
			'"'.JText::_('April_short').'"',
			'"'.JText::_('May_short').'"',
			'"'.JText::_('June_short').'"',
			'"'.JText::_('July_short').'"',
			'"'.JText::_('August_short').'"',
			'"'.JText::_('September_short').'"',
			'"'.JText::_('October_short').'"',
			'"'.JText::_('November_short').'"',
			'"'.JText::_('December_short').'"'
		);

		// Build the script.
		$i18n = array(
			'// Calendar i18n Setup.',
			'Calendar._FD = 0;',
			'Calendar._DN = new Array ('.implode(', ', $dayNames).');',
			'Calendar._SDN = new Array ('.implode(', ', $shortDayNames).');',
			'Calendar._MN = new Array ('.implode(', ', $monthNames).');',
			'Calendar._SMN = new Array ('.implode(', ', $shortMonthNames).');',
			'',
			'Calendar._TT = {};',
			'Calendar._TT["INFO"] = "'.JText::_('About the calendar').'";',
			'Calendar._TT["PREV_YEAR"] = "'.JText::_('Prev. year (hold for menu)').'";',
			'Calendar._TT["PREV_MONTH"] = "'.JText::_('Prev. month (hold for menu)').'";',
			'Calendar._TT["GO_TODAY"] = "'.JText::_('Go Today').'";',
			'Calendar._TT["NEXT_MONTH"] = "'.JText::_('Next month (hold for menu)').'";',
			'Calendar._TT["NEXT_YEAR"] = "'.JText::_('Next year (hold for menu)').'";',
			'Calendar._TT["SEL_DATE"] = "'.JText::_('Select date').'";',
			'Calendar._TT["DRAG_TO_MOVE"] = "'.JText::_('Drag to move').'";',
			'Calendar._TT["PART_TODAY"] = "'.JText::_('(Today)').'";',
			'Calendar._TT["DAY_FIRST"] = "'.JText::_('Display %s first').'";',
			'Calendar._TT["WEEKEND"] = "0,6";',
			'Calendar._TT["CLOSE"] = "'.JText::_('Close').'";',
			'Calendar._TT["TODAY"] = "'.JText::_('Today').'";',
			'Calendar._TT["TIME_PART"] = "'.JText::_('(Shift-)Click or drag to change value').'";',
			'Calendar._TT["DEF_DATE_FORMAT"] = "'.JText::_('%Y-%m-%d').'";',
			'Calendar._TT["TT_DATE_FORMAT"] = "'.JText::_('%a, %b %e').'";',
			'Calendar._TT["WK"] = "'.JText::_('wk').'";',
			'Calendar._TT["TIME"] = "'.JText::_('Time:').'";',
			'',
			'Calendar._TT["ABOUT"] =',
			'"DHTML Date/Time Selector\n" +',
			'"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" +',
			'"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +',
			'"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +',
			'"\n\n" +',
			'"Date selection:\n" +',
			'"- Use the \xab, \xbb buttons to select year\n" +',
			'"- Use the " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " buttons to select month\n" +',
			'"- Hold mouse button on any of the above buttons for faster selection.";',
			'',
			'Calendar._TT["ABOUT_TIME"] = "\n\n" +',
			'"Time selection:\n" +',
			'"- Click on any of the time parts to increase it\n" +',
			'"- or Shift-click to decrease it\n" +',
			'"- or click and drag for faster selection.";',
			''
		);

		// Ensure the i18n data isn't loaded more than once.
		$loaded = true;

		return implode("\n", $i18n);
	}
}

