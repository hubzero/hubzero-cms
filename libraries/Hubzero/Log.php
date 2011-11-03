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
defined('_JEXEC') or die( 'Restricted access' );

//
// Hubzero_Log Logging Class
//
// A work in progress. 
//

/**
 * Description for ''HUBZERO_LOG_EMERG''
 */
define('HUBZERO_LOG_EMERG', 1);

/**
 * Description for ''HUBZERO_LOG_ALERT''
 */
define('HUBZERO_LOG_ALERT', 2);

/**
 * Description for ''HUBZERO_LOG_CRIT''
 */
define('HUBZERO_LOG_CRIT',  4);

/**
 * Description for ''HUBZERO_LOG_ERR''
 */
define('HUBZERO_LOG_ERR',   8);

/**
 * Description for ''HUBZERO_LOG_WARNING''
 */
define('HUBZERO_LOG_WARNING', 16);

/**
 * Description for ''HUBZERO_LOG_NOTICE''
 */
define('HUBZERO_LOG_NOTICE', 32);

/**
 * Description for ''HUBZERO_LOG_INFO''
 */
define('HUBZERO_LOG_INFO', 64);

/**
 * Description for ''HUBZERO_LOG_DEBUG''
 */
define('HUBZERO_LOG_DEBUG', 128);

/**
 * Description for ''HUBZERO_LOG_AUTH''
 */
define('HUBZERO_LOG_AUTH', 256);

include_once(JPATH_ROOT.DS.'libraries'.DS.'Hubzero'.DS.'Log'.DS.'FileHandler.php');

/**
 * Short description for 'Hubzero_Log'
 * 
 * Long description (if any) ...
 */
class Hubzero_Log
{

	/**
	 * Description for '_handler'
	 * 
	 * @var array
	 */
	var $_handler = array();

	/**
	 * Short description for 'getSimpleTrace'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
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

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function __construct()
	{
		$this->_handler = array();
	}

	/**
	 * Short description for 'detach'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $priority Parameter description (if any) ...
	 * @param      unknown $handler Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
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

	/**
	 * Short description for 'attach'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $priority Parameter description (if any) ...
	 * @param      unknown $handler Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function attach($priority, $handler)
	{
		$this->_handler[$priority][] = $handler;
		return;
	}

	/**
	 * Short description for 'log'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $priority Parameter description (if any) ...
	 * @param      unknown $message Parameter description (if any) ...
	 * @param      boolean $trace Parameter description (if any) ...
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
	 * Short description for 'logEmergency'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $message Parameter description (if any) ...
	 * @param      boolean $trace Parameter description (if any) ...
	 * @return     void
	 */
	public function logEmergency($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_EMERG, $message, $trace);
	}

	/**
	 * Short description for 'logAlert'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $message Parameter description (if any) ...
	 * @param      boolean $trace Parameter description (if any) ...
	 * @return     void
	 */
	public function logAlert($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_ALERT, $message, $trace);
	}

	/**
	 * Short description for 'logCrit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $message Parameter description (if any) ...
	 * @param      boolean $trace Parameter description (if any) ...
	 * @return     void
	 */
	public function logCrit($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_CRIT, $message, $trace);
	}

	/**
	 * Short description for 'logError'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $message Parameter description (if any) ...
	 * @param      boolean $trace Parameter description (if any) ...
	 * @return     void
	 */
	public function logError($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_ERR, $message, $trace);
	}

	/**
	 * Short description for 'logWarning'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $message Parameter description (if any) ...
	 * @param      boolean $trace Parameter description (if any) ...
	 * @return     void
	 */
	public function logWarning($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_WARNING, $message, $trace);
	}

	/**
	 * Short description for 'logNotice'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $messsage Parameter description (if any) ...
	 * @param      boolean $trace Parameter description (if any) ...
	 * @return     void
	 */
	public function logNotice($messsage, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_NOTICE, $message, $trace);
	}

	/**
	 * Short description for 'logInfo'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $message Parameter description (if any) ...
	 * @param      boolean $trace Parameter description (if any) ...
	 * @return     void
	 */
	public function logInfo($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_INFO, $message, $trace);
	}

	/**
	 * Short description for 'logDebug'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $message Parameter description (if any) ...
	 * @param      boolean $trace Parameter description (if any) ...
	 * @return     void
	 */
	public function logDebug($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_DEBUG, $message, $trace);
	}

	/**
	 * Short description for 'logAuth'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $message Parameter description (if any) ...
	 * @param      boolean $trace Parameter description (if any) ...
	 * @return     void
	 */
	public function logAuth($message, $trace = false)
	{
		Hubzero_Log::log(HUBZERO_LOG_AUTH, $message, $trace);
	}
}

