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
JPlugin::loadLanguage( 'plg_members_contributions' );

//-----------

class plgMembersContributions extends JPlugin
{
	function plgMembersContributions(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'contributions' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onMembersAreas( $authorized )
	{
		$areas = array(
			'contributions' => JText::_('CONTRIBUTIONS')
		);
		return $areas;
	}

	//-----------

	function onMembers( $member, $option, $authorized, $areas )
	{
		$returnhtml = true;
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onMembersAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onMembersAreas( $authorized ) ) )) {
				$returnhtml = false;
			}
		}
		
		$database =& JFactory::getDBO();
		$dispatcher =& JDispatcher::getInstance();

		// Incoming paging vars
		$limit = JRequest::getInt( 'limit', 25 );
		$limitstart = JRequest::getInt( 'limitstart', 0 );
		$sort = JRequest::getVar( 'sort', 'date' );

		// Trigger the functions that return the areas we'll be using
		$areas = array();
		$searchareas = $dispatcher->trigger( 'onMembersContributionsAreas', array($authorized) );
		foreach ($searchareas as $area) 
		{
			$areas = array_merge( $areas, $area );
		}

		// Get the active category
		$area = JRequest::getVar( 'area', '' );
		if ($area) {
			$activeareas = array($area);
		} else {
			$limit = 5;
			$activeareas = $areas;
		}
		
		// If we're just returning metadata, we set the limitstart to -1 to use as a flag
		// This allows us to reduce the overall number of queries
		if (!$returnhtml) {
			$limitstart = -1;
		}
		
		// Get the search result totals
		$totals = $dispatcher->trigger( 'onMembersContributions', array(
				$member,
				$option,
				$authorized,
				0,
				$limitstart,
				$sort,
				$activeareas)
			);

		// Get the total results found (sum of all categories)
		$i = 0;
		$total = 0;
		$cats = array();
		foreach ($areas as $c=>$t) 
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

		// Build the HTML
		$html = '';
		$metadata = '';
		if ($returnhtml) {
			$document =& JFactory::getDocument();
			if (is_file(JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.js')) {
				$document->addScript('components'.DS.'com_resources'.DS.'resources.js');
			}
			
			$limit = ($limit == 0) ? 'all' : $limit;
		
			// Get the search results
			$results = $dispatcher->trigger( 'onMembersContributions', array(
				$member,
				$option,
				$authorized,
				$limit,
				$limitstart,
				$sort,
				$activeareas)
			);
			
			// Do we have an active area?
			if (count($activeareas) == 1 && !is_array(current($activeareas))) {
				$active = $activeareas[0];
			} else {
				$active = '';
			}
			
			$html = $this->display( $authorized, $totals, $results, $cats, $active, $option, $limitstart, $limit, $total, $member, $sort );
		} else {
			// Build the metadata
			$metadata = $this->metadata( $cats, $option, $member );
		}

		$arr = array(
				'html'=>$html,
				'metadata'=>$metadata
			);

		return $arr;
	}
	
	//-----------

	public function metadata( $cats, $option, $member ) 
	{
		$sef = JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=contributions');
		
		$html = '';
		
		// Loop through each category
		foreach ($cats as $cat) 
		{
			if ($cat['total'] > 0) {
				$html .= '<p class="'.strtolower($cat['title']).'"><a href="'.$sef.'">'.$cat['total'].' '.strtolower($cat['title']).'</a></p>'.n;
			}
		}
		
		return $html;
	}
	
	//-----------
	
	public function categories( $cats, $active, $total, $sef, $sort ) 
	{
		if (strstr( $sef, 'index' )) {
			$sef .= a;
		} else {
			$sef .= '?';
		}

		// Add the "all" category
		$all = array('category'=>'','title'=>JText::_('ALL_CONTRIBUTION_CATEGORIES'),'total'=>$total);
		
		array_unshift($cats, $all);

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
		// Do we actually have any links?
		// NOTE: this method prevents returning empty list tags "<ul></ul>"
		if (count($links) > 0) {
			// Yes - output the necessary HTML
			$html  = '<fieldset>'.n;
			$html .= t.'<label>'.n;
			$html .= t.t.JText::_('CONTRIBUTIONS_SORT_BY').n;
			$html .= t.t.'<select name="sort">'.n;
			$html .= t.t.t.'<option value="date"';
			if ($sort == 'date') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('CONTRIBUTIONS_SORT_BY_DATE').'</option>'.n;
			$html .= t.t.t.'<option value="title"';
			if ($sort == 'title') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('CONTRIBUTIONS_SORT_BY_TITLE').'</option>'.n;
			$html .= t.t.'</select>'.n;
			$html .= t.'</label>'.n;
			$html .= t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
			$html .= '</fieldset>'.n;
			$html .= '<ul class="sub-nav">'.n;
			$html .= implode( n, $links ).n;
			$html .= '</ul>'.n;
		} else {
			// No - nothing to output
			$html = '';
		}
		$html .= t.'<input type="hidden" name="area" value="'.$active.'" />'.n;

		return $html;
	}
	
	//-----------
	
	public function display( $authorized, $totals, $results, $cats, $active, $option, $start=0, $limit=0, $total, $member, $sort ) 
	{
		$sef = JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=contributions');
		
		$html  = MembersHtml::hed(3,'<a name="contributions"></a>'.JText::_('CONTRIBUTIONS')).n;
		$html .= '<form method="get" action="'.$sef.'">'.n;
		$html .= MembersHtml::div( 
					$this->categories( $cats, $active, $total, $sef, $sort ), 
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
				$f = 'plgMembers'.ucfirst($cats[$k]['category']).'Doc';
				if (function_exists($f)) {
					$f();
				}
				// Check if a method exist (using JPlugin style)
				$obj = 'plgMembers'.ucfirst($cats[$k]['category']);
				if (method_exists($obj, 'documents')) {
					$html .= call_user_func( array($obj,'documents') );
				}
			
				// Build the category HTML
				$html .= '<h4 class="category-header opened" id="rel-'.$divid.'">'.$name.' <small>'.$num.'</small></h4>'.n;
				$html .= '<div class="category-wrap" id="'.$divid.'">'.n;
				
				// Does this category have custom output?
				// Check if a function exist (using old style plugins)
				$func = 'plgMembers'.ucfirst($cats[$k]['category']).'Before';
				if (function_exists($func)) {
					$html .= $func();
				}
				// Check if a method exist (using JPlugin style)
				$obj = 'plgMembers'.ucfirst($cats[$k]['category']);
				if (method_exists($obj, 'before')) {
					$html .= call_user_func( array($obj,'before') );
				}
				
				$html .= '<ol class="search results">'.n;			
				foreach ($category as $row) 
				{
					$row->href = str_replace('&amp;', '&', $row->href);
					$row->href = str_replace('&', '&amp;', $row->href);
					
					// Does this category have a unique output display?
					$func = 'plgMembers'.ucfirst($row->section).'Out';
					// Check if a method exist (using JPlugin style)
					$obj = 'plgMembers'.ucfirst($cats[$k]['category']);
					
					if (function_exists($func)) {
						$html .= $func( $row, $authorized );
					} elseif (method_exists($obj, 'out')) {
						$html .= call_user_func( array($obj,'out'), $row, $authorized );
					} else {
						$html .= t.'<li>'.n;
						$html .= t.t.'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'.n;
						if ($row->text) {
							$html .= t.t.MembersHtml::shortenText(stripslashes($row->text)).n;
						}
						$html .= t.'</li>'.n;
					}
				}
				$html .= '</ol>'.n;
				// Initiate paging if we we're displaying an active category
				if ($dopaging) {
					jimport('joomla.html.pagination');
					$pageNav = new JPagination( $total, $start, $limit );

					//$html .= $pageNav->getListFooter();
					$pf = $pageNav->getListFooter();
					
					$nm = str_replace('com_','',$option);

					$pf = str_replace($nm.'/?',$nm.'/'.$member->get('uidNumber').'/contributions/?',$pf);
					$html .= $pf;
				} else {
					$html .= '<p class="moreresults">'.JText::sprintf('NUMBER_CONTRIBUTIONS_SHOWN', $amt);
					// Ad a "more" link if necessary
					//if ($totals[$k] > 5) {
					if ($cats[$k]['total'] > 5) {
						$qs = 'area='.urlencode(strToLower($cats[$k]['category']));
						$seff = JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=contributions');
						if (strstr( $seff, 'index' )) {
							$seff .= a.$qs;
						} else {
							$seff .= '?'.$qs;
						}
						$html .= ' | <a href="'.$seff.'">'.JText::_('MORE_CONTRIBUTIONS').'</a>';
					}
				}
				$html .= '</p>'.n.n;
				$html .= '</div><!-- / #'.$divid.' -->'.n;
			}
			$k++;
		}
		if (!$foundresults) {
			$html .= MembersHtml::warning( JText::_('NO_CONTRIBUTIONS') );
		}
		$html .= '</div><!-- / .subject -->'.n;
		$html .= MembersHtml::div('','clear').n;
		$html .= '</form>'.n;
		
		return $html;
	}
}