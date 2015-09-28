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
 * @package   framework
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 2.0.0
 */

namespace Hubzero\Database\Syntax;

/**
 * Database mysql query syntax class
 */
class Mysql
{
	/**
	 * The database connection object
	 *
	 * @var  object
	 **/
	protected $connection = null;

	/**
	 * The prepared statement binding parameters
	 *
	 * @var  array
	 **/
	protected $bindings = [];

	/**
	 * The syntax element containers
	 **/
	protected $select = [];
	protected $insert = '';
	protected $ignore = false;
	protected $update = '';
	protected $delete = '';
	protected $set    = [];
	protected $values = [];
	protected $from   = [];
	protected $join   = [];
	protected $where  = [];
	protected $group  = [];
	protected $having = [];
	protected $order  = [];
	protected $start  = '';
	protected $limit  = '';

	/**
	 * Constructs query syntax class, setting database connection
	 *
	 * @param   object  $connection  The database connection to use
	 * @return  void
	 * @since   2.0.0
	 **/
	public function __construct($connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Grabs the params bindings
	 *
	 * @return  array
	 * @since   2.0.0
	 **/
	public function getBindings()
	{
		return $this->bindings;
	}

	/**
	 * Sets a new bind value
	 *
	 * @param   string  $value  The value to bind
	 * @param   string  $type   The value type
	 * @return  $this
	 * @since   2.0.0
	 **/
	protected function bind($value, $type = null)
	{
		$this->bindings[] = $value;
	}

	/**
	 * Sets a select element on the query
	 *
	 * @param   string  $column  The column to select
	 * @param   string  $as      What to call the return val
	 * @param   bool    $count   Whether or not to count column
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setSelect($column, $as = null, $count = false)
	{
		// A default * is often added, get rid of it if anything else is added
		// This wouldn't get rid of table.* as that is likely added intentionally
		if (isset($this->select[0]) && $this->select[0]['column'] == '*')
		{
			$this->select = [];
		}

		$this->select[] = [
			'column' => $column,
			'as'     => $as,
			'count'  => $count
		];
	}

	/**
	 * Sets an insert element on the query
	 *
	 * @param   string  $table   The table into which we will be inserting
	 * @param   bool    $ignore  Whether or not to ignore errors produced related to things like duplicate keys
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setInsert($table, $ignore = false)
	{
		$this->insert = $table;
		$this->ignore = $ignore;
	}

	/**
	 * Sets an update element on the query
	 *
	 * @param   string  $table  The table whose fields will be updated
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setUpdate($table)
	{
		$this->update = $table;
	}

	/**
	 * Sets a delete element on the query
	 *
	 * @param   string  $table  The table whose row will be deleted
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setDelete($table)
	{
		$this->delete = $table;
	}

	/**
	 * Sets a from element on the query
	 *
	 * @param   string  $table  The table of interest
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setFrom($table)
	{
		$this->from[] = $table;
	}

	/**
	 * Sets a join element on the query
	 *
	 * @param   string  $table     The table join
	 * @param   string  $leftKey   The left side of the join condition
	 * @param   string  $rightKey  The right side of the join condition
	 * @param   string  $type      The join type to perform
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setJoin($table, $leftKey, $rightKey, $type = 'inner')
	{
		$this->join[] = [
			'table' => $table,
			'left'  => $leftKey,
			'right' => $rightKey,
			'type'  => $type
		];
	}

	/**
	 * Sets a set element on the query
	 *
	 * @param   array  $data  The data to be modified
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setSet($data)
	{
		$this->set = $data;
	}

	/**
	 * Sets a values element on the query
	 *
	 * @param   array  $data  The data to be inserted
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setValues($data)
	{
		$this->values = $data;
	}

	/**
	 * Sets a group element on the query
	 *
	 * @param   string  $column  The column on which to apply the group by
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setGroup($column)
	{
		$this->group[] = $column;
	}

	/**
	 * Sets a having element on the query
	 *
	 * @param   string  $column    The column to which the clause will apply
	 * @param   string  $operator  The operation that will compare column to value
	 * @param   string  $value     The value to which the column will be evaluated
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setHaving($column, $operator, $value)
	{
		$this->having[] = [
			'column'   => $column,
			'operator' => $operator,
			'value'    => $value
		];
	}

	/**
	 * Sets a where element on the query
	 *
	 * @param   string  $column    The column to which the clause will apply
	 * @param   string  $operator  The operation that will compare column to value
	 * @param   string  $value     The value to which the column will be evaluated
	 * @param   string  $logical   The operator between multiple clauses
	 * @param   int     $depth     The depth level of the clause, for sub clauses
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setWhere($column, $operator, $value, $logical = 'and', $depth = 0)
	{
		$this->where[] = [
			'column'   => $column,
			'operator' => $operator,
			'value'    => $value,
			'logical'  => $logical,
			'depth'    => $depth
		];
	}

	/**
	 * Sets a raw where element on the query
	 *
	 * @param   string  $raw       The raw where clause
	 * @param   array   $bindings  The clause bindings, if any
	 * @param   string  $logical   The operator between multiple clauses
	 * @param   int     $depth     The depth level of the clause, for sub clauses
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setRawWhere($raw, $bindings = [], $logical = 'and', $depth = 0)
	{
		$this->where[] = [
			'raw'      => $raw,
			'bindings' => $bindings,
			'logical'  => $logical,
			'depth'    => $depth
		];
	}

	/**
	 * Sets the query clause depth
	 *
	 * @param   int  $depth  The depth to set to
	 * @return  void
	 * @since   2.1.0
	 **/
	public function resetDepth($depth = 0)
	{
		$this->where[] = [
			'resetdepth' => true,
			'depth'      => $depth
		];
	}

	/**
	 * Sets a limit element on the query
	 *
	 * @param   int  $limit  Number of results to return on next query
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setLimit($limit)
	{
		$this->limit = $limit;
	}

	/**
	 * Sets a start element on the query
	 *
	 * @param   int  $start  Position to start from
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setStart($start)
	{
		$this->start = $start;
	}

	/**
	 * Sets an order element on the query
	 *
	 * @param   string  $column  The column to which the order by will apply
	 * @param   string  $dir     The direction in which the results will be ordered
	 * @return  void
	 * @since   2.0.0
	 **/
	public function setOrder($column, $dir)
	{
		$this->order[] = [
			'column' => $column,
			'dir'    => $dir
		];
	}

	/**
	 * Builds the given query element
	 *
	 * @param   string  $type  The query element to build
	 * @return  string
	 * @since   2.0.0
	 **/
	public function build($type)
	{
		if (empty($this->{$type})) return false;

		$method = 'build' . ucfirst($type);

		return $this->{$method}();
	}

	/**
	 * Builds a select statement from the set params
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	private function buildSelect()
	{
		$selects = [];

		foreach ($this->select as $select)
		{
			$string = ($select['count']) ? "COUNT({$select['column']})" : $select['column'];

			// See if we're including an alias
			if (isset($select['as'])) $string .= " AS {$select['as']}";

			// @FIXME: not quoting name here because we could have a function here as well
			// $selects[] = $this->connection->quoteName($string, $select['as']);
			$selects[] = $string;
		}

		return 'SELECT ' . implode(',', $selects);
	}

	/**
	 * Builds an insert statement from the set params
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function buildInsert()
	{
		return 'INSERT ' . (($this->ignore) ? 'IGNORE ' : '') . 'INTO ' . $this->connection->quoteName($this->insert);
	}

	/**
	 * Builds an update statement from the set params
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function buildUpdate()
	{
		return 'UPDATE ' . $this->connection->quoteName($this->update);
	}

	/**
	 * Builds a delete statement from the set params
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function buildDelete()
	{
		return 'DELETE FROM' . $this->connection->quoteName($this->delete);
	}

	/**
	 * Builds a from statement from the set params
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	private function buildFrom()
	{
		$froms = [];

		foreach ($this->from as $from)
		{
			$froms[] = $this->connection->quoteName($from);
		}

		return 'FROM ' . implode(',', $froms);
	}

	/**
	 * Builds a join statement from the set params
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function buildJoin()
	{
		$joins = [];

		foreach ($this->join as $join)
		{
			$joins[] = strtoupper($join['type']) . ' JOIN ' . $join['table'] . ' ON ' . $join['left'] . ' = ' . $join['right'];
		}

		return implode("\n", $joins);
	}

	/**
	 * Builds a where statement from the set params
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	private function buildWhere()
	{
		$strings = [];
		$first   = true;
		$depth   = 0;

		foreach ($this->where as $constraint)
		{
			$string = '';

			if (array_key_exists('resetdepth', $constraint))
			{
				$string .= str_repeat(')', ($depth - $constraint['depth']));
			}
			else
			{
				$string .= ($constraint['depth'] < $depth) ? ') ' : '';
				$string .= ($first) ? 'WHERE ' : strtoupper($constraint['logical']) . ' ';
				$string .= ($constraint['depth'] > $depth) ? '(' : '';

				// Make sure this isn't a 'raw' where clause
				if (array_key_exists('raw', $constraint))
				{
					$string .= $constraint['raw'];

					foreach ($constraint['bindings'] as $binding)
					{
						$this->bind($binding);
					}
				}
				else
				{
					$string .= $this->connection->quoteName($constraint['column']);
					$string .= ' ' . $constraint['operator'];
					if (is_array($constraint['value']))
					{
						$values = array();
						foreach ($constraint['value'] as $value)
						{
							$values[] = '?';
							$this->bind($value);
						}
						$string .= ' (' . ((!empty($values)) ? implode(',', $values) : "''") . ')';
					}
					else
					{
						$string .= ' ?';
						$this->bind($constraint['value']);
					}
				}
			}

			$strings[] = $string;
			$first     = false;
			$depth     = $constraint['depth'];
		}

		// Catch instance where last item was at a greater depth and never got a closing ')'
		if ($depth > 0) $strings[] = str_repeat(')', $depth);

		return implode("\n", $strings);
	}

	/**
	 * Builds a set statement from the set params
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function buildSet()
	{
		$updates = [];

		foreach ($this->set as $field => $value)
		{
			$updates[] = $this->connection->quoteName($field) . ' = ?';
			$this->bind(is_string($value) ? trim($value) : $value);
		}

		return 'SET ' . implode(',', $updates);
	}

	/**
	 * Builds a values statement from the set params
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function buildValues()
	{
		$fields = [];
		$values = [];

		foreach ($this->values as $field => $value)
		{
			$fields[] = $this->connection->quoteName($field);
			$values[] = (is_null($value)) ? 'NULL' : '?';
			$this->bind(is_string($value) ? trim($value) : $value);
		}

		return '(' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';
	}

	/**
	 * Builds a group statement from the set params
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function buildGroup()
	{
		return 'GROUP BY ' . implode(',', $this->group);
	}

	/**
	 * Builds a having statement from the set params
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function buildHaving()
	{
		$havings = [];

		foreach ($this->having as $having)
		{
			$havings[] = $having['column'] . ' ' . $having['operator'] . ' ?';

			$this->bind(is_string($having['value']) ? trim($having['value']) : $having['value']);
		}

		return 'HAVING ' . implode(" AND ", $havings);
	}

	/**
	 * Builds a limit statement from the set params
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function buildLimit()
	{
		$string  = 'LIMIT ';
		$string .= ((!empty($this->start)) ? (int)$this->start . ',' : '');
		$string .= ((!empty($this->limit)) ? (int)$this->limit : '18446744073709551615');

		return $string;
	}

	/**
	 * Builds an order statement from the set params
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function buildOrder()
	{
		$orders = [];

		foreach ($this->order as $order)
		{
			$string  = $this->connection->quoteName($order['column']);
			$string .= ' ' . strtoupper($order['dir']);

			$orders[] = $string;
		}
		return 'ORDER BY ' . implode(',', $orders);
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
		return 'SHOW FULL COLUMNS FROM ' . $this->connection->quoteName($table);
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
				$results[$field->Field] = $field->Type;
			}
		}
		// If we want the whole field data object add that to the list
		else
		{
			foreach ($data as $field)
			{
				$results[$field->Field] =
				[
					'name'      => $field->Field,
					'type'      => $field->Type,
					'allownull' => ($field->Null == 'NO') ? false : true,
					'default'   => $field->Default,
					'pk'        => ($field->Key == 'PRI') ? true : false
				];
			}
		}

		return $results;
	}
}