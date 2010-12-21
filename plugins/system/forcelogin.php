<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2010 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2010 by Purdue Research Foundation, West Lafayette, IN 47906.
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

jimport('joomla.event.plugin');

class plgSystemForceLogin extends JPlugin
{
	function plgSystemForceLogin(& $subject) 
	{
		parent::__construct($subject, NULL);
	}

	function onAfterRoute()
	{
		global $mainframe;

		if ($mainframe->isAdmin()) {
			return;
		}
	
		$juser = JFactory::getUser();

		if ($juser->get('guest') != '1') {
			return;
		}

		switch( JRequest::getCmd('option') )
		{
			case 'com_user':
			case 'com_register':
				break;

			default:
				$vars = array();
				$vars['option'] = "com_user";
				$vars['view'] = "login";
				$vars['return'] = base64_encode($_SERVER['REQUEST_URI']);
				JRequest::set($vars,'get',true);
				break;
		}
	}
}
