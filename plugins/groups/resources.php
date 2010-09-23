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
JPlugin::loadLanguage( 'plg_groups_resources' );

//-----------

class plgGroupsResources extends JPlugin
{
	private $_areas = null;
	private $_cats  = null;
	private $_total = null;
	
	//-----------
	
	public function plgGroupsResources(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'groups', 'resources' );
		$this->_params = new JParameter( $this->_plugin->params );
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'type.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'resource.php' );
	}

	//-----------
	
	public function &onGroupAreas( $authorized )
	{
		$areas = array(
			'resources' => JText::_('PLG_GROUPS_RESOURCES')
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
		
		$database =& JFactory::getDBO();
		$dispatcher =& JDispatcher::getInstance();

		// Incoming paging vars
		$sort = JRequest::getVar( 'sort', 'date' );
		$access = JRequest::getVar( 'access', 'all' );

		$config =& JComponentHelper::getParams( 'com_resources' );
		if ($return == 'metadata') {
			if ($config->get('show_ranking')) {
				$sort = 'ranking';
			} elseif ($config->get('show_rating')) {
				$sort = 'rating';
			}
		}

		// Trigger the functions that return the areas we'll be using
		$rareas = $this->getResourcesAreas();

		// Get the active category
		$area = JRequest::getVar( 'area', 'resources' );
		if ($area) {
			$activeareas = array($area);
		} else {
			$limit = 5;
			$activeareas = $rareas;
		}

		if ($return == 'metadata') {
			$ls = -1;
		} else {
			$ls = $limitstart;
		}

		// Get the search result totals
		$ts = $this->getResources(
				$group,
				$authorized,
				0,
				$ls,
				$sort,
				$access,
				$activeareas
			);
		$totals = array($ts);
		
		// Get the total results found (sum of all categories)
		$i = 0;
		$total = 0;
		$cats = array();
		foreach ($rareas as $c=>$t) 
		{
			$cats[$i]['category'] = $c;

			// Do sub-categories exist?
			if (is_array($t) && !empty($t)) {
				// They do - do some processing
				$cats[$i]['title'] = ucfirst($c);
				$cats[$i]['total'] = 0;
				$cats[$i]['_sub'] = array();
				$z = 0;
				// Loop through each sub-category
				foreach ($t as $s=>$st) 
				{
					// Ensure a matching array of totals exist
					if (is_array($totals[$i]) && !empty($totals[$i]) && isset($totals[$i][$z])) {
						// Add to the parent category's total
						$cats[$i]['total'] = $cats[$i]['total'] + $totals[$i][$z];
						// Get some info for each sub-category
						$cats[$i]['_sub'][$z]['category'] = $s;
						$cats[$i]['_sub'][$z]['title'] = $st;
						$cats[$i]['_sub'][$z]['total'] = $totals[$i][$z];
					}
					$z++;
				}
			} else {
				// No sub-categories - this should be easy
				$cats[$i]['title'] = $t;
				$cats[$i]['total'] = (!is_array($totals[$i])) ? $totals[$i] : 0;
			}

			// Add to the overall total
			$total = $total + intval($cats[$i]['total']);
			$i++;
		}

		// Do we have an active area?
		if (count($activeareas) == 1 && !is_array(current($activeareas))) {
			$active = $activeareas[0];
		} else {
			$active = '';
		}

		// Get the search results
		$r = $this->getResources(
				$group,
				$authorized,
				$limit,
				$limitstart,
				$sort,
				$access,
				$activeareas
			);
		$results = array($r);
		
		// Build the output
		switch ($return) 
		{
			case 'html':
				// Instantiate a vew
				ximport('Hubzero_Plugin_View');
				$view = new Hubzero_Plugin_View(
					array(
						'folder'=>'groups',
						'element'=>'resources',
						'name'=>'results'
					)
				);

				// Pass the view some info
				$view->option = $option;
				$view->group = $group;
				$view->authorized = $authorized;
				$view->totals = $totals;
				$view->results = $results;
				$view->cats = $cats;
				$view->active = $active;
				$view->limitstart = $limitstart;
				$view->limit = $limit;
				$view->total = $total;
				$view->sort = $sort;
				$view->access = $access;
				if ($this->getError()) {
					$view->setError( $this->getError() );
				}

				// Return the output
				$arr['html'] = $view->loadTemplate();
			break;
			
			case 'metadata':
				$arr['metadata'] = '<a href="'.JRoute::_('index.php?option='.$option.'&gid='.$group->cn.'&active=resources').'">'.JText::sprintf('PLG_GROUPS_RESOURCES_NUMBER_RESOURCES',$total).'</a>'."\n";
				
				// Instantiate a vew
				ximport('Hubzero_Plugin_View');
				$view = new Hubzero_Plugin_View(
					array(
						'folder'=>'groups',
						'element'=>'resources',
						'name'=>'dashboard'
					)
				);

				// Pass the view some info
				$view->option = $option;
				$view->group = $group;
				$view->authorized = $authorized;
				$view->config = $config;
				$view->results = $results[0];
				if ($this->getError()) {
					$view->setError( $this->getError() );
				}
				$arr['dashboard'] = $view->loadTemplate();
			break;
		}
		
		// Return the output
		return $arr;
	}

	//-----------
	
	public function onGroupDelete( $group ) 
	{
		// Get all the IDs for resources associated with this group
		$ids = $this->getResourceIDs( $group->get('cn') );
		
		// Start the log text
		$log = JText::_('PLG_GROUPS_RESOURCES_LOG').': ';
		if (count($ids) > 0) {
			$database =& JFactory::getDBO();
			
			// Loop through all the IDs for resources associated with this group
			foreach ($ids as $id)
			{
				// Disassociate the resource from the group and unpublish it
				$rr = new ResourcesResource( $database );
				$rr->load( $id->id );
				$rr->group_owner = '';
				$rr->published = 0;
				$rr->store();
				
				// Add the page ID to the log
				$log .= $id->id.' '."\n";
			}
		} else {
			$log .= JText::_('PLG_GROUPS_RESOURCES_NONE')."\n";
		}
		
		// Return the log
		return $log;
	}

	//-----------
	
	public function onGroupDeleteCount( $group ) 
	{
		return JText::_('PLG_GROUPS_RESOURCES_LOG').': '.count( $this->getResourceIDs( $group->get('cn') ));
	}

	//-----------
	
	private function getResourceIDs( $gid=NULL )
	{
		if (!$gid) {
			return array();
		}
		$database =& JFactory::getDBO();
		
		$rr = new ResourcesResource( $database );
		
		$database->setQuery( "SELECT id FROM ".$rr->getTableName()." AS r WHERE r.group_owner='".$gid."'" );
		return $database->loadObjectList();
	}
	
	//-----------
	
	public function getResourcesAreas()
	{
		$areas = $this->_areas;
		if (is_array($areas)) {
			return $areas;
		}
		
		$categories = $this->_cats;
		if (!is_array($categories)) {
			// Get categories
			$database =& JFactory::getDBO();
			$rt = new ResourcesType( $database );
			$categories = $rt->getMajorTypes();
			$this->_cats = $categories;
		}

		// Normalize the category names
		// e.g., "Oneline Presentations" -> "onlinepresentations"
		$normalized_valid_chars = 'a-zA-Z0-9';
		$cats = array();
		for ($i = 0; $i < count($categories); $i++) 
		{	
			$normalized = preg_replace("/[^$normalized_valid_chars]/", "", $categories[$i]->type);
			$normalized = strtolower($normalized);

			//$categories[$i]->title = $normalized;
			$cats[$normalized] = $categories[$i]->type;
		}

		$areas = array(
			'resources' => $cats
		);
		$this->_areas = $areas;
		return $areas;
	}
	
	//-----------
	
	public function getResources( $group, $authorized, $limit=0, $limitstart=0, $sort='date', $access='all', $areas=null )
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			$ars = $this->getResourcesAreas();
			if (!array_intersect( $areas, $ars ) 
			&& !array_intersect( $areas, array_keys( $ars ) )
			&& !array_intersect( $areas, array_keys( $ars['resources'] ) )) {
				return array();
			}
		}

		// Do we have a member ID?
		if (!$group->get('cn')) {
			return array();
		}
		
		$database =& JFactory::getDBO();

		// Instantiate some needed objects
		$rr = new ResourcesResource( $database );
		
		// Build query
		$filters = array();
		$filters['now'] = date( 'Y-m-d H:i:s', time() + 0 * 60 * 60 );
		$filters['sortby'] = $sort;
		$filters['group'] = $group->get('cn');
		$filters['access'] = $access;
		$filters['authorized'] = $authorized;

		// Get categories
		$categories = $this->_cats;
		if (!is_array($categories)) {
			$rt = new ResourcesType( $database );
			$categories = $rt->getMajorTypes();
		}

		// Normalize the category names
		// e.g., "Oneline Presentations" -> "onlinepresentations"
		$cats = array();
		$normalized_valid_chars = 'a-zA-Z0-9';
		for ($i = 0; $i < count($categories); $i++) 
		{	
			$normalized = preg_replace("/[^$normalized_valid_chars]/", "", $categories[$i]->type);
			$normalized = strtolower($normalized);

			$cats[$normalized] = array();
			$cats[$normalized]['id'] = $categories[$i]->id;
		}

		if ($limit) {
			if ($this->_total != null) {
				$total = 0;
				$t = $this->_total;
				foreach ($t as $l) 
				{
					$total += $l;
				}
			}
			if ($total == 0) {
				return array();
			}
			
			$filters['select'] = 'records';
			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;
			
			// Check the area of return. If we are returning results for a specific area/category
			// we'll need to modify the query a bit
			if (count($areas) == 1 && !isset($areas['resources']) && $areas[0] != 'resources') {
				$filters['type'] = $cats[$areas[0]]['id'];
			}

			// Get results
			$database->setQuery( $rr->buildPluginQuery( $filters ) );
			$rows = $database->loadObjectList();

			// Did we get any results?
			if ($rows) {
				// Loop through the results and set each item's HREF
				foreach ($rows as $key => $row) 
				{
					if ($row->alias) {
						$rows[$key]->href = JRoute::_('index.php?option=com_resources&alias='.$row->alias);
					} else {
						$rows[$key]->href = JRoute::_('index.php?option=com_resources&id='.$row->id);
					}
				}
			}

			// Return the results
			return $rows;
		} else {
			$filters['select'] = 'count';

			// Get a count
			$counts = array();
			$ares = $this->getResourcesAreas();
			foreach ($ares as $area=>$val) 
			{
				if (is_array($val)) {
					$i = 0;
					foreach ($val as $a=>$t) 
					{
						if ($limitstart == -1) {
							if ($i == 0) {
								$database->setQuery( $rr->buildPluginQuery( $filters ) );
								$counts[] = $database->loadResult();
							} else {
								$counts[] = 0;
							}
						} else {
							$filters['type'] = $cats[$a]['id'];
						
							// Execute a count query for each area/category
							$database->setQuery( $rr->buildPluginQuery( $filters ) );
							$counts[] = $database->loadResult();
						}
						$i++;
					}
				}
			}

			// Return the counts
			$this->_total = $counts;
			return $counts;
		}
	}
	
	//----------------------------------------------------------
	// Optional custom functions
	// uncomment to use
	//----------------------------------------------------------

	public function documents() 
	{
		// Push some CSS and JS to the tmeplate that may be needed
	 	$document =& JFactory::getDocument();
		$document->addScript('components'.DS.'com_resources'.DS.'resources.js');
		
		ximport('Hubzero_Document');
		Hubzero_Document::addComponentStylesheet('com_resources');

		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'helper.php' );
		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'usage.php' );
	}
	
	//-----------
	
	/*public function before()
	{
		// ...
	}*/
	
	//-----------

	public function out( $row, $authorized=false ) 
	{
		$database =& JFactory::getDBO();
		
		// Instantiate a helper object
		$RE = new ResourcesHelper($row->id, $database);
		$RE->getContributors();
		
		// Get the component params and merge with resource params
		$config =& JComponentHelper::getParams( 'com_resources' );
		$rparams =& new JParameter( $row->params );
		$params = $config;
		$params->merge( $rparams );

		// Set the display date
		switch ($params->get('show_date')) 
		{
			case 0: $thedate = ''; break;
			case 1: $thedate = JHTML::_('date', $row->created, '%d %b %Y');    break;
			case 2: $thedate = JHTML::_('date', $row->modified, '%d %b %Y');   break;
			case 3: $thedate = JHTML::_('date', $row->publish_up, '%d %b %Y'); break;
		}

		if (strstr( $row->href, 'index.php' )) {
			$row->href = JRoute::_($row->href);
		}
		$juri =& JURI::getInstance();
		if (substr($row->href,0,1) == '/') {
			$row->href = substr($row->href,1,strlen($row->href));
		}

		$html  = "\t".'<li class="';
		switch ($row->access)
		{
			case 1: $html .= 'registered'; break;
			case 2: $html .= 'special';    break;
			case 3: $html .= 'protected';  break;
			case 4: $html .= 'private';    break;
			case 0:
			default: $html .= 'public'; break;
		}
		$html .= ' resource">'."\n";
		$html .= "\t\t".'<p class="';
		/*if ($row->access == 4) {
			$html .= 'private ';
		} elseif ($row->access == 3) {
			$html .= 'protected ';
		}*/
		$html .= 'title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
			
		if ($params->get('show_ranking')) {
			$RE->getCitationsCount();
			$RE->getLastCitationDate();
			
			if ($row->category == 7) {
				$stats = new ToolStats($database, $row->id, $row->category, $row->rating, $RE->citationsCount, $RE->lastCitationDate);
			} else {
				$stats = new AndmoreStats($database, $row->id, $row->category, $row->rating, $RE->citationsCount, $RE->lastCitationDate);
			}
			$statshtml = $stats->display();
			
			$row->ranking = round($row->ranking, 1);
			
			$html .= "\t\t".'<div class="metadata">'."\n";
			$r = (10*$row->ranking);
			if (intval($r) < 10) {
				$r = '0'.$r;
			}
			$html .= "\t\t\t".'<dl class="rankinfo">'."\n";
			$html .= "\t\t\t\t".'<dt class="ranking"><span class="rank-'.$r.'">'.JText::_('PLG_GROUPS_RESOURCES_THIS_HAS').'</span> '.number_format($row->ranking,1).' '.JText::_('PLG_GROUPS_RESOURCES_RANKING').'</dt>'."\n";
			$html .= "\t\t\t\t".'<dd>'."\n";
			$html .= "\t\t\t\t\t".'<p>'.JText::_('PLG_GROUPS_RESOURCES_RANKING_EXPLANATION').'</p>'."\n";
			$html .= "\t\t\t\t\t".'<div>'."\n";
			$html .= $statshtml;
			$html .= "\t\t\t\t\t".'</div>'."\n";
			$html .= "\t\t\t\t".'</dd>'."\n";
			$html .= "\t\t\t".'</dl>'."\n";
			$html .= "\t\t".'</div>'."\n";
		} elseif ($params->get('show_rating')) {
			switch ($row->rating) 
			{
				case 0.5: $class = ' half-stars';      break;
				case 1:   $class = ' one-stars';       break;
				case 1.5: $class = ' onehalf-stars';   break;
				case 2:   $class = ' two-stars';       break;
				case 2.5: $class = ' twohalf-stars';   break;
				case 3:   $class = ' three-stars';     break;
				case 3.5: $class = ' threehalf-stars'; break;
				case 4:   $class = ' four-stars';      break;
				case 4.5: $class = ' fourhalf-stars';  break;
				case 5:   $class = ' five-stars';      break;
				case 0:
				default:  $class = ' no-stars';      break;
			}
			
			$html .= "\t\t".'<div class="metadata">'."\n";
			$html .= "\t\t\t".'<p class="rating"><span class="avgrating'.$class.'"><span>'.JText::sprintf('PLG_GROUPS_RESOURCES_OUT_OF_5_STARS',$row->rating).'</span>&nbsp;</span></p>'."\n";
			$html .= "\t\t".'</div>'."\n";
		}

		$html .= "\t\t".'<p class="details">'.$thedate.' <span>|</span> '.$row->area;
		if ($RE->contributors) {
			$html .= ' <span>|</span> '.JText::_('PLG_GROUPS_RESOURCES_CONTRIBUTORS').': '.$RE->contributors;
		}
		$html .= '</p>'."\n";
		if ($row->itext) {
			$html .= "\t\t".Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->itext)), 200)."\n";
		} else if ($row->ftext) {
			$html .= "\t\t".Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->ftext)), 200)."\n";
		}
		$html .= "\t\t".'<p class="href">'.$juri->base().$row->href.'</p>'."\n";
		$html .= "\t".'</li>'."\n";
		return $html;
	}

	//-----------
	
	/*public function after()
	{
		// ...
	}*/
}
