<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
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

/**
 * xHUB Factory Class
 **/

class XFactory
{
	function &getProfile()
	{
		static $instances = null;

		if (!is_object( $instances[0] ))
		{
			ximport('xprofile');
			$juser =& JFactory::getUser();
			$instances[0] =& XProfile::getInstance($juser->get('username'));

			if (is_object($instances[0]))
				return $instances[0];
		}

		return $instances[0];
	}

	function loadArrayList(&$arraylist, $namespace = null)
	{
		$config = &JFactory::getConfig();
		// If namespace is not set, get the default namespace
		
		if ($namespace == null) 
			$namespace = $config->_defaultNameSpace;

		if (!isset($config->_registry[$namespace])) {
			// If namespace does not exist, make it and load the data
			$config->makeNameSpace($namespace);
		}

		foreach($arraylist as $array) 
		{
			// Load the variables into the registry's default namespace.
			$k = $array['var'];
			$v = $array['value'];
			$config->_registry[$namespace]['data']->$array['var'] = $array['value'];
		}

		return true;
	}

	function &getHub()
	{
		 ximport('xhub');

		 static $instances;

		 if (!isset($instances))
			 $instances[0] = null;

		 if (!is_object($instances[0]))
			 $instances[0] =& new XHub();

		 return $instances[0];
	}

	function &getComponentFactory($component)
	{
		static $instances;

		if (!isset($instances[$component]) || !is_object($instances[$component])) 
		{
			$file = JPATH_SITE . '/administrator/components/com_' . $component . '/factory.php';
			$factoryclass = 'X' . $component . 'Factory';

			if (file_exists($file))
				include_once($file);

			if (class_exists($factoryclass))
				$factory =& new $factoryclass($component);
			else
				$factory =& new XComponentFactory($component);
		
			$instances[$component] = $factory;
		}

		return $instances[$component];
	}

	function &getLDC($primary = 0)
	{
		static $instances;
		$debug = 0;
		$xhub =& XFactory::getHub();
		$acctman = $xhub->getCfg('hubLDAPAcctMgrDN');
		$acctmanPW = $xhub->getCfg('hubLDAPAcctMgrPW');
		$aldap = $xhub->getCfg('hubLDAPSlaveHosts');
		$pldap = $xhub->getCfg('hubLDAPMasterHost');

		if ($debug) $xlog =& XFactory::getLogger();

		if (empty($primary) && empty($aldap))
			$primary = 1;

		if (!empty($primary)) 
		{
			if (empty($instances[1]))
			{
				$negotiate_tls = $xhub->getCfg('hubLDAPNegotiateTLS','0');
				$port          = '389';
			    	$use_ldapV3    = 1;
        			$no_referrals  = 1;

				if (!is_numeric($port))
					$port = '389';

		        	$pattern = "/^\s*(ldap[s]{0,1}:\/\/|)([^:]*)(\:(\d+)|)\s*$/";

        			if (preg_match($pattern, $pldap, $matches))
        			{
            				$pldap = $matches[2];

            				if ($matches[1] == 'ldaps://')
                				$negotiate_tls = false;

            				if (isset($matches[4]) && is_numeric($matches[4]))
                				$port = $matches[4];
        			}

				$instances[1] = @ldap_connect($pldap,$port);

				if ($instances[1] == false)
				{
					if ($debug) $xlog->logDebug("getLDC($primary): ldap_connect($pldap,$port) failed. [" . posix_getpid() . "] " . ldap_error($instances[1]));
					return false;
				}

				if ($debug) $xlog->logDebug("getLDC($primary): ldap_connect($pldap,$port) success. ");

				if ($use_ldapV3)
				{
					if (@ldap_set_option($instances[1], LDAP_OPT_PROTOCOL_VERSION, 3) == false)
					{
						if ($debug) $xlog->logDebug("getLDC($primary): ldap_set_option(LDAP_OPT_PROTOCOL_VERSION, 3) failed: " . ldap_error($instances[1]));
						return false;
					}

					if ($debug) $xlog->logDebug("getLDC($primary): ldap_set_option(LDAP_OPT_PROTOCOL_VERSION, 3) success.");
				}

				if (@ldap_set_option($instances[1], LDAP_OPT_RESTART, 1) == false)
				{
					if ($debug) $xlog->logDebug("getLDC($primary): ldap_set_option(LDAP_OPT_RESTART, 1) failed: " . ldap_error($instances[1]));
					return false;
				}

				if ($debug) $xlog->logDebug("getLDC($primary): ldap_set_option(LDAP_OPT_RESTART, 1) success.");

				
				if ( $use_ldapV3 && !@ldap_set_option($_ldc, LDAP_OPT_REFERRALS, $no_referrals ? false : true) )
				{
					if ($debug) $xlog->logDebug("getLDC($primary): ldap_set_option(LDAP_OPT_REFERRALS, " . ($no_referrals ? 'false' : 'true') . ") failed: " . ldap_error($instances[1]));
					return false;
				}

				if ($debug) $xlog->logDebug("getLDC($primary): ldap_set_option(LDAP_OPT_REFERRALS, " . ($no_referrals ? 'false' : 'true')  . ") success.");

				if ( $use_ldapV3 && $negotiate_tls )
				{
					if (!@ldap_start_tls($instances[1]))
					{
						if ($debug) $xlog->logDebug("getLDC($primary): ldap_start_tls() failed: " . ldap_error($instances[1]));
						return false;
					}

					if ($debug) $xlog->logDebug("getLDC($primary): ldap_start_tls() success.");
				}

				if (@ldap_bind($instances[1], $acctman, $acctmanPW) == false)
				{
					$err = ldap_errno($instances[1]);
					$errstr = ldap_error($instances[1]);
					$errstr2 = ldap_err2str($err);
					if ($debug) $xlog->logDebug("getLDC($primary): ldap_bind() failed. [" . posix_getpid() . "] " .  $errstr);
					return false;
				}

				if ($debug) $xlog->logDebug("getLDC($primary): ldap_bind() success.");
				
				if (empty($instances[0]))
					$instances[0] = $instances[1];
			}

			return $instances[1];
		}
		else 
		{
			if (empty($instances[0]))
			{
				$negotiate_tls = $xhub->getCfg('hubLDAPNegotiateTLS','0');
				$port          = '389';
			    	$use_ldapV3    = 1;
        			$no_referrals  = 1;

				$instances[0] = @ldap_connect($aldap);

				if ($instances[0] == false)
				{
					if ($debug) $xlog->logDebug("getLDC($primary): ldap_connect($aldap) failed. " . ldap_error($instances[0]));

					return false;
				}

				if ($debug) $xlog->logDebug("getLDC($primary): ldap_connect($aldap) success. ");

				if (@ldap_set_option($instances[0], LDAP_OPT_PROTOCOL_VERSION, 3) == false)
				{
					@ldap_close($instances[0]);
					$instances[0] = false;
					if ($debug) $xlog->logDebug("getLDC($primary): ldap_set_option(protocol v3) failed: " . ldap_error($instances[0]));
					return $instances[0];
				}

				if ($debug) $xlog->logDebug("getLDC($primary): ldap_set_option(protocol v3) success.");
				
				if (@ldap_bind($instances[0], $acctman, $acctmanPW) == false)
				{
					if ($debug) $xlog->logDebug("getLDC($primary): ldap_bind failed: " . ldap_error($instances[0]));
					@ldap_close($instances[0]);
					$instances[0] = false;
					return $instances[0];
				}

				if ($debug) $xlog->logDebug("getLDC($primary): ldap_bind() success.");

			}

			return $instances[0];
		}

	}

	function &getPLDC()
	{
		static $instances;

		if (empty($instances[0]))
		{
			$instances[0] = &XFactory::getLDC(1);
		}

		return $instances[0];
	}

	function &getLogger()
	{
		static $instances;

		if ( !is_object($instances[0]) ) 
		{
			ximport('xlog');

			$instances[0] =& new XLog();
			$handler =& new XLogFileHandler("/var/log/hub/xhub.log");
			$instances[0]->attach(XLOG_DEBUG, $handler);
		}

		return $instances[0];
	}

	function &getAuthLogger()
	{
		static $instances;

		if (!is_object($instances[0]) ) 
		{
			ximport('xlog');

			$instances[] =& new XLog();
			$handler =& new XLogFileHandler("/var/log/hub/auth.log");
			$instances[0]->attach(XLOG_AUTH, $handler);
		}

		return $instances[0];
	}

}

class XComponentFactory 
{
	var $_name;

	function XComponentFactory($name) 
	{
		$this->_name = $name;
		$this->loadConfig();
	}

	function loadConfig($file = '')
	{
		if ($file == '')
			$file = JPATH_SITE . '/administrator/components/com_' . $this->_name . '/config.php';

		$configclass = 'X' . $this->_name . 'Config';
		$namespace = 'xhub_' . $this->_name;

		if (file_exists($file))
			include_once($file);

		if ( class_exists($configclass) ) 
		{
			$registry =& JFactory::getConfig();
			$config =& new $configclass();
			$registry->loadObject($config, $namespace);
		}
	}

	function getCfg( $varname, $default = '' )
	{
		$registry =& JFactory::getConfig();

		return $registry->getValue('xhub_' . $this->_name . '.' . $varname, $default);
	}

	function &getDBO()
	{
		if (defined('_JEXEC'))
			return JFactory::getDBO();
		else
			return $GLOBALS['database'];
	}
}
?>
