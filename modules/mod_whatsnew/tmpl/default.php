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

// Output HTML
$html  = '<div';
$html .= ($modwhatsnew->moduleid) ? ' id="'.$modwhatsnew->moduleid.'"' : '';
$html .= '>'."\n";

if ($modwhatsnew->feed) {
	$html .= "\t".'<h3>' . $module->title;
	$html .= ' <a class="newsfeed" href="'.$modwhatsnew->feedlink.'" title="'.JText::_('MOD_WHATSNEW_SUBSCRIBE').'">'.JText::_('MOD_WHATSNEW_NEWS_FEED').'</a>';
	$html .= '</h3>'."\n";
}

if (!$modwhatsnew->tagged) {
	$rows = $modwhatsnew->rows;
	if (count($rows) > 0) {
		$count = 0;

		$html .= "\t".'<ul>'."\n";
		foreach ($rows as $row)
		{
			if (empty($row)) {
				continue;
			}
			$html .= "\t".' <li class="new">';
			$html .= '<a href="'. JRoute::_($row->href) .'">'.stripslashes($row->title).'</a><br />';
			$html .= '<span>'.JText::_('in').' ';
			$html .= ($row->area) ? JText::_($row->area) : JText::_(strtoupper($row->section));
			if ($row->publish_up) {
				$html .= ', '.JHTML::_('date', $row->publish_up, ' %b %d, %Y');
			}
			$html .= '</span></li>'."\n";

			$count++;
			if ($count >= 6) {
				break;
			}
		}
		$html .= "\t".'</ul>'."\n";
	} else {
		$html .= "\t".'<p>'.JText::_('MOD_WHATSNEW_NO_RESULTS').'</p>'."\n";
	}
} else {
	$juser =& JFactory::getUser();
	$rows2 = $modwhatsnew->rows2;

	$html .= "\t".'<p class="category-header-details">'."\n";
	if (count($modwhatsnew->tags) > 0) {
		$html .= "\t\t".'<span class="configure">[<a href="'.JRoute::_('index.php?option=com_members'.a.'task=edit'.a.'id='.$juser->get('id')).'">'.JText::_('MOD_WHATSNEW_EDIT').'</a>]</span>'."\n";
	} else {
		$html .= "\t\t".'<span class="configure">[<a href="'.JRoute::_('index.php?option=com_members'.a.'task=edit'.a.'id='.$juser->get('id')).'">'.JText::_('MOD_WHATSNEW_ADD_INTERESTS').'</a>]</span>'."\n";
	}
	$html .= "\t\t".'<span class="q">'.JText::_('MOD_WHATSNEW_MY_INTERESTS').': '.$modwhatsnew->formatTags($modwhatsnew->tags).'</span>'."\n";
	$html .= "\t".'</p>'.n;

	if (count($rows2) > 0) {
		$count = 0;

		$html .= "\t".'<ul class="expandedlist">'."\n";
		foreach ($rows2 as $row2)
		{
			if (empty($row2)) {
				continue;
			}
			$html .= "\t".' <li class="new">';
			$html .= '<a href="'. JRoute::_($row2->href) .'">'.stripslashes($row2->title).'</a><br />';
			$html .= '<span>'.JText::_('MOD_WHATSNEW_IN').' ';
			$html .= ($row2->section) ? JText::_($row2->area) : JText::_(strtoupper($row2->section));
			if ($row2->publish_up) {
				$html .= ', '.JHTML::_('date', $row2->publish_up, ' %b %d, %Y');
			}
			$html .= '</span></li>'."\n";

			$count++;
			if ($count >= 6) {
				break;
			}
		}
		$html .= "\t".'</ul>'."\n";
	} else {
		$html .= "\t".'<p>'.JText::_('MOD_WHATSNEW_NO_RESULTS').'</p>'."\n";
	}
}
$html .= "\t".'<p class="more"><a href="'.JRoute::_('index.php?option=com_whatsnew&period='.$modwhatsnew->area.':'.$modwhatsnew->period).'">'.JText::_('MOD_WHATSNEW_VIEW_MORE').'</a></p>'."\n";
$html .= '</div>'."\n";

echo $html;
?>