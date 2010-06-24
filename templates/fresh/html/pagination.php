<?php
/**
 * @version		$Id: pagination.php 9764 2007-12-30 07:48:11Z ircmaxell $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

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
	//$html .= $list['pageslinks'];
	$html .= pagination_list_render2($list, $list['pageslinks']);
	$html .= '</ul>'."\n";
	$html .= '<input type="hidden" name="limitstart" value="'.$list['limitstart'].'" />'."\n";

	return $html;
}

function pagination_list_render2($list, $pages) 
{
	$pages = explode("\n",$pages);
	
	$link = '';
	if (count($pages) > 1) {
		$html  = $pages[0]."\n";
		$html .= $pages[1]."\n";
		
		if (strstr( $pages[2], 'href' )) { 
			$link = $pages[2];
		} else {
			$link = $pages[3];
		}
		$link = str_replace('<li class="page"><strong><a href="', '', $link);
		$link = str_replace('</a></strong></li>', '', $link);
		$bits = explode('"', $link);
		$link = trim($bits[0]);

		$link = preg_replace('/limitstart=[0-9]+/i',"",$link);
		$link = preg_replace('/start=[0-9]+/i',"",$link);
		$link = preg_replace('/limit=[0-9]+/i',"",$link);
		$link = str_replace('?&amp;','?',$link);
	} else {
		$html  = "\t".'<li class="start">'.JText::_('Start').'</li>'."\n";
		$html .= "\t".'<li class="prev">'.JText::_('Prev').'</li>'."\n";
	}
	
	$displayed_pages = 10;
	$total_pages = ($list['limit'] > 0) ? ceil( $list['total'] / $list['limit'] ) : 1;
	$this_page = ($list['limit'] > 0) ? ceil( ($list['limitstart']+1) / $list['limit'] ) : $list['limitstart']+1;
	
	$pager_middle = ceil($displayed_pages / 2);
	$start_loop = $this_page - $pager_middle + 1;
	$stop_loop = $this_page + $displayed_pages - $pager_middle;
	$i = $start_loop;
	if ($stop_loop > $total_pages) {
		$i = $i + ($total_pages - $stop_loop);
		$stop_loop = $total_pages;
	}
	if ($i <= 0) {
		$stop_loop = $stop_loop + (1 - $i);
		$i = 1;
	}

	if ($i > 1) {
		$html .= "\t".'<li class="page">...</li>'."\n";
	}
	
	for (; $i <= $stop_loop && $i <= $total_pages; $i++) 
	{
		$page = ($i - 1) * $list['limit'];
		if ($i == $this_page) {
			$html .= "\t".'<li class="page"><strong>'. $i .'</strong></li>'."\n";
		} else {
			$html .= "\t".'<li class="page"><a href="'.$link.'limit='.$list['limit'].'&amp;limitstart='. $page  .'">'. $i .'</a></li>'."\n";
		}
	}
	
	if (($i - 1) < $total_pages) {
		$html .= "\t".'<li class="page">...</li>'."\n";
	}
	if (count($pages) > 1) {
		$b = array_pop($pages);
		$c = array_pop($pages);
		$html .= end($pages)."\n";
		$html .= $c."\n";
	} else {
		$html .= "\t".'<li class="next">'.JText::_('Next').'</li>'."\n";
		$html .= "\t".'<li class="end">'.JText::_('End').'</li>'."\n";
	}
	
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
				/*if (substr($url, (strlen($url) - 1), strlen($url)) != '/') {
					$url .= '/';
				}*/
				if ($url != trim(end($bits))) {
					$item->link = $url.'?'.end($bits);
				}
			} else {
				$item->link = $url.'&amp;'.end($bits);
			}
			if (!strstr( $item->link, 'limit' )) {
				if (strstr( $item->link, '?' )) {
					$item->link .= '&amp;';
				} else {
					$item->link .= '?';
				}
				$item->link .= 'limit='.$limit;
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