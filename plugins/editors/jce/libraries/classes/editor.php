<?php
/**
* @version		$Id: editor.php 109 2009-06-21 19:24:41Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL 2
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined('_JEXEC') or die('Restricted access');

/**
 * JCE class
 *
 * @static
 * @package		JCE
 * @since	1.5
 */

class JContentEditor extends JObject
{
	/*
	 * @var varchar
	 */
	var $version = '1.5.7.4';
	/*
	*  @var varchar
	*/
	var $site_url = null;
	/*
	*  @var varchar
	*/
	var $group = null;
	/*
	 *  @var object
	 */
	var $params = null;
	/*
	 *  @var array
	 */
	var $plugins = array();
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
	var $css = array();
	/*
	*  @var boolean
	*/
	var $_debug = false;
	/**
	* Constructor activating the default information of the class
	*
	* @access	protected
	*/
	function __construct($config = array())
	{
		global $mainframe;
		
		$this->setProperties($config);
		
		// Get user group
		$this->group 	= $this->getUserGroup();
		// Get editor and group params
		$this->params	= $this->getEditorParams();
	}
	/**
	 * Returns a reference to a editor object
	 *
	 * This method must be invoked as:
	 * 		<pre>  $browser = &JContentEditor::getInstance();</pre>
	 *
	 * @access	public
	 * @return	JCE  The editor object.
	 * @since	1.5
	 */
	function &getInstance()
	{
		static $instance;

		if (!is_object($instance)) {
			$instance = new JContentEditor();
		}
		return $instance;
	}
	/**
	 * Get the current version
	 * @return Version
	 */
	function getVersion()
	{
		// remove dots and return version
		return str_replace('.', '', $this->version);
	}
	/**
	 * Get the current users group if any
	 *
	 * @access public
	 * @return group or null
	*/
	function getUserGroup()
	{
		if($this->group){
			return $this->group;
		}
		
		global $mainframe;

		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$option = JRequest::getCmd('option');
		
		$query = 'SELECT *'
		. ' FROM #__jce_groups'
		. ' WHERE published = 1'
		. ' ORDER BY ordering ASC'
		;
		$db->setQuery($query);
		$groups = $db->loadObjectList();
		
		if ($option == 'com_jce') {
			$cid = JRequest::getVar('cid');
			
			if ($cid) {
				$query = 'SELECT *'
				. ' FROM #__components'
				. ' WHERE id = '.(int) $cid
				;
				$db->setQuery($query);
				$component = $db->loadObject();
				
				$option = $component->option;	
			} else {
				$option = '';
			}
		}
		
		$area = $mainframe->isAdmin() ? 2 : 1;
		
		foreach ($groups as $group) {
			// Set area default as Front-end / Back-end
			if (!isset($group->area) || $group->area == '') {
				$group->area = 0;
			}
			
			if ($group->area == $area || $group->area == 0) {
				$components = in_array($option, explode(',', $group->components));
				// Check user			
				if (in_array($user->id, explode(',', $group->users))) {
					if ($group->components) {
						if ($components) {
							return $group;
						}
					} else {
						return $group;
					}
				}
				// Check usertype
				if (in_array($user->gid, explode(',', $group->types))) {
					// Check components
					if ($group->components) {
						if ($components) {
							return $group;
						}
					} else {
						return $group;
					}
				}
				// Check components only
				if ($group->components && $components) {
					return $group;
				}
			}
		}
		return null;
	}
	/**
	 * Get the Super Administrator status
	 *
	 * Determine whether the user is a Super Administrator
	 *
	 * @return boolean
	*/
	function isSuperAdmin()
	{
		$user =& JFactory::getUser();
		return (strtolower($user->usertype) == 'superadministrator' || strtolower($user->usertype) == 'super administrator' || $user->gid == 25) ? true : false;	
    }
	/**
	 * Filter (remove) a parameter from a parameter string
	 * @return string Filtered parameter String
	 * @param object $params
	 * @param object $key
	 */
	function filterParams($params, $key)
	{
		$params = explode("\n", $params);					
		$return = array();
		
		foreach($params as $param) {
			if (preg_match('/'.$key.'/i', $param)) {
				$return[] = $param;
			}
		}
		return implode("\n", $return);
	}
	/**
	 * Return the JCE Editor's parameters
	 *
	 * @return object
	*/
	function getEditorParams()
	{		
		$db	=& JFactory::getDBO();
		
		if (isset($this->params)) {
			return $this->params;
		}
		
		$e_params = '';
		$g_params = '';
		
		$query = 'SELECT params FROM #__plugins'
		. ' WHERE element = '. $db->Quote('jce')
		. ' AND folder = '. $db->Quote('editors')
		. ' AND published = 1' 
		. ' LIMIT 1'
		;
		$db->setQuery($query);
		
		$e_params = $db->loadResult();
		// check if group params available
		if ($this->group) {
			$g_params = $this->filterParams($this->group->params, 'editor');
		}

		return new JParameter($e_params . $g_params);
	}
    /**
     * Get an Editor Parameter by key
     * 
     * @return string Editor Parameter
     * @param string $key The parameter key
     * @param string $default[optional] Value if no result
     */
	function getEditorParam($key, $default = '', $fallback = '')
	{		
		$params = $this->getEditorParams();
		return $this->getParam($params, $key, $default, $fallback);
	}
	/**
	 * Return the plugin parameter object
	 *
	 * @access 			public
	 * @param string	The plugin
	 * @return 			The parameter object
	*/
	function getPluginParams($plugin)
	{						
		$params = '';
		if ($this->group) {
			$params = $this->filterParams($this->group->params, $plugin);
		}				
		return new JParameter($params);
	}
	/**
	 * Get a group parameter from plugin and/or editor parameters
	 *
	 * @access 			public
	 * @param string	The parameter name
	 * @param string	The default value
	 * @return 			string
	*/
	function getSharedParam($plugin, $param, $default = '')
	{
		$e_params 	= $this->getEditorParams();
		$p_params 	= $this->getPluginParams($plugin);
		
		$ret = $p_params->get($plugin . '_' . $param, '');

		if ($ret == '') {			
			$ret = $e_params->get('editor_' . $param, $default);
		}
		return $this->cleanParam($ret);
	}
	/**
	 * Add a plugin to the plugins array
	 * 
	 * @return null
	 * @param array $plugins
	 */
	function addPlugins($plugins)
	{
		return $this->addKeys($this->plugins, $plugins);
	}
	
	/**
	 * Return a list of published JCE plugins
	 *
	 * @access public
	 * @return string list
	*/
	function getPlugins()
	{		
		$db	=& JFactory::getDBO();
		
		$plugins = array();
		
		if ($this->group) {			
			$query = "SELECT name"
			. " FROM #__jce_plugins"
			. " WHERE published = 1"
			. " AND type = ".$db->Quote('plugin')
			. " AND id IN (". $this->group->plugins. ")"
			;
			
			$db->setQuery($query);
			$plugins = $db->loadResultArray();
		}
	   	$plugins = array_merge($plugins, $this->plugins);
		
		foreach ($plugins as $plugin) {			
			$path = JPATH_PLUGINS.DS.'editors'.DS.'jce'.DS.'tiny_mce'.DS.'plugins'.DS.$plugin;
			
			$language = $this->getLanguage();
			
			if (!JFolder::exists($path) || !JFile::exists($path.DS.'editor_plugin.js')) {
				$this->removeKeys($plugins, $plugin);
			}
			
			if ($language != 'en') {
				if (JFile::exists($path.DS.'langs'.DS.'en.js') && !JFile::exists($path.DS.'langs'.DS.$language.'.js')) {
					$this->removeKeys($plugins, $plugin);
					JError::raiseNotice('SOME_ERROR_CODE', sprintf(JText::_('PLUGINREMOVEDLANGMISSING'), 'plugins/editors/jce/tiny_mce/plugins/'.$plugin.'langs/'.$language.'.js') . ' - ' . ucfirst($plugin));
				}
			}
		}
		
		return $plugins;
	}
	/**
	 * Get a list of editor font families
	 * 
	 * @return string font family list
	 * @param string $add Font family to add
	 * @param string $remove Font family to remove
	 */
	function getEditorFonts($add, $remove)
	{
		
		$add 	= explode(';', $this->getEditorParam('editor_theme_advanced_fonts_add', ''));
		$remove = preg_split('/[;,]+/', $this->getEditorParam('editor_theme_advanced_fonts_remove', ''));
		
		// Default font list
		$fonts = array(
		'Andale Mono=andale mono,times',
		'Arial=arial,helvetica,sans-serif',
		'Arial Black=arial black,avant garde',
		'Book Antiqua=book antiqua,palatino',
		'Comic Sans MS=comic sans ms,sans-serif',
		'Courier New=courier new,courier',
		'Georgia=georgia,palatino',
		'Helvetica=helvetica',
		'Impact=impact,chicago',
		'Symbol=symbol',
		'Tahoma=tahoma,arial,helvetica,sans-serif',
		'Terminal=terminal,monaco',
		'Times New Roman=times new roman,times',
		'Trebuchet MS=trebuchet ms,geneva',
		'Verdana=verdana,geneva',
		'Webdings=webdings',
		'Wingdings=wingdings,zapf dingbats'
		);
		
		if (count($remove)) {
			foreach($fonts as $key => $value) {
				foreach($remove as $gone) {
					if ($gone) {
						if (preg_match('/^'. $gone .'=/i', $value)) {
							// Remove family
							unset($fonts[$key]);
						}
					}
				}
			}
		}
		foreach($add as $new) {
		// Add new font family
			if (preg_match('/([^\=]+)(\=)([^\=]+)/', trim($new)) && !in_array($new, $fonts)) {
				$fonts[] = $new;
			}
		}
		natcasesort($fonts);
		return implode(';', $fonts);
	}
	/**
	 * Return the curernt language code
	 *
	 * @access public
	 * @return language code
	*/
	function getLanguageDir()
	{
		$language =& JFactory::getLanguage();
		return $language->isRTL() ? 'rtl' : 'ltr';
	}
	/**
	 * Return the curernt language code
	 *
	 * @access public
	 * @return language code
	*/
	function getLanguageTag()
	{
		$language =& JFactory::getLanguage();
		if ($language->isRTL()) {
			return 'en-GB';
		}
		return $language->getTag();
	}
	/**
	 * Return the curernt language code
	 *
	 * @access public
	 * @return language code
	*/
	function getLanguage()
	{
		$tag = $this->getLanguageTag();
		if (file_exists(JPATH_SITE .DS. 'language' .DS. $tag .DS. $tag .'.com_jce.xml')) {
			return substr($tag, 0, strpos($tag, '-'));
		}
		return 'en';
	}
	/**
	 * Load a language file
	 * 
	 * @param string $prefix Language prefix
	 * @param object $path[optional] Base path
	 */
	function loadLanguage($prefix, $path = JPATH_SITE)
	{
		$language =& JFactory::getLanguage();		
		$language->load($prefix, $path);
	}
	/**
	 * Return the current site template name
	 *
	 * @access public
	*/
	function getSiteTemplate()
	{
		$db =& JFactory::getDBO();
		
		$query = 'SELECT template'
		. ' FROM #__templates_menu'
		. ' WHERE client_id = 0'
		. ' AND menuid = 0'
		;
		
		$db->setQuery($query);
		
		return $db->loadResult();
	}
	function getSkin()
	{
		return $this->params->get('editor_inlinepopups_skin', 'clearlooks2');
	}
	/**
	 * Remove keys from an array
	 * 
	 * @return $array by reference
	 * @param arrau $array Array to edit
	 * @param array $keys Keys to remove
	 */
	function removeKeys(&$array, $keys)
	{		
		if (!is_array($keys)) {
			$keys = array($keys);
		}
		$array = array_diff($array, $keys);
	}
	/**
	 * Add keys to an array
	 *
	 * @return The string list with added key or the key
	 * @param string	The array
	 * @param string	The keys to add
	*/
	function addKeys(&$array, $keys)
	{
		if (!is_array($keys)) {
			$keys = array($keys);
		}
		$array = array_unique(array_merge($array, $keys));
	}
	/**
	 * Remove linebreaks and carriage returns from a parameter value
	 *
	 * @return The modified value
	 * @param string	The parameter value
	*/
	function cleanParam($param)
	{
		return trim(preg_replace('/\n|\r|\t(\r\n)[\s]+/', '', $param));
	}
	/**
	 * Get a JCE editor or plugin parameter
	 *
	 * @param object	The parameter object
	 * @param string	The parameter object key
	 * @param string	The parameter default value
	 * @param string	The parameter default value
	 * @access public
	 * @return The parameter
	*/
	function getParam($params, $key, $p, $t = '')
	{		
		$v = JContentEditor::cleanParam($params->get($key, $p));
		return ($v == $t) ? '' : $v;
	}
	/**
	 * Return a string of JCE Commands to be removed
	 *
	 * @access public
	 * @return The string list
	*/
	function getRemovePlugins()
	{
		$db =& JFactory::getDBO();
		
		$query = "SELECT name"
        . "\n FROM #__jce_plugins"
        . "\n WHERE type = 'command'"
		. "\n AND published = 0"
        ;
		
		$db->setQuery($query);
		
		$remove = $db->loadResultArray();
		if ($remove) {
			return implode(',', $remove);
		}else{
			return '';
		}
	}
	/**
	 * Return a list of icons for each JCE editor row
	 *
	 * @access public
	 * @param string	The number of rows
	 * @return The row array
	*/
	function getRows()
	{
		$db =& JFactory::getDBO();
		
		$rows 	= array();
		if ($this->group) {
			// Get all plugins that are in the group rows list
			$query = "SELECT id, icon"
			. " FROM #__jce_plugins"
			. " WHERE published = 1"
			. " AND id IN (". str_replace(';', ',', $this->group->rows) .")"
			;
			
			$db->setQuery($query);
			
			$icons 	= $db->loadObjectList();						
			$lists 	= explode(';', $this->group->rows);
			
			if ($icons) {
				for($i=1; $i<=count($lists); $i++) {
					$x = $i - 1;
					$items 	= explode(',', $lists[$x]);
					$result = array();
					// I'm sure you can use array_walk for this but I just can't figure out how!	
					foreach($items as $item) {
						// Add support for spacer
						if ($item == '00') {
							$result[] = '|';
						}else{
							foreach($icons as $icon) {
								if ($icon->id == $item) {
									$result[] = $icon->icon;
								}
							}
						}		
					}
					$rows[$i] = implode(',', $result);
				}
			}
		}else{	
			$num = intval($this->params->get('editor_layout_rows', '5'));
			for($i=1; $i<=$num; $i++) {
				$query = "SELECT icon"
				. " FROM #__jce_plugins"
				. " WHERE published = 1"
				. " AND row = ".$i
				. " ORDER BY ordering ASC"
				;
				
				$db->setQuery($query);
				
				$result = $db->loadResultArray();
				if ($result) {
					$rows[$i] 	= implode(',', $result);
				}
			}
		}
        return $rows;
	}
	/**
	 * Determine whether a plugin or command is loaded
	 *
	 * @access 			public
	 * @param string	The plugin
	 * @return 			boolean
	*/
	function isLoaded($plugin)
	{		
		$db =& JFactory::getDBO();  
		
		$query = 'SELECT count(id)'
		. ' FROM #__jce_plugins'
		. ' WHERE name = '. $db->Quote($plugin)
		. ' AND published = 1'
		. ' AND id IN ('. str_replace(';', ',', $this->group->rows) .')'
		;
		
		$db->setQuery($query);
		return $db->loadResult() ? true : false;
	}
	/**
	 * Get all loaded plugins config options
	 *
	 * @access 			public
	 * @param array		vars passed by reference
	*/
	function getPluginConfig(&$vars)
	{
		// Store path
		$path 		= JPATH_PLUGINS .DS. 'editors' .DS. 'jce' .DS. 'tiny_mce' .DS. 'plugins';
		$plugins 	= $vars['plugins'];
		
		if ($plugins && is_array($plugins)) {
			foreach($plugins as $plugin) {
				$file = $path .DS. $plugin .DS. 'classes' .DS. 'config.php';
				
				if (file_exists($file)) {
					require_once($file);
					// Create class name	
					$class = ucfirst($plugin . 'Config');
	
					// Check class and method
					if(class_exists($class)){
						if (method_exists(new $class, 'getConfig')) {
							call_user_func_array(array($class, 'getConfig'), array(&$vars));
						}
					}
				}
			}	
		}
	}
	/**
	 * Named wrapper to check access to a feature
	 *
	 * @access 			public
	 * @param string	The feature to check, eg: upload
	 * @param string	The defalt value
	 * @return 			string
	*/
	function checkUser()
	{
		if ($this->group) {
			return true;
		}
		return false;
	}
	/**
	 * XML encode a string.
	 *
	 * @access	public
	 * @param 	string	String to encode
	 * @return 	string	Encoded string
	*/
	function xmlEncode($string)
	{
		return preg_replace(array('/&/', '/</', '/>/', '/\'/', '/"/'), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), $string);
	}
	/**
	 * XML decode a string.
	 *
	 * @access	public
	 * @param 	string	String to decode
	 * @return 	string	Decoded string
	*/
	function xmlDecode($string)
	{
		return preg_replace(array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), array('/&/', '/</', '/>/', '/\'/', '/"/'), $string);
	}
}
?>