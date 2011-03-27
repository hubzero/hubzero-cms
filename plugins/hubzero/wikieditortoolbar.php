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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_hubzero_wikieditortoolbar' );

//-----------

class plgHubzeroWikiEditorToolbar extends JPlugin
{
	private $_pushscripts = true;
	
	//-----------
	
	public function plgHubzeroWikiEditorToolbar(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		//$this->_plugin = JPluginHelper::getPlugin( 'hubzero', 'wikieditortoolbar' );
		//$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------

	public function onInitEditor()
	{
		if ($this->_pushscripts) {
			$document =& JFactory::getDocument();
			$document->addScript(DS.'plugins'.DS.'hubzero'.DS.'wikieditortoolbar'.DS.'wikieditortoolbar.js');
			$document->addStyleSheet(DS.'plugins'.DS.'hubzero'.DS.'wikieditortoolbar'.DS.'wikieditortoolbar.css');
			
			$this->_pushscripts = false;
		}
		
		return '';
	}
	
	//-----------
	
	public function onDisplayEditor( $name, $id, $content, $cls='wiki-toolbar-content', $col=10, $row=35)
	{
		$cls = ($cls) ? 'wiki-toolbar-content '.$cls : 'wiki-toolbar-content';
		$editor  = '<ul id="wiki-toolbar-'.$id.'" class="wiki-toolbar hidden"></ul>'."\n";
		$editor .= '<textarea id="'.$id.'" name="'.$name.'" cols="'.$col.'" rows="'.$row.'" class="'.$cls.'">'.$content.'</textarea>'."\n";

		return $editor;
	}
	
	//-----------
	
	public function onGetEditorContent( $editor ) 
	{
		return "";
	}

	//-----------

	public function onSetEditorContent( $editor, $html ) 
	{
		return "";
	}
	
	//-----------

	public function onSaveEditorContent( $editor ) 
	{
		return "";
	}
}
