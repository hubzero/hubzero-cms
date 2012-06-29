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

/**
 * Hubzero_Factory Class
 */
class Hubzero_Factory
{
	/**
	 * Get the current user's profile
	 * 
	 * @return     object
	 */
	public static function &getProfile()
	{
		static $instances = null;

		if (!is_object($instances[0]))
		{
			ximport('Hubzero_User_Profile');
			$juser =& JFactory::getUser();
			$instances[0] =& Hubzero_User_Profile::getInstance($juser->get('id'));

			if (is_object($instances[0]))
			{
				return $instances[0];
			}
		}

		return $instances[0];
	}

	/**
	 * Load a list of arrays into the site config
	 * 
	 * @param      array  &$arraylist List of arrays
	 * @param      string $namespace  Namespace to load into
	 * @return     boolean True if list successfully loaded
	 */
	public static function loadArrayList(&$arraylist, $namespace = null)
	{
		$config = &JFactory::getConfig();
		// If namespace is not set, get the default namespace

		if ($namespace == null)
		{
			$namespace = $config->_defaultNameSpace;
		}

		if (!isset($config->_registry[$namespace])) 
		{
			// If namespace does not exist, make it and load the data
			$config->makeNameSpace($namespace);
		}

		foreach ($arraylist as $array)
		{
			// Load the variables into the registry's default namespace.
			$k = $array['var'];
			$v = $array['value'];
			$config->_registry[$namespace]['data']->$array['var'] = $array['value'];
		}

		return true;
	}

	/**
	 * Get a Hubzero_Hub object
	 * 
	 * @return     mixed Object if instances is set, null if not
	 */
	public static function &getHub()
	{
		static $instances;

		if (!isset($instances))
		{
			$instances[0] = null;
		}

		if (!is_object($instances[0]))
		{
			ximport('Hubzero_Hub');
			$instances[0] = new Hubzero_Hub();
		}

		return $instances[0];
	}

	/**
	 * Get a component factory
	 * 
	 * @param      string $component Component name
	 * @return     object
	 */
	public static function &getComponentFactory($component)
	{
		static $instances;

		if (!isset($instances[$component]) || !is_object($instances[$component]))
		{
			ximport('Hubzero_Component_Factory');

			$file = JPATH_SITE . '/administrator/components/com_' . $component . '/factory.php';
			$factoryclass = 'Hubzero_' . $component . '_Factory';

			if (file_exists($file))
			{
				include_once($file);
			}

			if (class_exists($factoryclass))
			{
				$factory = new $factoryclass($component);
			}
			else
			{
				$factory = new Hubzero_Component_Factory($component);
			}

			$instances[$component] = $factory;
		}

		return $instances[$component];
	}

	/**
	 * Get an LDAP connection
	 * 
	 * @param      integer $primary Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function &getLDC($primary = 0)
	{
		static $instances;

		$debug = 0;
		$xhub =& Hubzero_Factory::getHub();
		
		$ldap_params = JComponentHelper::getParams('com_ldap');

		$acctman   = $ldap_params->get('ldap_managerdn','cn=admin');
		$acctmanPW = $ldap_params->get('ldap_managerpw','');
		$aldap     = $ldap_params->get('ldap_primary', 'ldap://localhost');
		$pldap     = $ldap_params->get('ldap_secondary', '');
		
		if ($debug) 
		{
			$xlog =& Hubzero_Factory::getLogger();
		}

		if (empty($primary) && empty($aldap))
		{
			$primary = 1;
		}

		if (!empty($primary))
		{
			if (empty($instances[1]))
			{
				$negotiate_tls = $ldap_params->get('ldap_tls', 0);
				$port          = '389';
				$use_ldapV3    = 1;
				$no_referrals  = 1;

				if (!is_numeric($port))
				{
					$port = '389';

					$pattern = "/^\s*(ldap[s]{0,1}:\/\/|)([^:]*)(\:(\d+)|)\s*$/";

					if (preg_match($pattern, $pldap, $matches))
					{
						$pldap = $matches[2];

						if ($matches[1] == 'ldaps://')
						{
							$negotiate_tls = false;
						}

						if (isset($matches[4]) && is_numeric($matches[4]))
						{
							$port = $matches[4];
						}
					}
				}

				$instances[1] = @ldap_connect($pldap, $port);

				if ($instances[1] == false)
				{
					if ($debug) $xlog->logDebug("getLDC($primary): ldap_connect($pldap,$port) failed. [" . posix_getpid() . "] " . ldap_error($instances[1]));
					return false;
				}

				if ($debug) 
				{
					$xlog->logDebug("getLDC($primary): ldap_connect($pldap,$port) success. ");
				}

				if ($use_ldapV3)
				{
					if (@ldap_set_option($instances[1], LDAP_OPT_PROTOCOL_VERSION, 3) == false)
					{
						if ($debug) 
						{
							$xlog->logDebug("getLDC($primary): ldap_set_option(LDAP_OPT_PROTOCOL_VERSION, 3) failed: " . ldap_error($instances[1]));
						}
						return false;
					}

					if ($debug) 
					{
						$xlog->logDebug("getLDC($primary): ldap_set_option(LDAP_OPT_PROTOCOL_VERSION, 3) success.");
					}
				}

				if (@ldap_set_option($instances[1], LDAP_OPT_RESTART, 1) == false)
				{
					if ($debug) 
					{
						$xlog->logDebug("getLDC($primary): ldap_set_option(LDAP_OPT_RESTART, 1) failed: " . ldap_error($instances[1]));
					}
					return false;
				}

				if ($debug) 
				{
					$xlog->logDebug("getLDC($primary): ldap_set_option(LDAP_OPT_RESTART, 1) success.");
				}

				if ($use_ldapV3 && !@ldap_set_option($_ldc, LDAP_OPT_REFERRALS, $no_referrals ? false : true))
				{
					if ($debug) 
					{
						$xlog->logDebug("getLDC($primary): ldap_set_option(LDAP_OPT_REFERRALS, " . ($no_referrals ? 'false' : 'true') . ") failed: " . ldap_error($instances[1]));
					}
					return false;
				}

				if ($debug) 
				{
					$xlog->logDebug("getLDC($primary): ldap_set_option(LDAP_OPT_REFERRALS, " . ($no_referrals ? 'false' : 'true')  . ") success.");
				}

				if ($use_ldapV3 && $negotiate_tls)
				{
					if (!@ldap_start_tls($instances[1]))
					{
						if ($debug) 
						{
							$xlog->logDebug("getLDC($primary): ldap_start_tls() failed: " . ldap_error($instances[1]));
						}
						return false;
					}

					if ($debug) 
					{
						$xlog->logDebug("getLDC($primary): ldap_start_tls() success.");
					}
				}

				if (@ldap_bind($instances[1], $acctman, $acctmanPW) == false)
				{
					$err     = ldap_errno($instances[1]);
					$errstr  = ldap_error($instances[1]);
					$errstr2 = ldap_err2str($err);
					if ($debug) 
					{
						$xlog->logDebug("getLDC($primary): ldap_bind() failed. [" . posix_getpid() . "] " .  $errstr);
					}
					return false;
				}

				if ($debug) 
				{
					$xlog->logDebug("getLDC($primary): ldap_bind() success.");
				}

				if (empty($instances[0]))
				{
					$instances[0] = $instances[1];
				}
			}

			return $instances[1];
		}
		else
		{
			if (empty($instances[0]))
			{
				$negotiate_tls = $ldap_params->get('ldap_tls', 0);
				$port          = '389';
				$use_ldapV3    = 1;
				$no_referrals  = 1;

				$instances[0] = @ldap_connect($aldap);

				if ($instances[0] == false)
				{
					if ($debug) 
					{
						$xlog->logDebug("getLDC($primary): ldap_connect($aldap) failed. " . ldap_error($instances[0]));
					}

					return false;
				}

				if ($debug) 
				{
					$xlog->logDebug("getLDC($primary): ldap_connect($aldap) success. ");
				}

				if (@ldap_set_option($instances[0], LDAP_OPT_PROTOCOL_VERSION, 3) == false)
				{
					@ldap_close($instances[0]);
					$instances[0] = false;
					if ($debug) 
					{
						$xlog->logDebug("getLDC($primary): ldap_set_option(protocol v3) failed: " . ldap_error($instances[0]));
					}
					return $instances[0];
				}

				if ($debug) 
				{
					$xlog->logDebug("getLDC($primary): ldap_set_option(protocol v3) success.");
				}

				if (@ldap_bind($instances[0], $acctman, $acctmanPW) == false)
				{
					if ($debug) 
					{
						$xlog->logDebug("getLDC($primary): ldap_bind failed: " . ldap_error($instances[0]));
					}
					@ldap_close($instances[0]);
					$instances[0] = false;
					return $instances[0];
				}

				if ($debug) 
				{
					$xlog->logDebug("getLDC($primary): ldap_bind() success.");
				}
			}

			return $instances[0];
		}
	}

	/**
	 * Get the LDAP connection
	 * 
	 * @return     object
	 */
	public static function &getPLDC()
	{
		static $instances;

		if (empty($instances[0]))
		{
			$instances[0] = &Hubzero_Factory::getLDC(1);
		}

		return $instances[0];
	}

	/**
	 * Get the debug logger, creating it if it doesn't exist
	 * 
	 * @return     object
	 */
	public static function &getLogger()
	{
		static $instances;

		if (!is_object($instances[0]))
		{
			ximport('Hubzero_Log');

			$instances[0] = new Hubzero_Log();
			$handler = new Hubzero_Log_FileHandler("/var/log/hubzero/cmsdebug.log");
			$instances[0]->attach(HUBZERO_LOG_DEBUG, $handler);
		}

		return $instances[0];
	}

	/**
	 * Get the auth logger, creating it if it doesn't exist
	 * 
	 * @return     object
	 */
	public static function &getAuthLogger()
	{
		static $instances;

		if (!is_object($instances[0]))
		{
			ximport('Hubzero_Log');

			$instances[] = new Hubzero_Log();
			$handler = new Hubzero_Log_FileHandler("/var/log/hubzero/cmsauth.log");
			$instances[0]->attach(HUBZERO_LOG_AUTH, $handler);
		}

		return $instances[0];
	}
}

