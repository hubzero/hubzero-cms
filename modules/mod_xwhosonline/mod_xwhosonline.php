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

class modXWhosonline 
{
	private $attributes = array();

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	//-----------

	public function display() 
	{
		$database =& JFactory::getDBO();
		
		$params  =& $this->params;
		$online  = $params->get( 'online' );
		$users   = $params->get( 'users' );
		$moduleclass_sfx = $params->get( 'moduleclass_sfx' );
		$content = '';

		if ($online) {
			$query1 = "SELECT COUNT(DISTINCT ip) AS guest_online FROM #__session WHERE guest=1 AND (usertype is NULL OR usertype='')";
			$database->setQuery($query1);
			$guest_array = $database->loadResult();

			$query2 = "SELECT COUNT(DISTINCT username) AS user_online FROM #__session WHERE guest=0 AND usertype <> 'administrator' AND usertype <> 'superadministrator'";
			$database->setQuery($query2);
			$user_array = $database->loadResult();

			$admin = false;
			if ($user_array<>0) {
				//$xuser =& XFactory::getUser();
				$juser =& JFactory::getUser();
				$admin = $juser->authorize('mod_xwhosonline', 'manage');
			}
			if ($admin) {
				$content .= '<a href="/users/">';
			}

			if ($guest_array<>0 && $user_array==0) {
				if ($guest_array == 1) {
					$content .= JText::_('MODXWHOSONLINE_WE_HAVE');
					$content .= $guest_array.' '.JText::_('MODXWHOSONLINE_GUEST_COUNT');
				} else {
					$content .= JText::_('MODXWHOSONLINE_WE_HAVE');
					$content .= $guest_array.' '.JText::_('MODXWHOSONLINE_GUESTS_COUNT');
				}
				$content .= JText::_('MODXWHOSONLINE_ONLINE');
			}

			if ($guest_array==0 && $user_array<>0) {
				if ($user_array==1) {
					$content .= JText::_('MODXWHOSONLINE_WE_HAVE');
					$content .= $user_array.' '.JText::_('MODXWHOSONLINE_MEMBER_COUNT');
				} else {
					$content .= JText::_('MODXWHOSONLINE_WE_HAVE');
					$content .= $user_array.' '.JText::_('MODXWHOSONLINE_MEMBERS_COUNT');
				}
				$content .= JText::_('MODXWHOSONLINE_ONLINE');
			}

			if ($guest_array<>0 && $user_array<>0) {
				if ($guest_array==1) {
					$content .= JText::_('MODXWHOSONLINE_WE_HAVE');
					$content .= $guest_array.' '.JText::_('MODXWHOSONLINE_GUEST_COUNT');
					$content .= JText::_('MODXWHOSONLINE_AND');
				} else {
					$content .= JText::_('MODXWHOSONLINE_WE_HAVE');
					$content .= $guest_array.' '.JText::_('MODXWHOSONLINE_GUESTS_COUNT');
					$content .= JText::_('MODXWHOSONLINE_AND');
				}

				if ($user_array==1) {
					$content .= $user_array.' '.JText::_('MODXWHOSONLINE_MEMBER_COUNT');
				} else {
					$content .= $user_array.' '.JText::_('MODXWHOSONLINE_MEMBERS_COUNT');
				}
				$content .= JText::_('MODXWHOSONLINE_ONLINE');
			}

			if ($admin) {
				$content .= '</a>';
			}
		}

		if ($users) {
			$query = "SELECT DISTINCT a.username"
					."\n FROM #__session AS a"
					."\n WHERE (a.guest=0)";
			$database->setQuery($query);
			$rows = $database->loadObjectList();
			
			$content .= '<ul>'."\n";
			foreach ($rows as $row) 
			{
				$content .= '<li><strong>'. $row->username .'</strong></li>'."\n";
			}
			$content .= '</ul>'."\n";

			if ($content == '') {
				$content .= JText::_('MODXWHOSONLINE_NONE') ."\n";
			}
		}
	    return $content;
	}
}

// Editor usertype check
$jacl =& JFactory::getACL();
$jacl->addACL( 'mod_xwhosonline', 'manage', 'users', 'super administrator' );
$jacl->addACL( 'mod_xwhosonline', 'manage', 'users', 'administrator' );
$jacl->addACL( 'mod_xwhosonline', 'manage', 'users', 'manager' );

$modxwhosonline = new modXWhosonline();
$modxwhosonline->params = $params;

require( JModuleHelper::getLayoutPath('mod_xwhosonline') );
?>

