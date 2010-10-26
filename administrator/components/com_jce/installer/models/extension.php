<?php
/**
 * @version		$Id: extension.php 47 2009-05-26 18:06:30Z happynoodleboy $
 * @package		Joomla
 * @subpackage	Menus
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Import library dependencies
require_once(dirname(__FILE__).DS.'extensions.php');

/**
 * Installer Plugins Model
 *
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */
class InstallerModelExtension extends InstallerModel
{
	/**
	 * Extension Type
	 * @var	string
	 */
	var $_type = 'extension';

	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct()
	{
		global $mainframe;

		// Call the parent constructor
		parent::__construct();
	}

	function _loadItems()
	{
		global $mainframe, $option;

		$extensions = $this->findExtensions();

		$this->setState('pagination.total', count($extensions));
		if($this->_state->get('pagination.limit') > 0) {
			$this->_items = array_slice( $extensions, $this->_state->get('pagination.offset'), $this->_state->get('pagination.limit') );
		} else {
			$this->_items = $extensions;
		}
	}
	
	function findExtensions()
	{
		$db = & JFactory::getDBO();
		
		$query = 'SELECT name'
		. ' FROM #__jce_plugins'
		. ' WHERE type = '. $db->Quote('plugin')
		;
		$db->setQuery($query);
		$plugins = $db->loadResultArray();
		
		/*$query = 'SELECT extension'
		. ' FROM #__jce_extensions'
		;
		$db->setQuery($query);
		$installed = $db->loadResultArray();*/
		
		$extensions = array();
		
		$language =& JFactory::getLanguage();
		
		foreach ($plugins as $plugin) {
			$xml = JCE_PLUGINS.DS.$plugin.DS.$plugin.'.xml';
			$ext = JCE_PLUGINS.DS.$plugin.DS.'extensions';
			
			if (is_dir($ext) && file_exists($xml)) {
				$files = JFolder::files($ext, '\.xml$', true, true);
				
				foreach ($files as $file) {
					//if (!in_array(basename($file, '.xml'), $installed)) {
						$data = JApplicationHelper::parseXMLInstallFile($file);
				
						if (!is_array($data)) {
							continue;
						}
						$extension = new StdClass();
						
						// Populate the row from the xml meta file
						foreach ($data as $key => $value) {
							$extension->$key = $value;
						}
						
						$extension->id = '';
						
						// Read the file to see if it's a valid XML file
						$xml = & JFactory::getXMLParser('Simple');
				
						if ($xml->loadFile($file)) {
							if (is_object($xml->document) && $xml->document->attributes('type') == 'extension') {
								$plugin = $xml->document->attributes('plugin');
								$name 	= $xml->document->attributes('extension');
								
								$extension->id = $plugin.'.'.$xml->document->attributes('folder').'.'.$name;
								
								$language->load('com_jce_'.trim($plugin), JPATH_SITE);
								$language->load('com_jce_'.trim($plugin).'_'.trim($name), JPATH_SITE);
							
								$query = 'SELECT title'
								.' FROM #__jce_plugins'
								.' WHERE name = '.$db->Quote($xml->document->attributes('plugin'))
								;
								$db->setQuery($query);
								$extension->plugin = $db->loadResult();
							}
						}
						$extensions[] = $extension;
					//}
				}
				
			}
		}
		return $extensions;	
	}
}