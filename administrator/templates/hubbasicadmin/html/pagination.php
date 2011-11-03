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
	$html  = "\t" . '<div class="pagination">' . "\n";
	$html .= '<ul class="list-footer">'."\n";
	$html .= "\t".'<li class="counter">'.JText::_('Results').' '.($list['limitstart'] + 1).' - ';
	$html .= ($list['total'] > ($list['limitstart'] + $list['limit'])) ? ($list['limitstart'] + $list['limit']) : $list['total'];
	$html .= ' '.JText::_('of').' '.$list['total'].'</li>'."\n";
	$html .= "\t".'<li class="limit"><label for="limit">'.JText::_('Display Num').'</label> '.pagination_getLimitBox($list['total'], $list['limit']).'</li>'."\n";
	$html .= $list['pageslinks'];
	$html .= '</ul>'."\n";
	$html .= '<input type="hidden" name="limitstart" value="'.$list['limitstart'].'" />'."\n";
	$html .= "\t" . '<div class="clr"></div></div>' . "\n";

	return $html;
}

function pagination_list_render($list)
{
	// Initialize variables
	$html  = "\t".'<li class="start">'.$list['start']['data'].'</li>'."\n";
	$html .= "\t".'<li class="prev">'.$list['previous']['data'].'</li>'."\n";

	foreach ($list['pages'] as $page)
	{
		$html .= "\t".'<li class="page">';
		$html .= str_replace('span', 'strong', $page['data']);
		$html .= '</li>'."\n";
	}

	$html .= "\t".'<li class="next">'.$list['next']['data'].'</li>'."\n";
	$html .= "\t".'<li class="end">'.$list['end']['data'].'</li>'."\n";

	return $html;
}

function pagination_item_active(&$item)
{
	if ($item->base > 0) {
		return '<a title="' . $item->text . '" onclick="javascript: document.adminForm.limitstart.value=' . $item->base . '; submitform();return false;">' . $item->text . '</a>';
	} else {
		return '<a title="' . $item->text . '" onclick="javascript: document.adminForm.limitstart.value=0; submitform();return false;">' . $item->text . '</a>';
	}
}

function pagination_getLimitBox($total, $limit)
{
	// Initialize variables
	$limits = array();

	// Make the option list
	/*for ($i = 5; $i <= 30; $i += 5) {
		$limits[] = JHTML::_('select.option', "$i");
	}*/
	$limits[] = JHTML::_('select.option', '10');
	$limits[] = JHTML::_('select.option', '25');
	$limits[] = JHTML::_('select.option', '50');
	$limits[] = JHTML::_('select.option', '100');
	$limits[] = JHTML::_('select.option', '250');
	$limits[] = JHTML::_('select.option', '500');
	$limits[] = JHTML::_('select.option', '1000');
	$limits[] = JHTML::_('select.option', '0', JText::_('all'));

	$selected = ($limit == $total) ? 0 : $limit;

	// Build the select list
	$html = JHTML::_('select.genericlist',  $limits, 'limit', 'class="inputbox" size="1" onchange="submitform();"', 'value', 'text', $selected);

	return $html;
}

function pagination_item_inactive(&$item)
{
	return '<span>'.$item->text.'</span>';
}
