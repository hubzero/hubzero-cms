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

//-------------------------------------------------------------
// Joomla module
// "My Tools"
//    This module displays a list of recent tools, favorite
//    tools, and all tools.
// MiddleWare component "com_mw" REQUIRED
//-------------------------------------------------------------

include_once( JPATH_ROOT.DS.'components'.DS.'com_mw'.DS.'mw.utils.php');
include_once( JPATH_ROOT.DS.'components'.DS.'com_mw'.DS.'mw.class.php');

//----------------------------------------------------------
// This class holds information about one application.
// It may be either a running session or an app that can be invoked.
//----------------------------------------------------------

class MwModApp 
{
	var $name;
	var $caption;
	var $desc;
	var $middleware; // which environment to run in
	var $session;    // sessionid of application
	var $owner;      // owner of a running session
	var $num;        // Nth occurrence of this application in a list
	var $public;     // is this tool public?
	var $revision;   // what license is in use?
	
	function MwModApp($n,$c,$d,$m,$s,$o,$num,$p,$r, $tn) 
	{
		$this->name       = $n;
		$this->caption    = $c;
		$this->desc       = $d;
		$this->middleware = $m;
		$this->session    = $s;
		$this->owner      = $o;
		$this->num        = $num;
		$this->public     = $p;
		$this->revision   = $r;
		$this->toolname   = $tn;
	}
}

//----------------------------------------------------------
// Module class
//----------------------------------------------------------

class modToolList
{
	private $attributes = array();

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}
	
	//-----------
	// Get a list of applications that the user might invoke.
	
	private function _getToollist($lst=NULL)
	{
		$toollist = array();
		
		// Create a Tool object
		$database 	=& JFactory::getDBO();
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.tool.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );
		$obj = new Tool( $database );
		$objV = new ToolVersion( $database );
	
		if (is_array($lst)) {
			$tools = array();
			// Check if the list is empty or not
			if (!empty($lst)) {
			
				ksort($lst);
				// Get info for tools in the list
				foreach ($lst as $item) 
				{
					/*
					if (strstr($item, '_')) {
						$bits = explode('_',$item);
						$rev = (is_array($bits) && count($bits > 1)) ? array_pop($bits) : '';
						$item = trim(implode('_',$bits));
					}
					*/
					if (strstr($item, '_r')) {
						$bits = explode('_r',$item);
						$rev = (is_array($bits) && count($bits > 1)) ? array_pop($bits) : '';
						$item = trim(implode('_r',$bits));
					}
					$workspaces = array('workspace','workspace-med','workspace-big');
					if (in_array($item,$workspaces)) {
						$thistool = $objV->getVersionInfo('','current','workspace','');
					} else {
						$thistool = $objV->getVersionInfo('','current',$item,'');
					}

					if (is_array($thistool) && isset($thistool[0])) {
						$t = $thistool[0];
						if ($item == 'workspace-med') {
							$t->instance = 'workspace-med';
							$t->title = 'Workspace (1000x750)';
						} else if ($item == 'workspace-big') {
							$t->instance = 'workspace-big';
							$t->title = 'Workspace (1150x750)';
						}
						
						$tools[] = $t;
					}
				}
			} else {
				return array();
			}
		} else {
			// Get all available tools
			$tools = $obj->getMyTools();
		}
		
		$toolnames = array();
		
		// Turn it into an App array.
		foreach ($tools as $tool) 
		{
			if (!in_array(strtolower($tool->toolname), $toolnames)) { // include only one version
				$toollist[strtolower($tool->instance)] = new MwModApp($tool->instance,
						 $tool->title,
						 $tool->description,
						 $tool->mw,
						 0, '', 0,
						 1,
						 $tool->revision,
						 $tool->toolname);
				if ($lst === null && $tool->toolname == 'workspace') {
					$tool->title = 'Workspace (800x600)';
					$toollist[strtolower('Workspace (1000x750)')] = new MwModApp('workspace-med',
								'Workspace (1000x750)', $tool->description, $tool->mw, 0, '', 0, 1, $tool->revision, $tool->toolname);
					$toollist[strtolower('Workspace (1150x750)')] = new MwModApp('workspace-big',
								'Workspace (1150x750)', $tool->description, $tool->mw, 0, '', 0, 1, $tool->revision, $tool->toolname);
				}
			}
			$toolnames[] = strtolower($tool->toolname);
		}
		//ksort($toollist);
	
		return $toollist;
	}
	
	//-----------
	
	private function _prepText($txt) 
	{
		$txt = stripslashes($txt);
		$txt = str_replace('"','&quot;',$txt);
		return $txt;
	}
	
	//-----------
	
	private function _buildList(&$toollist, $type='all')
	{
		if ($type == 'favs') {
			$favs = array();
		} elseif ($type == 'all') {
			//$favs = (isset($this->favs)) ? $this->favs : array();
			$favs = $this->favs;
		}
		
		$database 	=& JFactory::getDBO();
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );
		$objV = new ToolVersion( $database );
		
		$html  = "\t\t".'<ul>'."\n";
		if (count($toollist) <= 0) {
			$html .= "\t\t".' <li>No tools found.</li>'."\n";
		} else {
			foreach ($toollist as $tool)
			{
				// Make sure we have some info before attempting to display it
				if (!empty($tool->caption)) {
					// Prep the text for XHTML output
					$tool->caption = $this->_prepText($tool->caption);
					$tool->desc = $this->_prepText($tool->desc);
		
					// Get the tool's name without any revision attachments
					// e.g. "qclab" instead of "qclab_r53"
					$toolname = $tool->toolname ? $tool->toolname : $tool->name;
				
					// from sep 28-07 version (svn revision) number is supplied at the end of the invoke command
					$url = 'index.php?option=com_mw&task=invoke&sess='.$tool->name.'&version='.$tool->revision;
					
					$workspaces = array('workspace','workspace-med','workspace-big');
					if (in_array($toolname,$workspaces)) {
						$toolname = 'workspace';
					}

					// Build the HTML
					$html .= "\t\t".' <li id="'.$tool->name.'"';
					// If we're in the 'all tools' pane ...
					if ($type == 'all') {
						// Highlight tools on the user's favorites list
						if (in_array($tool->name,$favs)) {
							$html .= ' class="favd"';
						}
					}
					$html .= '>'."\n";
					
					// Tool info link
					$html .= "\t\t\t".' <a href="tools/'.$tool->toolname.'/" class="tooltips" title="'.$tool->caption.' :: '.$tool->desc.'">'.$tool->caption.'</a>'."\n";
					
					// Only add the "favorites" button to the all tools list
					if ($type == 'all') {
						$html .= "\t\t\t".' <a href="javascript:void(0);" class="fav" title="Add '.$tool->caption.' to your favorites">'.$tool->caption.'</a>'."\n";
					}
					
					// Launch tool link
					$html .= "\t\t\t".' <a href="'.$url.'" class="launchtool" title="Launch '.$tool->caption.'">Launch '.$tool->caption.'</a>'."\n";
					$html .= "\t\t".' </li>'."\n";
				}
				// If we're in the 'favorites' pane ...
				// Add the tool's name to an array for the 'all tools' 
				// pane to use in highlighting favorite tools
				if ($type == 'favs') {
					$favs[] = $tool->name;
				}
			}
		}
		$html .= "\t\t".'</ul>'."\n";
		
		if ($type == 'favs') {
			$this->favs = $favs;
		}
		return $html;
	}
	
	//-----------
	
	public function display()
	{
		$juser =& JFactory::getUser();

		$params = $this->params;
		$html   = '';

		// See if we have an incoming string of favorite tools
		// This should only happen on AJAX requests
		$fav = JRequest::getVar( 'fav', '' );
		$no_html = JRequest::getVar( 'no_html', 0 );

		if ($fav || $no_html) {
			// We have a string of tools! This means we're updating the
			// favorite tools pane of the module via AJAX
			$favs = split(',',$fav);
			$favs = array_map('trim',$favs);
			$this->favs = $favs;
			$favtools = ($fav) ? $this->_getToollist($favs) : array();
			
			$html .= $this->_buildList($favtools, 'fav');
			$html .= "\t\t".'<p>You may edit this list by checking/unchecking items from the All Tools list.</p>'."\n";
		} else {
			// Add the JavaScript that does the AJAX magic to the template
			$document =& JFactory::getDocument();
			$document->addScript('/modules/mod_mytools/mod_mytools.js');
		
			// Push the module CSS to the template
			ximport('xdocument');
			XDocument::addModuleStyleSheet('mod_mytools');
		
			// Get a list of recent tools
			$database =& JFactory::getDBO();
			$rt = new RecentTool( $database );
			$rows = $rt->getRecords( $juser->get('id') );
			
			$recent = array();
			
			if (!empty($rows)) {
				foreach ($rows as $row)
				{
					$recent[] = $row->tool;
				}
			}
		
			// Get the user's list of favorites
			$fav = $params->get('myhub_favs');
			if ($fav) {
				$favs = split(',',$fav);
			} else {
				$favs = array();
			}
			$this->favs = $favs;
			
			// Get a list of applications that the user might invoke.
			$rectools = $this->_getToollist($recent);
			$favtools = $this->_getToollist($favs);
			$alltools = $this->_getToollist();

			// Build the HTML
			$html .= '<div id="myToolsTabs">'."\n";
			$html .= "\t".'<ul class="tab_titles">'."\n";
			$html .= "\t".' <li title="recenttools" class="active">Recent</li>'."\n";
			$html .= "\t".' <li title="favtools">Favorites</li>'."\n";
			$html .= "\t".' <li title="alltools">All Tools</li>'."\n";
			$html .= "\t".'</ul>'."\n";
			
			$html .= "\t".'<div id="recenttools" class="tab_panel active">'."\n";
			$html .= $this->_buildList($rectools, 'recent');
			$html .= "\t\t".'<p>These are your most recently used tools.</p>'."\n";
			$html .= "\t".'</div>'."\n";
			
			$html .= "\t".'<div id="favtools" class="tab_panel">'."\n";
			$html .= $this->_buildList($favtools, 'favs');
			$html .= "\t\t".'<p>You may edit this list by checking/unchecking items from the All Tools list.</p>'."\n";
			$html .= "\t".'</div>'."\n";
			
			$html .= "\t".'<div id="alltools" class="tab_panel">'."\n";
			$html .= $this->_buildList($alltools, 'all');
			$html .= "\t\t".'<p>Add a tool to your favorites by clicking a heart. Click the heart again to remove it.</p>'."\n";
			$html .= "\t".'</div>'."\n";
			$html .= '</div>'."\n";
		}
		
		// Output HTML
		echo $html;
	}
}

//----------------------------------------------------------

$modtoollist = new modToolList();
$modtoollist->params = $params;

require( JModuleHelper::getLayoutPath('mod_mytools') );
?>