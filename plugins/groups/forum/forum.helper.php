<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------
// Forum helper class
//----------------------------------------------------------

class ForumHelper 
{
	public function getMemberPhoto( $member, $anonymous=0 )
	{
		$config =& JComponentHelper::getParams( 'com_members' );
		
		if (!$anonymous && $member->get('picture')) {
			$thumb  = $config->get('webpath');
			if (substr($thumb, 0, 1) != DS) {
				$thumb = DS.$thumb;
			}
			if (substr($thumb, -1, 1) == DS) {
				$thumb = substr($thumb, 0, (strlen($thumb) - 1));
			}
			$thumb .= DS.ForumHelper::niceidformat($member->get('uidNumber')).DS.$member->get('picture');
			
			$thumb = ForumHelper::thumbit($thumb);
		} else {
			$thumb = '';
		}
		
		$dfthumb = $config->get('defaultpic');
		if (substr($dfthumb, 0, 1) != DS) {
			$dfthumb = DS.$dfthumb;
		}
		$dfthumb = ForumHelper::thumbit($dfthumb);
		
		if ($thumb && is_file(JPATH_ROOT.$thumb)) {
			return $thumb;
		} else if (is_file(JPATH_ROOT.$dfthumb)) {
			return $dfthumb;
		}
	}
	
	//-----------
	
	public function thumbit($thumb) 
	{
		$image = explode('.',$thumb);
		$n = count($image);
		$image[$n-2] .= '_thumb';
		$end = array_pop($image);
		$image[] = $end;
		$thumb = implode('.',$image);
		
		return $thumb;
	}
	
	//-----------

	public function niceidformat($someid) 
	{
		while (strlen($someid) < 5) 
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}
}
