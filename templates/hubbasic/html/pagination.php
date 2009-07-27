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

/**
 * This is a file to add template specific chrome to pagination rendering.
 *
 * pagination_list_footer
 * 	Input variable $list is an array with offsets:
 * 		$list[limit]		: int
 * 		$list[limitstart]	: int
 * 		$list[total]		: int
 * 		$list[limitfield]	: string
 * 		$list[pagescounter]	: string
 * 		$list[pageslinks]	: string
 *
 * pagination_list_render
 * 	Input variable $list is an array with offsets:
 * 		$list[all]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[start]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[previous]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[next]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[end]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[pages]
 * 			[{PAGE}][data]		: string
 * 			[{PAGE}][active]	: boolean
 *
 * pagination_item_active
 * 	Input variable $item is an object with fields:
 * 		$item->base	: integer
 * 		$item->link	: string
 * 		$item->text	: string
 *
 * pagination_item_inactive
 * 	Input variable $item is an object with fields:
 * 		$item->base	: integer
 * 		$item->link	: string
 * 		$item->text	: string
 *
 * This gives template designers ultimate control over how pagination is rendered.
 *
 * NOTE: If you override pagination_item_active OR pagination_item_inactive you MUST override them both
 */

function pagination_list_footer($list)
{
	$html  = '<ul class="list-footer">'."\n";
	//$html .= "\t".'<li class="counter">'.$list['pagescounter'].', '.$list['total'].'</li>'."\n";
	$html .= "\t".'<li class="counter">'.JText::_('Results').' '.($list['limitstart'] + 1).' - ';
	$html .= ($list['total'] > $list['limit']) ? ($list['limitstart'] + $list['limit']) : $list['total'];
	$html .= ' '.JText::_('of').' '.$list['total'].'</li>'."\n";
	$html .= "\t".'<li class="limit">'.JText::_('Display Num').' '.$list['limitfield'].'</li>'."\n";
	$html .= $list['pageslinks'];
	$html .= '</ul>'."\n";
	$html .= '<input type="hidden" name="limitstart" value="'.$list['limitstart'].'" />'."\n";

	return $html;
}

function pagination_list_render($list)
{
	// Initialize variables
	$html  = "\t".'<li class="start">'.$list['start']['data'].'</li>'."\n";
	$html .= "\t".'<li class="prev">'.$list['previous']['data'].'</li>'."\n";

	foreach ( $list['pages'] as $page )
	{
		$html .= "\t".'<li class="page">';
		if ($page['data']['active']) {
			$html .= '<strong>';
		}

		$html .= $page['data'];

		if ($page['data']['active']) {
			$html .= '</strong>';
		}
		$html .= '</li>'."\n";
	}

	$html .= "\t".'<li class="next">'.$list['next']['data'].'</li>'."\n";
	$html .= "\t".'<li class="end">'.$list['end']['data'].'</li>'."\n";
	
	return $html;
}

function pagination_item_active(&$item) 
{
	global $mainframe;
	
	$option = JRequest::getVar('option','');
	$task = JRequest::getVar('task','');
	$limit = JRequest::getInt('limit',25);
	$uri = 'index.php?option='.$option;
	//if ($task) {
	//	$uri .= '&task='.$task;
	//}
	$url = JRoute::_($uri);

	if ($mainframe->isAdmin())
	{
		if($item->base>0)
			return "<a title=\"".$item->text."\" onclick=\"javascript: document.adminForm.limitstart.value=".$item->base."; submitform();return false;\">".$item->text."</a>";
		else
			return "<a title=\"".$item->text."\" onclick=\"javascript: document.adminForm.limitstart.value=0; submitform();return false;\">".$item->text."</a>";
	} else {
		if ($item->link) {
			$bits = explode('?',$item->link);
			if (!strstr( $url, 'index.php' )) {
				if (substr($url, (strlen($url) - 1), strlen($url)) != '/') {
					$url .= '/';
				}
				$item->link = $url.'?'.end($bits);
			} else {
				$item->link = $url.'&'.end($bits);
			}
			if (!strstr( $item->link, 'limit' )) {
				$item->link .= '&limit='.$limit;
			}
		}
		return '<a href="'.$item->link.'" title="'.$item->text.'">'.$item->text.'</a>';
	}
}

function pagination_item_inactive(&$item) 
{
	return '<span>'.$item->text.'</span>';
}
?>
