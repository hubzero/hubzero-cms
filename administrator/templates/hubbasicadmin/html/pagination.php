<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	$html[] = '<li class="limit"><label for="' . $list['prefix'] . 'limit">' . Lang::txt('JGLOBAL_DISPLAY_NUM') . '</label> ' . $list['limitfield'] . '</li>';
	$html[] = $list['pageslinks'];

	$html[] = '</ul>';
	$html[] = '<input type="hidden" name="' . $list['prefix'] . 'limitstart" value="' . $list['limitstart'] . '" />';
	$html[] = '<div class="clr"></div></div>';

	return implode("\n", $html);
}
