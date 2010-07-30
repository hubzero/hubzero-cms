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
JPlugin::loadLanguage( 'plg_groups_datasharing' );

//-----------

class plgGroupsDatasharing extends JPlugin
{
	public function plgGroupsDatasharing(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'groups', 'datasharing' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onGroupAreas( $authorized )
	{
		$areas = array(
			'datasharing' => JText::_('PLG_GROUPS_DATASHARING')
		);
		return $areas;
	}

	//-----------

	public function onGroup( $group, $option, $authorized, $limit=0, $limitstart=0, $action='', $areas=null )
	{
		$return = 'html';

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onGroupAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onGroupAreas( $authorized ) ) )) {
				$return = '';
			}
		}
		
		// Are we on the overview page?
		if ($areas[0] == 'overview') {
			$return = 'metadata';
		}
		
		// The output array we're returning
		$arr = array(
			'html'=>'',
			'metadata'=>'',
			'dashboard'=>''
		);

		// Do we need to return any data?
		if ($return != 'html' && $return != 'metadata') {
			return $arr;
		}
		
		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html') {
			$this->group = $group;
			$this->option = $option;
			$this->name = substr($option,4,strlen($option));
			$this->authorized = $authorized;
			
			// Set the page title
			$document =& JFactory::getDocument();
			$document->setTitle( JText::_(strtoupper($this->name)).': '.$this->group->get('description').': '.JText::_('PLG_GROUPS_DATASHARING') );

			if (!$action) {
				$action = JRequest::getVar( 'task', '' );
			}
			
			ximport('xdocument');
			XDocument::addPluginStylesheet('groups', 'datasharing');
			
			$this->_msg = '';

			switch ($action) 
			{
				case 'listfiles': $arr['html'] .= $this->_listFiles(); break;
				case 'createbox': $arr['html'] .= $this->_createBox(); break;
				case 'overview':  $arr['html'] .= $this->_overview();  break;
				
				default: $arr['html'] .= $this->_overview(); break;
			}
		}

		// Return the output
		return $arr;
	}

	//-----------

	public function onGroupDelete( $group ) 
	{
		// Start the log text
		$log = '';
		
		// Do whatever you need for group deletion processing
		
		// Return the log
		return $log;
	}

	//-----------
	
	public function onGroupNew( $group ) 
	{
		// Start the log text
		$log = '';
		
		// Do whatever you need for group deletion processing
		
		// Return the log
		return $log;
	}
	
	//-----------
	
	private function _getDataDirectory() 
	{
		// Get the path from the plugin params
		$datadir = $this->_params->get('datadir');
		// Set to a default if nothing found
		$datadir = ($datadir) ? $datadir : '/data/groups';
		// Ensure the path does NOT end with /
		if (substr($datadir, -1) == DS) {
			$datadir = substr($datadir, 0, -1);
		}
		// Ensure the path starts with /
		if (substr($datadir, 0, 1) != DS) {
			$datadir = DS.$datadir;
		}
		return $datadir;
	}
	
	protected function _overview() 
	{
		$datadir  = $this->_getDataDirectory();
		$datadir .= DS.trim($this->group->cn);
		
		$name = 'overview';
		if (!is_dir($datadir)) {
			$name = 'intro';
		}
		
		// Instantiate a vew
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'groups',
				'element'=>'datasharing',
				'name'=>$name
			)
		);

		// Pass the view some info
		$view->option = $this->option;
		$view->group = $this->group;
		$view->authorized = $this->authorized;
		$view->datadir = $datadir;

		if ($name == 'overview') {
			
		}

		if ($this->_msg) {
			$view->_msg = $this->_msg;
		}
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Return the output
		return $view->loadTemplate();
	}
	
	//-----------
	
	protected function _createBox() 
	{
		// Do whatever you need for post group creation processing
		$createShare = JRequest::getVar('shareddir', 0, 'post');
		
		if ($createShare) {
			$cn = trim($this->group->cn);
			
			$datadir = $this->_getDataDirectory();
			
			exec("sudo /usr/lib/hubzero/bin/addgroupdatadir $cn $datadir", $results, $retval);

			if ($retval == 0) {
				$this->_msg = JText::_('New data directory created');
			} else {
				$this->setError(JText::_('Failed to create new data directory') .  " ($retval) ");
			}
		}

		// Return the log
		return $this->_overview();
	}
	
	//-----------
 
	protected function _listFiles() 
	{
		$path  = $this->_getDataDirectory();
		$path .= DS.trim($this->group->cn);

		// Get the directory we'll be reading out of
		$d = @dir($path);

		$images  = array();
		$folders = array();
		$docs    = array();
		
		if ($d) {
			// Loop through all files and separate them into arrays of images, folders, and other
			while (false !== ($entry = $d->read())) 
			{
				$img_file = $entry; 
				if (is_file($path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "bmp|gif|jpg|jpeg|jpe|tif|tiff|png", $img_file )) {
						$images[$entry] = $img_file;
					} else {
						$docs[$entry] = $img_file;
					}
				} else if (is_dir($path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'cvs') {
					$folders[$entry] = $img_file;
				}
			}
			$d->close();	

			ksort($images);
			ksort($folders);
			ksort($docs);
		} else {
			$this->setError( JText::sprintf('PLG_GROUPS_DROPBOX_ERROR_MISSING_DIRECTORY', $path) );
		}

		jimport('joomla.filesystem.folder');

		// Instantiate a vew
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'groups',
				'element'=>'datasharing',
				'name'=>'overview',
				'layout'=>'filelist'
			)
		);

		$view->option = $this->option;
		$view->group = $this->group;
		$view->authorized = $this->authorized;
		
		$view->docs = $docs;
		$view->folders = $folders;
		$view->images = $images;
		
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
}
