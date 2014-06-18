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
defined('_JEXEC') or die('Restricted access');

$content = '';

if ($this->online)
{
	$guest_array = $this->guest_array;
	$user_array  = $this->user_array;

	if ($this->admin)
	{
		$content .= '<a href="/users/">';
	}

	if ($guest_array<>0 && $user_array==0)
	{
		if ($guest_array == 1)
		{
			$content .= JText::_('MOD_XWHOSONLINE_WE_HAVE');
			$content .= $guest_array . ' ' . JText::_('MOD_XWHOSONLINE_GUEST_COUNT');
		}
		else
		{
			$content .= JText::_('MOD_XWHOSONLINE_WE_HAVE');
			$content .= $guest_array . ' ' . JText::_('MOD_XWHOSONLINE_GUESTS_COUNT');
		}
		$content .= JText::_('MOD_XWHOSONLINE_ONLINE');
	}

	if ($guest_array==0 && $user_array<>0)
	{
		if ($user_array==1)
		{
			$content .= JText::_('MOD_XWHOSONLINE_WE_HAVE');
			$content .= $user_array . ' ' . JText::_('MOD_XWHOSONLINE_MEMBER_COUNT');
		}
		else
		{
			$content .= JText::_('MOD_XWHOSONLINE_WE_HAVE');
			$content .= $user_array . ' ' . JText::_('MOD_XWHOSONLINE_MEMBERS_COUNT');
		}
		$content .= JText::_('MOD_XWHOSONLINE_ONLINE');
	}

	if ($guest_array<>0 && $user_array<>0)
	{
		if ($guest_array==1)
		{
			$content .= JText::_('MOD_XWHOSONLINE_WE_HAVE');
			$content .= $guest_array . ' ' . JText::_('MOD_XWHOSONLINE_GUEST_COUNT');
			$content .= JText::_('MOD_XWHOSONLINE_AND');
		}
		else
		{
			$content .= JText::_('MOD_XWHOSONLINE_WE_HAVE');
			$content .= $guest_array . ' ' . JText::_('MOD_XWHOSONLINE_GUESTS_COUNT');
			$content .= JText::_('MOD_XWHOSONLINE_AND');
		}

		if ($user_array==1)
		{
			$content .= $user_array . ' ' . JText::_('MOD_XWHOSONLINE_MEMBER_COUNT');
		}
		else
		{
			$content .= $user_array . ' ' . JText::_('MOD_XWHOSONLINE_MEMBERS_COUNT');
		}
		$content .= JText::_('MOD_XWHOSONLINE_ONLINE');
	}

	if ($this->admin)
	{
		$content .= '</a>';
	}
}

if ($this->users)
{
	$rows = $this->rows;
	if ($rows)
	{
		$content .= '<ul>' . "\n";
		foreach ($rows as $row)
		{
			$content .= '<li><strong>' . $row->username . '</strong></li>' . "\n";
		}
		$content .= '</ul>' . "\n";
	}
	else
	{
		$content .= '<p>' . JText::_('MOD_XWHOSONLINE_NONE') . '</p>' . "\n";
	}
}

echo $content;
