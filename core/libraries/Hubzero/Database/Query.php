<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

use Hubzero\Database\Syntax\Mysql;
use Hubzero\Database\Syntax\Sqlite;
use App;

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
	 * @var  object
	 **/
	private $connection = null;

	/**
	 * The query syntax
	 *
	 * @var  object
	 **/
	protected $syntax = null;

	/**
	 * The query results cache
	 *
	 * This is a key value dictionary of query md5 hash and query results.
	 *
	 * @var  array
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
	 * @var  array
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
	 * @var  array
	 **/
	private $insert = array(
		'insert',
		'values'
	);

	/**
	 * The elements of a basic update statement
	 *
	 * @var  array
	 **/
	private $update = array(
		'update',
		'set',
		'where'
	);

	/**
	 * The elements of a basic delete statement
	 *
	 * @var  array
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
	 * @var  string
	 **/
	protected $type = null;

	/**
	 * Constructs a new query instance
	 *
	 * @param   object  $connect  The database connection to use in the query builder
	 * @return  void
	 * @since   2.0.0
	 **/
	public function __construct($connection = null)
	{
		$this->connection = $connection ?: App::get('db');
		$this->reset();
	}

	/**
	 * Clones the query object, including its individual syntax elements
	 *
	 * We want to duplicate our syntax elements, as well as the overall query object,
	 * hence the need for this. Otherwise, PHP would only provide references to the
	 * syntax elements, which is counter productive in this instance.
	 *
	 * @return  void
	 * @since   2.0.0
	 **/
	public function __clone()
	{
		$this->syntax = clone $this->syntax;
	}

	/**
	 * Purges the query cache
	 *
	 * @return  void
	 * @since   2.0.0
	 **/
	public static function purgeCache()
	{
		self::$cache = array();
	}

	/**
	 * Empties a query clause of current values
	 *
	 * @param   string  $clause  [select, update, insert, delete, from, join, set, values, where, group, having, order]
	 * @return  $this
	 * @since   2.2.15
	 **/
	public function clear($clause = '')
	{
		if (!$clause)
		{
			$this->reset();
		}
		else
		{
			$clause = 'reset' . ucfirst(strtolower($clause));

			$this->syntax->$clause();
		}

		return $this;
	}

	/**
	 * Empties a query of current select values
	 *
	 * @return  $this
	 * @since   2.2.2
	 **/
	public function deselect()
	{
		$this->syntax->resetSelect();
		return $this;
	}

	/**
	 * Applies a select field to the pending query
	 *
	 * @param   string  $column  The column to select
	 * @param   string  $as      What to call the return val
	 * @param   bool    $count   Whether or not to count column
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function select($column, $as = null, $count = false)
	{
		$this->syntax->setSelect($column, $as, $count);
		$this->type = 'select';
		return $this;
	}

	/**
	 * Applies an insert statement to the pending query
	 *
	 * @param   string  $table   The table into which we will be inserting
	 * @param   bool    $ignore  Whether or not to ignore errors produced related to things like duplicate keys
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function insert($table, $ignore = false)
	{
		$this->syntax->setInsert($table, $ignore);
		$this->type = 'insert';
		return $this;
	}

	/**
	 * Applies an update statement to the pending query
	 *
	 * @param   string  $table  The table whose fields will be updated
	 * @return  $this
	 * @since   2.0.0
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
	 * @param   string  $table  The table whose row will be deleted
	 * @return  $this
	 * @since   2.0.0
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
	 * @param   string  $table  The table of interest
	 * @param   string  $as     What to call the table
	 * @return  $this
	 **/
	public function from($table, $as = null)
	{
		$this->syntax->setFrom($table, $as);
		return $this;
	}

	/**
	 * Defines a table join to be performed for the query
	 *
	 * @param   string  $table     The table join
	 * @param   string  $leftKey   The left side of the join condition
	 * @param   string  $rightKey  The right side of the join condition
	 * @param   string  $type      The join type to perform
	 * @return  $this
	 **/
	public function join($table, $leftKey, $rightKey, $type = 'inner')
	{
		$this->syntax->setJoin($table, $leftKey, $rightKey, $type);
		return $this;
	}

	/**
	 * Defines a table join to be performed for the query using a raw expression 
	 *
	 * @param   string  $table  The table join
	 * @param   string  $raw    The join clause (anything after the ON keyword)
	 * @param   string  $type   The join type to perform
	 * @return  $this
	 **/
	public function joinRaw($table, $raw, $type = 'inner')
	{
		$this->syntax->setRawJoin($table, $raw, $type);
		return $this;
	}

	/**
	 * Defines a table INNER join to be performed for the query
	 *
	 * @param   string  $table     The table join
	 * @param   string  $leftKey   The left side of the join condition
	 * @param   string  $rightKey  The right side of the join condition
	 * @return  $this
	 **/
	public function innerJoin($table, $leftKey, $rightKey)
	{
		$this->syntax->setJoin($table, $leftKey, $rightKey, 'inner');
		return $this;
	}

	/**
	 * Defines a table FULL OUTER join to be performed for the query
	 *
	 * @param   string  $table     The table join
	 * @param   string  $leftKey   The left side of the join condition
	 * @param   string  $rightKey  The right side of the join condition
	 * @return  $this
	 **/
	public function fullJoin($table, $leftKey, $rightKey)
	{
		$this->syntax->setJoin($table, $leftKey, $rightKey, 'full');
		return $this;
	}

	/**
	 * Defines a table LEFT join to be performed for the query
	 *
	 * @param   string  $table     The table join
	 * @param   string  $leftKey   The left side of the join condition
	 * @param   string  $rightKey  The right side of the join condition
	 * @return  $this
	 **/
	public function leftJoin($table, $leftKey, $rightKey)
	{
		$this->syntax->setJoin($table, $leftKey, $rightKey, 'left');
		return $this;
	}

	/**
	 * Defines a table RIGHT join to be performed for the query
	 *
	 * @param   string  $table     The table join
	 * @param   string  $leftKey   The left side of the join condition
	 * @param   string  $rightKey  The right side of the join condition
	 * @return  $this
	 **/
	public function rightJoin($table, $leftKey, $rightKey)
	{
		$this->syntax->setJoin($table, $leftKey, $rightKey, 'right');
		return $this;
	}

	/**
	 * Applies a where clause to the pending query
	 *
	 * @param   string  $column    The column to which the clause will apply
	 * @param   string  $operator  The operation that will compare column to value
	 * @param   string  $value     The value to which the column will be evaluated
	 * @param   string  $logical   The operator between multiple clauses
	 * @param   int     $depth     The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function where($column, $operator, $value, $logical = 'and', $depth = 0)
	{
		$this->syntax->setWhere($column, $operator, $value, $logical, $depth);
		return $this;
	}

	/**
	 * Applies a where clause to the pending query
	 *
	 * @param   string  $column    The column to which the clause will apply
	 * @param   string  $operator  The operation that will compare column to value
	 * @param   string  $value     The value to which the column will be evaluated
	 * @param   int     $depth     The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function orWhere($column, $operator, $value, $depth = 0)
	{
		$this->where($column, $operator, $value, 'or', $depth);
		return $this;
	}

	/**
	 * Applies a simple where equals clause to the pending query
	 *
	 * @param   string  $column  The column to which the clause will apply
	 * @param   string  $value   The value to which the column will be evaluated
	 * @param   int     $depth   The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function whereEquals($column, $value, $depth = 0)
	{
		$this->where($column, '=', $value, 'and', $depth);
		return $this;
	}

	/**
	 * Applies a simple where equals clause to the pending query
	 *
	 * @param   string  $column  The column to which the clause will apply
	 * @param   string  $value   The value to which the column will be evaluated
	 * @param   int     $depth   The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function orWhereEquals($column, $value, $depth = 0)
	{
		$this->where($column, '=', $value, 'or', $depth);
		return $this;
	}

	/**
	 * Applies a simple where in clause to the pending query
	 *
	 * @param   string  $column  The column to which the clause will apply
	 * @param   array   $value   The values to which the column will be evaluated
	 * @param   int     $depth   The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function whereIn($column, $values, $depth = 0)
	{
		$this->where($column, 'IN', $values, 'and', $depth);
		return $this;
	}

	/**
	 * Applies a simple where in clause to the pending query
	 *
	 * @param   string  $column  The column to which the clause will apply
	 * @param   array   $value   The values to which the column will be evaluated
	 * @param   int     $depth   The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function orWhereIn($column, $values, $depth = 0)
	{
		$this->where($column, 'IN', $values, 'or', $depth);
		return $this;
	}

	/**
	 * Applies a simple where not in clause to the pending query
	 *
	 * @param   string  $column  The column to which the clause will apply
	 * @param   array   $value   The values to which the column will be evaluated
	 * @param   int     $depth   The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function whereNotIn($column, $values, $depth = 0)
	{
		$this->where($column, 'NOT IN', $values, 'and', $depth);
		return $this;
	}

	/**
	 * Applies a simple where not in clause to the pending query
	 *
	 * @param   string  $column  The column to which the clause will apply
	 * @param   array   $value   The values to which the column will be evaluated
	 * @param   int     $depth   The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function orWhereNotIn($column, $values, $depth = 0)
	{
		$this->where($column, 'NOT IN', $values, 'or', $depth);
		return $this;
	}

	/**
	 * Applies a simple where like clause to the pending query
	 *
	 * @param   string  $column  The column to which the clause will apply
	 * @param   string  $value   The value to which the column will be evaluated
	 * @param   int     $depth   The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.1.0
	 **/
	public function whereLike($column, $value, $depth = 0)
	{
		$this->where($column, 'LIKE', "%{$value}%", 'and', $depth);
		return $this;
	}

	/**
	 * Applies a simple where like clause to the pending query
	 *
	 * @param   string  $column  The column to which the clause will apply
	 * @param   string  $value   The value to which the column will be evaluated
	 * @param   int     $depth   The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.1.0
	 **/
	public function orWhereLike($column, $value, $depth = 0)
	{
		$this->where($column, 'LIKE', "%{$value}%", 'or', $depth);
		return $this;
	}

	/**
	 * Applies an AND where is null clause to the pending query
	 *
	 * @param   string  $column  The column to which the clause will apply
	 * @param   int     $depth   The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.2.15
	 **/
	public function whereIsNull($column, $depth = 0)
	{
		$this->where($column, 'IS', null, 'and', $depth);
		return $this;
	}

	/**
	 * Applies a OR where is null clause to the pending query
	 *
	 * @param   string  $column  The column to which the clause will apply
	 * @param   int     $depth   The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.2.15
	 **/
	public function orWhereIsNull($column, $depth = 0)
	{
		$this->where($column, 'IS', null, 'or', $depth);
		return $this;
	}

	/**
	 * Applies an AND where is not null clause to the pending query
	 *
	 * @param   string  $column  The column to which the clause will apply
	 * @param   int     $depth   The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.2.15
	 **/
	public function whereIsNotNull($column, $depth = 0)
	{
		$this->where($column, 'IS NOT', null, 'and', $depth);
		return $this;
	}

	/**
	 * Applies a OR where is not null clause to the pending query
	 *
	 * @param   string  $column  The column to which the clause will apply
	 * @param   int     $depth   The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.2.15
	 **/
	public function orWhereIsNotNull($column, $depth = 0)
	{
		$this->where($column, 'IS NOT', null, 'or', $depth);
		return $this;
	}

	/**
	 * Applies a raw where clause to the pending query
	 *
	 * @param   string  $string    The raw where clause
	 * @param   array   $bindings  Any bindings to apply to the where clause
	 * @param   int     $depth     The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function whereRaw($string, $bindings = [], $depth = 0)
	{
		$this->syntax->setRawWhere($string, $bindings, 'and', $depth);
		return $this;
	}

	/**
	 * Applies a raw where clause to the pending query
	 *
	 * @param   string  $string    The raw where clause
	 * @param   array   $bindings  Any bindings to apply to the where clause
	 * @param   int     $depth     The depth level of the clause, for sub clauses
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function orWhereRaw($string, $bindings = [], $depth = 0)
	{
		$this->syntax->setRawWhere($string, $bindings, 'or', $depth);
		return $this;
	}

	/**
	 * Resets the depth of a nested statement back down to a given level
	 *
	 * @param   int  $depth  The depth to set to
	 * @return  $this
	 * @since   2.1.0
	 **/
	public function resetDepth($depth = 0)
	{
		$this->syntax->resetDepth($depth);
		return $this;
	}

	/**
	 * Applies 'order by' clause
	 *
	 * @param   string  $column  The column to which the order by will apply
	 * @param   string  $dir     The direction in which the results will be ordered
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function order($column, $dir)
	{
		$this->syntax->setOrder($column, $dir);
		return $this;
	}

	/**
	 * Removes 'order by' clause
	 *
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function unorder()
	{
		$this->syntax->resetOrder();
		return $this;
	}

	/**
	 * Sets query offset to start at a certain position
	 *
	 * @param   int    $start  Position to start from
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function start($start)
	{
		$this->syntax->setStart((int)$start);
		return $this;
	}

	/**
	 * Limits query results returned to a certain number
	 *
	 * @param   int    $limit  Number of results to return on next query
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function limit($limit)
	{
		$this->syntax->setLimit((int)$limit);
		return $this;
	}

	/**
	 * Sets the values to be inserted into the database
	 *
	 * @param   array  $data  The data to be inserted
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function values($data)
	{
		$this->syntax->setValues($data);
		return $this;
	}

	/**
	 * Sets the values to be modified in the database
	 *
	 * @param   array  $data  The data to be modified
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function set($data)
	{
		$this->syntax->setSet($data);
		return $this;
	}

	/**
	 * Sets the group by element on the query
	 *
	 * @param   string  $column  The column on which to apply the group by
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function group($column)
	{
		$this->syntax->setGroup($column);
		return $this;
	}

	/**
	 * Sets the having element on the query
	 *
	 * @param   string  $column    The column to which the clause will apply
	 * @param   string  $operator  The operation that will compare column to value
	 * @param   string  $value     The value to which the column will be evaluated
	 * @return  $this
	 * @since   2.0.0
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
	 * @param   string  $structure  The structure of the item(s) returned (if applicable)
	 * @param   bool    $noCache    Whether or not to check cache for results
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function fetch($structure = 'rows', $noCache = false)
	{
		// Build and hash query
		$query = $this->buildQuery();
		$key   = hash('md5', $structure . $query . serialize($this->syntax->getBindings()));

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
	 * @param   string    $table   The table name into which the data should be inserted
	 * @param   array     $data    An associative array of data to insert
	 * @param   bool      $ignore  Whether or not to perform an insert ignore
	 * @return  bool|int
	 * @since   2.0.0
	 **/
	public function push($table, $data, $ignore = false)
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
	 * @param   string  $table    The table to update
	 * @param   string  $pkField  The table field serving as primary key
	 * @param   mixed   $pkValue  The primary key value
	 * @param   array   $data     The data to update in the database
	 * @return  bool
	 * @since   2.0.0
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
	 * @param   string  $table    The table to update
	 * @param   string  $pkField  The table field serving as primary key
	 * @param   mixed   $pkValue  The primary key value
	 * @return  bool
	 * @since   2.0.0
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
	 * @return  mixed
	 * @since   2.0.0
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
	 * @param   string  $query      The query to perform
	 * @param   string  $structure  The structure of the item(s) returned (if applicable)
	 * @return  mixed
	 * @since   2.0.0
	 **/
	public function query($query, $structure = null)
	{
		// Check the type of query to decide what to return
		list($type) = explode(' ', $query, 2);
		$type       = strtolower($type);

		// Default structure if needed
		if ($type == 'select' && is_null($structure))
		{
			$structure = 'rows';
		}

		$this->connection->prepare($query)->bind($this->syntax->getBindings());

		$result = (isset($structure))
		        ? $this->connection->{constant('self::' . strtoupper($structure))}()
		        : $this->connection->query();

		return $result;
	}

	/**
	 * Retrieves the current query as a string (without executing it)
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function toString()
	{
		return $this->connection
		            ->prepare($this->buildQuery($this->type))
		            ->bind($this->syntax->getBindings())
		            ->toString();
	}

	/**
	 * Retrieves the current query as a string (without executing it)
	 *
	 * @return  string
	 * @since   2.1.9
	 **/
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Builds query based on the current query elements established
	 *
	 * @param   string  $type  The type of query to build
	 * @return  string
	 * @since   2.0.0
	 **/
	private function buildQuery($type = 'select')
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
	 * @return  void
	 * @since   2.0.0
	 **/
	private function reset()
	{
		// Reset the syntax element
		$syntax       = '\\Hubzero\\Database\\Syntax\\' . ucfirst($this->connection->getSyntax());
		$this->syntax = new $syntax($this->connection);
	}
}
