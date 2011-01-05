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