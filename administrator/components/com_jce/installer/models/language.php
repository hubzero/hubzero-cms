<?php
/**
 * @version		$Id: language.php 47 2009-05-26 18:06:30Z happynoodleboy $
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
jimport( 'joomla.filesystem.folder' );

/**
 * Installer Languages Model
 *
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */
class InstallerModelLanguage extends InstallerModel
{
	/**
	 * Extension Type
	 * @var	string
	 */
	var $_type = 'language';

	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct()
	{
		// Call the parent constructor
		parent::__construct();
	}

	function _loadItems()
	{
		global $mainframe, $option;

		$db = &JFactory::getDBO();

		// Get the site languages
		$langBDir = JLanguage::getLanguagePath(JPATH_SITE);
		$langDirs = JFolder::folders($langBDir);

		for ($i=0; $i < count($langDirs); $i++)
		{
			$lang = new stdClass();
			$lang->folder = $langDirs[$i];
			$lang->baseDir = $langBDir;
			$languages[] = $lang;
		}
		$rows = array();
		$rowid = 0;
		foreach ($languages as $language)
		{
			$files = JFolder::files( $language->baseDir.DS.$language->folder, '\.(com_jce)\.xml$' );
			foreach ($files as $file)
			{
				$data = JApplicationHelper::parseXMLInstallFile($language->baseDir.DS.$language->folder.DS.$file);

				$row 			= new StdClass();
				$row->id 		= $rowid;
				$row->language 	= $language->baseDir.DS.$language->folder;

				// If we didn't get valid data from the xml file, move on...
				if (!is_array($data)) {
					continue;
				}

				// Populate the row from the xml meta file
				foreach($data as $key => $value) {
					$row->$key = $value;
				}
				$row->jname = JString::strtolower( str_replace( " ", "_", $row->name ) );
				$rows[] = $row;
				$rowid++;
			}
		}
		$this->setState('pagination.total', count($rows));
		if($this->_state->get('pagination.limit') > 0) {
			$this->_items = array_slice( $rows, $this->_state->get('pagination.offset'), $this->_state->get('pagination.limit') );
		} else {
			$this->_items = $rows;
		}
	}

	/**
	 * Remove (uninstall) an extension
	 *
	 * @static
	 * @return boolean True on success
	 * @since 1.0
	 */
	function remove($eid=array())
	{
		global $mainframe;

		$lang =& JFactory::getLanguage();
		$lang->load('com_jce');

		// Initialize variables
		$failed = array ();

		/*
		 * Ensure eid is an array of extension ids
		 * TODO: If it isn't an array do we want to set an error and fail?
		 */
		if (!is_array($eid)) {
			$eid = array ($eid);
		}
		// construct the list of all language
		$this->_loadItems();

		// Get a database connector
		$db =& JFactory::getDBO();

		// Get an installer object for the extension type
		//jimport('joomla.installer.installer');
		require_once( JPATH_COMPONENT .DS. 'installer' .DS. 'installer.php' );
		$installer	=& JCEInstaller::getInstance($db, $this->_type);

		// Uninstall the chosen extensions
		foreach ($eid as $id)
		{
			$item = $this->_items[$id];
			$result = $installer->uninstall( 'language', $item->language );

			// Build an array of extensions that failed to uninstall
			if ($result === false) {
				$failed[] = $id;
			}
		}

		if (count($failed)) {
			// There was an error in uninstalling the package
			$msg = JText::sprintf('UNINSTALLEXT', JText::_($this->_type), JText::_('Error'));
			$result = false;
		} else {
			// Package uninstalled sucessfully
			$msg = JText::sprintf('UNINSTALLEXT', JText::_($this->_type), JText::_('Success'));
			$result = true;
		}

		$mainframe->enqueueMessage($msg);
		$this->setState('action', 'remove');
		$this->setState('message', $installer->message);
		// re-construct the list of all language
		$this->_loadItems();

		return $result;
	}

}