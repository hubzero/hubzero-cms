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

$content = '';

if ($modxwhosonline->online) {
	$guest_array = $modxwhosonline->guest_array;
	$user_array = $modxwhosonline->user_array;
	
	if ($modxwhosonline->admin) {
		$content .= '<a href="/users/">';
	}

	if ($guest_array<>0 && $user_array==0) {
		if ($guest_array == 1) {
			$content .= JText::_('MOD_XWHOSONLINE_WE_HAVE');
			$content .= $guest_array.' '.JText::_('MOD_XWHOSONLINE_GUEST_COUNT');
		} else {
			$content .= JText::_('MOD_XWHOSONLINE_WE_HAVE');
			$content .= $guest_array.' '.JText::_('MOD_XWHOSONLINE_GUESTS_COUNT');
		}
		$content .= JText::_('MOD_XWHOSONLINE_ONLINE');
	}

	if ($guest_array==0 && $user_array<>0) {
		if ($user_array==1) {
			$content .= JText::_('MOD_XWHOSONLINE_WE_HAVE');
			$content .= $user_array.' '.JText::_('MOD_XWHOSONLINE_MEMBER_COUNT');
		} else {
			$content .= JText::_('MOD_XWHOSONLINE_WE_HAVE');
			$content .= $user_array.' '.JText::_('MOD_XWHOSONLINE_MEMBERS_COUNT');
		}
		$content .= JText::_('MOD_XWHOSONLINE_ONLINE');
	}

	if ($guest_array<>0 && $user_array<>0) {
		if ($guest_array==1) {
			$content .= JText::_('MOD_XWHOSONLINE_WE_HAVE');
			$content .= $guest_array.' '.JText::_('MOD_XWHOSONLINE_GUEST_COUNT');
			$content .= JText::_('MOD_XWHOSONLINE_AND');
		} else {
			$content .= JText::_('MOD_XWHOSONLINE_WE_HAVE');
			$content .= $guest_array.' '.JText::_('MOD_XWHOSONLINE_GUESTS_COUNT');
			$content .= JText::_('MOD_XWHOSONLINE_AND');
		}

		if ($user_array==1) {
			$content .= $user_array.' '.JText::_('MOD_XWHOSONLINE_MEMBER_COUNT');
		} else {
			$content .= $user_array.' '.JText::_('MOD_XWHOSONLINE_MEMBERS_COUNT');
		}
		$content .= JText::_('MOD_XWHOSONLINE_ONLINE');
	}

	if ($modxwhosonline->admin) {
		$content .= '</a>';
	}
}

if ($modxwhosonline->users) {
	$rows = $modxwhosonline->rows;
	if ($rows) {
		$content .= '<ul>'."\n";
		foreach ($rows as $row) 
		{
			$content .= '<li><strong>'. $row->username .'</strong></li>'."\n";
		}
		$content .= '</ul>'."\n";
	} else {
		$content .= '<p>'.JText::_('MOD_XWHOSONLINE_NONE') .'</p>'."\n";
	}
}

echo $content;
?>