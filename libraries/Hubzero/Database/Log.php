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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 * @since     Class available since release 1.3.2
 */

namespace Hubzero\Database;

/**
 * Database log class
 */
class Log
{
	const HZPATH   = '/var/log/hubzero-cms';
	const FILENAME = 'sql.log';

	/**
	 * Logs queries to hubzero sql log
	 *
	 * @param  string $query the query to log
	 * @param  int    $time  the query time
	 * @return void
	 * @since  1.3.2
	 **/
	public static function add($query, $time=0)
	{
		list($file, $line) = self::parseBacktrace();
		list($type)        = explode(' ', $query, 2);

		self::write("$file $line " . strtoupper($type) . " {$time} " . str_replace("\n", ' ', $query));
	}

	/**
	 * Writes the log statment out
	 *
	 * @return void
	 * @since  1.3.2
	 **/
	public static function write($statement)
	{
		$logger = new \Hubzero\Log\Writer(
			new \Monolog\Logger(\JFactory::getConfig()->getValue('config.application_env')), 
			\JDispatcher::getInstance()
		);

		$path = (is_dir(self::HZPATH)) ? self::HZPATH : \JFactory::getConfig()->getValue('config.log_path');

		$logger->useFiles($path . DS . self::FILENAME, 'info', "%datetime% %message%\n", "Y-m-d\TH:i:s.uP", 0640);
		$logger->info($statement);
	}

	/**
	 * Parses the debug backtrace for the applicable file and line
	 *
	 * @return array
	 * @since  1.3.2
	 **/
	public static function parseBacktrace()
	{
		$file = '';
		$line = 0;

		// Loop through the backtrace items
		foreach (self::getBacktrace() as $item)
		{
			// Looking for the last instance of one of the following classes...
			// this will be our indicator of the command that originated this query
			if (isset($item['class'])
			 && ($item['class'] == 'Hubzero\Database\Relational'
			 ||  $item['class'] == 'Hubzero\Database\Relationship\Relationship'
			 ||  $item['class'] == 'Hubzero\Database\Rows'))
			{

				$file = (isset($item['file'])) ? str_replace(JPATH_ROOT, '', $item['file']) : $file;
				$line = (isset($item['line'])) ? $item['line'] : $line;
			}
		}

		return array($file, $line);
	}

	/**
	 * Gets the debug backtrace from php
	 *
	 * @return array
	 * @since  1.3.2
	 **/
	public static function getBacktrace()
	{
		return debug_backtrace();
	}
}