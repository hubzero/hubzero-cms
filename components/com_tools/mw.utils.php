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
defined('_JEXEC') or die( 'Restricted access' );

//-------------------------------------------------------------
// Contains functions used by multiple Session/Tool modules
//-------------------------------------------------------------

/**
 * Short description for 'MwUtils'
 * 
 * Long description (if any) ...
 */
class MwUtils
{
	// Get a list of existing application sessions.

	/**
	 * Short description for 'getMWDBO'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
	 */
	public function getMWDBO()
	{
		static $instance;

		if (!is_object($instance)) {
			$config =& JComponentHelper::getParams( 'com_tools' );
			$enabled = $config->get('mw_on');

			if (!$enabled) {
				return null;
			}

			$options['driver']   = $config->get('mwDBDriver');
			$options['host']     = $config->get('mwDBHost');
			$options['port']     = $config->get('mwDBPort');
			$options['user']     = $config->get('mwDBUsername');
			$options['password'] = $config->get('mwDBPassword');
			$options['database'] = $config->get('mwDBDatabase');
			$options['prefix']   = $config->get('mwDBPrefix');

			if ( defined('_JEXEC') ) {
				$instance =& JDatabase::getInstance($options);
			} else {
				$instance = new database($options['host'], $options['user'], $options['password'], $options['database'], $options['prefix']);
			}
		}

		if (JError::isError($instance)) {
			return null;
		}

		return $instance;
	}

	/**
	 * Short description for 'getDiskUsage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $username Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function getDiskUsage($username)
	{
		$info = array();

		$config =& JComponentHelper::getParams( 'com_tools' );
		$host = $config->get('storagehost');

		if ($username && $host) {
			$fp = stream_socket_client($host, $errno, $errstr, 30);
			if (!$fp) {
				$info[] = "$errstr ($errno)\n";
			} else {
				$msg = '';
				fwrite($fp, "getquota user=".$username."\n");
				while (!feof($fp))
				{
					$msg .= fgets($fp, 1024);
				}
				fclose($fp);
				$tokens = split(',',$msg);
				foreach ($tokens as $token)
				{
					if (!empty($token))
					{
						$t = split('=',$token);
						$info[$t[0]] = (isset($t[1])) ? $t[1] : '';
					}
				}
			}
		}
		return $info;
	}
}
?>