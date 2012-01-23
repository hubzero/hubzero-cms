<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgHubzeroWikiEditorWykiwyg extends JPlugin
{
	private $_pushscripts = true;
	
	/**
	 * Short description for 'plgGroupsForum'
	 * 
	 * @param      object $subject Parameter description (if any) ...
	 * @param      array  $config  Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		
		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin('hubzero', 'wikieditorwykiwyg');
		//$this->loadLanguage();
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$this->params = new JParameter($this->_plugin->params);
		}
	}
	
	//-----------

	public function onInitEditor()
	{
		if ($this->_pushscripts) {
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('hubzero', 'wikieditorwykiwyg');
			Hubzero_Document::addPluginScript('hubzero', 'wikieditorwykiwyg');
			
			$this->_pushscripts = false;
		}
		
		return '';
	}
	
	//-----------
	
	public function onDisplayEditor($name, $id, $content, $cls='wiki-toolbar-content', $col=10, $row=35)
	{
		$cls = ($cls) ? 'wiki-toolbar-content '.$cls : 'wiki-toolbar-content';
		$editor = '<textarea id="' . $id . '" name="' . $name . '" cols="' . $col . '" rows="' . $row . '" class="' . $cls . '">' . $content . '</textarea>' . "\n";

		return $editor;
	}
}
