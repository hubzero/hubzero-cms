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

if ($modmyfavorites->error) {
	echo '<p class="error">'.JText::_('MOD_MYFAVORITES_MISSING_TABLE').'</p>'."\n";
} else {
	$juser =& JFactory::getUser();
	
	$results = $modmyfavorites->results;
	$cats = $modmyfavorites->cats;
	$active = $modmyfavorites->active;
	
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
							break;
						}
					}
				}
			}

			// Build the category HTML
			$html .= '<h4 class="fav-header" id="rel-'.$divid.'">'.$name.' <span>'.JText::sprintf('MOD_MYFAVORITES_RESULTS',$total).'</span></h4>'."\n";
			$html .= '<div class="category-wrap" id="'.$divid.'">'."\n";
			$html .= '<ol class="compactlist">'."\n";			
			foreach ($category as $row) 
			{
				$row->href = str_replace('&amp;', '&', $row->href);
				$row->href = str_replace('&', '&amp;', $row->href);

				$html .= "\t".'<li class="favorite">'."\n";
				$html .= "\t\t".'<a href="'.$row->href.'">'.stripslashes($row->title).'</a>'."\n";
				$html .= "\t".'</li>'."\n";
			}
			$html .= '</ol>'."\n";

			// Add a "more" link if necessary
			if ($cats[$k]['total'] > 5) {
				$qs = 'area='.urlencode(strToLower($cats[$k]['category']));
				$seff = JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=favorites');
				if (strstr( $seff, 'index' )) {
					$seff .= '&amp;'.$qs;
				} else {
					$seff .= '?'.$qs;
				}
				
				$html .= '<p class="more">'.JText::sprintf('MOD_MYFAVORITES_NUMBER_FAVORITES_SHOWN', $amt);
				$html .= ' | <a href="'.$seff.'">'.JText::_('MOD_MYFAVORITES_MORE_FAVORITES').'</a>';
				$html .= '</p>';
			}
			$html .= '</div><!-- / #'.$divid.' -->'."\n";
		}
		$k++;
	}
	if (!$foundresults) {
		$html .= '<p>'. JText::_('MOD_MYFAVORITES_NO_FAVORITES') .'</p>';
	}

	// Output the HTML
	echo $html;
}
?>