<?php
/**
 * @version		$Id: language.php 97 2009-06-21 19:18:07Z happynoodleboy $
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
defined('JPATH_BASE') or die ();

/**
 * Language installer
 *
 * @package		JCE
 * @subpackage	Installer
 * @since		1.5
 */
class JCEInstallerLanguage extends JObject
{
    /**
     * Constructor
     *
     * @access	protected
     * @param	object	$parent	Parent object [JInstaller instance]
     * @return	void
     * @since	1.5
     */
    function __construct( & $parent)
    {
        $this->parent = & $parent;
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
        // Get database connector object
        $manifest = & $this->parent->getManifest();
        $this->manifest = & $manifest->document;

        // Get the language name
        // Set the extensions name
        $name = & $this->manifest->getElementByPath('name');
        $name = JFilterInput::clean($name->data(), 'cmd');
        $this->set('name', $name);

        // Get the Language tag [ISO tag, eg. en-GB]
        $tag = & $this->manifest->getElementByPath('tag');

        // Check if we found the tag - if we didn't, we may be trying to install from an older language package
        if (!$tag)
        {
            $this->parent->abort(JText::_('Language').' '.JText::_('Install').': '.JText::_('NO LANGUAGE TAG?'));
            return false;
        }

        $this->set('tag', $tag->data());
        $folder = $tag->data();

        $sitePath = JPATH_SITE.DS."language".DS.$folder;
        $adminPath = JPATH_ADMINISTRATOR.DS."language".DS.$folder;
        $tinyPath = JPATH_PLUGINS.DS."editors".DS."jce".DS."tiny_mce";

        // Set the installation target paths
        $this->parent->setPath('extension_site', $sitePath);
        $this->parent->setPath('extension_administrator', $adminPath);

        $this->adminElement = & $this->manifest->getElementByPath('administration');
        $this->siteElement = & $this->manifest->getElementByPath('site');
        $this->tinyElement = & $this->manifest->getElementByPath('tinymce');

        // Set overwrite flag if not set by Manifest
        if (!$this->parent->getOverwrite()) {
            $this->parent->setOverwrite(true);
        }

        // Copy site files
        foreach ($this->siteElement->children() as $child)
        {
            if (is_a($child, 'JSimpleXMLElement') && $child->name() == 'files') {
                if ($this->parent->parseFiles($child) === false) {
                    // Install failed, rollback any changes
                    $this->parent->abort();
                    return false;
                }
            }
        }
        // Copy admin files
        foreach ($this->adminElement->children() as $child)
        {
            if (is_a($child, 'JSimpleXMLElement') && $child->name() == 'files') {
                if ($this->parent->parseFiles($child, 1) === false) {
                    // Install failed, rollback any changes
                    $this->parent->abort();
                    return false;
                }
            }
        }
        // Copy tinymce files
        $this->parent->setPath('extension_site', $tinyPath);
        foreach ($this->tinyElement->children() as $child)
        {
            if (is_a($child, 'JSimpleXMLElement') && $child->name() == 'files') {
                if ($this->parent->parseFiles($child) === false) {
                    // Install failed, rollback any changes
                    $this->parent->abort();
                    return false;
                }
            }
        }

        // Set path back to site for manifest
        $this->parent->setPath('extension_site', $sitePath);
        // Lastly, we will copy the manifest file to its appropriate place.
        if (!$this->parent->copyManifest(0)) {
            // Install failed, rollback changes
            $this->parent->abort(JText::_('Component').' '.JText::_('Install').': '.JText::_('Could not copy setup file'));
            return false;
        }

        // Get the language description
        $description = & $this->manifest->getElementByPath('description');
        if (is_a($description, 'JSimpleXMLElement')) {
            $this->parent->set('message', $description->data());
        } else {
            $this->parent->set('message', '');
        }
        return true;
    }

    /**
     * Custom uninstall method
     *
     * @access	public
     * @param	string	$tag		The tag of the language to uninstall
     * @param	int		$clientId	The id of the client (unused)
     * @return	mixed	Return value for uninstall method in component uninstall file
     * @since	1.5
     */
    function uninstall($tag, $clientId)
    {
        $path = trim($tag);
        if (!JFolder::exists($path)) {
            JError::raiseWarning(100, 'Language Uninstall: '.JText::_('Language path is empty, cannot uninstall files'));
            return false;
        }
        $tag = basename($path);

        // Because JCE languages don't have their own folders we cannot use the standard method of finding an installation manifest
        $manifestFile = JPATH_ROOT.DS.'language'.DS.$tag.DS.$tag.'.com_jce.xml';
        if (file_exists($manifestFile))
        {
            $xml = & JFactory::getXMLParser('Simple');

            // If we cannot load the xml file return null
            if (!$xml->loadFile($manifestFile)) {
                JError::raiseWarning(100, JText::_('Language').' '.JText::_('Uninstall').': '.JText::_('Could not load manifest file'));
                return false;
            }

            /*
             * Check for a valid XML root tag.
             */
            $root = & $xml->document;
            if ($root->name() != 'install' && $root->attributes('type') != 'language') {
                JError::raiseWarning(100, JText::_('Language').' '.JText::_('Uninstall').': '.JText::_('Invalid manifest file'));
                return false;
            }

            // Get the admin and site paths for the component
            $sitePath = JPATH_SITE.DS."language".DS.$tag;
            $adminPath = JPATH_ADMINISTRATOR.DS."language".DS.$tag;
            $tinyPath = JPATH_PLUGINS.DS."editors".DS."jce".DS."tiny_mce";

            // Set the installation target paths
            $this->parent->setPath('extension_site', $sitePath);
            $this->parent->setPath('extension_administrator', $adminPath);

            if (!$this->parent->removeFiles($root->getElementByPath('site/files'))) {
                JError::raiseWarning(100, JText::_('Language').' '.JText::_('Uninstall').': '.JText::_('Unable to delete files'));
                return false;
            }
            if (!$this->parent->removeFiles($root->getElementByPath('administration/files'), 1)) {
                JError::raiseWarning(100, JText::_('Language').' '.JText::_('Uninstall').': '.JText::_('Unable to delete files'));
                return false;
            }

            $this->parent->setPath('extension_site', $tinyPath);
            if (!$this->parent->removeFiles($root->getElementByPath('tinymce/files'))) {
                JError::raiseWarning(100, JText::_('Language').' '.JText::_('Uninstall').': '.JText::_('Unable to delete files'));
                return false;
            }
            JFile::delete($manifestFile);
        } else {
            JError::raiseWarning(100, 'Language Uninstall: Manifest File invalid or not found');
            return false;
        }
        return true;
    }
}
