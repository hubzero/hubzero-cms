<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2010-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2010-2011 Purdue University. All rights reserved.
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

