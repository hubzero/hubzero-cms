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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.event.plugin');

/**
 * System plugin for hubzero
 */
class plgSystemHubzero extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @since 1.5
	 */
	public function __construct(& $subject)
	{
		parent::__construct($subject, NULL);
	}

	/**
	 * Hook for after app routing
	 *
	 * @return	   void
	 */
	public function onAfterRoute()
	{
		$app = JFactory::getApplication();
		/*if (!JPluginHelper::isEnabled('system', 'jquery'))
		{
			JHTML::_('behavior.mootools');
		}*/
	}

	/**
	 * Hook for after app initialization
	 *
	 * @return	   void
	 */
	public function onAfterInitialise()
	{
		// Get the application object
		$app = JFactory::getApplication();

		// Get the user object
		$user = JFactory::getUser();

		// Get the session object
		$session = JFactory::getSession();

		if ($session->isNew())
		{
			$tracker = array();

			// Transfer tracking cookie data to session

			jimport('joomla.utilities.utility');
			jimport('joomla.utilities.simplecrypt');
			jimport('joomla.user.helper');

			$hash = JUtility::getHash( JFactory::getApplication()->getName().':tracker');

			$crypt = new JSimpleCrypt();

			if ($str = JRequest::getString($hash, '', 'cookie', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM))
			{
				$sstr = $crypt->decrypt($str);
				$tracker = @unserialize($sstr);

				if ($tracker === false) // old tracking cookies encrypted with UA which is too short term for a tracking cookie
				{
					//Create the encryption key, apply extra hardening using the user agent string
					$key = JUtility::getHash(@$_SERVER['HTTP_USER_AGENT']);
					$crypt = new JSimpleCrypt($key);
					$sstr = $crypt->decrypt($str);
					$tracker = @unserialize($sstr);
				}
			}

			if (!is_array($tracker))
			{
				$tracker = array();
			}

			if (empty($tracker['user_id']))
			{
				$session->clear('tracker.user_id');
			}
			else
			{
				$session->set('tracker.user_id', $tracker['user_id']);
			}

			if (empty($tracker['username']))
			{
				$session->clear('tracker.username');
			}
			else
			{
				$session->set('tracker.username', $tracker['username']);
			}

			if (empty($tracker['sid']))
			{
				$session->clear('tracker.psid');
			}
			else
			{
				$session->set('tracker.psid', $tracker['sid']);
			}

			$session->set('tracker.sid', $session->getId());

			if (empty($tracker['ssid']))
			{
				$session->set('tracker.ssid', $session->getId());
			}
			else
			{
				$session->set('tracker.ssid', $tracker['ssid']);
			}

			if (empty($tracker['rsid']))
			{
				$session->set('tracker.rsid', $session->getId());
			}
			else
			{
				$session->set('tracker.rsid', $tracker['rsid']);
			}

			// log tracking cookie detection to auth log

			$username = (empty($tracker['username'])) ? '-' : $tracker['username'];
			$user_id = (empty($tracker['user_id'])) ? 0 : $tracker['user_id'];
			JFactory::getAuthLogger()->info( $username . ' ' . $_SERVER['REMOTE_ADDR'] . ' detect');

			// set new tracking cookie with current data
			$tracker = array();
			$tracker['user_id'] = $session->get('tracker.user_id');
			$tracker['username'] = $session->get('tracker.username');
			$tracker['sid']  = $session->get('tracker.sid');
			$tracker['rsid'] = $session->get('tracker.rsid');
			$tracker['ssid'] = $session->get('tracker.ssid');
			$cookie = $crypt->encrypt(serialize($tracker));
			$lifetime = time() + 365*24*60*60;
			setcookie($hash, $cookie, $lifetime, '/');
		}

		// all page loads set apache log data

		if (php_sapi_name() == 'apache')
		{
			apache_note('jsession', $session->getId());

			if ($user->get('id') != 0)
			{
				apache_note('auth','session');
				apache_note('userid', $user->get('id'));
			}
			else if (!empty($tracker['user_id']))
			{
				apache_note('auth','cookie');
				apache_note('userid', $tracker['user_id']);
			}
		}
	}

	/**
	 * Hook for login failure
	 *
	 * @param	   unknown $response Parameter description (if any) ...
	 * @return	   boolean
	 */
	public function onUserLoginFailure($response)
	{
		JFactory::getAuthLogger()->info($_POST['username'] . ' ' . $_SERVER['REMOTE_ADDR'] . ' invalid');
		apache_note('auth','invalid');

		return true;
	}
}
