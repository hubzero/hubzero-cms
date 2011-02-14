<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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