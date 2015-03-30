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

// @FIXME: dynamically determine syntax from connection
use Hubzero\Database\Syntax\Mysql as Syntax;

/**
 * Database query class
 *
 * @uses \Hubzero\Database\Row for results returned from queries
 */
class Query
{
	/**
	 * The actual database connection object
	 *
	 * @var object
	 **/
	private $connection = null;

	/**
	 * The query syntax
	 *
	 * @var object
	 **/
	private $syntax = null;

	/**
	 * The debug state of the union
	 *
	 * @var bool
	 **/
	private static $debug = null;

	/**
	 * The query results cache
	 *
	 * This is a key value dictionary of query md5 hash and query results.
	 *
	 * @var array
	 **/
	private static $cache = array();

	/**
	 * The database query type constants
	 **/
	const ROW    = 'loadObject';
	const ROWS   = 'loadObjectList';
	const COLUMN = 'loadColumn';

	/**
	 * The elements of a basic select statement
	 *
	 * @var array
	 **/
	private $select = array(
		'select',
		'from',
		'join',
		'where',
		'group',
		'having',
		'order',
		'limit'
	);

	/**
	 * The elements of a basic insert statement
	 *
	 * @var array
	 **/
	private $insert = array(
		'insert',
		'values'
	);

	/**
	 * The elements of a basic update statement
	 *
	 * @var array
	 **/
	private $update = array(
		'update',
		'set',
		'where'
	);

	/**
	 * The elements of a basic delete statement
	 *
	 * @var array
	 **/
	private $delete = array(
		'delete',
		'where'
	);

	/**
	 * The query type to be performed
	 *
	 * This is a silly way of tracking what type of query we think
	 * we're going to execute. This is used by the execute method.
	 *
	 * @var string
	 **/
	private $type = null;

	/**
	 * Constructs a new query instance
	 *
	 * @param  object $connect the database connection to use in the query builder
	 * @return void
	 * @since  1.3.2
	 **/
	public function __construct($connection=null)
	{
		$this->connection = $connection ?: App::get('db');
		$this->reset();

		// Set debugging
		self::$debug = (isset(self::$debug))
		             ? self::$debug
		             : \Hubzero\Plugin\Plugin::getParams('debug', 'system')->get('log-database-queries', false);
	}

	/**
	 * Clones the query object, including its individual syntax elements
	 *
	 * We want to duplicate our syntax elements, as well as the overall query object,
	 * hence the need for this. Otherwise, PHP would only provide references to the
	 * syntax elements, which is counter productive in this instance.
	 *
	 * @return void
	 * @since  1.3.2
	 **/
	public function __clone()
	{
		$this->syntax = clone $this->syntax;
	}

	/**
	 * Applies a select field to the pending query
	 *
	 * @param  string $column the column to select
	 * @param  string $as     what to call the return val
	 * @param  bool   $count  whether or not to count column
	 * @return $this
	 * @since  1.3.2
	 **/
	public function select($column, $as=null, $count=false)
	{
		$this->syntax->setSelect($column, $as, $count);
		$this->type = 'select';
		return $this;
	}

	/**
	 * Applies an insert statement to the pending query
	 *
	 * @param  string $table  the table into which we will be inserting
	 * @param  bool   $ignore whether or not to ignore errors produced related to things like duplicate keys
	 * @return $this
	 * @since  1.3.2
	 **/
	public function insert($table, $ignore=false)
	{
		$this->syntax->setInsert($table, $ignore);
		$this->type = 'insert';
		return $this;
	}

	/**
	 * Applies an update statement to the pending query
	 *
	 * @param  string $table the table whose fields will be updated
	 * @return $this
	 * @since  1.3.2
	 **/
	public function update($table)
	{
		$this->syntax->setUpdate($table);
		$this->type = 'update';
		return $this;
	}

	/**
	 * Applies a delete statement to the pending query
	 *
	 * @param  string $table the table whose row will be deleted
	 * @return $this
	 * @since  1.3.2
	 **/
	public function delete($table)
	{
		$this->syntax->setDelete($table);
		$this->type = 'delete';
		return $this;
	}

	/**
	 * Defines the table from which data should be retrieved
	 *
	 * @param  string $table the table of interest
	 * @return $this
	 **/
	public function from($table)
	{
		$this->syntax->setFrom($table);
		return $this;
	}

	/**
	 * Defines a table join to be performed for the query
	 *
	 * @param  string $table    the table join
	 * @param  string $leftKey  the left side of the join condition
	 * @param  string $rightKey the right side of the join condition
	 * @param  string $type     the join type to perform
	 * @return $this
	 **/
	public function join($table, $leftKey, $rightKey, $type='inner')
	{
		$this->syntax->setJoin($table, $leftKey, $rightKey, $type);
		return $this;
	}

	/**
	 * Applies a where clause to the pending query
	 *
	 * @param  string $column   the column to which the clause will apply
	 * @param  string $operator the operation that will compare column to value
	 * @param  string $value    the value to which the column will be evaluated
	 * @param  string $logical  the operator between multiple clauses
	 * @param  int    $depth    the depth level of the clause, for sub clauses
	 * @return $this
	 * @since  1.3.2
	 **/
	public function where($column, $operator, $value, $logical='and', $depth=0)
	{
		$this->syntax->setWhere($column, $operator, $value, $logical, $depth);
		return $this;
	}

	/**
	 * Applies a where clause to the pending query
	 *
	 * @param  string $column   the column to which the clause will apply
	 * @param  string $operator the operation that will compare column to value
	 * @param  string $value    the value to which the column will be evaluated
	 * @param  string $logical  the operator between multiple clauses
	 * @param  int    $depth    the depth level of the clause, for sub clauses
	 * @return $this
	 * @since  1.3.2
	 **/
	public function orWhere($column, $operator, $value, $logical='or', $depth=0)
	{
		$this->where($column, $operator, $value, $logical, $depth);
		return $this;
	}

	/**
	 * Applies a simple where equals clause to the pending query
	 *
	 * @param  string $column the column to which the clause will apply
	 * @param  string $value  the value to which the column will be evaluated
	 * @param  int    $depth  the depth level of the clause, for sub clauses
	 * @return $this
	 * @since  1.3.2
	 **/
	public function whereEquals($column, $value, $depth=0)
	{
		$this->where($column, '=', $value, 'and', $depth);
		return $this;
	}

	/**
	 * Applies a simple where equals clause to the pending query
	 *
	 * @param  string $column the column to which the clause will apply
	 * @param  string $value  the value to which the column will be evaluated
	 * @param  int    $depth  the depth level of the clause, for sub clauses
	 * @return $this
	 * @since  1.3.2
	 **/
	public function orWhereEquals($column, $value, $depth=0)
	{
		$this->where($column, '=', $value, 'or', $depth);
		return $this;
	}

	/**
	 * Applies a simple where in clause to the pending query
	 *
	 * @param  string $column the column to which the clause will apply
	 * @param  array  $value  the values to which the column will be evaluated
	 * @param  int    $depth  the depth level of the clause, for sub clauses
	 * @return $this
	 * @since  1.3.2
	 **/
	public function whereIn($column, $values, $depth=0)
	{
		$this->where($column, 'IN', $values, 'and', $depth);
		return $this;
	}

	/**
	 * Applies a simple where in clause to the pending query
	 *
	 * @param  string $column the column to which the clause will apply
	 * @param  array  $value  the values to which the column will be evaluated
	 * @param  int    $depth  the depth level of the clause, for sub clauses
	 * @return $this
	 * @since  1.3.2
	 **/
	public function orWhereIn($column, $values, $depth=0)
	{
		$this->where($column, 'IN', $values, 'or', $depth);
		return $this;
	}

	/**
	 * Applies a where clause comparing a field to the current juser id
	 *
	 * @param  string $column the field to use for ownership, defaulting to 'created_by'
	 * @return $this
	 * @since  1.3.2
	 **/
	public function whereIsMine($column='created_by')
	{
		$this->whereEquals($column, \JFactory::getUser()->get('id'));
		return $this;
	}

	/**
	 * Applies order by clause
	 *
	 * @param  string $column the column to which the order by will apply
	 * @param  string $dir    the direction in which the results will be ordered
	 * @return $this
	 * @since  1.3.2
	 **/
	public function order($column, $dir)
	{
		$this->syntax->setOrder($column, $dir);
		return $this;
	}

	/**
	 * Sets query offset to start at a certain position
	 *
	 * @param  int $start position to start from
	 * @return $this
	 * @since  1.3.2
	 **/
	public function start($start)
	{
		$this->syntax->setStart($start);
		return $this;
	}

	/**
	 * Limits query results returned to a certain number
	 *
	 * @param  int $limit number of results to return on next query
	 * @return $this
	 * @since  1.3.2
	 **/
	public function limit($limit)
	{
		$this->syntax->setLimit($limit);
		return $this;
	}

	/**
	 * Sets the values to be inserted into the database
	 *
	 * @param  array $data the data to be inserted
	 * @return $this
	 * @since  1.3.2
	 **/
	public function values($data)
	{
		$this->syntax->setValues($data);
		return $this;
	}

	/**
	 * Sets the values to be modified in the database
	 *
	 * @param  array $data the data to be modified
	 * @return $this
	 * @since  1.3.2
	 **/
	public function set($data)
	{
		$this->syntax->setSet($data);
		return $this;
	}

	/**
	 * Sets the group by element on the query
	 *
	 * @param  string $column the column on which to apply the group by
	 * @return $this
	 * @since  1.3.2
	 **/
	public function group($column)
	{
		$this->syntax->setGroup($column);
		return $this;
	}

	/**
	 * Sets the having element on the query
	 *
	 * @param  string $column   the column to which the clause will apply
	 * @param  string $operator the operation that will compare column to value
	 * @param  string $value    the value to which the column will be evaluated
	 * @return $this
	 * @since  1.3.2
	 **/
	public function having($column, $operator, $value)
	{
		$this->syntax->setHaving($column, $operator, $value);
		return $this;
	}

	/**
	 * Retrieves all applicable data
	 *
	 * @FIXME: this could result in slightly odd behavior if you call the same query
	 *         twice, but for some reason want differing structures of the returned data.
	 *
	 * @param  string $structure the structure of the item(s) returned (if applicable)
	 * @param  bool   $noCache   whether or not to check cache for results
	 * @return $this
	 * @since  1.3.2
	 **/
	public function fetch($structure='rows', $noCache=false)
	{
		// Build and hash query
		$query = $this->buildQuery();
		$key   = hash('md5', $query);

		// Check cache for results first
		if ($noCache || !isset(self::$cache[$key]))
		{
			self::$cache[$key] = $this->query($query, $structure);
		}

		// Clear elements
		$this->reset();

		return self::$cache[$key];
	}

	/**
	 * Inserts a new row using data provided into given table
	 *
	 * @param  string $table  the table name into which the data should be inserted
	 * @param  array  $data   an associative array of data to insert
	 * @param  bool   $ignore whether or not to perform an insert ignore
	 * @return bool|int
	 * @since  1.3.2
	 **/
	public function push($table, $data, $ignore=false)
	{
		// Add insert statement
		$this->insert($table, $ignore)
		     ->values($data);

		$result = $this->execute();

		// Return the inserted data
		return !$result ?: $this->connection->insertid();
	}

	/**
	 * Updates an existing item in the database using the provided data
	 *
	 * @param  string $table   the table to update
	 * @param  string $pkField the table field serving as primary key
	 * @param  mixed  $pkValue the primary key value
	 * @param  array  $data    the data to update in the database
	 * @return bool
	 * @since  1.3.2
	 **/
	public function alter($table, $pkField, $pkValue, $data)
	{
		// Add insert statement
		$this->update($table)
		     ->set($data);

		// Where primary key is...
		$this->whereEquals($pkField, $pkValue);

		// Return the result of the query
		return $this->execute();
	}

	/**
	 * Removes a record by its primary key
	 *
	 * @param  string $table   the table to update
	 * @param  string $pkField the table field serving as primary key
	 * @param  mixed  $pkValue the primary key value
	 * @return bool
	 * @since  1.3.2
	 **/
	public function remove($table, $pkField, $pkValue)
	{
		// Make sure we have an id (i.e. don't delete everything in the table!)
		if (is_null($pkValue) || empty($pkValue))
		{
			return false;
		}

		// Add delete statement
		$this->delete($table)
		     ->whereEquals($pkField, $pkValue);

		// Return result of the query
		return $this->execute();
	}

	/**
	 * Builds and executes the current query based on the elements present
	 *
	 * This is a fairly 'dumb' function, in that it just looks for whichever type was
	 * most recently set by one of the primary functions (select, insert, update, delete).
	 * Fetch should still be used for select queries as it offers result caching.
	 *
	 * @FIXME: maybe this should be combined with fetch?
	 *
	 * @return mixed
	 * @since  1.3.2
	 **/
	public function execute()
	{
		$result = $this->query($this->buildQuery($this->type));

		// Clear elements
		$this->reset();

		// Return result of the query
		return $result;
	}

	/**
	 * Performs the actual query and returns the results
	 *
	 * @param  string $query     the query to perform
	 * @param  string $structure the structure of the item(s) returned (if applicable)
	 * @return mixed
	 * @since  1.3.2
	 **/
	public function query($query, $structure='rows')
	{
		// Check the type of query to decide what to return
		list($type) = explode(' ', $query, 2);

		$this->connection->prepare($query)->bind($this->syntax->getBindings());

		$result = (strtolower($type) == 'select')
		        ? $this->connection->{constant('self::' . strtoupper($structure))}()
		        : $this->connection->query();

		// @FIXME: move to driver
		if (self::$debug) Log::add($query, $this->connection->timer);

		return $result;
	}

	/**
	 * Retrieves the current query as a string (without executing it)
	 *
	 * @return string
	 * @since  1.3.2
	 **/
	public function toString()
	{
		return $this->buildQuery($this->type);
	}

	/**
	 * Builds query based on the current query elements established
	 *
	 * @param  string $type the type of query to build
	 * @return string
	 * @since  1.3.2
	 **/
	private function buildQuery($type='select')
	{
		$pieces = array();

		// Loop through query elements
		foreach ($this->$type as $piece)
		{
			// If we have one of these elements, get its string value
			if ($element = $this->syntax->build($piece))
			{
				$pieces[] = $element;
			}
		}

		return implode("\n", $pieces);
	}

	/**
	 * Resets the query elements
	 *
	 * @return void
	 * @since  1.3.2
	 **/
	private function reset()
	{
		// Reset the syntax element
		$this->syntax = new Syntax($this->connection);
	}
}