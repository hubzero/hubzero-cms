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
	
	public function &onGroupAreas()
	{
		$area = array(
			'name' => 'wiki',
			'title' => JText::_('PLG_GROUPS_WIKI'),
			'default_access' => 'members'
		);
		
		return $area;
	}

	//-----------

	public function onGroup( $group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null )
	{
		$return = 'html';
		$active = 'wiki';
		
		// The output array we're returning
		$arr = array(
			'html'=>''
		);
		
		//get this area details
		$this_area = $this->onGroupAreas();
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			if(!in_array($this_area['name'],$areas)) {
				return;
			}
		}
		
		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html') {
			
			//set group members plugin access level
			$group_plugin_acl = $access[$active];
			
			//Create user object
			$juser =& JFactory::getUser();
		
			//get the group members
			$members = $group->get('members');

			//if set to nobody make sure cant access
			if($group_plugin_acl == 'nobody') {
				$arr['html'] = "<p class=\"info\">".JText::sprintf('GROUPS_PLUGIN_OFF', ucfirst($active))."</p>";
				return $arr;
			}
			
			//check if guest and force login if plugin access is registered or members
			if ($juser->get('guest') && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members')) {
				ximport('Hubzero_Module_Helper');
				$arr['html']  = "<p class=\"warning\">".JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active))."</p>";
				$arr['html'] .= Hubzero_Module_Helper::renderModules('force_mod');
				return $arr;
			}
			
			//check to see if user is member and plugin access requires members
			if(!in_array($juser->get('id'),$members) && $group_plugin_acl == 'members' && $authorized != 'admin') {
				$arr['html'] = "<p class=\"info\">".JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active))."</p>";
				return $arr;
			}
			
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
			ximport('Hubzero_User_Helper');
			
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'attachment.php');
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'author.php');
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'comment.php');
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'log.php');
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'page.php');
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'revision.php');
			
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'config.php');
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'differenceengine.php');
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'html.php');
			//include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'macro.php');
			//include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'math.php');
			//include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'parser.php');
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'sanitizer.php');
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'setup.php');
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'stringutils.php');
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'tags.php');
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'helpers'.DS.'utfnormalutil.php');
			
			include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'controller.php');

			// Instantiate controller
			$controller = new WikiController( array('name'=>'groups','sub'=>'wiki','group'=>$group->get('cn')) );
			$controller->mainframe = $mainframe;
			
			// Catch any echoed content with ob
			ob_start();
			$controller->execute();
			$controller->redirect();
			$content = ob_get_contents();
			ob_end_clean();
			
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('groups', 'wiki');

			// Return the content
			$arr['html'] = $content;
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
		include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'page.php');
		
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
