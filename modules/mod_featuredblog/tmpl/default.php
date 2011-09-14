<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$html = '';
if ($modfeaturedblog->error) {
	$html .= '<p class="error">'.JText::_('MOD_FEATUREDBLOG_MISSING_CLASS').'</p>'."\n";
} else {
	if ($modfeaturedblog->row) {
		ximport('Hubzero_View_Helper_Html');

		$html .= '<div class="'.$modfeaturedblog->cls.'">'."\n";
		//if ($modfeaturedblog->filters['show'] == 'contributors') {
		//	$html .= '<h3>'.JText::_('MOD_FEATUREDMEMBER_PROFILE').'</h3>'."\n";
		//} else {
			$html .= '<h3>'.JText::_('MOD_FEATUREDBLOG').'</h3>'."\n";
		//}
		// Do we have a picture to show?
		/*if (is_file(JPATH_ROOT.$modfeaturedblog->thumb)) {
			$html .= '<p class="featured-img"><a href="'.JRoute::_('index.php?option=com_members&id='.$modfeaturedblog->id).'"><img width="50" height="50" src="'.$modfeaturedblog->thumb.'" alt="'.htmlentities(stripslashes($modfeaturedblog->title)).'" /></a></p>'."\n";
		}*/
		$html .= '<p class="featured-img"><a href="'.JRoute::_('index.php?option=com_members&id='.$modfeaturedblog->row->created_by.'&active=blog&task='.JHTML::_('date',$modfeaturedblog->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$modfeaturedblog->row->publish_up, '%m', 0).'/'.$modfeaturedblog->row->alias).'"><img width="50" height="50" src="/modules/mod_featuredblog/images/blog_thumb.gif" alt="'.htmlentities(stripslashes($modfeaturedblog->title)).'" /></a></p>'."\n";
		$html .= '<p><a href="'.JRoute::_('index.php?option=com_members&id='.$modfeaturedblog->row->created_by.'&active=blog&task='.JHTML::_('date',$modfeaturedblog->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$modfeaturedblog->row->publish_up, '%m', 0).'/'.$modfeaturedblog->row->alias).'">'.stripslashes($modfeaturedblog->title).'</a>: '."\n";
		if ($modfeaturedblog->txt) {
			//$p = new WikiParser( stripslashes($modfeaturedblog->title), 'com_members', 'blog', $modfeaturedmember->alias, 0, $path );
			$html .= Hubzero_View_Helper_Html::shortenText($modfeaturedblog->encode_html(strip_tags($modfeaturedblog->txt)), $modfeaturedblog->txt_length, 0)."\n";
		}
		$html .= '</p>'."\n";
		$html .= '</div>'."\n";
	}
}

// Output HTML
echo $html;
?>