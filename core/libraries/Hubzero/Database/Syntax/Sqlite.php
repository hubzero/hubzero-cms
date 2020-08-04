<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Syntax;

use Hubzero\Database\Exception\UnsupportedSyntaxException;

/**
 * Database sqlite query syntax class
 */
class Sqlite extends Mysql
{
	/**
	 * Builds an insert statement from the set params
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function buildInsert()
	{
		return 'INSERT ' . (($this->ignore) ? 'OR IGNORE ' : '') . 'INTO ' . $this->connection->quoteName($this->insert);
	}

	/**
	 * Sets a join element on the query
	 *
	 * @param   string  $table     The table join
	 * @param   string  $leftKey   The left side of the join condition
	 * @param   string  $rightKey  The right side of the join condition
	 * @param   string  $type      The join type to perform
	 * @return  void
	 * @throws  Hubzero\Database\Exception\UnsupportedSyntaxException
	 * @since   2.2.15
	 **/
	public function setJoin($table, $leftKey, $rightKey, $type = 'inner')
	{
		if (in_array($type, ['right', 'full', 'full outer']))
		{
			throw new UnsupportedSyntaxException('RIGHT and FULL OUTER JOINs are not currently supported for SQLite', 500);
		}

		parent::setJoin($table, $leftKey, $rightKey, $type);
	}

	/**
	 * Sets a join element on the query
	 *
	 * @param   string  $table  The table join
	 * @param   string  $raw    The join clause
	 * @param   string  $type   The join type to perform
	 * @throws  Hubzero\Database\Exception\UnsupportedSyntaxException
	 * @return  $this
	 **/
	public function setRawJoin($table, $raw, $type = 'inner')
	{
		if (in_array($type, ['right', 'full', 'full outer']))
		{
			throw new UnsupportedSyntaxException('RIGHT and FULL OUTER JOINs are not currently supported for SQLite', 500);
		}

		parent::setRawJoin($table, $raw, $type);
	}

	/**
	 * Returns the proper query for generating a list of table columns per this syntax
	 *
	 * @param   string  $table  The name of the database table
	 * @return  array
	 * @since   2.0.0
	 */
	public function getColumnsQuery($table)
	{
		return 'PRAGMA table_info(' . $this->connection->quoteName($table) . ')';
	}

	/**
	 * Normalizes the results of the above query
	 *
	 * @param   array  $data      The raw column data
	 * @param   bool   $typeOnly  True (default) to only return field types
	 * @return  array
	 * @since   2.0.0
	 **/
	public function normalizeColumns($data, $typeOnly = true)
	{
		$results = [];

		// If we only want the type as the value add just that to the list
		if ($typeOnly)
		{
			foreach ($data as $field)
			{
				// @FIXME: should we try to normalize types too?
				$results[$field->name] = $field->type;
			}
		}
		// If we want the whole field data object add that to the list
		else
		{
			foreach ($data as $field)
			{
				$results[$field->name] =
				[
					'name'      => $field->name,
					'type'      => $field->type,
					'allownull' => $field->notnull ? false : true,
					'default'   => $field->dflt_value,
					'pk'        => $field->pk ? true : false
				];
			}
		}

		return $results;
	}
}
