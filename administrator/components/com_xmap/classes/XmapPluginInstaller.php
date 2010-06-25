<?php

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'XmapPlugin.php');
/**
 * Extension installer
 *
 */
class XmapPluginInstaller extends JObject
{
	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$parent	Parent object [JInstaller instance]
	 * @return	void
	 * @since	1.5
	 */
	function __construct(&$parent)
	{
		$this->parent =& $parent;
	}

	/**
	 * Custom install method
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function install()
	{
		// Get a database connector object
		$db =& $this->parent->getDBO();

		// Get the extension manifest object
		$manifest =& $this->parent->getManifest();
		$this->manifest =& $manifest->document;

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Manifest Document Setup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Set the extensions name
		$name =& $this->manifest->getElementByPath('name');
		$name = JFilterInput::clean($name->data(), 'cmd');
		$this->set('name', $name);

		// Get the component description
		$description = & $this->manifest->getElementByPath('description');
		if (is_a($description, 'JSimpleXMLElement')) {
			$this->parent->set('message', $description->data());
		} else {
			$this->parent->set('message', '' );
		}

		/*
		 * Backward Compatability
		 * @todo Deprecate in future version
		 */
		$type = $this->manifest->attributes('type');

		// Set the installation path
		$element =& $this->manifest->getElementByPath('files');
		if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
			$files =& $element->children();
			foreach ($files as $file) {
				if ($file->attributes($type)) {
					$pname = $file->attributes($type);
					break;
				}
			}
		}

		if ( !empty ($pname) ) {
			$this->parent->setPath('extension_root', JPATH_ROOT.DS.'/administrator/components/com_xmap/extensions');
		} else {
			$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('No extension file specified'));
			return false;
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Filesystem Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// If the extension directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->parent->getPath('extension_root'))) {
			if (!$created = JFolder::create($this->parent->getPath('extension_root'))) {
				$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Failed to create directory').': "'.$this->parent->getPath('extension_root').'"');
				return false;
			}
		}

		/*
		 * If we created the extension directory and will want to remove it if we
		 * have to roll back the installation, lets add it to the installation
		 * step stack
		 */
		if ($created) {
			$this->parent->pushStep(array ('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
		}

		// Copy all necessary files
		if ($this->parent->parseFiles($element, -1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Database Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Check to see if a extension by the same name is already installed
		$query = 'SELECT `id`' .
				' FROM `#__xmap_ext`' .
				' WHERE extension = '.$db->Quote($pname);
		$db->setQuery($query);
		if (!$db->Query()) {
			// Install failed, roll back changes
			$this->parent->abort('Extension Install: '.$db->stderr(true));
			return false;
		}
		$id = $db->loadResult();

		// Was there a module already installed with the same name?
		if ($id) {

			if (!$this->parent->getOverwrite())
			{
				// Install failed, roll back changes
				$this->parent->abort('Extension Install: Extension "' . $pname . '" already exists!' );
				return false;
			}

		} else {

	                // Check to see if there is a backup for this extension
	                $query = 'SELECT `id`' .
                                ' FROM `#__xmap_ext`' .
                                ' WHERE extension = '.$db->Quote($pname. '.bak');
		                $db->setQuery($query);
	                if (!$db->Query()) {
	                        // Install failed, roll back changes
	                        $this->parent->abort('Extension Install: '.$db->stderr(true));
				return false;
			}
			$id = $db->loadResult();

			$row = new XmapPlugin($db,$id);
			$row->extension = $pname;
			$row->published = 0;
			if (!$id ) {
				$row->setParams($this->parent->getParams(),'-1');
			}

			if (!$row->store()) {
				// Install failed, roll back changes
				$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.$db->stderr(true));
				return false;
			}

			// Since we have created a extension item, we add it to the installation step stack
			// so that if we have to rollback the changes we can undo it.
			$this->parent->pushStep(array ('type' => 'xmap_ext', 'id' => $row->id));
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Finalization and Cleanup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Lastly, we will copy the manifest file to its appropriate place.
		if (!$this->parent->copyManifest(-1)) {
			// Install failed, rollback changes
			$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Could not copy setup file'));
			return false;
		}
		return true;
	}

	/**
	 * Custom uninstall method
	 *
	 * @access	public
	 * @param	int		$cid	The id of the extension to uninstall
	 * @param	int		$clientId	The id of the client (unused)
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function uninstall($id, $clientId )
	{
		// Initialize variables
		$row	= null;
		$retval = true;
		$db		=& $this->parent->getDBO();

		// First order of business will be to load the module object table from the database.
		// This should give us the necessary information to proceed.
		$row = new XmapPlugin($db,(int) $id);
		// Set the extension root path
		$this->parent->setPath('extension_root', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_xmap'.DS.'extensions');

		// Because extensions don't have their own folders we cannot use the standard method of finding an installation manifest
		$manifestFile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_xmap'.DS.'extensions'.DS.$row->extension.'.xml';
		if (file_exists($manifestFile))
		{
			$xml =& JFactory::getXMLParser('Simple');

			// If we cannot load the xml file return null
			if (!$xml->loadFile($manifestFile)) {
				JError::raiseWarning(100, JText::_('Plugin').' '.JText::_('Uninstall').': '.JText::_('Could not load manifest file'));
				return false;
			}

			/*
			 * Check for a valid XML root tag.
			 * @todo: Remove backwards compatability in a future version
			 * Should be 'install', but for backward compatability we will accept 'mosinstall'.
			 */
			$root =& $xml->document;
			if ($root->name() != 'install' && $root->name() != 'mosinstall') {
				JError::raiseWarning(100, JText::_('Plugin').' '.JText::_('Uninstall').': '.JText::_('Invalid manifest file'));
				return false;
			}

			// Remove the extension files
			$this->parent->removeFiles($root->getElementByPath('images'), -1);
			$this->parent->removeFiles($root->getElementByPath('files'), -1);
			JFile::delete($manifestFile);

			// Remove all media and languages as well
			$this->parent->removeFiles($root->getElementByPath('media'));
			$this->parent->removeFiles($root->getElementByPath('languages'), 1);
		} else {
			JError::raiseWarning(100, 'Plugin Uninstall: Manifest File invalid or not found');
			return false;
		}

		// Now we will no longer need the extension object, so lets delete it
		$row->extension = $row->extension . '.bak';
		$row->store();
		unset ($row);

		return $retval;
	}

	/**
	 * Custom rollback method
	 * 	- Roll back the extension item
	 *
	 * @access	public
	 * @param	array	$arg	Installation step to rollback
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _rollback_extension($arg)
	{
		// Get database connector object
		$db =& $this->parent->getDBO();

		// Remove the entry from the #__extensions table
		$query = 'DELETE' .
				' FROM `#__xmap_ext`' .
				' WHERE id='.(int)$arg['id'];
		$db->setQuery($query);
		return ($db->query() !== false);
	}
}
