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
// XLog Logging Class
//
// A work in progress. 
//

define('XLOG_EMERG', 1);
define('XLOG_ALERT', 2);
define('XLOG_CRIT',  4);
define('XLOG_ERR',   8);
define('XLOG_WARNING', 16);
define('XLOG_NOTICE', 32);
define('XLOG_INFO', 64);
define('XLOG_DEBUG', 128);
define('XLOG_AUTH', 256);

class XLogFileHandler
{
	var $_filename = null;
	var $_fp = null;

	function XLogFileHandler($filename) 
	{
		$this->_filename = $filename;
		$this->_fp = null;
	}

	function log($priority, $message, $trace = false)
	{
		if (empty($this->_fp)) {
			if (is_null($this->_fp))
				$this->_fp = fopen($this->_filename, "ab");
		
			if (empty($this->_fp)) {
				return;
			}
		}

		flock($this->_fp, LOCK_EX);
		fwrite($this->_fp, date('Y-m-d H:i:s ') );
		fwrite($this->_fp, $message);

		if ($trace)
			fwrite($this->_fp, XLog::getSimpleTrace() );

		fwrite($this->_fp, "\n");
		fflush($this->_fp);
		flock($this->_fp, LOCK_UN);

		return;
	}

	function close()
	{
		if (!empty($this->_fp)) {
			fclose($this->_fp);
			$this->_fp = null;
		}
	}
}

class XLog
{
	var $_handler = array();

	function getSimpleTrace() 
        {
                $backtrace = debug_backtrace();

                foreach($backtrace as $file)
		{
			$filename = (!empty($file['file'])) ? basename( $file['file'] ) : 'unknown';
			$line     = (!empty($file['line'])) ? $file['line'] : 'unknown';

			if ($filename == 'xlog.php') // supress the trace through the xlog class
				continue; 

                        $files[] = "($filename:$line)";
		}

                return " [" . implode(',', $files) . "]";
        }

	function XLog() {
		$this->_handler = array();
	}

	function detach($priority, $handler) {
		if (!is_array($this->_handler[$priority]) )
			return false;

		$index = array_search( $handler, $this->_hander[$priority] );

		if ($index !== false) {
			unset( $this->_handler[$priority][$index] );
			return true;
		}

		return false;
	}

	function attach($priority, $handler) {
		$this->_handler[$priority][] = $handler;
		return;
	}
	
	function log($priority, $message, $trace = false) {
		foreach ($this->_handler[$priority] as $handler)
			$handler->log($priority, $message, $trace);
	}

	function logEmergency($message, $trace = false) {
		XLog::log(XLOG_EMERG, $message, $trace);
	}

	function logAlert($message, $trace = false) {
		XLog::log(XLOG_ALERT, $message, $trace);
	}

	function logCrit($message, $trace = false) {
		XLog::log(XLOG_CRIT, $message, $trace);
	}

	function logError($message, $trace = false) {
		XLog::log(XLOG_ERR, $message, $trace);
	}

	function logWarning($message, $trace = false) {
		XLog::log(XLOG_WARNING, $message, $trace);
	}

	function logNotice($messsage, $trace = false) {
		XLog::log(XLOG_NOTICE, $message, $trace);
	}

	function logInfo($message, $trace = false) {
		XLog::log(XLOG_INFO, $message, $trace);
	}

	function logDebug($message, $trace = false) {
		XLog::log(XLOG_DEBUG, $message, $trace);
	}

	function logAuth($message, $trace = false) {
		XLog::log(XLOG_AUTH, $message, $trace);
	}
}

?>
