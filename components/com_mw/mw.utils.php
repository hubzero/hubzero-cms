<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

//-------------------------------------------------------------
// Contains functions used by multiple Session/Tool modules
//-------------------------------------------------------------

class MwUtils
{
	// Get a list of existing application sessions.
	function getMWDBO()
	{
		static $instance;

		if (!is_object($instance)) {
			//include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_mw'.DS.'mw.config.php' );

			//$config = new MwConfig( 'com_mw' );
			$config =& JComponentHelper::getParams( 'com_mw' );
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

			if ( defined('_JEXEC') )
				$instance = &JDatabase::getInstance($options);
			else
				$instance = new database($options['host'], $options['user'], $options['password'], $options['database'], $options['prefix']);
		}

		if (JError::isError($instance))
			return null;

		return $instance;
	}

	//-----------
	
	function getDiskUsage($username)
	{
		$info = array();

		//$config = new MwConfig( 'com_mw' );
		$config =& JComponentHelper::getParams( 'com_mw' );
		$host = $config->get('storagehost');

		if ($username && $host) {
			$fp = stream_socket_client($host, $errno, $errstr, 30);
			if (!$fp) {
				$info[] = "$errstr ($errno)\n";
			} else {
				$msg = '';
				fwrite($fp, "getquota user=".$username."\n");
				while(!feof($fp)) {
					$msg .= fgets($fp, 1024);
				}
				fclose($fp);
				$tokens = split(',',$msg);
				foreach($tokens as $token)
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