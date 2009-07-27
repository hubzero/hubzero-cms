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
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.type.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php' );
	}

	//-----------
	
	public function &onGroupAreas( $authorized )
	{
		$areas = array(
			'resources' => JText::_('GROUPS_RESOURCES')
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
				$arr['html'] = $this->display( $authorized, $totals, $results, $cats, $active, $option, $limitstart, $limit, $total, $group, $sort, $access );
			break;
			
			case 'metadata':
				$arr['metadata'] = '<a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->cn.a.'active=resources').'">'.JText::sprintf('NUMBER_RESOURCES',$total).'</a>'.n;
				$arr['dashboard'] = $this->dashboard( $group, $results[0], $authorized, $config );
			break;
		}
		
		// Return the output
		return $arr;
	}

	//-----------

	private function dashboard( $group, $results, $authorized, $config ) 
	{
		// Did we find any results?
		if ($results) {
			include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.html.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.review.php' );
			
			$this->documents();
			
			$database =& JFactory::getDBO();
			$juser =& JFactory::getUser();

			$html  = '<table class="related-resources" summary="'.JText::_('RESOURCES_DASHBOARD_SUMMARY').'">'.n;
			$html .= t.'<tbody>'.n; 
			foreach ($results as $line)
			{
				$class = ResourcesHtml::getRatingClass( $line->rating );

				$helper = new ResourcesHelper( $line->id, $database );
				$helper->getContributors();

				// If the user is logged in, get their rating for this resource
				if (!$juser->get('guest')) {
					$mr = new ResourcesReview( $database );
					$myrating = $mr->loadUserRating( $line->id, $juser->get('id') );
				} else {
					$myrating = 0;
				}
				$myclass = ResourcesHtml::getRatingClass( $myrating );

				// Encode some potentially troublesome characters
				$line->title = ResourcesHtml::encode_html( $line->title );

				// Make sure we have an SEF, otherwise it's a querystring
				if (strstr($line->href,'option=')) {
					$d = a;
				} else {
					$d = '?';
				}

				$html .= t.t.'<tr>'.n; 
				if ($config->get('show_ranking')) {
					// Format the ranking
					$line->ranking = round($line->ranking, 1);
					$r = (10*$line->ranking);
					if (intval($r) < 10) {
						$r = '0'.$r;
					}
					
					$html .= t.t.t.'<td class="ranking">'.number_format($line->ranking,1).' <span class="rank-'.$r.'">'.JText::_('RESOURCES_RANKING').'</span></td>'.n;
				} elseif ($config->get('show_rating')) {
					$html .= t.t.t.'<td class="rating"><span class="avgrating'.$class.'"><span>'.JText::sprintf('RESOURCES_OUT_OF_5_STARS',$line->rating).'</span>&nbsp;</span></td>'.n;
				}
				$html .= t.t.t.'<td>';
				$html .= '<a href="'.$line->href.'" class="fixedResourceTip" title="DOM:rsrce'.$line->id.'">'. $line->title . '</a>'.n;
				$html .= t.t.'<div style="display:none;" id="rsrce'.$line->id.'">'.n;
				$html .= t.t.t.ResourcesHtml::hed(4,$line->title).n;
				$html .= t.t.t.'<div>'.n;
				$html .= t.t.t.t.'<table>'.n;
				$html .= ResourcesHtml::tableRow(JText::_('RESOURCES_TYPE'),$line->section);
				if ($helper->contributors) {
					$html .= ResourcesHtml::tableRow(JText::_('RESOURCES_CONTRIBUTORS'),$helper->contributors);
				}
				$html .= ResourcesHtml::tableRow(JText::_('RESOURCES_DATE'),JHTML::_('date',$line->publish_up, '%d %b, %Y'));
				$html .= ResourcesHtml::tableRow(JText::_('RESOURCES_AVG_RATING'),'<span class="avgrating'.$class.'"><span>'.JText::sprintf('RESOURCES_OUT_OF_5_STARS',$line->rating).'</span>&nbsp;</span> ('.$line->times_rated.')');
				$starz  = t.t.t.t.t.'<ul class="starsz'.$myclass.'">'.n;
				$starz .= t.t.t.t.t.' <li class="str1"><a href="'.$line->href.$d.'task=addreview'.a.'myrating=1#reviewform" title="'.JText::_('RESOURCES_RATING_POOR').'">'.JText::_('RESOURCES_RATING_1_STAR').'</a></li>'.n;
				$starz .= t.t.t.t.t.' <li class="str2"><a href="'.$line->href.$d.'task=addreview'.a.'myrating=2#reviewform" title="'.JText::_('RESOURCES_RATING_FAIR').'">'.JText::_('RESOURCES_RATING_2_STARS').'</a></li>'.n;
				$starz .= t.t.t.t.t.' <li class="str3"><a href="'.$line->href.$d.'task=addreview'.a.'myrating=3#reviewform" title="'.JText::_('RESOURCES_RATING_GOOD').'">'.JText::_('RESOURCES_RATING_3_STARS').'</a></li>'.n;
				$starz .= t.t.t.t.t.' <li class="str4"><a href="'.$line->href.$d.'task=addreview'.a.'myrating=4#reviewform" title="'.JText::_('RESOURCES_RATING_VERY_GOOD').'">'.JText::_('RESOURCES_RATING_4_STARS').'</a></li>'.n;
				$starz .= t.t.t.t.t.' <li class="str5"><a href="'.$line->href.$d.'task=addreview'.a.'myrating=5#reviewform" title="'.JText::_('RESOURCES_RATING_EXCELLENT').'">'.JText::_('RESOURCES_RATING_5_STARS').'</a></li>'.n;
				$starz .= t.t.t.t.t.'</ul>'.n;
				$html .= ResourcesHtml::tableRow(JText::_('RESOURCES_RATE_THIS'),$starz);
				$html .= t.t.t.t.'</table>';
				$html .= t.t.t.'</div>'.n;
				$html .= t.t.t.ResourcesHtml::shortenText( $line->itext ).n;
				$html .= t.t.'</div>'.n;
				$html .= '</td>'.n;
				$html .= t.t.t.'<td class="type">'.$line->area.'</td>'.n;
				$html .= t.t.'</tr>'.n;
			}
			$html .= t.'</tbody>'.n;
			$html .= '</table>'.n;
		} else {
			$html  = '<p>'.JText::_('NO_RESOURCES_FOUND').'</p>'.n;
		}
		
		return $html;
	}

	//-----------
	
	public function onGroupDelete( $group ) 
	{
		// Get all the IDs for resources associated with this group
		$ids = $this->getResourceIDs( $group->get('cn') );
		
		// Start the log text
		$log = JText::_('GROUPS_RESOURCES_LOG').': ';
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
				$log .= $id->id.' '.n;
			}
		} else {
			$log .= JText::_('NONE').n;
		}
		
		// Return the log
		return $log;
	}

	//-----------
	
	public function onGroupDeleteCount( $group ) 
	{
		return JText::_('GROUPS_RESOURCES_LOG').': '.count( $this->getResourceIDs( $group->get('cn') ));
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
		$database =& JFactory::getDBO();

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
	
	//-----------
	
	public function categories( $cats, $active, $total, $sef, $sort, $access, $authorized ) 
	{
		if (strstr( $sef, 'index' )) {
			$sef .= a;
		} else {
			$sef .= '?';
		}

		// Add the "all" category
		//$all = array('category'=>'','title'=>JText::_('ALL_RESOURCES_CATEGORIES'),'total'=>$total);
		
		//array_unshift($cats, $all);

		// An array for storing all the links we make
		$links = array();

		// Loop through each category
		foreach ($cats as $cat) 
		{
			// Only show categories that have returned search results
			if ($cat['total'] > 0) {
				// Is this the active category?
				$a = '';
				if ($cat['category'] == $active) {
					$a = ' class="active"';
				}
				// If we have a specific category, prepend it to the search term
				$blob = '';
				if ($cat['category']) {
					$blob = $cat['category'];
				}
				// Build the HTML
				$l = t.'<li'.$a.'><a href="'.$sef.'area='. urlencode(stripslashes($blob)) .'">' . $cat['title'] . ' ('.$cat['total'].')</a>';
				// Are there sub-categories?
				if (isset($cat['_sub']) && is_array($cat['_sub'])) {
					// An array for storing the HTML we make
					$k = array();
					// Loop through each sub-category
					foreach ($cat['_sub'] as $subcat) 
					{
						// Only show sub-categories that returned search results
						if ($subcat['total'] > 0) {
							// Is this the active category?
							$a = '';
							if ($subcat['category'] == $active) {
								$a = ' class="active"';
							}
							// If we have a specific category, prepend it to the search term
							$blob = '';
							if ($subcat['category']) {
								$blob = $subcat['category'];
							}
							// Build the HTML
							$k[] = t.t.t.'<li'.$a.'><a href="'.$sef.'area='. urlencode(stripslashes($blob)) .'">' . $subcat['title'] . ' ('.$subcat['total'].')</a></li>';
						}
					}
					// Do we actually have any links?
					// NOTE: this method prevents returning empty list tags "<ul></ul>"
					if (count($k) > 0) {
						$l .= t.t.'<ul>'.n;
						$l .= implode( n, $k );
						$l .= t.t.'</ul>'.n;
					}
				}
				$l .= '</li>';
				$links[] = $l;
			}
		}
	
			$html  = '<fieldset>'.n;
			$html .= t.'<label>'.n;
			$html .= t.t.JText::_('RESOURCES_SORT_BY').n;
			$html .= t.t.'<select name="sort">'.n;
			$xhub =& XFactory::getHub();
			if ($xhub->getCfg('hubShowRanking')) {
				$html .= t.t.t.'<option value="ranking"';
				if ($sort == 'ranking') {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('RESOURCES_SORT_BY_RANKING').'</option>'.n;
			} else {
				$html .= t.t.t.'<option value="rating"';
				if ($sort == 'rating') {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('RESOURCES_SORT_BY_RATING').'</option>'.n;
			}
			$html .= t.t.t.'<option value="date"';
			if ($sort == 'date') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('RESOURCES_SORT_BY_DATE').'</option>'.n;
			$html .= t.t.t.'<option value="title"';
			if ($sort == 'title') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('RESOURCES_SORT_BY_TITLE').'</option>'.n;
			$html .= t.t.'</select>'.n;
			$html .= t.'</label>'.n;
			$html .= t.'<label>'.n;
			$html .= t.t.JText::_('RESOURCES_ACCESS').n;
			$html .= t.t.'<select name="access">'.n;
			$html .= t.t.t.'<option value="all"';
			if ($access == 'all') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('RESOURCES_ACCESS_ALL').'</option>'.n;
			$html .= t.t.t.'<option value="public"';
			if ($access == 'public') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('RESOURCES_ACCESS_PUBLIC').'</option>'.n;
			$html .= t.t.t.'<option value="protected"';
			if ($access == 'protected') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('RESOURCES_ACCESS_PROTECTED').'</option>'.n;
			if ($authorized) {
				$html .= t.t.t.'<option value="private"';
				if ($access == 'private') {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('RESOURCES_ACCESS_PRIVATE').'</option>'.n;
			}
			$html .= t.t.'</select>'.n;
			$html .= t.'</label>'.n;
			//$html .= t.'<input type="hidden" value="'.$active.'" />'.n;
			$html .= t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
			$html .= '</fieldset>'.n;
		// Do we actually have any links?
		// NOTE: this method prevents returning empty list tags "<ul></ul>"
		if (count($links) > 0) {
			// Yes - output the necessary HTML
			$html .= '<ul class="sub-nav">'.n;
			$html .= implode( n, $links );
			$html .= '</ul>'.n;
		}
			$html .= '<p class="add"><a href="'.JRoute::_('index.php?option=com_contribute&task=start').'">'.JText::_('START_A_CONTRIBUTION').'</a></p>'.n;
		//} else {
			// No - nothing to output
			//$html = '';
		//}
		$html .= t.'<input type="hidden" name="area" value="'.$active.'" />'.n;

		return $html;
	}
	
	//-----------
	
	public function display( $authorized, $totals, $results, $cats, $active, $option, $start=0, $limit=0, $total, $group, $sort, $access ) 
	{
		$sef = JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'active=resources');
		
		$html  = GroupsHtml::hed(3,'<a name="resources"></a>'.JText::_('RESOURCES')).n;
		$html .= '<form method="get" action="'.$sef.'">'.n;
		$html .= GroupsHtml::div( 
					$this->categories( $cats, $active, $total, $sef, $sort, $access, $authorized ), 
					'aside'
				);
		
		$html .= '<div class="subject">'.n;
		
		$foundresults = false;
		$dopaging = false;
	
		$k = 0;
		foreach ($results as $category)
		{
			$amt = count($category);
			
			if ($amt > 0) {
				$foundresults = true;
				
				$name  = $cats[$k]['title'];
				$total = $cats[$k]['total'];
				$divid = 'search'.$cats[$k]['category'];
				
				// Is this category the active category?
				if (!$active || $active == $cats[$k]['category']) {
					// It is - get some needed info
					$name  = $cats[$k]['title'];
					$total = $cats[$k]['total'];
					$divid = 'search'.$cats[$k]['category'];
					
					if ($active == $cats[$k]['category']) {
						$dopaging = true;
					}
				} else {
					// It is not - does this category have sub-categories?
					if (isset($cats[$k]['_sub']) && is_array($cats[$k]['_sub'])) {
						// It does - loop through them and see if one is the active category
						foreach ($cats[$k]['_sub'] as $sub) 
						{
							if ($active == $sub['category']) {
								// Found an active category
								$name  = $sub['title'];
								$total = $sub['total'];
								$divid = 'search'.$sub['category'];
								
								$dopaging = true;
								break;
							}
						}
					}
				}
				
				$num  = $total .' result';
				$num .= ($total > 1) ? 's' : '';
			
				// A function for category specific items that may be needed
				// Check if a function exist (using old style plugins)
				$f = 'plgGroups'.ucfirst($cats[$k]['category']).'Doc';
				if (function_exists($f)) {
					$f();
				}
				// Check if a method exist (using JPlugin style)
				$obj = 'plgGroups'.ucfirst($cats[$k]['category']);
				if (method_exists($obj, 'documents')) {
					$html .= call_user_func( array($obj,'documents') );
				}
			
				// Build the category HTML
				$html .= '<h4 class="category-header opened" id="rel-'.$divid.'">'.$name.' <small>'.$num.'</small></h4>'.n;
				$html .= '<div class="category-wrap" id="'.$divid.'">'.n;
				
				// Does this category have custom output?
				// Check if a function exist (using old style plugins)
				$func = 'plgGroups'.ucfirst($cats[$k]['category']).'Before';
				if (function_exists($func)) {
					$html .= $func();
				}
				// Check if a method exist (using JPlugin style)
				$obj = 'plgGroups'.ucfirst($cats[$k]['category']);
				if (method_exists($obj, 'before')) {
					$html .= call_user_func( array($obj,'before') );
				}
				
				$html .= '<ol class="search results">'.n;			
				foreach ($category as $row) 
				{
					$row->href = str_replace('&amp;', '&', $row->href);
					$row->href = str_replace('&', '&amp;', $row->href);
					
					// Does this category have a unique output display?
					$func = 'plgGroups'.ucfirst($row->section).'Out';
					// Check if a method exist (using JPlugin style)
					$obj = 'plgGroups'.ucfirst($cats[$k]['category']);
					
					if (function_exists($func)) {
						$html .= $func( $row, $authorized );
					} elseif (method_exists($obj, 'out')) {
						$html .= call_user_func( array($obj,'out'), $row, $authorized );
					} else {
						$html .= t.'<li>'.n;
						$html .= t.t.'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'.n;
						if ($row->text) {
							$html .= t.t.GroupsHtml::shortenText(stripslashes($row->text)).n;
						}
						$html .= t.'</li>'.n;
					}
				}
				$html .= '</ol>'.n;
				// Initiate paging if we we're displaying an active category
				if ($dopaging) {
					jimport('joomla.html.pagination');
					$pageNav = new JPagination( $total, $start, $limit );

					$pf = $pageNav->getListFooter();
					
					$nm = str_replace('com_','',$option);

					$pf = str_replace($nm.'/?',$nm.'/'.$group->get('cn').'/'.$active.'/?',$pf);
					$html .= $pf;
				} else {
					$html .= '<p class="moreresults">'.JText::sprintf('NUMBER_RESOURCES_SHOWN', $amt);
					// Ad a "more" link if necessary
					if ($totals[$k] > 5) {
						$qs = 'area='.urlencode(strToLower($cats[$k]['category']));
						$seff = JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'active=resources');
						if (strstr( $seff, 'index' )) {
							$seff .= a.$qs;
						} else {
							$seff .= '?'.$qs;
						}
						$html .= ' | <a href="'.$seff.'">'.JText::_('MORE_RESOURCES').'</a>';
					}
				}
				$html .= '</p>'.n.n;
				$html .= '</div><!-- / #'.$divid.' -->'.n;
			}
			$k++;
		}
		if (!$foundresults) {
			$html .= GroupsHtml::warning( JText::_('NO_RESOURCES_FOUND') );
		}
		$html .= '</div><!-- / .subject -->'.n;
		$html .= GroupsHtml::div('','clear').n;
		$html .= '</form>'.n;
		
		return $html;
	}
	
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

		$html  = t.'<li class="resource">'.n;
		$html .= t.t.'<p class="';
		if ($row->access == 4) {
			$html .= 'private ';
		} elseif ($row->access == 3) {
			$html .= 'protected ';
		}
		$html .= 'title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'.n;
			
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
			
			$html .= t.t.'<div class="metadata">'.n;
			$html .= plgGroupsResources::ranking( $row->ranking, $statshtml, $row->id, $row->href );
			$html .= t.t.'</div>'.n;
		} elseif ($params->get('show_rating')) {
			$html .= t.t.'<div class="metadata">'.n;
			$html .= t.t.t.'<p class="rating"><span class="avgrating'.plgGroupsResources::getRatingClass( $row->rating ).'"><span>'.JText::sprintf('RESOURCES_OUT_OF_5_STARS',$row->rating).'</span>&nbsp;</span></p>'.n;
			$html .= t.t.'</div>'.n;
		}

		$html .= t.t.'<p class="details">'.$thedate.' <span>|</span> '.$row->area;
		if ($RE->contributors) {
			$html .= ' <span>|</span> '.JText::_('CONTRIBUTORS').': '.$RE->contributors;
		}
		$html .= '</p>'.n;
		if ($row->itext) {
			$html .= t.t.GroupsHtml::shortenText(stripslashes($row->itext)).n;
		} else if ($row->ftext) {
			$html .= t.t.GroupsHtml::shortenText(stripslashes($row->ftext)).n;
		}
		$html .= t.t.'<p class="href">'.$juri->base().$row->href.'</p>'.n;
		$html .= t.'</li>'.n;
		return $html;
	}

	//-----------

	public function ranking( $rank, $stats, $id, $sef='' )
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
	
	public function getRatingClass($rating=0)
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

	public function documents() 
	{
		// Push some CSS and JS to the tmeplate that may be needed
		ximport('xdocument');
		XDocument::addComponentStylesheet('com_resources');

		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.extended.php' );
		ximport('resourcestats');
	}
}