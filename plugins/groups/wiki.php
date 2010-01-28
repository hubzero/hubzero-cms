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
JPlugin::loadLanguage( 'plg_groups_wiki' );

//-----------

class plgGroupsWiki extends JPlugin
{
	public function plgGroupsWiki(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'groups', 'wiki' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onGroupAreas( $authorized )
	{
		$areas = array(
			'wiki' => JText::_('PLG_GROUPS_WIKI')
		);

		return $areas;
	}

	//-----------

	public function onGroup( $group, $option, $authorized, $limit=0, $limitstart=0, $action='', $areas=null )
	{
		$return = 'html';
		$active = 'wiki';
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onGroupAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onGroupAreas( $authorized ) ) )) {
				$return = '';
				//$active = $areas[0];
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
			// Set some variables for the wiki
			$_REQUEST['task'] = $action;
			$scope = trim(JRequest::getVar( 'scope', '' ));
			if (!$scope) {
				$_REQUEST['scope'] = $group->get('cn').DS.$active;
			}
			
			// Initiate the wiki code
			//$arr['html'] = $this->wiki( $group );
			global $mainframe;

			// Import some needed libraries
			ximport('wiki.wiki');

			// Instantiate controller
			$controller = new WikiController( array('name'=>'groups','sub'=>'wiki','group'=>$group->get('cn')) );
			$controller->mainframe = $mainframe;

			// Catch any echoed content with ob
			ob_start();
			$controller->execute();
			$controller->redirect();
			$content = ob_get_contents();
			ob_end_clean();

			// Return the content
			$arr['html'] = $content;
		} else {
			// Get a count of the number of pages
			$database =& JFactory::getDBO();
			
			$access = " AND w.access!=1";
			if ($authorized) {
				$access = "";
			}

			$query = "SELECT v.pageid, w.title, w.pagename, w.scope, w.group, w.access, v.version, v.created_by, v.created
						FROM #__wiki_page AS w, #__wiki_version AS v
						WHERE w.id=v.pageid AND v.approved=1 AND w.group='".$group->get('cn')."' AND w.scope='".$group->get('cn').DS.$active."' $access
						ORDER BY created DESC LIMIT $limitstart,$limit";
			$database->setQuery( $query );
			$rows = $database->loadObjectList();

			$database->setQuery( "SELECT COUNT(*) FROM #__wiki_page AS w WHERE w.scope='".$group->get('cn').DS.'wiki'."' AND w.group='".$group->get('cn')."' $access" );
			$num = $database->loadResult();

			// Build the HTML meant for the "profile" tab's metadata overview
			$arr['metadata'] = '<a href="'.JRoute::_('index.php?option='.$option.'&gid='.$group->get('cn').'&active=wiki').'">'.JText::sprintf('PLG_GROUPS_WIKI_NUMBER_PAGES',$num).'</a>'."\n";
			//$arr['dashboard'] = $this->dashboard( $group, $rows, $authorized, $option );
			
			// Instantiate a vew
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'groups',
					'element'=>'wiki',
					'name'=>'dashboard'
				)
			);

			// Pass the view some info
			$view->option = $option;
			$view->group = $group;
			$view->authorized = $authorized;
			$view->rows = $rows;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$arr['dashboard'] = $view->loadTemplate();
		}
		
		// Return the output
		return $arr;
	}
	
	//-----------
	
	public function onGroupDelete( $group ) 
	{
		// Get all the IDs for pages associated with this group
		$ids = $this->getPageIDs( $group->get('cn') );

		// Import needed libraries
		ximport('wiki.page');
		
		// Instantiate a WikiPage object
		$database =& JFactory::getDBO();
		$wp = new WikiPage( $database );

		// Start the log text
		$log = JText::_('PLG_GROUPS_WIKI_LOG').': ';
		
		if (count($ids) > 0) {
			// Loop through all the IDs for pages associated with this group
			foreach ($ids as $id)
			{
				// Delete all items linked to this page
				$wp->deleteBits( $id->id );
				
				// Delete the wiki page last in case somehting goes wrong
				$wp->delete( $id->id );
				
				// Add the page ID to the log
				$log .= $id->id.' '."\n";
			}
		} else {
			$log .= JText::_('PLG_GROUPS_WIKI_NO_RESULTS_FOUND')."\n";
		}
		
		// Return the log
		return $log;
	}
	
	//-----------
	
	public function onGroupDeleteCount( $group ) 
	{
		return JText::_('PLG_GROUPS_WIKI_LOG').': '.count( $this->getPageIDs( $group->get('cn') ));
	}
	
	//-----------
	
	public function getPageIDs( $gid=NULL )
	{
		if (!$gid) {
			return array();
		}
		$database =& JFactory::getDBO();
		$database->setQuery( "SELECT id FROM #__wiki_page AS p WHERE p.group='".$gid."'" );
		return $database->loadObjectList();
	}
}