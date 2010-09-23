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

//
// Hubzero_Log Logging Class
//
// A work in progress. 
//

define('HUBZERO_LOG_EMERG', 1);
define('HUBZERO_LOG_ALERT', 2);
define('HUBZERO_LOG_CRIT',  4);
define('HUBZERO_LOG_ERR',   8);
define('HUBZERO_LOG_WARNING', 16);
define('HUBZERO_LOG_NOTICE', 32);
define('HUBZERO_LOG_INFO', 64);
define('HUBZERO_LOG_DEBUG', 128);
define('HUBZERO_LOG_AUTH', 256);

include_once(JPATH_ROOT.DS.'libraries'.DS.'Hubzero'.DS.'Log'.DS.'FileHandler.php');


class Hubzero_Log
{
	var $_handler = array();

	//-----------

	public function getSimpleTrace() 
	{
		$backtrace = debug_backtrace();
		
		foreach ($backtrace as $file)
		{
			$filename = (!empty($file['file'])) ? basename( $file['file'] ) : 'unknown';
			$line     = (!empty($file['line'])) ? $file['line'] : 'unknown';
			
			if ($filename == 'Log.php') {// supress the trace through the xlog class
				continue;
			}
			
			$files[] = "($filename:$line)";
		}
		return " [" . implode(',', $files) . "]";
	}
	
	//-----------

	public function __construct() 
	{
		$this->_handler = array();
	}
	
	//-----------
	
	public function detach($priority, $handler) 
	{
		if (!is_array($this->_handler[$priority]) ) {
			return false;
		}

		$index = array_search( $handler, $this->_hander[$priority] );

		if ($index !== false) {
			unset( $this->_handler[$priority][$index] );
			return true;
		}

		return false;
	}
	
	//-----------

	public function attach($priority, $handler) 
	{
		$this->_handler[$priority][] = $handler;
		return;
	}
	
	//-----------
	
	public public function log($priority, $message, $trace = false) 
	{
		foreach ($this->_handler[$priority] as $handler) 
		{
			$handler->log($priority, $message, $trace);
		}
	}
	
	//-----------

	public function logEmergency($message, $trace = false) 
	{
		Hubzero_Log::log(HUBZERO_LOG_EMERG, $message, $trace);
	}
	
	//-----------

	public function logAlert($message, $trace = false) 
	{
		Hubzero_Log::log(HUBZERO_LOG_ALERT, $message, $trace);
	}
	
	//-----------

	public function logCrit($message, $trace = false) 
	{
		Hubzero_Log::log(HUBZERO_LOG_CRIT, $message, $trace);
	}
	
	//-----------

	public function logError($message, $trace = false) 
	{
		Hubzero_Log::log(HUBZERO_LOG_ERR, $message, $trace);
	}
	
	//-----------

	public function logWarning($message, $trace = false) 
	{
		Hubzero_Log::log(HUBZERO_LOG_WARNING, $message, $trace);
	}
	
	//-----------

	public function logNotice($messsage, $trace = false) 
	{
		Hubzero_Log::log(HUBZERO_LOG_NOTICE, $message, $trace);
	}
	
	//-----------

	public function logInfo($message, $trace = false) 
	{
		Hubzero_Log::log(HUBZERO_LOG_INFO, $message, $trace);
	}

	//-----------

	public function logDebug($message, $trace = false) 
	{
		Hubzero_Log::log(HUBZERO_LOG_DEBUG, $message, $trace);
	}
	
	//-----------

	public function logAuth($message, $trace = false) 
	{
		Hubzero_Log::log(HUBZERO_LOG_AUTH, $message, $trace);
	}
}
