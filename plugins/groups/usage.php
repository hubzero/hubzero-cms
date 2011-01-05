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
	
	public function &onGroupAreas( $authorized ) 
	{
		$areas = array(
			'usage' => JText::_('USAGE')
		);
		return $areas;
	}

	//-----------

	public function onGroup( $group, $option, $authorized, $limit=0, $limitstart=0, $action='', $areas=null )
	{
		$return = 'html';
		$active = 'usage';
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
