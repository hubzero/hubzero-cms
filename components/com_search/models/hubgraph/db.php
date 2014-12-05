<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('JPATH_BASE') or die();

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
			$cfg = new JConfig;
			self::$dbh = new PDO('mysql:host=localhost;dbname=' . $cfg->db, $cfg->user, $cfg->password, array(
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
		$tablePrefix = \JFactory::getConfig()->get('dbprefix');

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
