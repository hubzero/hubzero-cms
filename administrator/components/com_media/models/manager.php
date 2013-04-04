<?php
/**
 * @version		$Id: manager.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * Weblinks Component Weblink Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class MediaModelManager extends JModel
{

	function getState($property = null)
	{
		static $set;

		if (!$set) {
			$folder = JRequest::getVar( 'folder', '', '', 'path' );
			$this->setState('folder', $folder);

			$parent = str_replace("\\", "/", dirname($folder));
			$parent = ($parent == '.') ? null : $parent;
			$this->setState('parent', $parent);
			$set = true;
		}
		return parent::getState($property);
	}

	/**
	 * Image Manager Popup
	 *
	 * @param string $listFolder The image directory to display
	 * @since 1.5
	 */
	function getFolderList($base = null)
	{
		global $mainframe;

		// Get some paths from the request
		if (empty($base)) {
			$base = COM_MEDIA_BASE;
		}

		// Get the list of folders
		jimport('joomla.filesystem.folder');
		$folders = JFolder::folders($base, '.', true, true);

		// Load appropriate language files
		$lang = & JFactory::getLanguage();
		$lang->load(JRequest::getCmd( 'option' ), JPATH_ADMINISTRATOR);

		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('Insert Image'));

		// Build the array of select options for the folder list
		$options[] = JHTML::_('select.option', "","/");
		foreach ($folders as $folder) {
			$folder 	= str_replace(COM_MEDIA_BASE, "", $folder);
			$value		= substr($folder, 1);
			$text	 	= str_replace(DS, "/", $folder);
			$options[] 	= JHTML::_('select.option', $value, $text);
		}

		// Sort the folder list array
		if (is_array($options)) {
			sort($options);
		}

		// Create the drop-down folder select list
		$list = JHTML::_('select.genericlist',  $options, 'folderlist', "class=\"inputbox\" size=\"1\" onchange=\"ImageManager.setFolder(this.options[this.selectedIndex].value)\" ", 'value', 'text', $base);
		return $list;
	}

	function getFolderTree($base = null)
	{
		// Get some paths from the request
		if (empty($base)) {
			$base = COM_MEDIA_BASE;
		}
		$mediaBase = str_replace(DS, '/', COM_MEDIA_BASE.'/');

		// convert splfileinfo instance into the structure the view wants. return the relative and parent path as well since it's useful to find where the node belongs
		$mkData = function($fi) use($mediaBase) {
			$rel = preg_replace('#^'.preg_quote($mediaBase).'\/?#', '', $fi);
			$lastSlash = strrpos($rel, DIRECTORY_SEPARATOR);
			return array(array(
				'data' => (object)array(
					'name'     => preg_replace('#[.].*?$#', '', $fi->getBaseName()),
					'relative' => $rel,
					'absolute' => (string)$fi
				),
				'children' => array()
			), $rel, $lastSlash ? substr($rel, 0, $lastSlash) : '');
		};
		list($path) = $mkData(new SplFileInfo($base));

		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base), RecursiveIteratorIterator::SELF_FIRST) as $file) {
			// skip hidden files
			if (substr($file->getFileName(), 0, 1) == '.') {
				continue;
			}
			list($data, $rel, $parent) = $mkData($file);
	
			// find a place to put the node by walking through parents
			$pos =& $path;
			if (($parents = explode(DIRECTORY_SEPARATOR, $parent))) {
				foreach ($parents as $idx=>$par) {
					if ($key = implode(DIRECTORY_SEPARATOR, array_slice($parents, 0, $idx + 1))) {
						$pos =& $pos['children'][$key];
					}
				}
			}
			$pos['children'][$rel] = $data;
		}

		// recursive natural sort
		$naturalSort = function(&$node) use(&$naturalSort) {
			uksort($node['children'], function($a, $b) { return strcasecmp($a, $b); } );
			$node['children'] = array_map($naturalSort, $node['children']);
			return $node;
		};
		return $naturalSort($path);
	}
}
