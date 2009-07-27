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
// "My Favorites"
//    This module displays "favorites" assigned to the 
//    user currently logged in.
// Members "favorites" plugin REQUIRED
//-------------------------------------------------------------

class modMyFavorites
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
	
	public function display() 
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		$params =& $this->params;
		$moduleclass = $params->get( 'moduleclass' );
		$limit = intval( $params->get( 'limit' ) );
		$limit = ($limit) ? $limit : 5;
		
		// Check for the existence of required tables that should be
		// installed with the com_support component
		$database->setQuery("SHOW TABLES");
		$tables = $database->loadResultArray();
		
		if ($tables && array_search($database->_table_prefix.'xfavorites', $tables)===false) {
			// Support tickets table not found!
			echo JText::_('Required database table not found.');
			return false;
	    }

		$authorized = true;

		JPluginHelper::importPlugin( 'members' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Trigger the functions that return the areas we'll be using
		$areas = array();
		$searchareas = $dispatcher->trigger( 'onMembersFavoritesAreas', array($authorized) );
		foreach ($searchareas as $area) 
		{
			$areas = array_merge( $areas, $area );
		}

		// Get the active category
		$area = '';
		$limitstart = 0;
		$activeareas = $areas;

		$option = 'com_members';
		
		ximport('xprofile');
		$member = new XProfile();
		$member->load( $juser->get('id') );

		// Get the search result totals
		$totals = $dispatcher->trigger( 'onMembersFavorites', array(
				$member,
				$option,
				$authorized,
				0,
				0,
				$activeareas)
			);

		// Get the search results
		$results = $dispatcher->trigger( 'onMembersFavorites', array(
				$member,
				$option,
				$authorized,
				$limit,
				$limitstart,
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

		// Do we have an active area?
		if (count($activeareas) == 1 && !is_array(current($activeareas))) {
			$active = $activeareas[0];
		} else {
			$active = '';
		}
		
		// Push the module CSS to the template
		ximport('xdocument');
		XDocument::addModuleStyleSheet('mod_myfavorites');
		
		// Build the HTML
		$foundresults = false;
		$dopaging = false;

		$k = 0;
		$html = '';
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
			
				// Build the category HTML
				$html .= '<h4 class="fav-header" id="rel-'.$divid.'">'.$name.' <span>'.$num.'</span></h4>'.n;
				$html .= '<div class="category-wrap" id="'.$divid.'">'.n;
				
				$html .= '<ol class="compactlist">'.n;			
				foreach ($category as $row) 
				{
					$row->href = str_replace('&amp;', '&', $row->href);
					$row->href = str_replace('&', '&amp;', $row->href);
					
					$html .= t.'<li class="favorite">'.n;
					$html .= t.t.'<a href="'.$row->href.'">'.stripslashes($row->title).'</a>'.n;
					$html .= t.'</li>'.n;
				}
				$html .= '</ol>'.n;

				// Add a "more" link if necessary
				if ($cats[$k]['total'] > 5) {
					$html .= '<p class="more">'.JText::sprintf('NUMBER_FAVORITES_SHOWN', $amt);
					$qs = 'area='.urlencode(strToLower($cats[$k]['category']));
					$seff = JRoute::_('index.php?option=com_members'.a.'id='.$member->get('uidNumber').a.'active=favorites');
					if (strstr( $seff, 'index' )) {
						$seff .= a.$qs;
					} else {
						$seff .= '?'.$qs;
					}
					$html .= ' | <a href="'.$seff.'">'.JText::_('MORE_FAVORITES').'</a>';
					$html .= '</p>'.n.n;
				}
				$html .= '</div><!-- / #'.$divid.' -->'.n;
			}
			$k++;
		}
		if (!$foundresults) {
			$html .= '<p>'. JText::_('NO_FAVORITES') .'</p>';
		}
		
		// Output the HTML
		return $html;
	}
}

//-------------------------------------------------------------

$modmyfavorites = new modMyFavorites();
$modmyfavorites->params = $params;

require( JModuleHelper::getLayoutPath('mod_myfavorites') );
?>
