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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Contains functions used by multiple Session/Tool modules
 */
class ToolsHelperUtils
{
	/**
	 * Return a middleware database object
	 * 
	 * @return     mixed
	 */
	public function getMWDBO()
	{
		static $instance;

		if (!is_object($instance)) 
		{
			$config =& JComponentHelper::getParams('com_tools');
			$enabled = $config->get('mw_on');

			if (!$enabled) 
			{
				return null;
			}

			$options['driver']   = $config->get('mwDBDriver');
			$options['host']     = $config->get('mwDBHost');
			$options['port']     = $config->get('mwDBPort');
			$options['user']     = $config->get('mwDBUsername');
			$options['password'] = $config->get('mwDBPassword');
			$options['database'] = $config->get('mwDBDatabase');
			$options['prefix']   = $config->get('mwDBPrefix');

			if ((!isset($options['password']) || $options['password'] == '') 
			 && (!isset($options['user']) || $options['user'] == '')
			 && (!isset($options['database']) || $options['database'] == '')) 
			{
				$instance =& JFactory::getDBO();
			}
			else 
			{
				$instance =& JDatabase::getInstance($options);
				if (JError::isError($instance)) 
				{
					$instance =& JFactory::getDBO();
				}
			}
		}

		if (JError::isError($instance)) 
		{
			return null;
		}

		return $instance;
	}

	/**
	 * Return the amount of disk space used
	 * 
	 * @param      string $username User to look up disk space for
	 * @return     array
	 */
	public function getDiskUsage($username)
	{
		$info = array();

		$config =& JComponentHelper::getParams('com_tools');
		$host = $config->get('storagehost');

		if ($username && $host) 
		{
			$fp = stream_socket_client($host, $errno, $errstr, 30);
			if (!$fp) 
			{
				$info[] = "$errstr ($errno)\n";
			} 
			else 
			{
				$msg = '';
				fwrite($fp, "getquota user=" . $username . "\n");
				while (!feof($fp))
				{
					$msg .= fgets($fp, 1024);
				}
				fclose($fp);
				$tokens = explode(',', $msg);
				foreach ($tokens as $token)
				{
					if (!empty($token))
					{
						$t = preg_split('/=/', $token);
						$info[$t[0]] = (isset($t[1])) ? $t[1] : '';
					}
				}
			}
		}
		return $info;
	}

	/**
	 * Short description for 'makeArray'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $string Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function makeArray($string='') 
	{
		$string = preg_replace('/ /', ',', $string);
		$arr    = explode(',', $string);
		$arr    = self::cleanArray($arr);
		$arr    = array_unique($arr);

		return $arr;
	}

	/**
	 * Short description for 'cleanArray'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $array Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function cleanArray($array) 
	{
		foreach ($array as $key => $value) 
		{
			$value = trim($value);
			if ($value == '') 
			{
				unset($array[$key]);
			}
		}
		return $array;
	}

	/**
	 * Short description for 'check_validInput'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $field Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function check_validInput($field)
	{
		if (preg_match("#^[_0-9a-zA-Z.:-]+$#i", $field) or $field=='') 
		{
			return(0);
		} 
		else 
		{
			return(1);
		}
	}

	/**
	 * Short description for 'getLicenses'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $database Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getLicenses($database)
	{
		$database->setQuery("SELECT text, name, title FROM #__tool_licenses ORDER BY ordering ASC");
		return $database->loadObjectList();
	}

	/**
	 * Short description for 'transform'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $array Parameter description (if any) ...
	 * @param      unknown $label Parameter description (if any) ...
	 * @param      array $newarray Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function transform($array, $label, $newarray=array()) 
	{
		if (count($array) > 0) 
		{
			foreach ($array as $a) 
			{
				if (is_object($a)) 
				{
					$newarray[] = $a->$label;
				}
				else 
				{
					$newarray[] = $a;
				}
			}
		}

		return $newarray;
	}

	/**
	 * Short description for 'getLogins'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $uids Parameter description (if any) ...
	 * @param      array $logins Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function getLogins($uids, $logins = array()) 
	{
		if (is_array($uids)) 
		{
			foreach ($uids as $uid) 
			{
				$juser =& JUser::getInstance($uid);
				if ($juser) 
				{
					$logins[] = $juser->get('username');
				}
			}
		}
		return $logins;
	}
}
