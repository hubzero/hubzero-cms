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

namespace Components\Publications\Models;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'logs.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'html.php');

use Hubzero\Base\Model;
use Components\Publications\Helpers\Html;
use Components\Publications\Tables;

/**
 * Publications log model class
 */
class Log extends Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Publications\\Tables\\Log';

	/**
	 * Registry
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
	 * @param      string  $logFile
	 * @param      string  $type     primary or view
	 * @return     void
	 */
	public function parseLog($pubLog = NULL, $logFile = NULL, $type = 'view', $category = 'unique')
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
				if ($pubLog->checkBotIp($ip))
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
	public function digestLogs($pid = NULL, $type = 'view', $numMonths = 1, $includeCurrent = false)
	{
		if (!$pid)
		{
			return false;
		}

		$stats = array('unique' => array(), 'filtered' => array());
		$types = ($type == 'all') ? array('view', 'primary') : array($type);

		// Get all public versions
		$row  = new Tables\Version($this->_db);
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
				$date     = date('Y-m', strtotime("-" . $a . " month"));
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
			$this->_config = Component::params('com_publications');
		}
		if (!$pid || !$vid)
		{
			return false;
		}

		// Build log path (access logs)
		$logPath = Html::buildPubPath($pid, $vid, $this->_config->get('webpath'), 'logs', 1);

		if (!is_dir($logPath))
		{
			return false;
		}
		return $logPath;
	}
}

