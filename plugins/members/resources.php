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
JPlugin::loadLanguage( 'plg_members_resources' );

//-----------

class plgMembersResources extends JPlugin
{
	private $_areas = null;
	private $_cats  = null;
	private $_total = null;
	
	//-----------
	
	function plgMembersResources(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'resources' );
		$this->_params = new JParameter( $this->_plugin->params );
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.type.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php' );
	}
	
	//-----------

	function onMembersContributionsAreas( $authorized )
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

			$cats[$normalized] = $categories[$i]->type;
		}

		$areas = array(
			'resources' => $cats
		);
		$this->_areas = $areas;
		return $areas;
	}

	//-----------

	function onMembersContributionsCount( $authorized ) 
	{
		$query = "SELECT COUNT(R.id) FROM #__resources AS R, #__author_assoc AS AA WHERE AA.authorid=m.uidNumber AND R.id = AA.subid AND AA.subtable = 'resources' AND R.published=1 AND R.standalone=1";
		return $query;
	}

	//-----------

	function onMembersContributions( $member, $option, $authorized, $limit=0, $limitstart=0, $sort, $areas=null )
	{
		$database =& JFactory::getDBO();

		if (is_array( $areas ) && $limit) {
			$ars = $this->onMembersContributionsAreas( $authorized );
			if (!array_intersect( $areas, $ars ) 
			&& !array_intersect( $areas, array_keys( $ars ) )
			&& !array_intersect( $areas, array_keys( $ars['resources'] ) )) {
				return array();
			}
		}

		// Do we have a member ID?
		if (get_class($member) == 'XProfile') {
			if (!$member->get('uidNumber')) {
				return array();
			} else {
				$uidNumber = $member->get('uidNumber');
			}
		} else {
			if (!$member->uidNumber) {
				return array();
			} else {
				$uidNumber = $member->uidNumber;
			}
		}

		// Instantiate some needed objects
		$rr = new ResourcesResource( $database );
		
		// Build query
		$filters = array();
		$filters['author'] = $uidNumber;
		$filters['sortby'] = $sort;
		$filters['authorized'] = $authorized;

		ximport('xuserhelper');
		$filters['usergroups'] = XUserHelper::getGroups($uidNumber, 'all');

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
			//if (count($areas) == 1 && key($areas[0]) != 'resources') {
			if (count($areas) == 1 && $areas[0] != 'resources') {
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
			$ares = $this->onMembersContributionsAreas( $authorized );
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

	//-----------

	function out( $row, $authorized=false ) 
	{
		$database =& JFactory::getDBO();

		// Instantiate a helper object
		$helper = new ResourcesHelper($row->id, $database);
		$helper->getContributors();

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
		
		$html  = t.'<li class="resource">'.n;
		$html .= t.t.'<p class="';
		if ($row->access == 4) {
			$html .= 'private ';
		} elseif ($row->access == 3) {
			$html .= 'protected ';
		}
		$html .= 'title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a>';
		if ($authorized) {
			switch ($row->state) 
			{
				case 5: $html .= ' <span style="color: blue;">'.JText::_('RESOURCE_STATUS_PENDING').' (internal)</span>'.n; break;
				case 4: $html .= ' <span style="color: #000;">'.JText::_('RESOURCE_STATUS_DELETED').'</span>'.n; break;
				case 3: $html .= ' <span style="color: blue;">'.JText::_('RESOURCE_STATUS_PENDING').'</span>'.n; break;
				case 2: $html .= ' <span style="color: orange;">'.JText::_('RESOURCE_STATUS_DRAFT').'</span>'.n; break;
				case 1: $html .= ' <span style="color: green;">'.JText::_('RESOURCE_STATUS_PUBLISHED').'</span>'.n; break;
				case 0:
				default: $html .= ' <span style="color: red;">'.JText::_('RESOURCE_STATUS_UNPUBLISHED').'</span>'.n; break;
			}
		}
		$html .= '</p>'.n;
		if ($params->get('show_ranking')) {
			$helper->getCitationsCount();
			$helper->getLastCitationDate();
			
			if ($row->category == 7) {
				$stats = new ToolStats($database, $row->id, $row->category, $row->rating, $helper->citationsCount, $helper->lastCitationDate);
			} else {
				$stats = new AndmoreStats($database, $row->id, $row->category, $row->rating, $helper->citationsCount, $helper->lastCitationDate);
			}
			$statshtml = $stats->display();
			
			$row->ranking = round($row->ranking, 1);
			
			$html .= t.t.'<div class="metadata">'.n;
			$html .= plgMembersResources::ranking( $row->ranking, $statshtml, $row->id, $row->href );
			$html .= t.t.'</div>'.n;
		} elseif ($params->get('show_rating')) {
			$html .= t.t.'<div class="metadata">'.n;
			$html .= t.t.t.'<p class="rating"><span class="avgrating'.plgMembersResources::getRatingClass( $row->rating ).'"><span>'.JText::sprintf('RESOURCES_OUT_OF_5_STARS',$row->rating).'</span>&nbsp;</span></p>'.n;
			$html .= t.t.'</div>'.n;
		}
		$html .= t.t.'<p class="details">'.$thedate.' <span>|</span> '.$row->area;
		if ($helper->contributors) {
			$html .= ' <span>|</span> '.JText::_('CONTRIBUTORS').': '.$helper->contributors;
		}
		$html .= '</p>'.n;
		if ($row->itext) {
			$html .= t.t.MembersHtml::shortenText(stripslashes($row->itext)).n;
		} else if ($row->ftext) {
			$html .= t.t.MembersHtml::shortenText(stripslashes($row->ftext)).n;
		}
		$html .= t.'</li>'.n;
		return $html;
	}

	//-----------

	function ranking( $rank, $stats, $id, $sef='' )
	{
		$r = (10*$rank);
		if (intval($r) < 10) {
			$r = '0'.$r;
		}

		$html  = '<dl class="rankinfo">'.n;
		$html .= ' <dt class="ranking"><span class="rank-'.$r.'">'.JText::_('THIS_RESOURCE_HAS').'</span> '.number_format($rank,1).' '.JText::_('RANKING').'</dt>'.n;
		$html .= ' <dd>'.n;
		$html .= t.'<p>'.n;
		$html .= t.t.JText::_('RANKING_EXPLANATION').n;
		$html .= t.'</p>'.n;
		$html .= t.'<div>'.n;
		$html .= $stats;
		$html .= t.'</div>'.n;
		$html .= ' </dd>'.n;
		$html .= '</dl>'.n;
		return $html;
	}

	//-----------
	
	function getRatingClass($rating=0)
	{
		switch ($rating) 
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
		return $class;
	}

	//-----------

	function documents() 
	{
		// Push some CSS and JS to the tmeplate that may be needed
	 	//$document =& JFactory::getDocument();
		//$document->addStyleSheet('components'.DS.'com_resources'.DS.'resources.css','text/css','screen');
		//$document->addScript('components'.DS.'com_resources'.DS.'resources.js');
		ximport('xdocument');
		XDocument::addComponentStylesheet('com_resources');

		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.extended.php' );
		ximport('resourcestats');
	}
	
	//-----------

	function onMembersFavoritesAreas( $authorized )
	{
		return $this->onMembersContributionsAreas( $authorized );
	}

	//-----------

	function onMembersFavorites( $member, $option, $authorized, $limit=0, $limitstart=0, $areas=null )
	{
		$database =& JFactory::getDBO();

		if (is_array( $areas ) && $limit) {
			$ars = $this->onMembersFavoritesAreas( $authorized );
			if (!array_intersect( $areas, $ars ) 
			&& !array_intersect( $areas, array_keys( $ars ) )
			&& !array_intersect( $areas, array_keys( $ars['resources'] ) )) {
				return array();
			}
		}

		// Do we have a member ID?
		if (get_class($member) == 'XProfile') {
			if (!$member->get('uidNumber')) {
				return array();
			} else {
				$uidNumber = $member->get('uidNumber');
			}
		} else {
			if (!$member->uidNumber) {
				return array();
			} else {
				$uidNumber = $member->uidNumber;
			}
		}
		
		// Instantiate some needed objects
		$rr = new ResourcesResource( $database );
		
		// Build query
		$filters = array();
		$filters['favorite'] = $uidNumber;
		$filters['sortby'] = 'date';
		
		ximport('xuserhelper');
		$filters['usergroups'] = XUserHelper::getGroups($uidNumber, 'all');

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
			
			if (count($areas) == 1 && key($areas) != 'resources') {
				$filters['type'] = $cats[$areas[0]]['id'];
			}

			// Get results
			$database->setQuery( $rr->buildPluginQuery( $filters ) );
			$rows = $database->loadObjectList();

			if ($rows) {
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
			$ares = $this->onMembersFavoritesAreas( $authorized );
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
}