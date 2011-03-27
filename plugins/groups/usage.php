<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

// No direct access
defined('_JEXEC') or die('Restricted access');

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_groups_usage' );

//-----------

class plgGroupsUsage extends JPlugin
{
	public function plgGroupsUsage(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'groups', 'usage' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onGroupAreas() 
	{
		$area = array(
			'name' => 'usage',
			'title' => JText::_('USAGE'),
			'default_access' => 'members'
		);
		
		return $area;
	}

	//-----------

	public function onGroup( $group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null )
	{
		$return = 'html';
		$active = 'usage';
		
		// The output array we're returning
		$arr = array(
			'html'=>''
		);
		
		//get this area details
		$this_area = $this->onGroupAreas();
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			if(!in_array($this_area['name'],$areas)) {
				$return = '';
			}
		}
		
		if ($return == 'html') {
			$database =& JFactory::getDBO();
			
			ximport('Hubzero_Document');
			Hubzero_Document::addComponentStylesheet('com_usage');
			
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'groups',
					'element'=>'usage',
					'name'=>'index'
				)
			);
			$view->option = $option;
			$view->group = $group;
			$view->authorized = $authorized;
			$view->database = $database;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}

			$arr['html'] = $view->loadTemplate();
		}

		return $arr;
	}

	//-----------
	
	public function getResourcesCount( $gid=NULL, $authorized )
	{
		if (!$gid) {
			return 0;
		}
		$database =& JFactory::getDBO();
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'resource.php' );
		$rr = new ResourcesResource( $database );
		
		$database->setQuery( "SELECT COUNT(*) FROM ".$rr->getTableName()." AS r WHERE r.group_owner='".$gid."'" );
		return $database->loadResult();
	}
	
	//-----------
	
	public function getWikipageCount( $gid=NULL, $authorized ) 
	{
		if (!$gid) {
			return 0;
		}
		$database =& JFactory::getDBO();
		
		$database->setQuery( "SELECT COUNT(*) FROM #__wiki_page AS p WHERE p.scope='".$gid.DS.'wiki'."' AND p.group='".$gid."'" );
		return $database->loadResult();
	}
	
	//-----------
	
	public function getWikifileCount( $gid=NULL, $authorized ) 
	{
		if (!$gid) {
			return 0;
		}
		$database =& JFactory::getDBO();
		
		$database->setQuery( "SELECT id FROM #__wiki_page AS p WHERE p.scope='".$gid.DS.'wiki'."' AND p.group='".$gid."'" );
		$pageids = $database->loadObjectList();
		if ($pageids) {
			$ids = array();
			foreach ($pageids as $pageid) 
			{
				$ids[] = $pageid->id;
			}
			
			$database->setQuery( "SELECT COUNT(*) FROM #__wiki_attachments WHERE pageid IN (".implode(',', $ids).")" );
			return $database->loadResult();
		} else {
			return 0;
		}
	}
	
	//-----------
	
	public function getForumCount( $gid=NULL, $authorized, $state='' ) 
	{
		if (!$gid) {
			return 0;
		}
		$database =& JFactory::getDBO();
		
		//ximport('xforum');
		include_once(JPATH_ROOT.DS.'plugins'.DS.'groups'.DS.'forum'.DS.'forum.class.php');
		
		$filters = array();
		$filters['authorized'] = $authorized;
		switch ($state) 
		{
			case 'sticky': 
				$filters['sticky'] = 1;
				//$filters['state'] = 0;
			break;
			case 'closed': 
				$filters['state'] = 1;
			break;
			case 'open': 
			default:
				$filters['state'] = 0;
			break;
		}
		$filters['start'] = 0;
		$filters['group'] = $gid;
		
		$forum = new XForum( $database );
		return $forum->getCount( $filters );
	}
}

