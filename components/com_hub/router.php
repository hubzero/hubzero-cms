<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
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

function hubBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['task'])) {
		switch ($query['task'])
		{
			case 'logout':
				$segments[] = 'logout';
				unset($query['task']);
			break;
			case 'lostpassword':
				$segments[] = 'lostpassword';
				unset($query['task']);
			break;
			case 'lostusername':
				$segments[] = 'lostusername';
				unset($query['task']);
			break;
			default:
				if (isset($query['view'])) {
					if ($query['view'] == 'resend' 
					 || $query['view'] == 'change'
					 || $query['view'] == 'confirm'
					 || $query['view'] == 'unconfirmed') {
						$segments[] = 'registration';
						$segments[] = $query['view'];
						unset($query['task']);
						unset($query['view']);
						if (isset($query['confirm'])) {
							$segments[] = $query['confirm'];
							unset($query['confirm']);
						}
					} else if ($query['view'] == 'login') {
						$segments[] = 'login';
						unset($query['task']);
					}
				}
			break;
		}
	}

	return $segments;
}

function hubParseRoute($segments)
{
	$xhub = &Hubzero_Factory::getHub();

	$vars = array();
	$count = count($segments);

	if (empty($segments)) 
	{
		return JError::raiseError( 404, "Invalid Request" );
	}
	else if ($segments[0] == 'login')
	{
		if ($count > 3)
			return JError::raiseError( 404, "Invalid Request" );

		if (isset($segments[1]))
		{
			$vars['option'] = 'com_hub';
			$vars['view'] = 'login';
			$vars['task'] = 'login';
			$vars['realm'] = $segments[1];

			if (isset($segments[2]))
				$vars['act'] = $segments[2];
		}
		else
		{
			$vars['option'] = 'com_hub';
			$vars['view'] = 'login';
			$vars['task'] = 'realm';
			$vars['act'] = 'show';
		}
	}
	else if ($segments[0] == 'logout') 
	{
		if ($count > 1)
			return JError::raiseError( 404, "Invalid Request" );

		$vars['option'] = 'com_hub';
		$vars['view'] = 'logout';
		$vars['task'] = 'logout';
		$vars['act']  = 'logout';
	}
	else if ($segments[0] == 'register')
	{
		if ($count > 1)
			return JError::raiseError( 404, "Invalid Request" );

		$vars['option'] = 'com_hub';
		$vars['view'] = 'registration';
		$vars['task'] = 'select';
		$vars['act'] = 'show';
	}
	else if ($segments[0] == 'registration')
	{
		if (($count > 3) || ($count < 2))
			return JError::raiseError( 404, "Invalid Request" );

		if (isset($segments[1]))
		{
			if ($segments[1] == 'update')
			{
				if ($count > 2)
					return JError::raiseError( 404, "Invalid Request" );

				$vars['option'] = 'com_hub';
				$vars['view'] = 'registration';
				$vars['task'] = 'update';
				$vars['act'] = 'show';
			}
			else if ($segments[1] == 'edit')
			{
				$vars['option'] = 'com_hub';
				$vars['view'] = 'registration';
				$vars['task'] = 'edit';
				$vars['act'] = 'show';

				if (isset($segments[2]))
					$vars['username'] = $segments[2];
			}
			else if ($segments[1] == 'select')
			{
				if ($count > 2)
					return JError::raiseError( 404, "Invalid Request" );

				$vars['option'] = 'com_hub';
				$vars['view'] = 'registration';
				$vars['task'] = 'select';
				$vars['act'] = 'show';
			}
			else if ($segments[1] == 'new' || $segments[1] == 'create')
			{
				if ($count > 2)
					return JError::raiseError( 404, "Invalid Request" );

				$vars['option'] = 'com_hub';
				$vars['view'] = 'registration';
				$vars['task'] = 'create';
				$vars['act'] = 'show';
			}
			else if ($segments[1] == 'proxy' || $segments[1] == 'proxycreate')
			{
				$vars['option'] = 'com_hub';
				$vars['view'] = 'registration';
				$vars['task'] = 'proxycreate';
				$vars['act'] = 'show';

				if (isset($segments[2]))
					$vars['username'] = $segments[2];
			}
			else if ($segments[1] == 'resend')
			{
				$vars['option'] = 'com_hub';
				$vars['view'] = 'registration';
				$vars['task'] = 'resend';
				$vars['act'] = 'show';
			}
			else if ($segments[1] == 'change')
			{
				$vars['option'] = 'com_hub';
				$vars['view'] = 'registration';
				$vars['task'] = 'change';
				$vars['act'] = 'show';
			}
			else if ($segments[1] == 'confirm')
			{
				$vars['option'] = 'com_hub';
				$vars['view'] = 'registration';
				$vars['task'] = 'confirm';
				$vars['act'] = 'show';
				$vars['confirm'] = (isset($segments[2])) ? $segments[2] : '';
			}
			else if ($segments[1] == 'unconfirmed')
			{
				$vars['option'] = 'com_hub';
				$vars['view'] = 'registration';
				$vars['task'] = 'unconfirmed';
				$vars['act'] = 'show';
			}
			else
			{
				return JError::raiseError( 404, "Invalid Request" );
			}
		}
	}
	else if ($segments[0] == 'lostpassword')
	{
		if ($count > 1)
			return JError::raiseError( 404, "Invalid Request" );

		$vars['option'] = 'com_hub';
		$vars['view'] = 'lostpassword';
		$vars['task'] = 'lostpassword';
		$vars['act'] = 'lostpassword';
	}
	else if ($segments[0] == 'lostusername')
	{
		if ($count > 1)
			return JError::raiseError( 404, "Invalid Request" );

		$vars['option'] = 'com_hub';
		$vars['view'] = 'lostusername';
		$vars['task'] = 'lostusername';
		$vars['act'] = 'lostusername';
	}
	else
	{
		return JError::raiseError( 404, "Invalid Request" );
	}

	return $vars;
}

?>