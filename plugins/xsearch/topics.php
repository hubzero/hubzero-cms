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
JPlugin::loadLanguage( 'plg_xsearch_topics' );

//-----------

class plgXSearchTopics extends JPlugin
{
	function plgXSearchTopics(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xsearch', 'topics' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onXSearchAreas() 
	{
		$areas = array(
			'topics' => JText::_('TOPICS')
		);
		return $areas;
	}

	//-----------

	function onXSearch( $searchquery, $limit=0, $limitstart=0, $areas=null )
	{
		$database =& JFactory::getDBO();

		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onXSearchAreas() ) && !array_intersect( $areas, array_keys( $this->onXSearchAreas() ) )) {
				return array();
			}
		}
		
		// Do we have a search term?
		$t = $searchquery->searchTokens;
		if (empty($t)) {
			return array();
		}
		
		ximport('wiki.page');
		
		// Instantiate some needed objects
		$wp = new WikiPage( $database );
		
		// Build query
		$filters = array();
		$filters['search'] = $searchquery;
		$filters['authorized'] = $this->_authorize();

		if (!$limit) {
			// Get a count
			$filters['select'] = 'count';
			
			$database->setQuery( $wp->buildPluginQuery( $filters ) );
			return $database->loadResult();
		} else {
			// Get results
			$filters['select'] = 'records';
			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;
			$filters['sortby'] = 'relevance';
			
			$query = $wp->buildPluginQuery( $filters );
			if (count($areas) > 1) {
				return $query;
			}
			
			$database->setQuery( $query );
			$rows = $database->loadObjectList();

			if ($rows) {
				foreach ($rows as $key => $row) 
				{
					if ($row->group != '' && $row->scope != '') {
						$rows[$key]->href = JRoute::_('index.php?option=com_groups&scope='.$row->category.'&pagename='.$row->alias);
					} else {
						$rows[$key]->href = JRoute::_('index.php?option=com_topics&scope='.$row->category.'&pagename='.$row->alias);
					}
				}
			}

			return $rows;
		}
	}

	//-----------

	function _authorize() 
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		// Check if they're a site admin (from LDAP)
		$xuser =& XFactory::getUser();
		if (is_object($xuser)) {
			$app =& JFactory::getApplication();
			if (in_array(strtolower($app->getCfg('sitename')), $xuser->get('admin'))) {
				return 'admin';
			}
		}
		
		return true;
	}

	//----------------------------------------------------------
	// Optional custom functions
	// uncomment to use
	//----------------------------------------------------------

	/*function documents() 
	{
		// ...
	}

	//-----------

	function before()
	{
		// ...
	}*/

	//-----------

	function out( $row, $keyword ) 
	{
		if (strstr( $row->href, 'index.php' )) {
			if ($row->area != '' && $row->category != '') {
				$row->href = JRoute::_('index.php?option=com_groups&scope='.$row->category.'&pagename='.$row->alias);
			} else {
				$row->href = JRoute::_('index.php?option=com_topics&scope='.$row->category.'&pagename='.$row->alias);
			}
		}
		$juri =& JURI::getInstance();
		if (substr($row->href,0,1) == '/') {
			$row->href = substr($row->href,1,strlen($row->href));
		}
			
		$html  = t.'<li class="topic">'.n;
		$html .= t.t.'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'.n;
		$html .= t.t.'<p class="details">';
		if ($row->area != '' && $row->category != '') {
			$html .= JText::_('GROUP_WIKI').': '.$row->area;
		} else {
			$html .= JText::_(strtoupper($row->section));
		}
		$html .= '</p>'.n;
		if ($row->itext) {
			//if ($row->access == 1 && !$authorized) {
			//	$html .= t.t.XSearchHtml::warning(JText::_('WIKI_NOT_AUTHORIZED')).n;
			//} else {
			$html .= t.t.'<p>&#133; '.stripslashes($row->itext).' &#133;</p>'.n;
			//}
		}
		$html .= t.t.'<p class="href">'.$juri->base().$row->href.'</p>'.n;
		$html .= t.'</li>'.n;
		return $html;
	}

	//-----------

	/*function after()
	{
		// ...
	}*/
}
