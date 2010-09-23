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

$html = '';
if ($modfeaturedmember->error) {
	$html .= '<p class="error">'.JText::_('MOD_FEATUREDMEMBER_MISSING_CLASS').'</p>'."\n";
} else {
	if ($modfeaturedmember->row) {
		ximport('Hubzero_View_Helper_Html');
		
		$html .= '<div class="'.$modfeaturedmember->cls.'">'."\n";
		if ($modfeaturedmember->filters['show'] == 'contributors') {
			$html .= '<h3>'.JText::_('MOD_FEATUREDMEMBER_PROFILE').'</h3>'."\n";
		} else {
			$html .= '<h3>'.JText::_('MOD_FEATUREDMEMBER').'</h3>'."\n";
		}
		// Do we have a picture to show?
		if (is_file(JPATH_ROOT.$modfeaturedmember->thumb)) {
			$html .= '<p class="featured-img"><a href="'.JRoute::_('index.php?option=com_members&id='.$modfeaturedmember->id).'"><img width="50" height="50" src="'.$modfeaturedmember->thumb.'" alt="'.htmlentities(stripslashes($modfeaturedmember->title)).'" /></a></p>'."\n";
		}
		$html .= '<p><a href="'.JRoute::_('index.php?option=com_members&id='.$modfeaturedmember->id).'">'.stripslashes($modfeaturedmember->title).'</a>: '."\n";
		if ($modfeaturedmember->txt) {
			$html .= Hubzero_View_Helper_Html::shortenText($modfeaturedmember->encode_html(strip_tags($modfeaturedmember->txt)), $modfeaturedmember->txt_length, 0)."\n";
		}
		$html .= '</p>'."\n";
		$html .= '</div>'."\n";
	}
}

// Output HTML
echo $html;
?>