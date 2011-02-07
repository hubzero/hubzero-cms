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


class PageMacro extends WikiMacro 
{
	public function description() 
	{
		$txt = array();
		$txt['wiki'] = 'This macro will insert a linked title to a wiki page. It can be passed either an ID or alias.';
		$txt['html'] = '<p>This macro will insert a linked title to a wiki page. It can be passed either an ID or alias.</p>';
		return $txt['html'];
	}
	
	//-----------
	
	public function render() 
	{
		$et = $this->args;
		
		if (!$et) {
			return '';
		}
		
		$p = split(',', $et);
		$page = array_shift($p);

		$nolink = false;
		$p = explode(' ',end($p));
		foreach ($p as $a) 
		{
			$a = trim($a);
			
			if ($a == 'nolink') {
				$nolink = true;
			}
		}

		// Is it numeric?
		$scope = '';
		if (is_numeric($page)) {
			// Yes
			$page = intval($page);
		} else {
			if (substr($page,-1) == '/') {
				$page = substr($page,0,-1);
			}
			if (substr($page,0,1) == '/') {
				$page = substr($page,1,strlen($page));
			}
			if (strstr($page, '/')) {
				$bits = explode('/',$page);
				$page = array_pop($bits);
				$scope = implode('/',$bits);
			}
		}

		if ($this->domain != '' && $scope == '') {
			$scope = $this->scope;
		}
		
		// No, get resource by alias
		$g = new WikiPage( $this->_db );
		$g->load( $page, $scope );
		if (!$g->id) {
			return '(Page('.$et.') failed)';
		}

		if ($nolink) {
			return stripslashes($g->title);
		} else {
			// Build and return the link
			if ($g->group != '' && $g->scope != '') {
				$link = 'index.php?option=com_groups&scope='.$g->scope.'&pagename='.$g->pagename;
			} else {
				$link = 'index.php?option=com_topics&scope='.$g->scope.'&pagename='.$g->pagename;
			}
			//return '['.JRoute::_( $link ).' '.stripslashes($r[1]).']';
			return '<a href="'.JRoute::_( $link ).'">'.stripslashes($g->title).'</a>';
		}
	}
}
