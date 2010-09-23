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

//----------------------------------------------------------
// Blog helper class
//----------------------------------------------------------

class AnswersHelperMember 
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
			$thumb .= DS.AnswersHelperMember::niceidformat($member->get('uidNumber')).DS.$member->get('picture');
			
			$thumb = AnswersHelperMember::thumbit($thumb);
		} else {
			$thumb = '';
		}
		
		$dfthumb = $config->get('defaultpic');
		if (substr($dfthumb, 0, 1) != DS) {
			$dfthumb = DS.$dfthumb;
		}
		$dfthumb = AnswersHelperMember::thumbit($dfthumb);
		
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