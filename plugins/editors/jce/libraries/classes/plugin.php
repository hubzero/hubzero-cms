<?php
/**
* @version		$Id: plugin.php 108 2009-06-21 19:24:12Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined( '_JEXEC') or die( 'Restricted access');

// Needed for cyrillic languages?
header("Content-type: text/html; charset=utf-8");

require_once( JCE_LIBRARIES .DS. 'classes' .DS. 'editor.php');
require_once( JCE_LIBRARIES .DS. 'classes' .DS. 'utils.php');

/**
 * JCE class
 *
 * @static
 * @package		JCE
 * @since	1.5
 */

class JContentEditorPlugin extends JContentEditor 
{
	/*
	*  @var array
	*/
	var $plugin = null;
	/*
	*  @var varchar
	*/
	var $url = array();
	/*
	*  @var varchar
	*/
	var $request = null;
	/*
	*  @var array
	*/
	var $scripts = array();
	/*
	*  @var array
	*/
	var $styles = array();
	/*
	*  @var array
	*/
	var $head = array();
	/*
	*  @var array
	*/
	var $alerts = array();
	/**
	* Constructor activating the default information of the class
	*
	* @access	protected
	*/
	function __construct()
	{
		// Call parent
		parent::__construct();
		
		$db =& JFactory::getDBO();
		
		$plugin = JRequest::getVar('plugin');
		
		if ($plugin) {
			$query = "SELECT id"
			. " FROM #__jce_plugins"
			. " WHERE name = '". $plugin ."'"
			;
			
			$db->setQuery($query);
			$id = $db->loadResult();
			
			$this->plugin = new JObject();
			
			$this->plugin->name 	= $plugin;
			$this->plugin->id 		= $id;
			$this->plugin->params 	= $this->getPluginParams();
			$this->plugin->type 	= JRequest::getVar('type', 'standard');	
			
			define('JCE_PLUGIN', JCE_PLUGINS . DS . $plugin);
		} else {
			die(JError::raiseError(403, JText::_('Access Forbidden')));
		}
	}
	/**
	 * Returns a reference to a editor object
	 *
	 * This method must be invoked as:
	 * 		<pre>  $browser = &JCE::getInstance();</pre>
	 *
	 * @access	public
	 * @return	JCE  The editor object.
	 * @since	1.5
	 */
	function &getInstance() {
		static $instance;

		if (!is_object($instance)) {
			$instance = new JContentEditorPlugin();
		}
		return $instance;
	}
	/**
	 * Get a plugin by key or current plugin
	 * @return object Plugin
	 * @param string $key[optional]
	 */
	function getPlugin($key = '') 
	{
		if ($key) {
			if (isset($this->plugin->$key)) {
				return $this->plugin->$key;
			}
		}
		return $this->plugin;
	}
	/**
	 * Return the plugin parameter object
	 *
	 * @access 			public
	 * @param string	The plugin
	 * @return 			The parameter object
	*/
	function getPluginParams() 
	{				
		if (isset($this->plugin->params)) {
			return $this->plugin->params;
		}
		$params = $this->filterParams($this->group->params, $this->plugin->name);				
		return new JParameter($params);
	}
	/**
	 * Get a plugin parameter
	 * 
	 * @return string Parameter
	 * @param string $key Parameter key
	 * @param string $default[optional] Default value
	 */
	function getPluginParam($key, $default = '') 
	{
		$params = $this->getPluginParams();
		return $this->cleanParam($params->get($key, $default));
	}
	/**
	 * Get a group parameter from plugin and/or editor parameters
	 *
	 * @access 			public
	 * @param string	The parameter name
	 * @param string	The default value
	 * @return 			string
	*/
	function getSharedParam($param, $default = '')
	{
		$e_params 	= $this->getEditorParams();
		$p_params 	= $this->getPluginParams();
		
		$ret = $p_params->get($this->plugin->name . '_' . $param, '');

		if ($ret == '') {			
			$ret = $e_params->get('editor_' . $param, $default);
		}
		return $this->cleanParam($ret);
	}
	/**
	 * Load current plugin language file
	 */
	function loadPluginLanguage()
	{
		$this->loadLanguage('com_jce_'. trim($this->plugin->name));
	}
	/**
	 * Load the language files for the current plugin
	*/
	function loadLanguages() 
	{
		$this->loadLanguage('com_jce');	
		$this->loadPluginLanguage();
	}
	/**
	 * Return the curernt language code. Override of JContentEditor::getLanguageDir
	 *
	 * @return language code
	*/
	function getLanguageDir() 
	{
		/* $language =& JFactory::getLanguage();
		return $language->isRTL() ? 'rtl' : 'ltr';
		We can only support ltr at them moment...!
		*/
		return 'ltr';
	}
	/**
	 * Named wrapper to check access to a feature
	 *
	 * @access 			public
	 * @param string	The feature to check, eg: upload
	 * @param string	The defalt value
	 * @return 			string
	*/
	function checkAccess($option, $default = '') 
	{
		return $this->getSharedParam($option, $default);
	}
	/**
	 * Check the user is in an authorized group
	 * Check the users group is authorized to use the plugin
	 *
	 * @access 			public
	 * @return 			boolean
	*/
	function checkPlugin() 
	{
		if ($this->isSuperAdmin()) {
			return true;
		}
		if ($this->checkUser()) {	
			return in_array($this->plugin->id, explode(',', $this->group->plugins));
		}
		return false;
	}
	/**
	 * Returns a an array of Help topics
	 *
	 * @access	public
	 * @return	Array
	 * @since	1.5
	 */
	function getHelpTopics() 
	{
		// Load plugin xml file
		$result = '';
		if ($this->plugin->type == 'manager') {
			$file = JCE_LIBRARIES .DS. "xml" .DS. "help" .DS. "manager.xml";			
			$result .= '<dl><dt><span>'. JText::_('MANAGER HELP') .'<span></dt>';		
			if (file_exists($file)) {				
				$xml =& JFactory::getXMLParser('Simple');
				if ($xml->loadFile($file)) {
					$root =& $xml->document;									
					if ($root) {
						foreach ($root->children() as $topic) {
							$result .= '<dd id="'. $topic->attributes('key') .'"><a href="javascript:;" onclick="helpDialog.loadFrame(this.parentNode.id)">'. JText::_($topic->attributes('title')) .'</a></dd>';
						}
					}
				}
			}
			$result .= '</dl>';
		}
		
		$file = JCE_PLUGIN .DS. $this->plugin->name. ".xml";			
		$result .= '<dl><dt><span>'. JText::_('HELP') .'<span></dt>';
		
		if (file_exists($file)) {
			$xml =& JFactory::getXMLParser('Simple');
			
			if ($xml->loadFile($file)) {
				$root 	=& $xml->document;				
				$topics = $root->getElementByPath('help');
				
				if ($topics) {
					foreach ($topics->children() as $topic) {
						$result .= '<dd id="'. $topic->attributes('key') .'"><a href="javascript:;" onclick="helpDialog.loadFrame(this.parentNode.id)">'. trim(JText::_($topic->attributes('title'))) .'</a></dd>';
					}
				}
			}
		}
		$result .= '</dl>';
		return $result;
	}
	/**
	 * Add an alert array to the stack 
	 * 
	 * @param object $class[optional] Alert classname
	 * @param object $title[optional] Alert title
	 * @param object $text[optional]  Alert text
	 */
	function addAlert($class = 'info', $title = '', $text = '') 
	{
		$this->alerts[] = array(
			'class' => $class,
			'title'	=> $title,
			'text'	=> $text
		);
	}
	/**
	 * Get current alerts
	 * @return object Alerts as object
	 */
	function getAlerts() 
	{
		return $this->json_encode($this->alerts);
	}
	/**
	 * Returns a JCE resource url
	 *
	 * @access	public
	 * @param	string 	The path to resolve eg: libaries
	 * @param	boolean Create a relative url
	 * @return	full url
	 * @since	1.5
	 */
	function url($path) 
	{
		// Check if value is already stored
		if (!array_key_exists($path, $this->url)) {
			switch($path) {
				// JCE root folder
				case 'jce':
					$pre = 'plugins/editors/jce';
					break;
				// JCE libraries resource folder
				default:
				case 'libraries':
					$pre = 'plugins/editors/jce/libraries';
					break;
				// JCE skin resource folder
				case 'skins':
					$pre = 'plugins/editors/jce/tiny_mce/themes/advanced/skins/dialog/' .$this->getSkin();
					break;
				// TinyMCE folder
				case 'tiny_mce':
					$pre = 'plugins/editors/jce/tiny_mce';
					break;
				// JCE current plugin folder
				case 'plugins':
					$pre = 'plugins/editors/jce/tiny_mce/plugins/' .$this->plugin->name;
					break;
				// Joomla! media folder
				case 'extensions':
					$pre = 'plugins/editors/jce/tiny_mce/plugins/' .$this->plugin->name. '/extensions';
					break;
				// Joomla! folders
				case 'joomla':
					$pre = '';
					break;
				case 'media':
					$pre = 'media/system';
					break;
			}
			// Store url
			$this->url[$path] =  JURI::root(true) .'/'. $pre;	
		}	
		return $this->url[$path];
	}
	/**
	 * Upload form action url
	 *
	 * @access	public
	 * @param	string 	The target action file eg: upload.php
	 * @return	Joomla! component url
	 * @since	1.5
	 */
	function getFormAction() 
	{		
		$session 	=& JFactory::getSession();
		$file 		= JRequest::getVar('file', $this->plugin->name);
		return JURI::base(true) .'/index.php?option=com_jce&amp;task=plugin&amp;plugin=' . $this->plugin->name . '&amp;file=' . $file . '&amp;method=form&amp;' . $session->getName() .'='. $session->getId(); 
	}
	/**
	 * Convert a url to path
	 *
	 * @access	public
	 * @param	string 	The url to convert
	 * @return	Full path to file
	 * @since	1.5
	 */
	function urlToPath($url) 
	{
		jimport('joomla.filesystem.path');
		$bool = strpos($url,  JURI::root()) === false;
		return Utils::makePath(JPATH_SITE, JPath::clean(str_replace(JURI::root($bool), '', $url)));
	}
	function removeScript($script) 
	{
		unset($this->scripts[$script]);
	}
	function removeCss($css) 
	{
		unset($this->styles[$css]);
	}
	/**
	 * Loads a javascript file
	 *
	 * @access	public
	 * @param	string 	The file to load including path eg: libaries.manager
	 * @param	boolean Debug mode load src file
	 * @return	echo script html
	 * @since	1.5
	 */
	function script($files, $root = 'libraries') 
	{		
		//settype($files, 'array');
		$files = (array) $files;
		foreach ($files as $file) {
			$parts = explode('.', $file);
			$parts = preg_replace('#[^A-Z0-9-_]#i', '', $parts);
			
			$file	= array_pop($parts);
			$path	= implode('/', $parts);
			
			if ($path) {
				$path .= '/';
			}

			// Different path for tiny_mce file
			if ($root != 'tiny_mce') {
				$file = 'js/' .$file;
			}
			
			if (!in_array($this->url($root). "/" . $path.$file, $this->scripts)) {
				$this->scripts[] = $this->url($root). "/" . $path.$file; 
				
				// hack in JCE Tips
				if ((!$root || $root == 'libraries') && $file == 'js/plugin') {
					$this->scripts[] = $this->url($root). "/" . $path.'js/tips';
				}
			}
		}  
    }
	/**
	 * Loads a css file
	 *
	 * @access	public
	 * @param	string The file to load including path eg: libaries.manager
	 * @param	string Root folder
	 * @return	echo css html
	 * @since	1.5
	 */
	function css($files, $root = 'libraries') 
	{
		//settype($files, 'array');
		$files = (array) $files;
		foreach ($files as $file) {
			$parts 	= explode('.', $file);
			$parts 	= preg_replace('#[^A-Z0-9-_]#i', '', $parts);
			
			$file	= array_pop($parts);
			$path	= implode('/', $parts);
			
			if ($path) {
				$path .= '/';
			}
			
			// Different path for tiny_mce file
			if ($root != 'tiny_mce') {
				$file = 'css/' .$file;
			}		
			
			$url = $this->url($root). "/" .$path.$file;
			if (!in_array($url, $this->styles)) {
				$this->styles[] = $url; 
			}
		}
	}
	/**
	 * Print <script> html
	 *
	 * @access	public
	 * @return	echo <script> html
	 * @since	1.5
	 */
	function printScripts() 
	{
		$version 	= $this->getVersion();
		$stamp		= preg_match('/\d+/', $version) ? '?version='. $version : '';
		foreach ($this->scripts as $script) {
			echo "\t<script type=\"text/javascript\" src=\"" . $script . ".js". $stamp ."\"></script>\n";		
		}
	}
	/**
	 * Print <link> css html with browser detection
	 *
	 * @access	public
	 * @return	echo <link> html
	 * @since	1.5
	 */
	function printCss() 
	{
		jimport('joomla.environment.browser');
		$browser 	= &JBrowser::getInstance();
		$version 	= $this->getVersion();
		$stamp		= preg_match('/\d+/', $version) ? '?version='. $version : '';
		foreach ($this->styles as $style) {
			echo "\t<link href=\"" . $style . ".css". $stamp ."\" rel=\"stylesheet\" type=\"text/css\" />\n";
			if ($browser->getBrowser() == 'msie') {
				// Version specific css file
				$file =  $style. '_ie' .$browser->getMajor(). '.css';
				if (file_exists($this->urlToPath($file))) {
					echo "\t<link href=\"" . $file . "\" rel=\"stylesheet\" type=\"text/css\" />\n";
				} else {
					// All versions
					$file =  $style. '_ie.css';
					if (file_exists($this->urlToPath($file))) {
						echo "\t<link href=\"" . $file . "\" rel=\"stylesheet\" type=\"text/css\" />\n";
					}
				}
			}
		}
	}
	/**
	 * Setup head data
	 *
	 * @access	public
	 * @since	1.5
	 */
	function setHead($data) 
	{
		if (is_array($data)) {
			$this->head = array_merge($this->head, $data);
		} else {
			$this->head[] = $data;
		}
	}
	/**
	 * Print additional head html.
	 *
	 * @access	public
	 * @return	echo <head> html
	 * @since	1.5
	 */
	function printHead() {
		foreach ($this->head as $head) {
			echo "\t". $head ."\n";		
		}
	}
	/**
	 * Returns an image url
	 *
	 * @access	public
	 * @param	string 	The file to load including path and extension eg: libaries.image.gif
	 * @return	Image url
	 * @since	1.5
	 */
	function image($image, $root = 'libraries') {
		$parts = explode('.', $image);
		$parts = preg_replace('#[^A-Z0-9-_]#i', '', $parts);
			
		$ext	= array_pop($parts);
		$name	= array_pop($parts);
		$path	= implode('/', $parts);		
			
		return $this->url($root). "/" .$path. "/img/" . $name . "." . $ext;
	}
	/**
	 * Load a plugin extension
	 *
	 * @access	public
	 * @since	1.5
	 */
	function getExtensions($plugin, $extension = '') {
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		$path 		= JCE_PLUGINS.DS.$plugin.DS.'extensions';
		$extensions = array();	
		
		if (JFolder::exists($path)) {
			$types = JFolder::folders($path);
			
			foreach ($types as $type) {
				if ($extension) {
					if (JFile::exists($path.DS.$type.DS.$extension.'.xml') && JFile::exists($path.DS.$type.DS.$extension.'.php')) {
						$object = new StdClass();
						
						$object->folder 	= $type;
						$object->extension 	= $extension;
						
						$extensions[] = $object;
					}
				} else {
					$files = JFolder::files($path.DS.$type, '\.xml$');
					foreach ($files as $file) {
						$object = new StdClass();
						$object->folder = $type;
						$name = JFile::stripExt($file);
						if (JFile::exists($path.DS.$type.DS.$name.'.php')) {
							$object->extension = $name;
						}
						$extensions[] = $object;
					}
				}
				
			}
		}
		return $extensions;
	}
	/**
	 * Load & Call an extension
	 *
	 * @access	public
	 * @since	1.5
	 */
	function loadExtensions($base_dir = '', $plugin = '', $base_path = JCE_PLUGIN) {		
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		if (!$plugin) {
			$plugin = $this->plugin->name;
		}
		// Create extensions path
		$base = $base_path .DS. 'extensions';
		// Get installed extensions
		$extensions = $this->getExtensions($plugin);

		$result = array();
		
		if (!empty($extensions)) {
			foreach ($extensions as $extension) {
				$name 	= $extension->extension;
				$folder = $extension->folder;

				$path = $base .DS. $folder;
				$root = $path .DS. $name .'.php';
				
				if (file_exists($root)) {
					// Load root extension file
					require_once($root);
					// Load Extension language file
					$this->loadLanguage('com_jce_'. $plugin .'_'. $name, JPATH_SITE);
					
					// Load javascript
					$js = JFolder::files($path .DS. $name .DS. 'js', '\.(js)$');
					if (!empty($js)) {
						foreach ($js as $file) {
							$this->script(array($folder .'.'. $name .'.'. JFile::stripExt($file)), 'extensions');
						}
					}
					// Load css
					$css = JFolder::files($path .DS. $name .DS. 'css', '\.(css)$');
					if (!empty($css)) {
						foreach ($css as $file) {
							$this->css(array($folder .'.'. $name .'.'. JFile::stripExt($file)), 'extensions');
						}
					}
					$self = &$this;
					// Call as function, eg joomlalinks() to array
					$result[$name] = call_user_func($name, $self);
				}
			}
		}
		// Return array
		return $result;
	}
	/**
	 * Setup an ajax function
	 *
	 * @access public
	 * @param array		An array containing the function and object
	*/
	function setXHR($function) {
		if (is_array($function)) {
			$this->request[$function[1]] = array(
				'fn' => array($function[0], $function[1])
			);
		} else {
			$this->request[$function] = array(
				'fn' => $function
			);
		}
	}
	/**
	 * Returns a reference to a json object
	 *
	 * This method must be invoked as:
	 * 		<pre>  $json =& JContentEditor::getJson();</pre>
	 *
	 * @access	public
	 * @return	json  a json services object.
	 * @since	1.5
	 */
	function &getJson() {
		static $json;
		if (!is_object($json)) {
			if (!class_exists('Services_JSON')) {
				include_once(dirname(__FILE__) .DS. 'json' .DS. 'json.php');
			}
			$json = new Services_JSON();
		}
		return $json;
	}
	/**
	 * JSON Encode wrapper for PHP function or PEAR class
	 *
	 * @access public
	 * @param string	The string to encode
	 * @return			The json encoded string
	*/
	function json_encode($string) {
		if (function_exists('json_encode')) {
			return json_encode($string);
		} else {
			$json =& $this->getJson();
			return $json->encode($string);
		}
	}
	/**
	 * JSON Decode wrapper for PHP function or PEAR class
	 *
	 * @access public
	 * @param string	The string to decode
	 * @return			The json decoded string
	*/
	function json_decode($string) {
		if (function_exists('json_decode')) {
			return json_decode($string);
		} else {
			$json =& $this->getJson();
			return $json->decode($string);
		}
	}
	/**
	 * Process an ajax call and return result
	 *
	 * @access public
	 * @return string
	*/
	function processXHR($array = false) {										
		$json 	= JRequest::getVar('json', '', 'POST', 'STRING', 2);
		$method = JRequest::getVar('method', '');
						
		if ($method == 'form' || $json) {			
			$GLOBALS['xhrErrorHandlerText'] = '';
			set_error_handler('_xhrErrorHandler');
		
			$result = null;
			$error	= null;
			
			$fn 	= JRequest::getVar('action');			
			$args 	= array();
				
			if ($json) {
				$json 	= $this->json_decode($json);
				$fn 	= $json->fn;
				$args 	= $json->args;
			}
			$func = $this->request[$fn]['fn'];

			if (array_key_exists($fn, $this->request)) {
				if (!is_array($args)) {
					$result = call_user_func($func, $args);
				} else {
					$result = call_user_func_array($func, $args);
				}
				if (!empty($GLOBALS['xhrErrorHandlerText'])) {			
					$error = 'PHP Error Message: ' . addslashes($GLOBALS['xhrErrorHandlerText']);
				}
			} else {
				if ($fn) {
					$error = 'Cannot call function '. addslashes($fn) .'. Function not registered!';
				} else {
					$error = 'No function call specified!';
				}
			}
			$output = array(
				"result" 	=> $result,
				"error" 	=> $error
			);
			if ($json) {
				header('Content-Type: text/json');
				header('Content-Encoding: UTF-8');
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");
			}
			exit($this->json_encode($output));
		}
	}
}
/**
 * XHR error handler function
 *
 * @param string The error code
 * @param string The error string
 * @param string The file producing the error
 * @param string The line number of the error
 * @access private
 * @return error string
*/
function _xhrErrorHandler($num, $string, $file, $line) 
{
	$reporting = error_reporting();
	if (($num & $reporting) == 0) return;
		
	switch($num) {
	case E_NOTICE :
		$type = "NOTICE";
		break;
	case E_WARNING :
		$type = "WARNING";
		break;
	case E_USER_NOTICE :
		$type = "USER NOTICE";
		break;
	case E_USER_WARNING :
		$type = "USER WARNING";
		break;
	case E_USER_ERROR :
		$type = "USER FATAL ERROR";
		break;
	case E_STRICT :
		return;
		break;
	default:
			$type = "UNKNOWN: ". $num;
	}
	$GLOBALS['xhrErrorHandlerText'] .= $type . $string ."Error in line ". $line ." of file ".$file;
}
?>