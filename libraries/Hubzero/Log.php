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
 * Emergency message
 */
define('HUBZERO_LOG_EMERG', 1);

/**
 * Alert message
 */
define('HUBZERO_LOG_ALERT', 2);

/**
 * Critical message
 */
define('HUBZERO_LOG_CRIT',  4);

/**
 * Error message
 */
define('HUBZERO_LOG_ERR',   8);

/**
 * Waring message
 */
define('HUBZERO_LOG_WARNING', 16);

/**
 * Notice message
 */
define('HUBZERO_LOG_NOTICE', 32);

/**
 * Info message
 */
define('HUBZERO_LOG_INFO', 64);

/**
 * Debug message
 */
define('HUBZERO_LOG_DEBUG', 128);

/**
 * Authorization message
 */
define('HUBZERO_LOG_AUTH', 256);

include_once(JPATH_ROOT . DS . 'libraries' . DS . 'Hubzero' . DS . 'Log' . DS . 'FileHandler.php');

/**
 * Hubzero_Log Logging Class
 * 
 * A work in progress. 
 */
class Hubzero_Log
{
	/**
	 * Container for message types
	 * 
	 * @var array
	 */
	var $_handler = array();

	/**
	 * Get a simple stacktrace
	 * 
	 * @return     string
	 */
	public function getSimpleTrace()
	{
		$backtrace = debug_backtrace();

		foreach ($backtrace as $file)
		{
			$filename = (!empty($file['file'])) ? basename($file['file']) : 'unknown';
			$line     = (!empty($file['line'])) ? $file['line']           : 'unknown';

			if ($filename == 'Log.php') 
			{
				// supress the trace through the xlog class
				continue;
			}

			$files[] = "($filename:$line)";
		}
		return ' [' . implode(',', $files) . ']';
	}

	/**
	 * Constructor
	 * 
	 * @return     void
	 */
	public function __construct()
	{
		$this->_handler = array();
	}

	/**
	 * Unset an array of messages for a specific type
	 * 
	 * @param      integer $priority Message type
	 * @param      array   $handler  Message types container
	 * @return     boolean True on success
	 */
	public function detach($priority, $handler)
	{
		if (!is_array($this->_handler[$priority])) 
		{
			return false;
		}

		$index = array_search($handler, $this->_hander[$priority]);

		if ($index !== false) 
		{
			unset($this->_handler[$priority][$index]);
			return true;
		}

		return false;
	}

	/**
	 * Set an array of messages for a specific type
	 * 
	 * @param      integer $priority Message type
	 * @param      array   $handler  Message types container
	 * @return     void
	 */
	public function attach($priority, $handler)
	{
		$this->_handler[$priority][] = $handler;
		return;
	}

	/**
	 * Log a message
	 * 
	 * @param      integer $priority Priority level
	 * @param      string  $message  Message to log
	 * @param      boolean $trace    Log the stacktrace?
	 * @return     void
	 */
	public function log($priority, $message, $trace = false)
	{
		foreach ($this->_handler[$priority] as $handler)
		{
			$handler->log($priority, $message, $trace);
		}
	}

	/**
	 * Log an emergency message
	 * 
	 * @param      string  $message Message to log
	 * @param      boolean $trace   Log the stacktrace?
	 * @return     void
	 */
	public function logEmergency($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_EMERG, $message, $trace);
	}

	/**
	 * Log an alert message
	 * 
	 * @param      string  $message Message to log
	 * @param      boolean $trace   Log the stacktrace?
	 * @return     void
	 */
	public function logAlert($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_ALERT, $message, $trace);
	}

	/**
	 * Log a critical message
	 * 
	 * @param      string  $message Message to log
	 * @param      boolean $trace   Log the stacktrace?
	 * @return     void
	 */
	public function logCrit($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_CRIT, $message, $trace);
	}

	/**
	 * Log an error message
	 * 
	 * @param      string  $message Message to log
	 * @param      boolean $trace   Log the stacktrace?
	 * @return     void
	 */
	public function logError($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_ERR, $message, $trace);
	}

	/**
	 * Log a warning message
	 * 
	 * @param      string  $message Message to log
	 * @param      boolean $trace   Log the stacktrace?
	 * @return     void
	 */
	public function logWarning($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_WARNING, $message, $trace);
	}

	/**
	 * Log a notice message
	 * 
	 * @param      string  $message Message to log
	 * @param      boolean $trace   Log the stacktrace?
	 * @return     void
	 */
	public function logNotice($messsage, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_NOTICE, $message, $trace);
	}

	/**
	 * Log info
	 * 
	 * @param      string  $message Message to log
	 * @param      boolean $trace   Log the stacktrace?
	 * @return     void
	 */
	public function logInfo($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_INFO, $message, $trace);
	}

	/**
	 * Log a debug message
	 * 
	 * @param      string  $message Message to log
	 * @param      boolean $trace   Log the stacktrace?
	 * @return     void
	 */
	public function logDebug($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_DEBUG, $message, $trace);
	}

	/**
	 * Log an authorization message
	 * 
	 * @param      string  $message Message to log
	 * @param      boolean $trace   Log the stacktrace?
	 * @return     void
	 */
	public function logAuth($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_AUTH, $message, $trace);
	}
}

