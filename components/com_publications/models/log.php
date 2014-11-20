<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'logs.php');

/**
 * Publications log model class
 */
class PublicationsModelLog extends \Hubzero\Base\Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'PublicationLog';

	/**
	 * JRegistry
	 *
	 * @var object
	 */
	private $_config = null;

	/**
	 * Table class object
	 *
	 * @var object
	 */
	public $pubLog = null;

	/**
	 * Parse log file
	 *
	 * @param      string 	$logFile
	 * @param      string 	$type		primary or view
	 * @return     void
	 */
	public function parseLog ( $pubLog = NULL, $logFile = NULL, $type = 'view', $category = 'unique' )
	{
		$ips = array();
		$filtered = array();
		$unique = 0;
		$all = 0;

		if (!is_file($logFile))
		{
			return NULL;
		}
		if (!$pubLog)
		{
			$pubLog = new $this->_tbl_name($this->_db);
		}

		$file_handle = fopen($logFile, "r");
		while (!feof($file_handle))
		{
			$line = fgets($file_handle);
			$parts = explode("\t", $line);

			if ((count($parts) == 4) && trim($parts[3]) == $type)
			{
				$ip = trim($parts[1]);

				// Check if bot
				if ($pubLog->checkBotIp( $ip ))
				{
					// Skip bots
					continue;
				}

				if (!in_array($ip, $ips))
				{
					// Add to list of unique IPs
					$ips[] = trim($parts[1]);
					$unique++;
				}
				// Add to non-unique list
				$all++;
			}

		}
		fclose($file_handle);
		return $category == 'filtered' ? $all : $unique;
	}

	/**
	 * Get user data from log file
	 *
	 * @return     void
	 */
	public function digestLogs ( $pid = NULL, $type = 'view', $numMonths = 1, $includeCurrent = false )
	{
		if (!$pid)
		{
			return false;
		}

		$stats = array('unique' => array(), 'filtered' => array());
		$types = ($type == 'all') ? array('view', 'primary') : array($type);

		// Get all public versions
		$row  = new PublicationVersion( $this->_db );
		$versions = $row->getVersions($pid, $filters = array('public' => 1));

		if (!$versions)
		{
			return $stats;
		}

		// Collect data for each version
		foreach ($versions as $version)
		{
			$logPath = $this->getLogPath($pid, $version->id);
			if (!$logPath)
			{
				continue;
			}

			$n = ($includeCurrent) ? 0 : 1;

			for ($a = $numMonths; $a >= $n; $a--)
			{
				$yearNum  = intval(date('y', strtotime("-" . $a . " month")));
				$monthNum = intval(date('m', strtotime("-" . $a . " month")));
				$date 	  = date('Y-m', strtotime("-" . $a . " month"));
				$logFile  = 'pub-' . $pid . '-v-' . $version->id . '.' . $date . '.log';

				$fpath = $logPath . DS . $logFile;
				$mo = date('M', strtotime("-" . $a . " month"));

				$pubLog = new $this->_tbl_name($this->_db);

				foreach ($types as $type)
				{
					if (!trim($type))
					{
						continue;
					}
					if (!isset($stats['unique'][$type]))
					{
						$stats['unique'][$type] = array();
					}
					if (!isset($stats['filtered'][$type]))
					{
						$stats['filtered'][$type] = array();
					}
					if (!isset($stats['unique'][$type][$mo]))
					{
						$stats['unique'][$type][$mo] = 0;
					}
					if (!isset($stats['filtered'][$type][$mo]))
					{
						$stats['filtered'][$type][$mo] = 0;
					}
					if (is_file($fpath))
					{
						// Get count of unique views/accesses
						$count = $this->parseLog($pubLog, $fpath, $type);
						$stats['unique'][$type][$mo] = $stats['unique'][$type][$mo] + $count;

						// Log unique
						$pubLog->logParsed(
							$pid,
							$version->id,
							date('y', strtotime($date)),
							date('m', strtotime($date)),
							$count,
							$type,
							'unique'
						);

						// Get filtered count
						$count = $this->parseLog($pubLog, $fpath, $type, 'filtered');
						$stats['filtered'][$type][$mo] = $stats['filtered'][$type][$mo] + $count;

						// Log filtered
						$pubLog->logParsed(
							$pid,
							$version->id,
							date('y', strtotime($date)),
							date('m', strtotime($date)),
							$count,
							$type,
							'filtered'
						);
					}
				}
			}
		}

		return $stats;
	}

	/**
	 * Get path to log file
	 *
	 * @return     void
	 */
	public function getLogPath($pid = 0, $vid = 0)
	{
		if (!isset($this->_config))
		{
			$this->_config = JComponentHelper::getParams('com_publications');
		}
		if (!$pid || !$vid)
		{
			return false;
		}

		// Build log path (access logs)
		$logPath = PublicationHelper::buildPath($pid, $vid, $this->_config->get('webpath'), 'logs', 1);

		if (!is_dir($logPath))
		{
			return false;
		}
		return $logPath;
	}
}

