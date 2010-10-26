<?php
/**
 * @version		$Id: installer.php 96 2009-06-21 19:15:38Z happynoodleboy $
 * @package		JCE
 * @copyright	Copyright (C) 2009 Ryan Demmer. All rights reserved.
 * @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
 * @license		GNU/GPL
 * This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.installer.installer');

class JCEInstaller extends JInstaller
{
	/**
	 * Returns a reference to the global Installer object, only creating it
	 * if it doesn't already exist.
	 *
	 * @static
	 * @return	object	An installer object
	 * @since 1.5
	 */
	function &getInstance()
	{
		static $instance;

		if (!isset ($instance)) {
			$instance = new JCEInstaller();
		}
		return $instance;
	}

	/**
	 * Set an installer adapter by name
	 *
	 * @access	public
	 * @param	string	$name		Adapter name
	 * @param	object	$adapter	Installer adapter object
	 * @return	boolean True if successful
	 * @since	1.5
	 */
	function setAdapter($name, $adapter = null)
	{
		// Check if valid extension type
		if( $name == 'plugin' || $name == 'language' || $name == 'extension' ){
			if (!is_object($adapter))
			{			
				// Try to load the adapter object
				require_once(dirname(__FILE__).DS.'adapters'.DS.strtolower($name).'.php');
				$class = 'JCEInstaller'.ucfirst($name);
				if (!class_exists($class)) {
					return false;
				}
				$adapter = new $class($this);
				$adapter->parent =& $this;
			}
			$this->_adapters[$name] =& $adapter;
			return true;
		}else{
			$this->abort(JText::_('Incorrect version!'));
		}
	}
	/**
	 * Method to parse the variables of a plugin, build the INI
	 * string for it's default variables, and return the INI string.
	 *
	 * @access	public
	 * @return	string	INI string of parameter values
	 * @since	1.5
	 */
	function getVariables()
	{
		// Get the manifest document root element
		$root = & $this->_manifest->document;

		// Get the element of the tag names
		$element =& $root->getElementByPath('variables');
		if (!is_a($element, 'JSimpleXMLElement') || !count($element->children())) {
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return null;
		}

		// Get the array of variable nodes to process
		$vars = $element->children();
		if (count($vars) == 0) {
			// No variables to process
			return null;
		}

		// Process each variable in the $vars array.
		$ini = null;
		foreach ($vars as $var) {
			if (!$name = $var->attributes('name')) {
				continue;
			}

			if (!$value = $var->attributes('default')) {
				continue;
			}

			$ini .= $name."=".$value."\n";
		}
		return $ini;
	}
}