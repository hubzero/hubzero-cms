<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Helpers;

use Exception;
use Component;
use Request;
use User;
use Lang;
use App;
use stdClass;

/**
 * Contains functions used by multiple Session/Tool modules
 */
class Utils
{
	/**
	 * Return a middleware database object
	 *
	 * @return     mixed
	 */
	public static function getMWDBO()
	{
		static $instance;

		if (!is_object($instance))
		{
			$config  = Component::params('com_tools');
			$enabled = $config->get('mw_on');

			if (!$enabled && !App::isAdmin())
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
				$instance = \App::get('db');
			}
			else
			{
				$instance = \JDatabase::getInstance($options);
				if ($instance instanceof Exception)
				{
					$instance = \App::get('db');
				}
			}
		}

		if ($instance instanceof Exception)
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
	public static function getDiskUsage($username)
	{
		$info = array();

		$config = Component::params('com_tools');
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
	 * Create a user's home director (if it doesn't exist)
	 *
	 * @param      string $username User for which to create home directory
	 * @return     array
	 */
	public static function createHomeDirectory($username)
	{
		$command = "create_userhome '{$username}'";
		$cmd = "/bin/sh " . dirname(__DIR__) . "/scripts/mw {$command} 2>&1 </dev/null";

		exec($cmd, $results, $status);

		// Check exec status
		if (!isset($status) || $status != 0)
		{
			// Something went wrong
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'makeArray'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $string Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public static function makeArray($string='')
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
	public static function cleanArray($array)
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
	public static function check_validInput($field)
	{
		if (preg_match("#^[_0-9a-zA-Z.:-]+$#i", $field) or $field=='')
		{
			return(0);
		}

		return(1);
	}

	/**
	 * Short description for 'getLicenses'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $database Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public static function getLicenses($database)
	{
		$database->setQuery("SELECT text, name, title FROM `#__tool_licenses` ORDER BY ordering ASC");
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
	public static function transform($array, $label, $newarray=array())
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
	public static function getLogins($uids, $logins = array())
	{
		if (is_array($uids))
		{
			foreach ($uids as $uid)
			{
				$user = User::getInstance($uid);
				if ($user)
				{
					$logins[] = $user->get('username');
				}
			}
		}
		return $logins;
	}

	/**
	 * Return a path to resource
	 *
	 * @param	$createdDate	Resource creation date
	 * @param	$resourceId		Resource ID
	 * @param	$versionId		Resource Version ID
	 *
	 * @return     path
	 */
	public static function getResourcePath($createdDate, $resourceId, $versionId)
	{
		//include the resources html helper file
		include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');

		//get resource upload path
		$resourceParams = Component::params('com_resources');
		$path = DS . trim($resourceParams->get("uploadpath"), DS);

		//build path based on resource creation date and id
		$path .= \Components\Resources\Helpers\Html::build_path($createdDate, $resourceId, '');

		//append version id if we have one
		if ($versionId)
		{
			$path .= DS . $versionId;
		}

		return $path;
	}

	/**
	 * Return tool access
	 *
	 * @param	$tool	Tool name we are getting access rights to
	 * @param	$login	User Login name
	 *
	 * @return     BOOL
	 */
	public static function getToolAccess($tool, $login = '')
	{
		//include tool models
		include_once(dirname(__DIR__) . DS . 'tables' . DS . 'tool.php');
		include_once(dirname(__DIR__) . DS . 'tables' . DS . 'group.php');
		include_once(dirname(__DIR__) . DS . 'tables' . DS . 'version.php');

		//instantiate objects
		$access = new stdClass();
		$access->error = new stdClass();
		$database = \App::get('db');

		// Ensure we have a tool
		if (!$tool)
		{
			$access->valid = 0;
			$access->error->message = 'No tool provided.';
			\Log::debug("mw::_getToolAccess($tool,$login) FAILED null tool check");
			return $access;
		}

		// Ensure we have a login
		if ($login == '')
		{
			$login = User::get('username');
			if ($login == '')
			{
				$access->valid = 0;
				$access->error->message = 'Unable to grant tool access to user, no user was found.';
				\Log::debug("mw::_getToolAccess($tool,$login) FAILED null user check");
				return $access;
			}
		}

		//load tool version
		$toolVersion = new \Components\Tools\Tables\Version($database);
		$toolVersion->loadFromInstance($tool);
		if (empty($toolVersion))
		{
			$access->valid = 0;
			$access->error->message = 'Unable to load the tool';
			$xlog->debug("mw::_getToolAccess($tool,$login) FAILED null tool version check");
			return $access;
		}

		//load the tool groups
		$toolGroup = new \Components\Tools\Tables\Group($database);
		$query = "SELECT * FROM " . $toolGroup->getTableName() . " WHERE toolid=" . $toolVersion->toolid;
		$database->setQuery($query);
		$toolgroups = $database->loadObjectList();

		//get users groups
		$xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'members');

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
		$ctconfig = Component::params('com_tools');
		if ($ctconfig->get('admingroup') != '' && in_array($ctconfig->get('admingroup'), $groups))
		{
			$admin = true;
		}

		//get access settings
		$exportAllowed = \Components\Tools\Helpers\Utils::getToolExportAccess($toolVersion->exportControl);
		$isToolPublished = ($toolVersion->state == 1);
		$isToolDev = ($toolVersion->state == 3);
		$isGroupControlled = ($toolVersion->toolaccess == '@GROUP');

		//check for dev tools
		if ($isToolDev)
		{
			//if were not in the dev group or an admin we must deny
			if (!$indevgroup && !$admin)
			{
				$access->valid = 0;
				$access->error->message = 'The development version of this tool may only be accessed by members of it\'s development group.';
				\Log::debug("mw::_getToolAccess($tool,$login): DEV TOOL ACCESS DENIED (USER NOT IN DEVELOPMENT OR ADMIN GROUPS)");
			}
			else
			{
				$access->valid = 1;
			}
		}
		//check for published tools
		else if ($isToolPublished)
		{
			//are we checking for a group controlled tool
			if ($isGroupControlled)
			{
				//if were not in the group that controls it and not admin we must deny
				if (!$ingroup && !$admin)
				{
					$access->valid = 0;
					$access->error->message = 'This tool may only be accessed by members of it\'s access control groups.';
					\Log::debug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS DENIED (USER NOT IN ACCESS OR ADMIN GROUPS)");
				}
				else
				{
					$access->valid = 1;
				}
			}
			else
			{
				if (!$exportAllowed->valid)
				{
					$access->valid = 0;
					$access->error->message = 'Export Access Denied';
					\Log::debug("mw::_getToolAccess($tool,$login): PUBLISHED TOOL ACCESS DENIED (EXPORT DENIED)");
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
			\Log::debug("mw::_getToolAccess($tool,$login): UNPUBLISHED TOOL ACCESS DENIED (TOOL NOT PUBLISHED)");
		}

		//return access
		return $access;
	}


	/**
	 * Return a tool export access
	 *
	 * @param	$export_control 	Export control level for tool
	 *
	 * @return     BOOL
	 */
	public static function getToolExportAccess($export_control)
	{
		//instaniate objects
		$export_access = new stdClass;

		$ip = Request::ip();

		//get the export control level
		$export_control = strtolower($export_control);

		//get the users country based on ip address
		$country = \Hubzero\Geocode\Geocode::ipcountry($ip);

		//if we dont know the users location and its a restricted to we have to deny access
		if (empty($country) && in_array($export_control, array('us', 'd1', 'pu')))
		{
			$export_access->valid = 0;
			$export_access->error->message = 'This tool may not be accessed from your unknown current location due to export/license restrictions.';
			\Log::debug("mw::_getToolExportControl($export_control) FAILED location export control check");
			return $export_access;
		}

		//if the user is in an E1 nation
		if (\Hubzero\Geocode\Geocode::is_e1nation(\Hubzero\Geocode\Geocode::ipcountry($ip)))
		{
			$export_access->valid = 0;
			$export_access->error->message = 'This tool may not be accessed from your current location due to E1 export/license restrictions.';
			\Log::debug("mw::_getToolExportControl($export_control) FAILED E1 export control check");
			return $export_access;
		}

		//run checks depending on the export ac
		switch ($export_control)
		{
			case 'us':
				if (\Hubzero\Geocode\Geocode::ipcountry($ip) != 'us')
				{
					$export_access->valid = 0;
					$export_access->error->message = 'This tool may only be accessed from within the U.S. due to export/licensing restrictions.';
					\Log::debug("mw::_getToolExportControl($export_control) FAILED US export control check");
					return $export_access;
				}
			break;

			case 'd1':
				if (\Hubzero\Geocode\Geocode::is_d1nation(\Hubzero\Geocode\Geocode::ipcountry($ip)))
				{
					$export_access->valid = 0;
					$export_access->error->message = 'This tool may not be accessed from your current location due to export/license restrictions.';
					\Log::debug("mw::_getToolExportControl($export_control) FAILED D1 export control check");
					return $export_access;
				}
			break;

			case 'pu':
				if (!\Hubzero\Geocode\Geocode::is_iplocation($ip, $export_control))
				{
					$export_access->valid = 0;
					$export_access->error->message = 'This tool may only be accessed by authorized users while on the West Lafayette campus of Purdue University due to license restrictions.';
					\Log::debug("mw::_getToolExportControl($export_control) FAILED PURDUE export control check");
					return $export_access;
				}
			break;
		}

		//passed all checks
		$export_access->valid = 1;
		return $export_access;
	}

	/**
	 * Record Tool Usage
	 *
	 * @param		$tool		Alias of tool
	 * @param		$userid		User ID
	 *
	 * @return 		BOOL
	 */
	public static function recordToolUsage($tool, $userid = '')
	{
		//include needed files
		include_once(dirname(__DIR__) . DS . 'tables' . DS . 'version.php');
		include_once(dirname(__DIR__) . DS . 'tables' . DS . 'recent.php');

		//instantiate needed objects
		$database = \App::get('db');

		//load tool version
		$toolVersion = new \Components\Tools\Tables\Version($database);
		$toolVersion->loadFromName($tool);

		//make sure we have a user id
		if (!$userid)
		{
			$userid = User::get('id');
		}

		//get recent tools
		$recentTool = new \Components\Tools\Tables\Recent($database);
		$rows = $recentTool->getRecords($userid);

		//check to see if any recently used tools are this one
		$thisapp = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			if ($tool == trim($rows[$i]->tool))
			{
				$thisapp = $rows[$i]->id;
			}
		}

		// Get the oldest entry. We may need this later.
		$oldest = end($rows);

		//createed date
		$created = Date::toSql();

		// Check if any recent tools are the same as the one just launched
		if ($thisapp)
		{
			// There was one, so just update its creation time
			$recentTool->id      = $thisapp;
			$recentTool->uid     = $userid;
			$recentTool->tool    = $tool;
			$recentTool->created = $created;
		}
		else
		{
			// Check if we've reached 5 recent tools or not
			if (count($rows) < 5)
			{
				// Still under 5, so insert a new record
				$recentTool->uid     = $userid;
				$recentTool->tool    = $tool;
				$recentTool->created = $created;
			}
			else
			{
				// We reached the limit, so update the oldest entry effectively replacing it
				$recentTool->id      = $oldest->id;
				$recentTool->uid     = $userid;
				$recentTool->tool    = $tool;
				$recentTool->created = $created;
			}
		}

		//store usage
		if (!$recentTool->store())
		{
			return false;
		}

		return true;
	}

	/**
	 * Run Middleware Scripts
	 *
	 * @param	$comm		Command to run on middleware
	 * @param	$output		Output to be returned from middleware
	 *
	 * @return     mixed
	 */
	public static function middleware($comm, &$output)
	{
		$retval = true; // Assume success.

		$comm = escapeshellcmd($comm);

		$cmd = "/bin/sh ". dirname(__DIR__) . "/scripts/mw $comm 2>&1 </dev/null";

		exec($cmd, $results, $status);

		// Check exec status
		if ($status != 0)
		{
			// Uh-oh. Something went wrong...
			$retval = false;
			$this->setError($results[0]);
		}

		if (is_array($results))
		{
			$results = implode('', $results);
		}
		$results = trim($results);

		try
		{
			$output = @json_decode($results);

			if ($output === null && json_last_error() !== JSON_ERROR_NONE)
			{
				throw new \Exception(Lang::txt('COM_TOOLS_ERROR_BAD_DATA'));
			}
		}
		catch (\Exception $e)
		{
			$output = new stdClass();

			// If it's a new session, catch the session number...
			if ($retval && preg_match("/^Session is ([0-9]+)/", $results, $sess))
			{
				$retval = $sess[1];
				$output->session = $sess[1];
			}
			else
			{
				$patterns = array(
					'id' => 'applet id=(["\'])(?:(?=(\\?))\2.)*?\1',
					'code' => 'code=(["\'])(?:(?=(\\?))\2.)*?\1',
					'archive' => 'archive=(["\'])(?:(?=(\\?))\2.)*?\1',
					'class' => 'class=(["\'])(?:(?=(\\?))\2.)*?\1',
					'height' => 'height=\"(\d+)\"',
					'width' => 'width=\"(\d+)\"',
					'height' => 'height=\"(\d+)\"',
					'port' => '<param name=\"PORT\" value=\"?(\d+)\"?>',
					'host' => '<param name=\"HOST\" value=\"?([^>]+)\"?>',
					'encpassword' => '<param name=\"ENCPASSWORD\" value=\"?([^>]+)\"?>',
					'name' => '<param name=\"name\" value=\"?([^>]+)\"?>',
					'connect' => '<param name=\"CONNECT\" value=\"?([^>]+)\"?>',
					'encoding' => '<param name=\"ENCODING\" value=\"?([^>]+)\"?>',
					'show_local_cursor' => '<param name=\"ShowLocalCursor\" value=\"?([^>]+)\"?>',
					'trust_all_vnc_certs' => '<param name=\"trustAllVncCerts\" value=\"?([^>]+)\"?>',
					'offer_relogin' => '<param name=\"Offer relogin\" value=\"?([^>]+)\"?>',
					'disable_ssl' => '<param name=\"DisableSSL\" value=\"?([^>]+)\"?>',
					'permissions' => '<param name=\"permissions\" value=\"?([^>]+)\"?>',
					'view_only' => '<param name=\"View Only\" value=\"?([^>]+)\"?>',
					'show_controls' => '<param name=\"Show Controls\" value=\"?([^>]+)\"?>',
					'debug' => '<param name=\"Debug\" value=\"?([^>]+)\"?>'
				);
				foreach ($patterns as $key => $pattern)
				{
					if (preg_match("/$pattern/i", $results, $param))
					{
						$output->$key = trim($param[1], '"');
					}
				}
			}
		}

		if ($output == null || (is_object($output) && count(get_object_vars($output)) <= 0))
		{
			$retval = false;
		}

		return $retval;
	}
}
