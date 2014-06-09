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
 * NOTE: If you override pagination_item_active OR pagination_item_inactive you MUST override them both
 */

function pagination_list_footer($list)
{
	$html = array();
	$html[] = '<div class="pagination">';
	$html[] = '<ul class="list-footer">';

	$html[] = '<li class="counter">' . $list['pagescounter'] . '</li>';
	$html[] = '<li class="limit"><label for="' . $list['prefix'] . 'limit">' . JText::_('JGLOBAL_DISPLAY_NUM') . '</label> ' . $list['limitfield'] . '</li>';
	$html[] = $list['pageslinks'];

	$html[] = '</ul>';
	$html[] = '<input type="hidden" name="' . $list['prefix'] . 'limitstart" value="' . $list['limitstart'] . '" />';
	$html[] = '<div class="clr"></div></div>';

	return implode("\n", $html);
}
