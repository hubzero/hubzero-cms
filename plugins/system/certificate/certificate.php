<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2012 Purdue University. All rights reserved.
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
 * @package   HUBzero
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * System plugin for client side certificate restrictions
 */
class plgSystemCertificate extends \Hubzero\Plugin\Plugin
{
	/**
	 * Constructor
	 *
	 * @param object $subject the object to observe
	 * @param array  $config  an array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Hook for after parsing route
	 *
	 * @param  array $vars the request vars
	 * @return void
	 */
	public function onParseRoute($vars)
	{
		// First, check for presence of subject dn, which is the minimum required field
		if (!isset($_SERVER['SSL_CLIENT_S_DN']) || !$_SERVER['SSL_CLIENT_S_DN'])
		{
			\JFactory::getApplication()->redirect($this->params->get('failure_location', '/invalidcert.php'));
			return;
		}

		if (\JFactory::getUser()->get('guest'))
		{
			// If so, redirect to login
			$return['option']        = 'com_users';
			$return['task']          = 'user.login';
			$return['authenticator'] = 'certificate';
			$return['return']        = base64_encode(\JURI::current());

			return $return;
		}

		// Check if user is registered and if current session is linked to cert identity
		$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'certificate', $_SERVER['SSL_CLIENT_I_DN_CN']);
		if ($link = \Hubzero\Auth\Link::getInstance($hzad->id, $_SERVER['SSL_CLIENT_S_DN_CN']))
		{
			if ($link->user_id == \JFactory::getUser()->get('id'))
			{
				// All clear...return nothing
				return;
			}
		}

		// Otherwise, we have a cert-based user that doesn't match the current user
		$return['option'] = 'com_users';
		$return['task']   = 'user.logout';

		return $return;
	}
}