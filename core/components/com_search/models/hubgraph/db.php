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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Hubgraph;

use PDO;

/**
 * Database connection class
 */
class Db
{
	private static $dbh, $log, $timer;

	/**
	 * Get the database handle
	 *
	 * @return  object
	 */
	public static function getHandle()
	{
		if (!self::$dbh)
		{
			self::$dbh = new PDO('mysql:host=localhost;dbname=' . \Config::get('db'), \Config::get('user'), \Config::get('password'), array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			));
		}
		return self::$dbh;
	}

	/**
	 * Get the statement handle
	 *
	 * @param   string   $sql      The SQL statement to prepare.
	 * @param   array    $params
	 * @param   boolean  $success
	 * @return  object
	 */
	public static function getStatementHandle($sql, $params = array(), &$success = NULL)
	{
		$sql = self::replacePrefix($sql);
		$sth = self::getHandle()->prepare($sql);
		$success = $sth->execute($params);
		return $sth;
	}

	/**
	 * Execute a query
	 *
	 * @param   string  $sql     The SQL statement to prepare.
	 * @param   array   $params
	 * @return  object
	 */
	public static function query($sql, $params = array())
	{
		return self::getStatementHandle($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Execute a scalar query
	 *
	 * @param   string  $sql     The SQL statement to prepare.
	 * @param   array   $params
	 * @return  mixed
	 */
	public static function scalarQuery($sql, $params = array())
	{
		$rv = self::getStatementHandle($sql, $params)->fetchAll(PDO::FETCH_NUM);
		return $rv && array_key_exists(0, $rv[0]) ? $rv[0][0] : NULL;
	}

	/**
	 * Execute an insert
	 *
	 * @param   string  $sql     The SQL statement to prepare.
	 * @param   array   $params
	 * @return  mixed
	 */
	public static function insert($sql, $params = array())
	{
		self::getStatementHandle($sql, $params, $success);
		return $success ? self::$dbh->lastInsertId() : NULL;
	}

	/**
	 * Execute an update
	 *
	 * @param   string  $sql     The SQL statement to prepare.
	 * @param   array   $params
	 * @return  mixed
	 */
	public static function update($sql, $params = array())
	{
		$sth = self::getStatementHandle($sql, $params, $success);
		return $success ? $sth->rowCount() : FALSE;
	}

	/**
	 * Execute a statement
	 *
	 * @param   string  $sql     The SQL statement to prepare.
	 * @param   array   $params
	 * @return  mixed
	 */
	public static function execute($sql, $params = array())
	{
		return self::update($sql, $params);
	}

	/**
	 * Start a transaction
	 *
	 * @return  void
	 */
	public static function startTransaction()
	{
		self::getHandle()->query('START TRANSACTION');
	}

	/**
	 * Commit a transaction
	 *
	 * @return  void
	 */
	public static function commit()
	{
		self::getHandle()->query('COMMIT');
	}

	/**
	 * Rollback a transaction
	 *
	 * @return  void
	 */
	public static function rollback()
	{
		self::getHandle()->query('ROLLBACK');
	}

	/**
	 * This function replaces a string identifier <var>$prefix</var> with the string held is the
	 * <var>tablePrefix</var> class variable.
	 *
	 * @param   string  $sql     The SQL statement to prepare.
	 * @param   string  $prefix  The common table prefix.
	 * @return  string  The processed SQL statement.
	 */
	public static function replacePrefix($sql, $prefix = '#__')
	{
		// Initialize variables.
		$escaped   = false;
		$startPos  = 0;
		$quoteChar = '';
		$literal   = '';
		$tablePrefix = \Config::get('dbprefix');

		$sql = trim($sql);
		$n = strlen($sql);

		while ($startPos < $n)
		{
			$ip = strpos($sql, $prefix, $startPos);
			if ($ip === false)
			{
				break;
			}

			$j = strpos($sql, "'", $startPos);
			$k = strpos($sql, '"', $startPos);
			if (($k !== false) && (($k < $j) || ($j === false)))
			{
				$quoteChar = '"';
				$j = $k;
			}
			else
			{
				$quoteChar = "'";
			}

			if ($j === false)
			{
				$j = $n;
			}

			$literal .= str_replace($prefix, $tablePrefix, substr($sql, $startPos, $j - $startPos));
			$startPos = $j;

			$j = $startPos + 1;

			if ($j >= $n)
			{
				break;
			}

			// Quote comes first, find end of quote
			while (true)
			{
				$k = strpos($sql, $quoteChar, $j);
				$escaped = false;
				if ($k === false)
				{
					break;
				}
				$l = $k - 1;
				while ($l >= 0 && $sql{$l} == '\\')
				{
					$l--;
					$escaped = !$escaped;
				}
				if ($escaped)
				{
					$j = $k + 1;
					continue;
				}
				break;
			}
			if ($k === false)
			{
				// Error in the query - no end quote; ignore it
				break;
			}
			$literal .= substr($sql, $startPos, $k - $startPos + 1);
			$startPos = $k + 1;
		}
		if ($startPos < $n)
		{
			$literal .= substr($sql, $startPos, $n - $startPos);
		}

		return $literal;
	}
}
