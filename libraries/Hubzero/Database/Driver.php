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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 * @since     Class available since release 2.0.0
 */

namespace Hubzero\Database;

use Hubzero\Console\Event;
use Hubzero\Utility\String;

use \Hubzero\Error\Exception\RuntimeException;
use \Hubzero\Error\Exception\BadMethodCallException;

/**
 * Base database driver
 */
abstract class Driver
{
	/**
	 * The connection instances factory
	 *
	 * @var   array
	 * @since 2.0.0
	 **/
	protected static $instances = [];

	/**
	 * The cumulative query timer (in miliseconds)
	 *
	 * @var   int
	 * @since 2.0.0
	 */
	protected $timer = 0;

	/**
	 * The database connection resource/object
	 *
	 * This will likely be an object for all modern database drivers.
	 *
	 * @var   object|resource
	 * @since 2.0.0
	 */
	protected $connection;

	/**
	 * The incremental count of executed queries
	 *
	 * @var   int
	 * @since 2.0.0
	 */
	protected $count = 0;

	/**
	 * The database connection statement
	 *
	 * For prepared statements, this would the be actual statement class, for non-prepared
	 * statements, this will simply be the last executed or upcoming query.
	 *
	 * @var   object|string
	 * @since 2.0.0
	 */
	protected $statement;

	/**
	 * The internal query log
	 *
	 * For prepared statements, we'll try to interpolate prepared statement into basic strings,
	 * even though this isn't really an accurate representation of the query.
	 *
	 * @var   array
	 * @since 2.0.0
	 */
	protected $log = [];

	/**
	 * The character(s) used to quote items such as table names or field names
	 *
	 * @var   string
	 * @since 2.0.0
	 */
	protected $wrapper = '`%s`';

	/**
	 * The null or zero representation of a timestamp for the database driver
	 *
	 * @var   string
	 * @since 2.0.0
	 */
	protected $nullDate = '0000-00-00 00:00:00';

	/**
	 * The common database table prefix
	 *
	 * @var   string
	 * @since 2.0.0
	 */
	protected $tablePrefix = 'jos_';

	/**
	 * The state of debugging
	 *
	 * @var   bool
	 * @since 2.0.0
	 */
	protected $debug = false;

	/**
	 * The name of the database
	 *
	 * @var   string
	 * @since 2.0.0
	 */
	protected $database;

	/**
	 * Constructs a new object, setting some class properties
	 *
	 * @param array $options list of options used to configure the connection
	 * @since 2.0.0
	 */
	protected function __construct($options)
	{
		$this->tablePrefix = (isset($options['prefix']))   ? $options['prefix']   : $this->tablePrefix;
		$this->database    = (isset($options['database'])) ? $options['database'] : $this->database;
		$this->setUTF();
	}

	/**
	 * Provides alias support for quote() and quoteName()
	 *
	 * @param      string $method the called method name
	 * @param      array  $args   the array of arguments passed to the method
	 * @return     string
	 * @since      2.0.0
	 * @deprecated 2.0.0
	 * @throws     \Hubzero\Error\Exception\BadMethodCallException
	 */
	public function __call($method, $args)
	{
		// We have to have args
		if (empty($args)) return;

		switch ($method)
		{
			case 'q':
				return $this->quote($args[0], isset($args[1]) ? $args[1] : true);
				break;
			case 'nq':
			case 'qn':
				return $this->quoteName($args[0], isset($args[1]) ? $args[1] : null);
				break;
		}

		// This method doesn't exist
		throw new BadMethodCallException("'{$method}' method does not exist.", 500);
	}

	/**
	 * Returns a driver instance based on the given options
	 *
	 * There are three global options and then the rest are specific to the database driver:
	 *  * The 'driver' option defines which driver class is used for the connection -- the default is 'pdo'.
	 *  * The 'database' option determines which database is to be used for the connection.
	 *  * The 'select' option determines whether the connector should automatically select the chosen database.
	 *
	 * @param  array $options parameters to construct the database driver requested
	 * @return static
	 * @since  2.0.0
	 * @throws \Hubzero\Error\Exception\RuntimeException
	 */
	public static function getInstance($options=[])
	{
		// Sanitize the database connector options
		$options['driver']   = (isset($options['driver']))   ? preg_replace('/[^A-Z0-9_\.-]/i', '', $options['driver']) : 'pdo';
		$options['database'] = (isset($options['database'])) ? $options['database']                                     : null;
		$options['select']   = (isset($options['select']))   ? $options['select']                                       : true;

		// Get the options signature for the database connector
		$signature = md5(serialize($options));

		// If we already have a database connector instance for these options, then just use that
		if (!isset(self::$instances[$signature]))
		{
			// Derive the class name from the driver
			$class = __NAMESPACE__ . '\Driver\\' . ucfirst(strtolower($options['driver']));

			// If the class doesn't exist we have a problem
			if (!class_exists($class))
			{
				throw new RuntimeException('Database driver not available.', 500);
			}

			// Set the new connector to the global instances based on signature
			self::$instances[$signature] = new $class($options);
		}

		return self::$instances[$signature];
	}

	/**
	 * Sets the SQL statement string for later execution
	 *
	 * @param  string $query the SQL statement to set
	 * @return $this
	 * @since  2.0.0
	 */
	public function setQuery($query)
	{
		$this->prepare($query);

		return $this;
	}

	/**
	 * Quotes and optionally escape a string to database requirements for insertion into the database
	 *
	 * @param  string $text   the string to quote
	 * @param  bool   $escape true to escape the string, false to leave it unchanged
	 * @return string
	 * @since  2.0.0
	 */
	public function quote($text, $escape=true)
	{
		return '\'' . ($escape ? $this->escape($text) : $text) . '\'';
	}

	/**
	 * Wraps an SQL statement identifier name such as column, table or database names
	 * in quotes to prevent injection risks and reserved word conflicts
	 *
	 * @param  string $name the identifier name to wrap in quotes, supporting dot-notation names
	 * @param  string $as   the AS query part associated to $name
	 * @return string
	 * @since  2.0.0
	 */
	public function quoteName($name, $as=null)
	{
		$parts = (strpos($name, '.') !== false) ? explode('.', $name) : (array)$name;
		$bits  = '';

		foreach ($parts as $part)
		{
			$bits[] = sprintf($this->wrapper, $part);
		}

		// Put back together and add 'AS' clause
		$string  = implode('.', $bits);
		$string .= (isset($as)) ? ' AS ' . sprintf($this->wrapper, $as) : '';

		return $string;
	}

	/**
	 * Quotes table names and columns in the appropriate characters
	 *
	 * The only difference between this and quoteName above is that this
	 * allows you to directly pass in a string already including the AS
	 * statement, and still correctly quote it.
	 *
	 * @param  string $value the table definition
	 * @return string
	 */
	public function wrap($value)
	{
		// Look for an 'AS' identifier first, and make sure not to choke on that
		if (strpos(strtolower($value), ' as ') !== false)
		{
			$parts = explode(' ', $value);

			return $this->wrap($parts[0]) . ' AS ' . $this->wrap($parts[2]);
		}

		$quoted = [];
		$parts  = explode('.', $value);

		foreach ($parts as $part)
		{
			// Make sure it's not an *, which shouldn't be quoted
			$quoted[] = $part !== '*' ? sprintf($this->wrapper, $part) : $part;
		}

		// Put it back together and return
		return implode('.', $quoted);
	}

	/**
	 * Inserts a row into a table based on an object's properties
	 *
	 * @param  string $table   the name of the database table to insert into
	 * @param  object &$object a reference to an object whose public properties match the table fields
	 * @param  string $key     the name of the primary key. If provided the object property is updated
	 * @return bool
	 * @since  2.0.0
	 */
	public function insertObject($table, &$object, $key=null)
	{
		// Initialise some variables
		$fields = [];
		$values = [];

		// Create the base insert statement
		$statement = 'INSERT INTO ' . $this->quoteName($table) . ' (%s) VALUES (%s)';

		// Iterate over the object variables to build the query fields and values
		foreach (get_object_vars($object) as $k => $v)
		{
			// Only process non-null scalars
			if (is_array($v) or is_object($v) or $v === null) continue;

			// Ignore any internal fields
			if ($k[0] == '_') continue;

			// Prepare and sanitize the fields and values for the database query
			$fields[] = $this->quoteName($k);
			$values[] = '?';
			$binds[]  = $v;
		}

		// Set the query and execute the insert
		$this->prepare(sprintf($statement, implode(',', $fields), implode(',', $values)))
		     ->bind($binds);

		if (!$this->execute()) return false;

		// Update the primary key if it exists
		$id = $this->insertid();
		if ($key && $id)
		{
			$object->$key = $id;
		}

		return true;
	}

	/**
	 * Updates a row in a table based on an object's properties
	 *
	 * @param  string $table   the name of the database table to update
	 * @param  object &$object a reference to an object whose public properties match the table fields
	 * @param  string $key     the name of the primary key
	 * @param  bool   $nulls   true to update null fields or false to ignore them
	 * @return bool
	 * @since  2.0.0
	 */
	public function updateObject($table, &$object, $key, $nulls=false)
	{
		// Initialise variables
		$fields = [];
		$where  = '';

		// Create the base update statement
		$statement = 'UPDATE ' . $this->quoteName($table) . ' SET %s WHERE %s';

		// Iterate over the object variables to build the query fields/value pairs
		foreach (get_object_vars($object) as $k => $v)
		{
			// Only process scalars that are not internal fields.
			if (is_array($v) or is_object($v) or $k[0] == '_') continue;

			// Set the primary key to the WHERE clause instead of a field to update
			if ($k == $key)
			{
				$where = $this->quoteName($k) . '=' . $this->quote($v);
				continue;
			}

			// Prepare and sanitize the fields and values for the database query
			if ($v === null)
			{
				// If the value is null and we want to update nulls then set it
				if ($nulls)
				{
					$val = 'NULL';
				}
				// If the value is null and we do not want to update nulls then ignore this field
				else
				{
					continue;
				}
			}
			// The field is not null so we prep it for update
			else
			{
				$val = $this->quote($v);
			}

			// Add the field to be updated
			$fields[] = $this->quoteName($k) . '=' . $val;
		}

		// We don't have any fields to update
		if (empty($fields)) return true;

		// Set the query and execute the update.
		$this->setQuery(sprintf($statement, implode(",", $fields), $where));

		return $this->execute();
	}

	/**
	 * Gets the first row of the result set from the database query
	 * as an associative array of type: ['field_name' => 'row_value']
	 *
	 * @return array|null
	 * @since  2.0.0
	 */
	public function loadAssoc()
	{
		// Initialise variables
		$return = null;

		// Execute the query
		if (!$this->execute()) return null;

		// Get the first row from the result set as an associative array
		if ($row = $this->fetchAssoc())
		{
			$return = $row;
		}

		// Free up system resources and return
		$this->freeResult();

		return $return;
	}

	/**
	 * Gets an array of the result set rows from the database query where each row is an associative array
	 * of ['field_name' => 'row_value']. The array of rows can optionally be keyed by a field name,
	 * but defaults to a sequential numeric array.
	 *
	 * NOTE: Chosing to key the result array by a non-unique field name can result in unwanted
	 * behavior and should be avoided.
	 *
	 * @param  string $key    the name of a field on which to key the result array
	 * @param  string $column instead of the whole row, only this column value will be in the result array
	 * @return array|null
	 * @since  2.0.0
	 */
	public function loadAssocList($key=null, $column=null)
	{
		// Initialise variables
		$array = [];

		// Execute the query
		if (!$this->execute()) return null;

		// Get all of the rows from the result set
		while ($row = $this->fetchAssoc())
		{
			$value = ($column) ? (isset($row[$column]) ? $row[$column] : $row) : $row;
			if ($key)
			{
				$array[$row[$key]] = $value;
			}
			else
			{
				$array[] = $value;
			}
		}

		// Free up system resources and return
		$this->freeResult();

		return $array;
	}

	/**
	 * Gets an array of values from the offset field in each row of the result set from the database query
	 *
	 * @param  int $offset the row offset to use to build the result array
	 * @return array|null
	 * @since  2.0.0
	 */
	public function loadColumn($offset=0)
	{
		// Initialise variables
		$column = [];

		// Execute the query
		if (!$this->execute()) return null;

		// Get all of the rows from the result set as arrays
		while ($row = $this->fetchArray())
		{
			$column[] = $row[$offset];
		}

		// Free up system resources and return
		$this->freeResult();

		return $column;
	}

	/**
	 * Gets the next row in the result set from the database query as an object
	 *
	 * You must call query() or execute() before calling this method, otherwise
	 * you'll have nothing to load.
	 *
	 * @param  string $class the class name to use for the returned row object
	 * @return object|bool
	 * @since  2.0.0
	 */
	public function loadNextObject($class='stdClass')
	{
		// Get the next row from the result set as an object of type $class
		if ($row = $this->fetchObject($class)) return $row;

		// Free up system resources and return
		$this->freeResult();

		return false;
	}

	/**
	 * Gets the next row in the result set from the database query as an array
	 *
	 * You must call query() or execute() before calling this method, otherwise
	 * you'll have nothing to load.
	 *
	 * @return array|bool
	 * @since  2.0.0
	 */
	public function loadNextRow()
	{
		// Get the next row from the result set as an array
		if ($row = $this->fetchArray()) return $row;

		// Free up system resources and return
		$this->freeResult();

		return false;
	}

	/**
	 * Gets the first row of the result set from the database query as an object
	 *
	 * @param  string $class the class name to use for the returned row object
	 * @return object|null
	 * @since  2.0.0
	 */
	public function loadObject($class='stdClass')
	{
		// Initialise variables
		$return = null;

		// Execute the query
		if (!$this->execute()) return null;

		// Get the first row from the result set as an object of type $class
		if ($row = $this->fetchObject($class))
		{
			$return = $row;
		}

		// Free up system resources and return
		$this->freeResult();

		return $return;
	}

	/**
	 * Gets the first field of the first row of the result set from the database query
	 *
	 * @return string|null
	 * @since  2.0.0
	 */
	public function loadResult()
	{
		// Initialise variables
		$return = null;

		// Execute the query
		if (!$this->execute()) return null;

		// Get the first row from the result set as an array
		if ($row = $this->fetchArray())
		{
			$return = $row[0];
		}

		// Free up system resources and return
		$this->freeResult();

		return $return;
	}

	/**
	 * Gets the first row of the result set from the database query as an array
	 *
	 * @return array|null
	 * @since  2.0.0
	 */
	public function loadRow()
	{
		// Initialise variables
		$return = null;

		// Execute the query
		if (!$this->execute()) return null;

		// Get the first row from the result set as an array
		if ($row = $this->fetchArray())
		{
			$return = $row;
		}

		// Free up system resources and return
		$this->freeResult();

		return $return;
	}

	/**
	 * Gets an array of the result set rows from the database query where each row is an array.  The array
	 * of objects can optionally be keyed by a field offset, but defaults to a sequential numeric array.
	 *
	 * NOTE: Choosing to key the result array by a non-unique field can result in unwanted
	 * behavior and should be avoided.
	 *
	 * @param  string $key the name of a field on which to key the result array
	 * @return array|null
	 * @since  2.0.0
	 */
	public function loadRowList($key=null)
	{
		// Initialise variables
		$rows = [];

		// Execute the query
		if (!$this->execute()) return null;

		// Get all of the rows from the result set as arrays
		while ($row = $this->fetchArray())
		{
			if ($key !== null)
			{
				$rows[$row[$key]] = $row;
			}
			else
			{
				$rows[] = $row;
			}
		}

		// Free up system resources and return
		$this->freeResult();

		return $rows;
	}

	/**
	 * Gets an array of the result set rows from the database query where each row is an object.
	 * The array of objects can optionally be keyed by a field name, but defaults to a sequential numeric array.
	 *
	 * NOTE: Choosing to key the result array by a non-unique field name can result in unwanted
	 * behavior and should be avoided.
	 *
	 * @param  string $key   the name of the field on which to key the result array
	 * @param  string $class the class name to use for the returned row objects
	 * @return array
	 * @since  2.0.0
	 */
	public function loadObjectList($key='', $class='stdClass')
	{
		$rows = [];

		// Execute the query
		if (!$this->execute()) return null;

		// Get all of the rows from the result set as objects of type $class
		while ($row = $this->fetchObject($class))
		{
			if ($key)
			{
				$rows[$row->$key] = $row;
			}
			else
			{
				$rows[] = $row;
			}
		}

		// Free up system resources and return the rows
		$this->freeResult();

		return $rows;
	}

	/**
	 * Get a list of available database connectors.  The list will only be populated with connectors that both
	 * the class exists and the static test method returns true.  This gives us the ability to have a multitude
	 * of connector classes that are self-aware as to whether or not they are able to be used on a given system.
	 *
	 * @return array
	 * @since  2.0.0
	 */
	public static function getConnectors()
	{
		// Instantiate variables
		$connectors = [];

		// Get a list of types, only including php files
		$types = \JFolder::files(dirname(__FILE__) . DS . 'Driver', '.*\.php$');

		// Loop through the types and find the ones that are available
		foreach ($types as $type)
		{
			// Derive the class name from the type
			$class = __NAMESPACE__ . '\\Driver\\' . str_ireplace('.php', '', ucfirst(trim($type)));

			// If the class doesn't exist...these are not the droids you're looking for...
			if (!class_exists($class)) continue;

			// Our class exists, so now we just need to know if it passes it's test method
			if (call_user_func_array(array($class, 'test'), array()))
			{
				// Connector names should not have file extensions
				$connectors[] = str_ireplace('.php', '', $type);
			}
		}

		return $connectors;
	}

	/**
	 * Replaces a string placeholder with the string held in the class variable
	 *
	 * @param  string $sql    the SQL statement to prepare
	 * @param  string $prefix the common table prefix
	 * @return string
	 * @since  2.0.0
	 */
	public function replacePrefix($sql, $prefix='#__')
	{
		// As we replace strings of different lengths, subsequent prefix positions will become invalid.
		// Thus, we track that differential here to account for the shifting locations
		$differential = strlen($this->tablePrefix) - strlen($prefix);
		$count        = 0;

		foreach (String::findLiteral($prefix, $sql) as $prefixPosition)
		{
			$sql = substr_replace($sql, $this->tablePrefix, $prefixPosition + ($differential*$count), strlen($prefix));
			$count++;
		}

		return $sql;
	}

	/**
	 * Splits a string of multiple queries into an array of individual queries
	 *
	 * @param  string $sql input SQL string from which to split into individual queries
	 * @return array
	 * @since  2.0.0
	 */
	public static function splitSql($sql)
	{
		$start   = 0;
		$length  = strlen($sql);
		$queries = [];

		foreach (String::findLiteral(';', $sql) as $queryEndPosition)
		{
			$queries[] = trim(substr($sql, $start, $queryEndPosition - $start + 1));
			$start     = $queryEndPosition + 1;
		}

		// Grab the last query in case it doesn't end with a ';'
		if ($end = trim(substr($sql, $start)))
		{
			$queries[] = $end;
		}

		return $queries;
	}

	/**
	 * Logs the current sql statement
	 *
	 * @return $this
	 * @since  2.0.0
	 **/
	protected function log($time)
	{
		// Build the actual query
		if (is_object($this->statement))
		{
			$query = $this->interpolate($this->statement->queryString, $this->bindings);
		}
		else
		{
			$query = $this->statement;
		}

		Event::fire('database.query', $query, $time);

		// Add it to the internal logs
		$this->log[] = $query;
		$this->count++;
		$this->timer += $time;
	}

	/**
	 * Builds a string version of the prepared statement
	 *
	 * @return string
	 * @since  2.0.0
	 **/
	private function interpolate($query, $bindings)
	{
		$offset = 0;
		$index  = 0;

		foreach (String::findLiteral('?', $query) as $placeholder)
		{
			$sub     = $this->quote($bindings[$index]);
			$query   = substr_replace($query, $sub, $placeholder + $offset, 1);
			$offset += (strlen($sub) - 1);
			$index++;
		}

		return $query;
	}

	/**
	 * Executes the SQL statement (basically an alias for execute())
	 *
	 * @return static
	 * @since  2.0.0
	 */
	public function query()
	{
		return $this->execute();
	}

	/**
	 * Sets the database debugging state for the driver
	 *
	 * @param  bool $level true to enable debugging
	 * @return $this
	 * @since  2.0.0
	 */
	public function setDebug($level)
	{
		$this->debug = (bool) $level;

		return $this;
	}

	/**
	 * Enables debugging
	 *
	 * @return $this
	 * @since  2.0.0
	 */
	public function enableDebugging()
	{
		return $this->setDebug(true);
	}

	/**
	 * Disables debugging
	 *
	 * @return $this
	 * @since  2.0.0
	 */
	public function disableDebugging()
	{
		return $this->setDebug(false);
	}

	/**
	 * Truncates a table
	 *
	 * @param  string $table the table to truncate
	 * @return void
	 * @since  2.0.0
	 */
	public function truncateTable($table)
	{
		$this->setQuery('TRUNCATE TABLE ' . $this->quoteName($table));
		$this->execute();
	}

	/**
	 * Grabs the underlying database connection
	 *
	 * Useful for when you need to call a proprietary method such as postgresql's lo_* methods.
	 *
	 * @return mixed
	 * @since  2.0.0
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Gets the total number of SQL statements executed by the database driver
	 *
	 * @return int
	 * @since  2.0.0
	 */
	public function getCount()
	{
		return $this->count;
	}

	/**
	 * Gets the name of the database in use by this connection
	 *
	 * @return string
	 * @since  2.0.0
	 */
	protected function getDatabase()
	{
		return $this->database;
	}

	/**
	 * Returns a PHP date() function compliant date format for the database driver
	 *
	 * @return string
	 * @since  2.0.0
	 */
	public function getDateFormat()
	{
		return 'Y-m-d H:i:s';
	}

	/**
	 * Gets the database driver SQL statement log
	 *
	 * @return array
	 * @since  2.0.0
	 */
	public function getLog()
	{
		return $this->log;
	}

	/**
	 * Gets the null or zero representation of a timestamp for the database driver
	 *
	 * @return string
	 * @since  2.0.0
	 */
	public function getNullDate()
	{
		return $this->nullDate;
	}

	/**
	 * Gets the common table prefix for the database driver
	 *
	 * @return string
	 * @since  2.0.0
	 */
	public function getPrefix()
	{
		return $this->tablePrefix;
	}

	/**
	 * Gets the current sql statement
	 *
	 * @return string
	 * @since  2.0.0
	 */
	public function getStatement()
	{
		return (is_object($this->statement))
				? $this->interpolate($this->statement->queryString, $this->bindings)
				: $this->statement;
	}

	/**
	 * Fetchs a row from the result set as an object
	 *
	 * @param  string $class the class name to use for the returned row object
	 * @return mixed
	 * @since  2.0.0
	 */
	abstract protected function fetchObject($class='stdClass');

	/**
	 * Fetches a row from the result set as an array
	 *
	 * @return mixed
	 * @since  2.0.0
	 */
	abstract protected function fetchArray();

	/**
	 * Fetches a row from the result set as an associative array
	 *
	 * @return mixed
	 * @since  2.0.0
	 */
	abstract protected function fetchAssoc();

	/**
	 * Frees up the memory used for the result set
	 *
	 * @return $this
	 * @since  2.0.0
	 */
	abstract protected function freeResult();

	/**
	 * Sets the error reporting mode to throw exceptions
	 *
	 * @return $this
	 * @since  2.0.0
	 **/
	abstract public function throwExceptions();

	/**
	 * Sets the error reporting mode to return errors
	 *
	 * @return $this
	 * @since  2.0.0
	 **/
	abstract public function returnErrors();

	/**
	 * Checks for a database connection, throwing an exception if not
	 *
	 * @return $this
	 * @since  2.0.0
	 **/
	abstract public function hasConnectionOrFail();

	/**
	 * Prepares a query for binding
	 *
	 * @return $this
	 * @since  2.0.0
	 **/
	abstract public function prepare($statement);

	/**
	 * Binds the given bindings to the prepared statement
	 *
	 * @param  array  $bindings the param bindings
	 * @param  string $type     the param type
	 * @return $this
	 * @since  2.0.0
	 **/
	abstract public function bind($bindings, $type=null);

	/**
	 * Gets the auto-incremented value from the last INSERT statement
	 *
	 * @return int
	 * @since  2.0.0
	 */
	abstract public function insertid();

	/**
	 * Sets the connection to use UTF-8 character encoding
	 *
	 * @return bool
	 * @since  2.0.0
	 */
	abstract public function setUTF();

	/**
	 * Initializes a transaction
	 *
	 * @return void
	 * @since  2.0.0
	 */
	abstract public function transactionStart();

	/**
	 * Rolls back a transaction
	 *
	 * @return void
	 * @since  2.0.0
	 */
	abstract public function transactionRollback();

	/**
	 * Commits a transaction
	 *
	 * @return void
	 * @since  2.0.0
	 */
	abstract public function transactionCommit();

	/**
	 * Unlocks all tables in the database
	 *
	 * @return $this
	 * @since  2.0.0
	 */
	abstract public function unlockTables();

	/**
	 * Locks a table in the database
	 *
	 * @param  string $tableName the name of the table to lock
	 * @return $this
	 * @since  2.0.0
	 */
	abstract public function lockTable($tableName);

	/**
	 * Executes the set SQL statement
	 *
	 * @return $this|bool
	 * @since  2.0.0
	 */
	abstract public function execute();

	/**
	 * Renames a table in the database
	 *
	 * @param  string $oldTable the name of the table to be renamed
	 * @param  string $newTable the new name for the table
	 * @param  string $backup   table prefix
	 * @param  string $prefix   for the table - used to rename constraints in non-mysql databases
	 * @return $this
	 * @since  2.0.0
	 */
	abstract public function renameTable($oldTable, $newTable, $backup=null, $prefix=null);

	/**
	 * Selects a database for use
	 *
	 * @param  string $database the name of the database to select for use
	 * @return bool
	 * @since  2.0.0
	 */
	abstract public function select($database);

	/**
	 * Gets a new query for the current driver
	 *
	 * @return Query
	 * @since  2.0.0
	 */
	abstract public function getQuery();

	/**
	 * Retrieves field information about the given tables
	 *
	 * @param  string $table    the name of the database table
	 * @param  bool   $typeOnly true (default) to only return field types
	 * @return array
	 * @since  2.0.0
	 */
	abstract public function getTableColumns($table, $typeOnly=true);

	/**
	 * Shows the table CREATE statement that creates the given tables
	 *
	 * @param  string|array $tables a table name or a list of table names
	 * @return array
	 * @since  2.0.0
	 */
	abstract public function getTableCreate($tables);

	/**
	 * Retrieves key information about the given tables
	 *
	 * @param  string|array $tables a table name or a list of table names
	 * @return array
	 * @since  2.0.0
	 */
	abstract public function getTableKeys($tables);

	/**
	 * Gets an array of all tables in the database
	 *
	 * @return array
	 * @since  2.0.0
	 */
	abstract public function getTableList();

	/**
	 * Gets the version of the database connector
	 *
	 * @return string
	 * @since  2.0.0
	 */
	abstract public function getVersion();

	/**
	 * Determines if the connection to the server is active
	 *
	 * @return boolean
	 * @since  2.0.0
	 */
	abstract public function connected();

	/**
	 * Drops a table from the database
	 *
	 * @param  string $table    the name of the database table to drop
	 * @param  boole  $ifExists optionally specify that the table must exist before it is dropped
	 * @return $this
	 * @since  2.0.0
	 */
	abstract public function dropTable($table, $ifExists=true);

	/**
	 * Escapes a string for usage in an SQL statement
	 *
	 * @param  string  $text  the string to be escaped
	 * @param  boolean $extra optional parameter to provide extra escaping
	 * @return string
	 * @since  2.0.0
	 */
	abstract public function escape($text, $extra=false);

	/**
	 * Gets the number of affected rows for the previous executed SQL statement
	 *
	 * @return int
	 * @since  2.0.0
	 */
	abstract public function getAffectedRows();

	/**
	 * Gets the database collation in use by sampling a text field of a table in the database
	 *
	 * @return string|bool
	 * @since  2.0.0
	 */
	abstract public function getCollation();

	/**
	 * Grabs the number of returned rows for the previous executed SQL statement
	 *
	 * @return int
	 * @since  2.0.0
	 */
	abstract public function getNumRows();

	/**
	 * Checks for the existance of a table
	 *
	 * @param  string $table the table we're looking for
	 * @return bool
	 * @since  2.0.0
	 */
	abstract public function tableExists($table);

	/**
	 * Returns whether or not the given table has a given field
	 *
	 * @param  string $table a table name
	 * @param  string $field a field name
	 * @return bool
	 * @since  2.0.0
	 */
	abstract public function tableHasField($table, $field);

	/**
	 * Returns whether or not the given table has a given key
	 *
	 * @param  string $table a table name
	 * @param  string $key   a key name
	 * @return bool
	 * @since  2.0.0
	 */
	abstract public function tableHaskey($table, $key);

	/**
	 * Gets the primary key of a table
	 *
	 * @return string
	 * @since  2.0.0
	 **/
	abstract public function getPrimaryKey($table);

	/**
	 * Gets the database engine of the given table
	 *
	 * @param  string $table the table for which to retrieve the engine type
	 * @return string|bool
	 * @since  2.0.0
	 **/
	abstract public function getEngine($table);

	/**
	 * Gets the database character set of the given table
	 *
	 * @param  string $table the table for which to retrieve the character set
	 * @param  string $field the field to check (optional)
	 * @return string|bool
	 * @since  2.0.0
	 **/
	abstract public function getCharacterSet($table, $field=null);

	/**
	 * Gets the auto-increment value for the given table
	 *
	 * @param  string $table the table for which to retrieve the character set
	 * @return int|bool
	 * @since  2.0.0
	 **/
	abstract public function getAutoIncrement($table);
}