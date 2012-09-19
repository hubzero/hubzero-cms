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
 * Log class for opening/reading/writing log files
 */
class Hubzero_Log_FileHandler
{
	/**
	 * Log file name
	 * 
	 * @var string
	 */
	var $_filename = null;

	/**
	 * File connection
	 * 
	 * @var unknown
	 */
	var $_fp = null;

	/**
	 * Constructor
	 * 
	 * @param      string $filename NAme of log file
	 * @return     void
	 */
	public function __construct($filename)
	{
		$this->_filename = $filename;
		$this->_fp = null;
	}

	/**
	 * Write a message to the log file
	 * 
	 * @param      integer $priority Message priority level
	 * @param      string  $message  Message to log
	 * @param      boolean $trace    Include a stack-trace?
	 * @return     void
	 */
	public function log($priority, $message, $trace = false)
	{
		if (empty($this->_fp)) 
		{
			if (is_null($this->_fp))
			{
				$this->_fp = @fopen($this->_filename, "ab");
			}

			if (empty($this->_fp)) 
			{
				return;
			}
		}

		flock($this->_fp, LOCK_EX);
		fwrite($this->_fp, date('Y-m-d H:i:s '));
		fwrite($this->_fp, $message);

		if ($trace) 
		{
			fwrite($this->_fp, Hubzero_Log::getSimpleTrace());
		}

		fwrite($this->_fp, "\n");
		fflush($this->_fp);
		flock($this->_fp, LOCK_UN);

		return;
	}

	/**
	 * Close file connection
	 * 
	 * @return     void
	 */
	public function close()
	{
		if (!empty($this->_fp)) 
		{
			fclose($this->_fp);
			$this->_fp = null;
		}
	}
}

