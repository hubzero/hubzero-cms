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
	$html .= "\t".'<li class="counter">'.JText::_('Results').' '.($list['limitstart'] + 1).' - ';
	$html .= ($list['total'] > ($list['limitstart'] + $list['limit'])) ? ($list['limitstart'] + $list['limit']) : $list['total'];
	$html .= ' '.JText::_('of').' '.$list['total'].'</li>'."\n";
	$html .= "\t".'<li class="limit"><label for="limit">'.JText::_('Display Num').'</label> '.$list['limitfield'].'</li>'."\n";
	//$html .= $list['pageslinks'];
	$html .= pagination_list_render2($list, $list['pageslinks']);
	$html .= '</ul>'."\n";
	$html .= '<input type="hidden" name="limitstart" value="'.$list['limitstart'].'" />'."\n";

	return $html;
}

function pagination_list_render2($list, $pages) 
{
	$html = '';
	if (isset($pages['start']))
	{
		$html .= "\t" . '<li class="start">';
		if ($pages['start']->link) 
		{
			$html .= '<a href="' . $pages['start']->link .'">' . $pages['start']->text .'</a>';
		}
		else 
		{
			$html .= '<span>' . $pages['start']->text .'</span>';
		}
		$html .= '</li>' . "\n";
	}
	if (isset($pages['previous']))
	{
		$html .= "\t" . '<li class="prev">';
		if ($pages['previous']->link) 
		{
			$html .= '<a href="' . $pages['previous']->link .'">' . $pages['previous']->text .'</a>';
		}
		else 
		{
			$html .= '<span>' . $pages['previous']->text .'</span>';
		}
		$html .= '</li>' . "\n";
	}
	
	$link = '';
	/*foreach ($pages['pages'] as $key => $page)
	{
		$keyd = $key + 1;
		if ($pages['pages'][$key]->base && $pages['pages'][$keyd]->base)
		{
			$link = $pages['pages'][$key]->link;
			$list['limit'] = intval($pages['pages'][$keyd]->base) - intval($pages['pages'][$key]->base);
			break;
		}
	}*/
	
	if (!empty($pages['pages']) && count($pages['pages']) > 1) 
	{
		if ($pages['pages'][0]->link) 
		{ 
			$link = $pages['pages'][0]->link;
		} 
		else 
		{
			$link = $pages['pages'][1]->link;
		}

		$link = preg_replace('/limitstart=[0-9]+/i',"",$link);
		$link = preg_replace('/start=[0-9]+/i',"",$link);
		$link = preg_replace('/limit=[0-9]+/i',"",$link);
		$link = str_replace('?&amp;','?',$link);
		$link = str_replace('?','',$link);
	}
	
	$displayed_pages = 10;
	$total_pages = ($list['limit'] > 0) ? ceil( $list['total'] / $list['limit'] ) : 1;
	$this_page = ($list['limit'] > 0) ? ceil( ($list['limitstart']+1) / $list['limit'] ) : $list['limitstart']+1;
	
	$pager_middle = ceil($displayed_pages / 2);
	$start_loop = $this_page - $pager_middle + 1;
	$stop_loop = $this_page + $displayed_pages - $pager_middle;
	$i = $start_loop;
	if ($stop_loop > $total_pages) 
	{
		$i = $i + ($total_pages - $stop_loop);
		$stop_loop = $total_pages;
	}
	if ($i <= 0) 
	{
		$stop_loop = $stop_loop + (1 - $i);
		$i = 1;
	}

	if ($i > 1) 
	{
		$html .= "\t".'<li class="page">...</li>'."\n";
	}
	
	for (; $i <= $stop_loop && $i <= $total_pages; $i++) 
	{
		$page = ($i - 1) * $list['limit'];
		if ($i == $this_page) 
		{
			$html .= "\t".'<li class="page"><strong>'. $i .'</strong></li>'."\n";
		} 
		else 
		{
			$html .= "\t".'<li class="page"><a href="'.$link.'?limit='.$list['limit'].'&amp;limitstart='. $page  .'">'. $i .'</a></li>'."\n";
		}
	}
	
	if (($i - 1) < $total_pages) 
	{
		$html .= "\t".'<li class="page">...</li>'."\n";
	}

	if (isset($pages['next']))
	{
		$html .= "\t" . '<li class="next">';
		if ($pages['next']->link) 
		{
			$html .= '<a href="' . $pages['next']->link .'">' . $pages['next']->text .'</a>';
		}
		else 
		{
			$html .= '<span>' . $pages['next']->text .'</span>';
		}
		$html .= '</li>' . "\n";
	}
	if (isset($pages['end']))
	{
		$html .= "\t" . '<li class="end">';
		if ($pages['end']->link) 
		{
			$html .= '<a href="' . $pages['end']->link .'">' . $pages['end']->text .'</a>';
		}
		else 
		{
			$html .= '<span>' . $pages['end']->text .'</span>';
		}
		$html .= '</li>' . "\n";
	}
	
	return $html;
}

function pagination_list_render($list)
{
	// All we're really doing here is gathering Joomla's pagination data
	// so we can use it in pagination_list_render2
	
	// Who do all the work elsewhere? Because we don't have the limit number
	// in this data. Joomla only passes that when we get to pagination_list_footer()
	$pages = array();
	$pages['start'] = $list['start']['data'];
	$pages['previous'] = $list['previous']['data'];
	$pages['pages'] = array();
	foreach ($list['pages'] as $page)
	{
		$pages['pages'][] = $page['data'];
	}
	$pages['next'] = $list['next']['data'];
	$pages['end'] = $list['end']['data'];
	
	return $pages;
}

function pagination_item_active(&$item) 
{
	return $item;
}

function pagination_item_inactive(&$item) 
{
	return $item;
}
