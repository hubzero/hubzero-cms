<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Debug
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Joomla! Debug plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  System.Debug
 * @since       1.5
 */
class plgSystemDebug extends JPlugin
{
	protected $linkFormat = '';

	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @since 1.5
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Log the deprecated API.
		if ($this->params->get('log-deprecated'))
		{
			\JLog::addLogger(array('text_file' => 'deprecated.php'), \JLog::ALL, array('deprecated'));
		}

		// Log database errors
		if ($this->params->get('log-database-errors'))
		{
			\JLog::addLogger(array('text_file' => 'jdatabase.error.php'), \JLog::ALL, array('database'));
		}

		// Log database queries
		if ($this->params->get('log-database-queries'))
		{
			\JLog::addLogger(array('text_file' => 'jdatabase.query.php'), \JLog::ALL, array('databasequery'));
		}

		// Only if debugging or language debug is enabled
		if (JDEBUG || \JFactory::getApplication()->getCfg('debug_lang'))
		{
			\JFactory::getConfig()->set('gzip', 0);
			ob_start();
			ob_implicit_flush(false);
		}

		$this->linkFormat = ini_get('xdebug.file_link_format');
	}

	/**
	 * Add the CSS for debug. We can't do this in the constructor because
	 * stuff breaks.
	 *
	 * @return  void
	 */
	public function onAfterDispatch()
	{
		$base = str_replace('/administrator', '', rtrim(\JURI::base(true), '/'));

		// Only if debugging or language debug is enabled
		if (JDEBUG || \JFactory::getApplication()->getCfg('debug_lang'))
		{
			JFactory::getDocument()->addStyleSheet($base . '/media/cms/css/debug.css?v=' . filemtime(JPATH_ROOT . '/media/cms/css/debug.css'));
		}

		// [!] HUBZERO - Add CSS diagnostics
		if (JDEBUG && $this->params->get('css', 0) && is_file(JPATH_SITE . '/media/system/css/diagnostics.css'))
		{
			JFactory::getDocument()->addStyleSheet($base . '/media/system/css/diagnostics.css?v=' . filemtime(JPATH_ROOT . '/media/system/css/diagnostics.css'));
		}
	}

	/**
	 * Show the debug info
	 */
	public function __destruct()
	{
		// Do not render if debugging or language debug is not enabled
		if (!JDEBUG && !\JFactory::getApplication()->getCfg('debug_lang'))
		{
			return;
		}

		// Load the language
		$this->loadLanguage();

		// Capture output
		// [!] zooley Nov 03, 2014 - PHP 5.4 changed behavior for ob_end_clean().
		//     ob_end_clean(), called in JError, clears and stops buffering.
		//     On error, pages, there will be no buller to get so ob_get_contents()
		//     will be false.
		$contents = ob_get_contents();

		if ($contents)
		{
			ob_end_clean();
		}
		else
		{
			return;
		}

		// No debug for Safari and Chrome redirection
		if (isset($_SERVER['HTTP_USER_AGENT']) && strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'webkit') !== false
			&& substr($contents, 0, 50) == '<html><head><meta http-equiv="refresh" content="0;')
		{
			echo $contents;
			return;
		}

		// Only render for HTML output
		if ('html' !== \JFactory::getDocument()->getType())
		{
			echo $contents;
			return;
		}

		// If the user is not allowed to view the output then end here
		$filterGroups = (array) $this->params->get('filter_groups', null);

		if (!empty($filterGroups))
		{
			$userGroups = \JFactory::getUser()->get('groups');

			if (!array_intersect($filterGroups, $userGroups))
			{
				echo $contents;
				return;
			}
		}
		/* [!] HUBZERO - Add ability to show deubg output to specified users
		 * zooley (2012-08-29)
		 */
		else
		{
			$filterUsers = $this->params->get('filter_users', null);

			if (!empty($filterUsers))
			{
				$filterUsers = explode(',', $filterUsers);
				$filterUsers = array_map('trim', $filterUsers);

				if (!in_array(\JFactory::getUser()->get('username'), $filterUsers))
				{
					echo $contents;
					return;
				}
			}
		}

		// Load language file
		$this->loadLanguage('plg_system_debug');

		//$last = end(\JProfiler::getInstance('Application')->getBuffer());

		$html = '';

		// Some "mousewheel protecting" JS
		$html .= '<div id="system-debug" class="' . $this->params->get('theme', 'dark') . ' profiler">';

		$html .= '<div class="debug-head" id="debug-head">';
		$html .= '<h1>' . JText::_('PLG_DEBUG_TITLE') . '</h1>';
		$html .= '<a class="debug-close-btn" href="javascript:" onclick="Debugger.close();"><span class="icon-remove">' . JText::_('PLG_DEBUG_CLOSE') . '</span></a>';

		if (JDEBUG)
		{
			if ($this->params->get('memory', 1))
			{
				$html .= '<span class="debug-indicator"><span class="icon-memory text" data-hint="' . JText::_('PLG_DEBUG_MEMORY_USAGE') . '">' .$this->displayMemoryUsage(). '</span></span>';
			}
			if (\JError::getErrors())
			{
				$html .= '<a href="javascript:" class="debug-tab" onclick="Debugger.toggleContainer(this, \'debug-errors\');"><span class="text">' . JText::_('PLG_DEBUG_ERRORS') . '</span><span class="badge">' . count(\JError::getErrors()) . '</span></a>';
			}

			$dumper = \Hubzero\Utility\Debug::getInstance();
			if ($dumper->hasMessages())
			{
				$html .= '<a href="javascript:" class="debug-tab" onclick="Debugger.toggleContainer(this, \'debug-debug\');"><span class="text">' . JText::_('PLG_DEBUG_CONSOLE') . '</span>';
				$html .= '<span class="badge">' . count($dumper->messages()) . '</span>';
				$html .= '</a>';
			}

			$html .= '<a href="javascript:" class="debug-tab" onclick="Debugger.toggleContainer(this, \'debug-request\');"><span class="text">' . JText::_('PLG_DEBUG_REQUEST_DATA') . '</span></a>';
			$html .= '<a href="javascript:" class="debug-tab" onclick="Debugger.toggleContainer(this, \'debug-session\');"><span class="text">' . JText::_('PLG_DEBUG_SESSION') . '</span></a>';
			if ($this->params->get('profile', 1))
			{
				$html .= '<a href="javascript:" class="debug-tab" onclick="Debugger.toggleContainer(this, \'debug-profile_information\');"><span class="text">' . JText::_('PLG_DEBUG_PROFILE_TIMELINE') . '</span></a>';
			}
			if ($this->params->get('queries', 1))
			{
				$html .= '<a href="javascript:" class="debug-tab" onclick="Debugger.toggleContainer(this, \'debug-queries\');"><span class="text">' . JText::_('PLG_DEBUG_QUERIES') . '</span><span class="badge">' . \JFactory::getDbo()->getCount() . '</span></a>';
			}
		}
		if (\JFactory::getApplication()->getCfg('debug_lang'))
		{
			if ($this->params->get('language_errorfiles', 1))
			{
				$html .= '<a href="javascript:" class="debug-tab" onclick="Debugger.toggleContainer(this, \'debug-language_files_in_error\');"><span class="text">' . JText::_('PLG_DEBUG_LANGUAGE_FILE_ERRORS') . '</span>';
				$html .= '<span class="badge">' . count(\JFactory::getLanguage()->getErrorFiles()) . '</span>';
				$html .= '</a>';
			}

			if ($this->params->get('language_files', 1))
			{
				$total = 0;
				foreach (\JFactory::getLanguage()->getPaths() as $extension => $files)
				{
					$total += count($files);
				}
				$html .= '<a href="javascript:" class="debug-tab" onclick="Debugger.toggleContainer(this, \'debug-language_files_loaded\');"><span class="text">' . JText::_('PLG_DEBUG_LANGUAGE_FILES_LOADED') . '</span>';
				$html .= '<span class="badge">' . $total . '</span>';
				$html .= '</a>';
			}

			if ($this->params->get('language_strings'))
			{
				$html .= '<a href="javascript:" class="debug-tab" onclick="Debugger.toggleContainer(this, \'debug-untranslated_strings\');"><span class="text">' . JText::_('PLG_DEBUG_UNTRANSLATED') . '</span>';
				$html .= '<span class="badge">' . count(\JFactory::getLanguage()->getOrphans()) . '</span>';
				$html .= '</a>';
			}
		}
		$html .= '</div>';
		$html .= '<div class="debug-body" id="debug-body">';

		if (JDEBUG)
		{
			if (\JError::getErrors())
			{
				$html .= $this->display('errors');
			}

			if ($dumper->hasMessages())
			{
				$html .= $this->display('debug');
			}

			$html .= $this->display('request');
			$html .= $this->display('session');

			if ($this->params->get('profile', 1))
			{
				$html .= $this->display('profile_information');
			}

			if ($this->params->get('memory', 1))
			{
				$html .= $this->display('memory_usage');
			}

			if ($this->params->get('queries', 1))
			{
				$html .= $this->display('queries');
			}
		}

		if (\JFactory::getApplication()->getCfg('debug_lang'))
		{
			if ($this->params->get('language_errorfiles', 1))
			{
				$languageErrors = \JFactory::getLanguage()->getErrorFiles();
				$html .= $this->display('language_files_in_error', $languageErrors);
			}

			if ($this->params->get('language_files', 1))
			{
				$html .= $this->display('language_files_loaded');
			}

			if ($this->params->get('language_strings'))
			{
				$html .= $this->display('untranslated_strings');
			}
		}

		$html .= '</div>';
		$html .= '</div>';

		$html .= "<script type=\"text/javascript\">
		if (!document.getElementsByClassName) {
			document.getElementsByClassName = (function(){
				function traverse (node, callback) {
					callback(node);
					for (var i=0;i < node.childNodes.length; i++) {
						traverse(node.childNodes[i],callback);
					}
				}
				return function (name) {
					var result = [];
					traverse(document.body,function(node){
						if (node.className && (' ' + node.className + ' ').indexOf(' ' + name + ' ') > -1) {
							result.push(node);
						}
					});
					return result;
				}
			})();
		}
		Debugger = {
			toggleShortFull: function(id) {
				var d = document.getElementById('debug-' + id + '-short');
				if (!Debugger.hasClass(d, 'open')) {
					Debugger.addClass(d, 'open');
				} else {
					Debugger.removeClass(d, 'open');
				}

				var g = document.getElementById('debug-' + id + '-full');
				if (!Debugger.hasClass(g, 'open')) {
					Debugger.addClass(g, 'open');
				} else {
					Debugger.removeClass(g, 'open');
				}
			},
			close: function() {
				var d = document.getElementById('system-debug');
				if (Debugger.hasClass(d, 'open')) {
					Debugger.removeClass(d, 'open');
				}

				Debugger.deactivate();
			},
			deactivate: function() {
				var items = document.getElementsByClassName('debug-tab');
				for (var i=0;i<items.length;i++)
				{
					if (Debugger.hasClass(items[i], 'active')) {
						Debugger.removeClass(items[i], 'active');
					}
				}

				var items = document.getElementsByClassName('debug-container');
				for (var i=0;i<items.length;i++)
				{
					if (Debugger.hasClass(items[i], 'open')) {
						Debugger.removeClass(items[i], 'open');
					}
				}
			},
			toggleContainer: function(el, name) {
				if (!Debugger.hasClass(el, 'active')) {
					var d = document.getElementById('system-debug');
					if (!Debugger.hasClass(d, 'open')) {
						Debugger.addClass(d, 'open');
					}

					Debugger.deactivate();
					Debugger.addClass(el, 'active');

					var e = document.getElementById(name);
					if (e) {
						Debugger.toggleClass(e, 'open');
					}
				} else {
					Debugger.close();
				}
			},
			hasClass: function(elem, className) {
				return new RegExp(' ' + className + ' ').test(' ' + elem.className + ' ');
			},
			addClass: function(elem, className) {
				if (!Debugger.hasClass(elem, className)) {
					elem.className += ' ' + className;
				}
			},
			removeClass: function(elem, className) {
				var newClass = ' ' + elem.className.replace( /[\\t\\r\\n]/g, ' ') + ' ';
				if (Debugger.hasClass(elem, className)) {
					while (newClass.indexOf(' ' + className + ' ') >= 0 ) {
						newClass = newClass.replace(' ' + className + ' ', ' ');
					}
					elem.className = newClass.replace(/^\s+|\s+\$/g, '');
				}
			},
			toggleClass: function(elem, className) {
				var newClass = ' ' + elem.className.replace( /[\\t\\r\\n]/g, ' ') + ' ';
				if (Debugger.hasClass(elem, className)) {
					while (newClass.indexOf(' ' + className + ' ') >= 0 ) {
						newClass = newClass.replace(' ' + className + ' ', ' ');
					}
					elem.className = newClass.replace(/^\s+|\s+\$/g, '');
				} else {
					elem.className += ' ' + className;
				}
			},
			addEvent: function(obj, type, fn) {
				if (obj.attachEvent) {
					obj['e'+type+fn] = fn;
					obj[type+fn] = function() {
						obj['e'+type+fn]( window.event );
					};
					obj.attachEvent('on' + type, obj[type+fn]);
				} else {
					obj.addEventListener( type, fn, false );
				}
			},
			removeEvent: function( obj, type, fn ) {
				if (obj.detachEvent) {
					obj.detachEvent('on' + type, obj[type+fn]);
					obj[type+fn] = null;
				} else {
					obj.removeEventListener(type, fn, false);
				}
			}
		};

		Function.prototype.bindD = function(obj) {
			var _method = this;
			return function() {
				return _method.apply(obj, arguments);
			};
		}

		function debugDrag(id) {
			this.id = 'id';
			this.direction = 'y';
		}
		debugDrag.prototype = {
			init: function(settings) {
				for (var i in settings)
				{
					this[i] = settings[i];

					for (var j in settings[i])
					{
						this[i][j] = settings[i][j];
					}
				}

				this.elem = (this.id.tagName==undefined) ? document.getElementById(this.id) : this.id;
				this.container = this.elem.parentNode;
				this.elem.onmousedown = this._mouseDown.bindD(this);
			},

			_mouseDown: function(e) {
				e = e || window.event;

				this.elem.onselectstart=function(){return false};

				this._event_docMouseMove = this._docMouseMove.bindD(this);
				this._event_docMouseUp = this._docMouseUp.bindD(this);

				if (this.onstart) this.onstart();

				this.x = e.clientX || e.PageX;
				this.y = e.clientY || e.PageY;

				//this.left = parseInt(this._getstyle(this.elem, 'left'));
				//this.top = parseInt(this._getstyle(this.elem, 'top'));
				this.top = parseInt(this._getstyle(this.container, 'height'));

				Debugger.addEvent(document, 'mousemove', this._event_docMouseMove);
				Debugger.addEvent(document, 'mouseup', this._event_docMouseUp);

				return false;
			},

			_getstyle: function(elem, prop) {
				if (document.defaultView) {
					return document.defaultView.getComputedStyle(elem, null).getPropertyValue(prop);
				} else if (elem.currentStyle) {
					var prop = prop.replace(/-(\w)/gi, function($0,$1)
					{
						return $1.toUpperCase();
					});
					return elem.currentStyle[prop];
				} else {
					return null;
				}
			},

			_docMouseMove: function(e) {
				this.setValuesClick(e);
				if (this.ondrag) this.ondrag();
			},

			_docMouseUp: function(e) {
				Debugger.removeEvent(document, 'mousemove', this._event_docMouseMove);

				if (this.onstop) this.onstop();

				Debugger.removeEvent(document, 'mouseup', this._event_docMouseUp);
			},

			setValuesClick: function(e) {
				if (!Debugger.hasClass(this.container, 'open')) {
					return;
				}

				this.mouseX = e.clientX || e.PageX;
				this.mouseY = e.clientY || e.pageY;

				this.Y = this.top + this.y - this.mouseY - parseInt(this._getstyle(document.getElementById('debug-head'), 'height')); //this.top + this.mouseY - this.y;

				//this.container.style.height = (this.Y + 6) +'px';
				document.getElementById('debug-body').style.height = (this.Y + 6) +'px';
			},

			_limit: function(val, mn, mx) {
				return Math.min(Math.max(val, Math.min(mn, mx)), Math.max(mn, mx));
			}
		}
		var dragBar = new debugDrag();
		dragBar.init({id:'debug-head'});
		</script>";

		echo str_replace('</body>', $html . '</body>', $contents);
	}

	/**
	 * General display method.
	 *
	 * @param   string  $item    The item to display
	 * @param   array   $errors  Errors occured during execution
	 * @return  string
	 */
	protected function display($item, array $errors = array())
	{
		$title = \JText::_('PLG_DEBUG_' . strtoupper($item));

		$status = '';

		if (count($errors))
		{
			$status = ' dbgerror';
		}

		$fncName = 'display' . ucfirst(str_replace('_', '', $item));

		if (!method_exists($this, $fncName))
		{
			return __METHOD__ . ' -- Unknown method: ' . $fncName . '<br />';
		}

		$html  = '';
		$html .= '<div class="debug-container" id="debug-' . $item . '">';
		$html .= $this->$fncName();
		$html .= '</div>';

		return $html;
	}

	/**
	 * Display super global data
	 *
	 * @return  string
	 */
	protected function displayRequest()
	{
		$get     = $this->_arr($_GET);
		$post    = $this->_arr($_POST);
		$cookies = $this->_arr($_COOKIE);
		$server  = $this->_arr($_SERVER);

		$html  = '';
		$html .= '
		<dl class="debug-varlist">
			<dt class="key">$_GET</dt>
			<dd class="value">
				<span id="debug-get-short" class="open" onclick="Debugger.toggleShortFull(\'get\');">' . \Hubzero\Utility\String::truncate(strip_tags($get), 100, array('exact' => true)) . '</span>
				<span id="debug-get-full" onclick="Debugger.toggleShortFull(\'get\');">' . nl2br($get) . '</span>
			</dd>
			<dt class="key">$_POST</dt>
			<dd class="value">
				<span id="debug-post-short" class="open" onclick="Debugger.toggleShortFull(\'post\');">' . \Hubzero\Utility\String::truncate(strip_tags($post), 100, array('exact' => true)) . '</span>
				<span id="debug-post-full" onclick="Debugger.toggleShortFull(\'post\');">' . nl2br($post) . '</span>
			</dd>
			<dt class="key">$_COOKIE</dt>
			<dd class="value">
				<span id="debug-cookies-short" class="open" onclick="Debugger.toggleShortFull(\'cookies\');">' . \Hubzero\Utility\String::truncate(strip_tags($cookies), 100, array('exact' => true)) . '</span>
				<span id="debug-cookies-full" onclick="Debugger.toggleShortFull(\'cookies\');">' . nl2br($cookies) . '</span>
			</dd>
			<dt class="key">$_SERVER</dt>
			<dd class="value">
				<span id="debug-server-short" class="open" onclick="Debugger.toggleShortFull(\'server\');">' . \Hubzero\Utility\String::truncate(strip_tags($server), 100, array('exact' => true)) . '</span>
				<span id="debug-server-full" onclick="Debugger.toggleShortFull(\'server\');">' . nl2br($server) . '</span>
			</dd>
		</dl>';

		return $html;
	}

	/**
	 * Display super global data
	 *
	 * @return  string
	 */
	protected function displayDebug()
	{
		$dumper = \Hubzero\Utility\Debug::getInstance();

		return $dumper->render();
	}

	/**
	 * Turn an array into a pretty print format
	 *
	 * @param   array  $arr
	 * @return  string
	 */
	protected function _arr($arr)
	{
		$html = 'Array( ' . "\n";
		$a = array();
		foreach ($arr as $key => $val)
		{
			if (is_array($val))
			{
				$a[] = "\t" . '<code class="ky">' . $key . '</code> <code class="op">=></code> <code class="vl">' . $this->_arr($val) . '</code>';
			}
			else if (is_object($val))
			{
				$a[] = "\t" . '<code class="ky">' . $key . '</code> <code class="op">=></code> <code class="vl">' . get_class($val) . '</code>';
			}
			else
			{
				$a[] = "\t" . '<code class="ky">' . $key . '</code> <code class="op">=></code> <code class="vl">' . htmlentities($val, ENT_COMPAT, 'UTF-8') . '</code>';
			}
		}
		$html .= implode(", \n", $a) . "\n" . ' )' . "\n";

		return $html;
	}

	/**
	 * Display session information.
	 *
	 * Called recursive.
	 *
	 * @param   string   $key      A session key
	 * @param   mixed    $session  The session array, initially null
	 * @param   integer  $id       The id is used for JS toggling the div
	 * @return  string
	 */
	protected function displaySession($key = '', $session = null, $id = 0)
	{
		if (!$session)
		{
			$session = $_SESSION;
		}

		static $html = '';
		static $id;

		if (!is_array($session))
		{
			$html .= $key . ' &rArr;' . $session . PHP_EOL;
		}
		else
		{
			foreach ($session as $sKey => $entries)
			{
				$display = true;

				if ($sKey == 'password_clear')
				{
					continue;
				}

				if (is_array($entries) && $entries)
				{
					$display = false;
				}

				if (is_object($entries))
				{
					$o = \JArrayHelper::fromObject($entries);

					if ($o)
					{
						$entries = $o;
						$display = false;
					}
				}

				if (!$display)
				{
					$html .= '<div class="debug-sub-container">';
					$id++;

					// Recurse...
					$this->displaySession($sKey, $entries, $id);

					$html .= '</div>';

					continue;
				}

				if (is_array($entries))
				{
					$entries = implode($entries);
				}

				if (is_string($entries))
				{
					$html .= '<code>';
					$html .= '<span class="ky">' . $sKey . '</span> <span class="op">&rArr;</span> <span class="vl">' . $entries . '</span><br />';
					$html .= '</code>';
				}
			}
		}

		return $html;
	}

	/**
	 * Display errors.
	 *
	 * @return  string
	 */
	protected function displayErrors()
	{
		//$html  = '<div class="debug-container" id="debug-errors">';

		$html  = '<ol>';

		while ($error = \JError::getError(true))
		{
			$col = (E_WARNING == $error->get('level')) ? 'dbg-error' : 'dbg-warning';

			$html .= '<li>';
			$html .= '<strong class="' . $col . '">' . $error->getMessage() . '</strong><br />';

			$info = $error->get('info');

			if ($info)
			{
				$html .= '<pre>' . print_r($info, true) . '</pre><br />';
			}

			$html .= $this->renderBacktrace($error);
			$html .= '</li>';
		}

		$html .= '</ol>';

		//$html .= '</div>';

		return $html;
	}

	/**
	 * Display profile information.
	 *
	 * @return  string
	 */
	protected function displayProfileInformation()
	{
		$html = '<ul class="debug-timeline">';

		foreach (\JProfiler::getInstance('Application')->getBuffer() as $mark)
		{
			$html .= '<li>' . $mark . '</li>';
		}

		$html .= '</ul>';

		return $html;
	}

	/**
	 * Display memory usage
	 *
	 * @return  string
	 */
	protected function displayMemoryUsage()
	{
		$html = '';

		$bytes = \JProfiler::getInstance('Application')->getMemory();

		//$html .= '<code>';
		$html .= \JHtml::_('number.bytes', $bytes);
		//$html .= ' (' . number_format($bytes) . ' Bytes)';
		//$html .= '</code>';

		return $html;
	}

	/**
	 * Display logged queries.
	 *
	 * @return  string
	 */
	protected function displayQueries()
	{
		$db	= \JFactory::getDbo();

		$log = $db->getLog();

		if ( ! $log)
		{
			return;
		}

		$html = '';

		$html .= '<div class="status"><h4>' . \JText::sprintf('PLG_DEBUG_QUERIES_LOGGED',  $db->getCount()) .": ".$db->timer.' seconds</h4></div>';

		$html .= '<ol>';

		$selectQueryTypeTicker = array();
		$otherQueryTypeTicker = array();

		foreach ($log as $k => $sql)
		{
			// Start Query Type Ticker Additions
			$fromStart = stripos($sql, 'from');
			$whereStart = stripos($sql, 'where', $fromStart);

			if ($whereStart === false)
			{
				$whereStart = stripos($sql, 'order by', $fromStart);
			}

			if ($whereStart === false)
			{
				$whereStart = strlen($sql) - 1;
			}

			$fromString = substr($sql, 0, $whereStart);
			$fromString = str_replace("\t", " ", $fromString);
			$fromString = str_replace("\n", " ", $fromString);
			$fromString = trim($fromString);

			// Initialize the select/other query type counts the first time:
			if (!isset($selectQueryTypeTicker[$fromString]))
			{
				$selectQueryTypeTicker[$fromString] = 0;
			}

			if (!isset($otherQueryTypeTicker[$fromString]))
			{
				$otherQueryTypeTicker[$fromString] = 0;
			}

			// Increment the count:
			if (stripos($sql, 'select') === 0)
			{
				$selectQueryTypeTicker[$fromString] = $selectQueryTypeTicker[$fromString] + 1;
				unset($otherQueryTypeTicker[$fromString]);
			}
			else
			{
				$otherQueryTypeTicker[$fromString] = $otherQueryTypeTicker[$fromString] + 1;
				unset($selectQueryTypeTicker[$fromString]);
			}

			$text = $this->highlightQuery($sql);

			$html .= '<li><code>' . $text . '</code></li>';
		}

		$html .= '</ol>';

		if (!$this->params->get('query_types', 0))
		{
			return $html;
		}

		// Get the totals for the query types:
		$totalSelectQueryTypes = count($selectQueryTypeTicker);
		$totalOtherQueryTypes = count($otherQueryTypeTicker);
		$totalQueryTypes = $totalSelectQueryTypes + $totalOtherQueryTypes;

		$html .= '<h4>' . \JText::sprintf('PLG_DEBUG_QUERY_TYPES_LOGGED', $totalQueryTypes) . '</h4>';

		if ($totalSelectQueryTypes)
		{
			$html .= '<h5>' . \JText::_('PLG_DEBUG_SELECT_QUERIES') . '</h5>';

			arsort($selectQueryTypeTicker);

			$html .= '<ol>';

			foreach ($selectQueryTypeTicker as $query => $occurrences)
			{
				$html .= '<li><code>'
					. \JText::sprintf('PLG_DEBUG_QUERY_TYPE_AND_OCCURRENCES', $this->highlightQuery($query), $occurrences)
					. '</code></li>';
			}

			$html .= '</ol>';
		}

		if ($totalOtherQueryTypes)
		{
			$html .= '<h5>' . \JText::_('PLG_DEBUG_OTHER_QUERIES') . '</h5>';

			arsort($otherQueryTypeTicker);

			$html .= '<ol>';

			foreach ($otherQueryTypeTicker as $query => $occurrences)
			{
				$html .= '<li><code>'
					. \JText::sprintf('PLG_DEBUG_QUERY_TYPE_AND_OCCURRENCES', $this->highlightQuery($query), $occurrences)
					. '</code></li>';
			}
			$html .= '</ol>';
		}

		return $html;
	}

	/**
	 * Displays errors in language files.
	 *
	 * @return  string
	 */
	protected function displayLanguageFilesInError()
	{
		$html = '';

		$errorfiles = \JFactory::getLanguage()->getErrorFiles();

		if (!count($errorfiles))
		{
			$html .= '<p>' . \JText::_('JNONE') . '</p>';

			return $html;
		}

		$html .= '<ul>';

		foreach ($errorfiles as $file => $error)
		{
			$html .= '<li>' . $this->formatLink($file) . str_replace($file, '', $error) . '</li>';
		}

		$html .= '</ul>';

		return $html;
	}

	/**
	 * Display loaded language files.
	 *
	 * @return  string
	 */
	protected function displayLanguageFilesLoaded()
	{
		$html = '<ul class="debug-varlist">';

		foreach (\JFactory::getLanguage()->getPaths() as $extension => $files)
		{
			foreach ($files as $file => $status)
			{
				$html .= '<li>';

				$html .= ($status)
					? '<span class="debug-loaded"><strong>' . \JText::_('PLG_DEBUG_LANG_LOADED') . '</strong>'
					: '<span class="debug-notloaded"><strong>' . \JText::_('PLG_DEBUG_LANG_NOT_LOADED') . '</strong>';

				$html .= ' '; //: ';
				$html .= $this->formatLink($file) . '</span>';
				$html .= '</li>';
			}
		}

		$html .= '</ul>';

		return $html;
	}

	/**
	 * Display untranslated language strings.
	 *
	 * @return  string
	 */
	protected function displayUntranslatedStrings()
	{
		$stripFirst	= $this->params->get('strip-first');
		$stripPref	= $this->params->get('strip-prefix');
		$stripSuff	= $this->params->get('strip-suffix');

		$orphans = \JFactory::getLanguage()->getOrphans();

		$html = '';

		if ( ! count($orphans))
		{
			$html .= '<p>' . \JText::_('JNONE') . '</p>';

			return $html;
		}

		ksort($orphans, SORT_STRING);

		$guesses = array();

		foreach ($orphans as $key => $occurance)
		{
			if (is_array($occurance) && isset($occurance[0]))
			{
				$info = $occurance[0];
				$file = ($info['file']) ? $info['file'] : '';

				if (!isset($guesses[$file]))
				{
					$guesses[$file] = array();
				}

				// Prepare the key

				if (($pos = strpos($info['string'], '=')) > 0)
				{
					$parts	= explode('=', $info['string']);
					$key	= $parts[0];
					$guess	= $parts[1];
				}
				else
				{
					$guess = str_replace('_', ' ', $info['string']);

					if ($stripFirst)
					{
						$parts = explode(' ', $guess);
						if (count($parts) > 1)
						{
							array_shift($parts);
							$guess = implode(' ', $parts);
						}
					}

					$guess = trim($guess);

					if ($stripPref)
					{
						$guess = trim(preg_replace(chr(1) . '^' . $stripPref . chr(1) . 'i', '', $guess));
					}

					if ($stripSuff)
					{
						$guess = trim(preg_replace(chr(1) . $stripSuff . '$' . chr(1) . 'i', '', $guess));
					}
				}

				$key = trim(strtoupper($key));
				$key = preg_replace('#\s+#', '_', $key);
				$key = preg_replace('#\W#', '', $key);

				// Prepare the text
				$guesses[$file][] = '<li><span class="ky">' . $key . '</span><span class="op">=</span>"<span class="vl">' . $guess . '</span>"</li>';
			}
		}

		foreach ($guesses as $file => $keys)
		{
			$html .= '<ul class="debug-untrans debug-varlist"><li># ' . ($file ? $this->formatLink($file) : \JText::_('PLG_DEBUG_UNKNOWN_FILE')) . '</li>';
			$html .= implode("\n", $keys) . '</ul>';
		}

		return $html;
	}

	/**
	 * Simple highlight for SQL queries.
	 *
	 * @param   string  $sql  The query to highlight
	 *
	 * @return  string
	 *
	 * @since   2.5
	 */
	protected function highlightQuery($sql)
	{
		$newlineKeywords = '#\b(FROM|LEFT|INNER|OUTER|WHERE|SET|VALUES|ORDER|GROUP|HAVING|LIMIT|ON|AND|CASE)\b#i';

		$sql = htmlspecialchars($sql, ENT_QUOTES);

		$sql = preg_replace($newlineKeywords, '<br />&#160;&#160;\\0', $sql);

		$regex = array(

			// Tables are identified by the prefix
			'/(=)/'
			=> '<b class="dbgOperator">$1</b>',

			// All uppercase words have a special meaning
			'/(?<!\w|>)([A-Z_]{2,})(?!\w)/x'
			=> '<span class="dbgCommand">$1</span>',

			// Tables are identified by the prefix
			'/(' . \JFactory::getDbo()->getPrefix() . '[a-z_0-9]+)/'
			=> '<span class="dbgTable">$1</span>'

		);

		$sql = preg_replace(array_keys($regex), array_values($regex), $sql);

		$sql = str_replace('*', '<b class="dbgStar">*</b>', $sql);

		return $sql;
	}

	/**
	 * Render the backtrace.
	 *
	 * Stolen from JError to prevent it's removal.
	 *
	 * @param   integer  $error  The error
	 * @return  string  Contents of the backtrace
	 */
	protected function renderBacktrace($error)
	{
		$backtrace = $error->getTrace();

		$html = '';

		if (is_array($backtrace))
		{
			$j = 1;

			$html .= '<table>';

			$html .= '<caption>Call stack</caption>';

			$html .= '<thead>';
			$html .= '<tr>';
			$html .= '<th scope="col">#</th>';
			$html .= '<th scope="col">Function</th>';
			$html .= '<th scope="col">Location</th>';
			$html .= '</tr>';
			$html .= '</thead>';
			$html .= '<tbody>';

			for ($i = count($backtrace) - 1; $i >= 0; $i--)
			{
				$link = '&#160;';

				if (isset($backtrace[$i]['file']))
				{
					$link = $this->formatLink($backtrace[$i]['file'], $backtrace[$i]['line']);
				}

				$html .= '<tr>';
				$html .= '<td>' . $j . '</td>';

				if (isset($backtrace[$i]['class']))
				{
					$html .= '<td>' . $backtrace[$i]['class'] . $backtrace[$i]['type'] . $backtrace[$i]['function'] . '()</td>';
				}
				else
				{
					$html .= '<td>' . $backtrace[$i]['function'] . '()</td>';
				}

				$html .= '<td>' . $link . '</td>';

				$html .= '</tr>';
				$j++;
			}

			$html .= '</tbody>';
			$html .= '</table>';
		}

		return $html;
	}

	/**
	 * Replaces the Joomla! root with "JROOT" to improve readability.
	 * Formats a link with a special value xdebug.file_link_format
	 * from the php.ini file.
	 *
	 * @param   string  $file  The full path to the file.
	 * @param   string  $line  The line number.
	 *
	 * @return  string
	 *
	 * @since   2.5
	 */
	protected function formatLink($file, $line = '')
	{
		$link = str_replace(JPATH_ROOT, 'ROOT', $file);
		$link .= ($line) ? ':' . $line : '';

		if ($this->linkFormat)
		{
			$href = $this->linkFormat;
			$href = str_replace('%f', $file, $href);
			$href = str_replace('%l', $line, $href);

			$html = '<a href="' . $href . '">' . $link . '</a>';
		}
		else
		{
			$html = $link;
		}

		return $html;
	}
}
