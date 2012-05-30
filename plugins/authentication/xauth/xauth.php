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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

// TODO: hzldap plugin should be built into here maybe

/**
 * Authentication Plugin class for XAuth
 */
class plgAuthenticationXauth extends JPlugin
{

	/**
	 * Short description for 'attach'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $obsert Parameter description (if any) ...
	 * @return     void
	 */
	public function attach($obsert)
	{
		// I don't know what this is for at the moment, but it complains if this
		// function is missing
	}

	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		JPluginHelper::importPlugin('xauthentication');
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param   array 	$credentials Array holding the user credentials
	 * @param 	array   $options     Array of extra options
	 * @param	object	$response	 Authentication response object
	 * @return	boolean
	 * @since 1.5
	 */
	public function onAuthenticate($credentials, $options, &$response)
	{
		$plugins = JPluginHelper::getPlugin('xauthentication');

		foreach ($plugins as $plugin)
		{
			if (empty($options['domain']))
			{
				$options['domain'] = $plugin->name;
			}

			$className = 'plg' . $plugin->type . $plugin->name;

			if (($options['domain'] == $plugin->name) && class_exists($className))
			{
				ximport('Hubzero_User_Helper');

				$xauthplugin = new $className($this, (array)$plugin);
				$xauthplugin->onAuthenticate($credentials, $options, $response);
				$response->type = 'xauth';

				if (($options['domain'] == 'hzldap') || empty($options['domain']) || ($options['domain'] == 'hubzero'))
				{
					return;
				}

				ximport('Hubzero_User_Helper');

				$domain_id = Hubzero_User_Helper::getXDomainId($options['domain']);

				if ($domain_id === false)
				{
					$domain_id = Hubzero_User_Helper::createXDomain($options['domain']);
				}

				$uid = Hubzero_User_Helper::getXDomainUserId($response->username, $options['domain']);

				if ($uid)
				{
					$juser = JUser::getInstance($uid);

					if ($juser && !JError::isError($juser))
					{
						$response->username = $juser->get('username');
						$response->fullname = $juser->get('name');
						$response->email    = $juser->get('email');
						return;
					}
				}

				if (trim($response->fullname) == '')
				{
					$response->fullname = '-' . $domain_id . ':' . bin2hex($response->username) . ':';
				}

				$response->email = '-' . $domain_id . '-' . bin2hex($response->email) . '@' . bin2hex($response->username) . '.localhost.invalid';
				$response->username = '-' . $domain_id . ':' . bin2hex($response->username) . ':';

				return;
			}
		}

		$response->type = 'xauth';
		$response->status = JAUTHENTICATE_STATUS_FAILURE;
		$response->error_message = 'Invalid domain';
	}

	/**
	 * Short description for 'onAuthenticateOld'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $credentials Parameter description (if any) ...
	 * @param      array $options Parameter description (if any) ...
	 * @param      object &$response Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function onAuthenticateOld($credentials, $options, &$response)
	{
		$plugins = JPluginHelper::getPlugin('xauthentication');

		foreach ($plugins as $plugin)
		{
			if (empty($options['domain']))
			{
				$options['domain'] = $plugin->name;
			}

			$className = 'plg' . $plugin->type . $plugin->name;

			if (($options['domain'] == $plugin->name) && class_exists($className))
			{
				$plugin = new $className($this, (array)$plugin);
				$plugin->onAuthenticate($credentials, $options, $response);
				$response->type = 'xauth';
				return;
			}
		}

		$response->type = 'xauth';
		$response->status = JAUTHENTICATE_STATUS_FAILURE;
		$response->error_message = 'Invalid domain';
	}
}
