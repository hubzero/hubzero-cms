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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 2.0.0
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
	 * @param   string  $query  The query to log
	 * @param   int     $time   The query time
	 * @return  void
	 * @since   2.0.0
	 **/
	public static function add($query, $time = 0)
	{
		list($file, $line) = self::parseBacktrace();
		list($type)        = explode(' ', $query, 2);

		self::write("$file $line " . strtoupper($type) . " {$time} " . str_replace("\n", ' ', $query));
	}

	/**
	 * Writes the log statment out
	 *
	 * @return  void
	 * @since   2.0.0
	 **/
	public static function write($statement)
	{
		$logger = new \Hubzero\Log\Writer(
			new \Monolog\Logger(\Config::get('application_env')),
			\Event::getRoot()
		);

		$path = (is_dir(self::HZPATH)) ? self::HZPATH : \Config::get('log_path');

		$logger->useFiles($path . DS . self::FILENAME, 'info', "%datetime% %message%\n", "Y-m-d\TH:i:s.uP", 0640);
		$logger->info($statement);
	}

	/**
	 * Parses the debug backtrace for the applicable file and line
	 *
	 * @return  array
	 * @since   2.0.0
	 **/
	public static function parseBacktrace()
	{
		$file = '-';
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

				$file = (isset($item['file'])) ? str_replace(PATH_CORE, '', $item['file']) : $file;
				$line = (isset($item['line'])) ? $item['line'] : $line;
			}
		}

		return array($file, $line);
	}

	/**
	 * Gets the debug backtrace from php
	 *
	 * @return  array
	 * @since   2.0.0
	 **/
	public static function getBacktrace()
	{
		return debug_backtrace();
	}
}