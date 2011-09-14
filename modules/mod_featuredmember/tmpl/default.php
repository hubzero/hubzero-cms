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