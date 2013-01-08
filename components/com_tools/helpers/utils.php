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
			$fp = @stream_socket_client($host, $errno, $errstr, 30);
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
	
	
	public static function getToolAccess( $tool, $login = '')
	{
		//import needed HUBzero libraries
		ximport('Hubzero_Geo');
		ximport('Hubzero_Factory');
		ximport('Hubzero_User_Helper');
		
		//include tool models
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'tool.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'group.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');

		//instantiate objects
		$access 	= new stdClass();
		$juser		=& JFactory::getUser();
		$database 	=& JFactory::getDBO();
		$xlog 		=& Hubzero_Factory::getLogger();
		
		// Ensure we have a tool
		if (!$tool) 
		{
			$access->valid = 0;
			$access->error->message = 'No tool provided.';
			$xlog->logDebug("mw::_getToolAccess($tool,$login) FAILED null tool check");
			return $access;
		}
		
		// Ensure we have a login
		if ($login == '') 
		{
			$login = $juser->get('username');
			if ($login == '') 
			{
				$access->valid = 0;
				$access->error->message = 'Unable to grant tool access to user, no user was found.';
				$xlog->logDebug("mw::_getToolAccess($tool,$login) FAILED null user check");
				return $access;
			}
		}
		
		//load tool version
		$toolVersion = new ToolVersion( $database );
		$toolVersion->loadFromInstance( $tool );
		if (empty($toolVersion)) 
		{
			$access->valid = 0;
			$access->error->message = 'Unable to load the tool';
			$xlog->logDebug("mw::_getToolAccess($tool,$login) FAILED null tool version check");
			return $access;
		}
		
		//load the tool groups
		$toolGroup = new ToolGroup( $database );
		$query = "SELECT * FROM " . $toolGroup->getTableName() . " WHERE toolid=" . $toolVersion->toolid;
		$database->setQuery( $query );
		$toolgroups = $database->loadObjectList();
		
		//get users groups
		$xgroups = Hubzero_User_Helper::getGroups( $juser->get('id'), 'members' );

		// Check if the user is in any groups for this app
		$ingroup = false;
		$groups = array();
		$indevgroup = false;
		if ($xgroups) 
		{
			foreach ($xgroups as $xgroup)
			{
				$groups[] = $xgroup->cn;
			}
			if ($toolgroups) 
			{
				foreach ($toolgroups as $toolgroup)
				{
					if (in_array($toolgroup->cn, $groups)) 
					{
						$ingroup = true;
						if ($toolgroup->role == 1)
						{
							$indevgroup = true;
						}
					}
				}
			}
		}
		
		//check to see if we are an admin
		$admin = false;
		$ctconfig =& JComponentHelper::getParams('com_tools');
		if ($ctconfig->get('admingroup') != '' && in_array($ctconfig->get('admingroup'), $groups)) 
		{
			$admin = true;
		}
		
		//get access settings
		$exportAllowed = ToolsHelperUtils::getToolExportAccess( $toolVersion->exportControl );
		$isToolPublished = ($toolVersion->state == 1);
		$isToolDev = ($toolVersion->state == 3);
	    $isToolGroupControlled = ($toolVersion->toolaccess == '@GROUP');
		
		//check for dev tools
		if ($isToolDev) 
		{
			//if were not in the dev group or an admin we must deny
			if(!$indevgroup && !$admin)
			{
				$access->valid = 0;
				$access->error->message = 'The development version of this tool may only be accessed by members of it\'s development group.';
				$xlog->logDebug("mw::_getToolAccess($tool,$login): DEV TOOL ACCESS DENIED (USER NOT IN DEVELOPMENT OR ADMIN GROUPS)");
			}
			else
			{
				$access->valid = 1;
			}
		}
		//check for published tools
		else if($isToolPublished)
		{
			//are we checking for a group controlled tool
			if($isToolGroupControlled)
			{
				//if were not in the group that controls it and not admin we must deny
				if(!$ingroup && !$admin)
				{
					$access->valid = 0;
					$access->error->message = 'This tool may only be accessed by members of it\'s access control groups.';
					$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS DENIED (USER NOT IN ACCESS OR ADMIN GROUPS)");
				}
				else
				{
					$access->valid = 1;
				}
			}
			else
			{
				if(!$exportAllowed->valid)
				{
					$access->valid = 0;
					$access->error->message = 'Export Access Denied';
					$xlog->logDebug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS DENIED (EXPORT DENIED)");
				}
				else
				{
					$access->valid = 1;
				}
			}
		}
		//not published tool
		else
		{
			$access->valid = 0;
			$access->error->message = 'This tool version is not published.';
			$xlog->logDebug("mw::_getToolAccess($tool,$login): UNPUBLISHED TOOL ACCESS DENIED (TOOL NOT PUBLISHED)");
		}
		
		//return access
		return $access;
	}
	
	
	public function getToolExportAccess( $export_control )
	{
		//include needed HUBzero libraries
		ximport('Hubzero_Factory');
		ximport('Hubzero_Environment');
		
		//instaniate objects
		$export_access = new stdClass;
		$xlog =& Hubzero_Factory::getLogger();
		$ip = Hubzero_Environment::ipAddress();
		
		//get the export control level
		$export_control = strtolower( $export_control );
		
		//get the users country based on ip address
		$country = Hubzero_Geo::ipcountry( $ip );
		
		//if we dont know the users location and its a restricted to we have to deny access
		if (empty($country) && in_array($export_control, array('us', 'd1', 'pu')))
		{
			$export_access->valid = 0;
			$export_access->error->message = 'This tool may not be accessed from your unknown current location due to export/license restrictions.';
			$xlog->logDebug("mw::_getToolExportControl($export_control) FAILED location export control check");
			return $export_access;
		}
		
		//if the user is in an E1 nation
		if (Hubzero_Geo::is_e1nation(Hubzero_Geo::ipcountry($ip))) 
		{
			$export_access->valid = 0;
			$export_access->error->message = 'This tool may not be accessed from your current location due to E1 export/license restrictions.';
			$xlog->logDebug("mw::_getToolExportControl($export_control) FAILED E1 export control check");
			return $export_access;
		}
		
		//run checks depending on the export ac
		switch ($export_control)
		{
			case 'us':
				if (Hubzero_Geo::ipcountry( $ip ) != 'us') 
				{
					$export_access->valid = 0;
					$export_access->error->message = 'This tool may only be accessed from within the U.S. due to export/licensing restrictions.';
					$xlog->logDebug("mw::_getToolExportControl($export_control) FAILED US export control check");
					return $export_access;
				}
			break;

			case 'd1':
				if (Hubzero_Geo::is_d1nation( Hubzero_Geo::ipcountry( $ip ) )) 
				{
					$export_access->valid = 0;
					$export_access->error->message = 'This tool may not be accessed from your current location due to export/license restrictions.';
					$xlog->logDebug("mw::_getToolExportControl($export_control) FAILED D1 export control check");
					return $export_access;
				}
			break;

			case 'pu':
				if (!Hubzero_Geo::is_iplocation( $ip, $export_control )) 
				{
					$export_access->valid = 0;
					$export_access->error->message = 'This tool may only be accessed by authorized users while on the West Lafayette campus of Purdue University due to license restrictions.';
					$xlog->logDebug("mw::_getToolExportControl($export_control) FAILED PURDUE export control check");
					return $export_access;
				}
			break;
		}
		
		//passed all checks
		$export_access->valid = 1;
		return $export_access;
	}
}
