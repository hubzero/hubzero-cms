<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Driver;

use Hubzero\Database\Driver;
use Hubzero\Database\Query;
use Hubzero\Database\Exception\ConnectionFailedException;
use Hubzero\Database\Exception\QueryFailedException;
use Hubzero\Database\Exception\UnsupportedEngineException;

/**
 * Pdo database driver
 *
 * @FIXME: can we get rid of mysql-specific syntax and only have driver calls here?
 */
class Pdo extends Driver
{
	/**
	 * Constructs a new database object based on the given params
	 *
	 * @param   array  $options  The database connection params
	 * @return  void
	 * @since   2.0.0
	 */
	public function __construct($options)
	{
		// Make sure the pdo extension for PHP is installed and enabled
		if (!class_exists('PDO'))
		{
			throw new ConnectionFailedException('PDO does not appear to be installed or enabled.', 500);
		}

		// Try to connect
		try
		{
			// Make sure the DSN is set
			if (!isset($options['dsn']) || !$options['dsn'])
			{
				throw new ConnectionFailedException('DSN for PDO connection not set.', 500);
			}

			// Make sure extra PDO options array is set
			if (!isset($options['extras']))
			{
				$options['extras'] = [];
			}

			// Establish connection string
			$this->setConnection(new \PDO(
				(string)$options['dsn'],
				(string)$options['user'],
				(string)$options['password'],
				(array)$options['extras']
			));
		}
		catch (\PDOException $e)
		{
			throw new ConnectionFailedException($e->getMessage(), 500);
		}

		// Set error reporting to throw exceptions
		$this->throwExceptions();

		// Call parent construct
		parent::__construct($options);

		// @FIXME: Set sql_mode to non_strict mode?
	}

	/**
	 * Sets the error reporting mode to throw exceptions
	 *
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function throwExceptions()
	{
		$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

		return $this;
	}

	/**
	 * Sets the error reporting mode to return errors
	 *
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function returnErrors()
	{
		// Even though this says "SILENT", that doesn't mean that it isn't registering errors
		$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);

		return $this;
	}

	/**
	 * Checks for a database connection
	 *
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function hasConnectionOrFail()
	{
		if (!is_object($this->connection))
		{
			throw new ConnectionFailedException('No database connection.', 500);
		}

		return $this;
	}

	/**
	 * Prepares a query for binding
	 *
	 * @param   string  $statement  The statement to prepare
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function prepare($statement)
	{
		$this->statement = $this->connection->prepare($this->replacePrefix($statement));

		return $this;
	}

	/**
	 * Binds the given bindings to the prepared statement
	 *
	 * If you're going to pass in types, they must be keyed
	 * the same as the bindings.
	 *
	 * @param   array  $bindings  The param bindings
	 * @param   array  $type      The param types
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function bind($bindings, $type = [])
	{
		$idx = 1;

		$this->bindings = $bindings;

		foreach ($bindings as $binding)
		{
			// We use bindValue here because that allows us to pass in plain old strings
			$this->statement->bindValue(
				$idx,
				$binding,
				isset($type[$idx]) ? $this->translateType($type[$idx]) : $this->inferType($binding)
			);

			$idx++;
		}

		return $this;
	}

	/**
	 * Explicitly translate generic type to driver specific types
	 *
	 * @param   string  $type  The variable type (bool, null, int, str)
	 * @return  int
	 * @since   2.0.0
	 **/
	private function translateType($type)
	{
		return constant('\PDO::PARAM_' . strtoupper($type));
	}

	/**
	 * Infers the variable type from the variable itself
	 *
	 * Some sql syntax is more particular about type than others.
	 *
	 * @param   mixed  $binding  The binding to infer from
	 * @return  int
	 * @since   2.0.0
	 **/
	private function inferType($binding)
	{
		if (is_bool($binding))
		{
			$type = \PDO::PARAM_BOOL;
		}
		elseif (is_null($binding))
		{
			$type = \PDO::PARAM_NULL;
		}
		elseif (is_int($binding))
		{
			$type = \PDO::PARAM_INT;
		}
		else
		{
			$type = \PDO::PARAM_STR;
		}

		return $type;
	}

	/**
	 * Executes the SQL statement
	 *
	 * @return  $this
	 * @since   2.0.0
	 * @throws  QueryFailedException
	 */
	public function execute()
	{
		// Check connection
		$this->hasConnectionOrFail();

		// Capture the start time
		$start = microtime(true);

		// Execute the query
		try
		{
			$this->statement->execute();
		}
		catch (\PDOException $e)
		{
			// @FIXME: this should honor error reporting settings
			throw new QueryFailedException($e->getMessage(), 500, $e);
		}

		if ($this->debug)
		{
			// Log it
			$this->log(microtime(true) - $start);
		}

		return $this;
	}

	/**
	 * Fetches a row from the result set cursor as an object
	 *
	 * @param   string       $class  The class name to use for the returned row object
	 * @return  object|null
	 * @since   2.0.0
	 */
	protected function fetchObject($class = 'stdClass')
	{
		return $this->statement->fetchObject($class);
	}

	/**
	 * Fetches a row from the result set as an array
	 *
	 * @return  mixed
	 * @since   2.0.0
	 */
	protected function fetchArray()
	{
		return $this->statement->fetch(\PDO::FETCH_NUM);
	}

	/**
	 * Fetches a row from the result set as an associative array
	 *
	 * @return  mixed
	 * @since   2.0.0
	 */
	protected function fetchAssoc()
	{
		return $this->statement->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Gets the auto-incremented value from the last INSERT statement
	 *
	 * @return  int
	 * @since   2.0.0
	 */
	public function insertid()
	{
		return $this->connection->lastInsertId();
	}

	/**
	 * Frees up the memory used for the result set
	 *
	 * @return  $this
	 * @since   2.0.0
	 */
	protected function freeResult()
	{
		$this->statement->closeCursor();

		return $this;
	}

	/**
	 * Drops a table from the database
	 *
	 * @param   string   $tableName  The name of the database table to drop
	 * @param   boolean  $ifExists   Optionally specify that the table must exist before it is dropped
	 * @return  $this
	 * @since   2.0.0
	 */
	public function dropTable($tableName, $ifExists = true)
	{
		$this->setQuery('DROP TABLE ' . ($ifExists ? 'IF EXISTS ' : '') . $this->quoteName($tableName))
		     ->execute();

		return $this;
	}

	/**
	 * Gets the database collation in use
	 *
	 * @return  string|bool
	 * @since   2.0.0
	 */
	public function getCollation()
	{
		// Attempt to get the database collation by accessing the server system variable.
		$this->setQuery('SHOW VARIABLES LIKE "collation_database"');
		$result = $this->loadObject();

		if (property_exists($result, 'Value'))
		{
			return $result->Value;
		}

		return false;
	}

	/**
	 * Shows the table CREATE statement that creates the given tables
	 *
	 * @param   string|array  $tables  A table name or a list of table names
	 * @return  array
	 * @since   2.0.0
	 */
	public function getTableCreate($tables)
	{
		// Initialise variables
		$result = [];

		foreach ((array)$tables as $table)
		{
			// Set the query to get the table CREATE statement.
			$this->setQuery('SHOW CREATE table ' . $this->quoteName($this->escape($table)));
			$row = $this->loadRow();

			// Populate the result array based on the create statements
			$result[$table] = $row[1];
		}

		return $result;
	}

	/**
	 * Retrieves field information about the given table
	 *
	 * @param   string  $table     The name of the database table
	 * @param   bool    $typeOnly  True (default) to only return field types
	 * @return  array
	 * @since   2.0.0
	 */
	public function getTableColumns($table, $typeOnly = true)
	{
		$result = [];

		// Set the query to get the table fields statement
		$this->setQuery('SHOW FULL COLUMNS FROM ' . $this->quoteName($this->escape($table)));
		$fields = $this->loadObjectList();

		// If we only want the type as the value add just that to the list
		if ($typeOnly)
		{
			foreach ($fields as $field)
			{
				$result[$field->Field] = preg_replace("/[(0-9)]/", '', $field->Type);
			}
		}
		// If we want the whole field data object add that to the list
		else
		{
			foreach ($fields as $field)
			{
				$result[$field->Field] = $field;
			}
		}

		return $result;
	}

	/**
	 * Retrieves key information about the given tables
	 *
	 * @param   string|array  $tables  A table name or a list of table names
	 * @return  array
	 * @since   2.0.0
	 */
	public function getTableKeys($table)
	{
		// Get the details columns information
		$this->setQuery('SHOW KEYS FROM ' . $this->quoteName($table));
		$keys = $this->loadObjectList('Key_name');

		return $keys;
	}

	/**
	 * Gets an array of all tables in the database
	 *
	 * @return  array
	 * @since   2.0.0
	 */
	public function getTableList()
	{
		// Set the query to get the tables statement
		$this->setQuery('SHOW TABLES');
		$tables = $this->loadColumn();

		return $tables;
	}

	/**
	 * Locks a table in the database
	 *
	 * @param   string  $tableName  The name of the table to lock
	 * @return  $this
	 * @since   2.0.0
	 */
	public function lockTable($table)
	{
		$this->setQuery('LOCK TABLES ' . $this->quoteName($table) . ' WRITE')->execute();

		return $this;
	}

	/**
	 * Renames a table in the database
	 *
	 * @param   string  $oldTable  The name of the table to be renamed
	 * @param   string  $newTable  The new name for the table
	 * @param   string  $backup    Table prefix
	 * @param   string  $prefix    For the table - used to rename constraints in non-mysql databases
	 * @return  $this
	 * @since   2.0.0
	 */
	public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
	{
		$this->setQuery('RENAME TABLE ' . $oldTable . ' TO ' . $newTable)->execute();

		return $this;
	}

	/**
	 * Commits a transaction
	 *
	 * @return  void
	 * @since   2.0.0
	 */
	public function transactionCommit()
	{
		$this->setQuery('COMMIT')->execute();
	}

	/**
	 * Rolls back a transaction
	 *
	 * @return  void
	 * @since   2.0.0
	 */
	public function transactionRollback()
	{
		$this->setQuery('ROLLBACK')->execute();
	}

	/**
	 * Initializes a transaction
	 *
	 * @return  void
	 * @since   2.0.0
	 */
	public function transactionStart()
	{
		$this->setQuery('START TRANSACTION')->execute();
	}

	/**
	 * Unlocks all tables in the database
	 *
	 * @return  $this
	 * @since   2.0.0
	 */
	public function unlockTables()
	{
		$this->setQuery('UNLOCK TABLES')->execute();

		return $this;
	}

	/**
	 * Checks for the existance of a table
	 *
	 * @param   string  $table  The table we're looking for
	 * @return  bool
	 * @since   2.0.0
	 */
	public function tableExists($table)
	{
		$query = 'SHOW TABLES LIKE ' . str_replace('#__', $this->tablePrefix, $this->quote($table, false));
		$this->setQuery($query)->execute();

		return ($this->getAffectedRows() > 0) ? true : false;
	}

	/**
	 * Returns whether or not the given table has a given field
	 *
	 * @param   string  $table  A table name
	 * @param   string  $field  A field name
	 * @return  bool
	 * @since   2.0.0
	 */
	public function tableHasField($table, $field)
	{
		$this->setQuery('SHOW FIELDS FROM ' . $table);
		$fields = $this->loadObjectList('Field');

		if (!is_array($fields))
		{
			return false;
		}

		return (in_array($field, array_keys($fields))) ? true : false;
	}

	/**
	 * Returns whether or not the given table has a given key
	 *
	 * @param   string  $table  A table name
	 * @param   string  $key    A key name
	 * @return  bool
	 * @since   2.0.0
	 */
	public function tableHaskey($table, $key)
	{
		$keys = $this->getTableKeys($table);

		if (!is_array($keys))
		{
			return false;
		}

		return isset($keys[$key]);
	}

	/**
	 * Gets the primary key of a table
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function getPrimaryKey($table)
	{
		$keys = $this->getTableKeys($table);
		$key  = false;

		if ($keys && count($keys) > 0)
		{
			foreach ($keys as $k)
			{
				if ($k->Key_name == 'PRIMARY')
				{
					$key = $k->Column_name;
				}
			}
		}

		return $key;
	}

	/**
	 * Gets the database engine of the given table
	 *
	 * @param   string       $table  The table for which to retrieve the engine type
	 * @return  string|bool
	 * @since   2.0.0
	 **/
	public function getEngine($table)
	{
		$this->setQuery('SHOW TABLE STATUS WHERE Name = ' . str_replace('#__', $this->tablePrefix, $this->quote($table, false)));

		return ($info = $this->loadObjectList()) ? $info[0]->Engine : false;
	}

	/**
	 * Set the database engine of the given table
	 *
	 * @param   string  $table   The table for which to retrieve the engine type
	 * @param   string  $engine  The engine type to set
	 * @return  bool
	 * @since   2.2.15
	 **/
	public function setEngine($table, $engine)
	{
		$supported = ['innodb', 'myisam', 'archive', 'merge', 'memory', 'csv', 'federated'];

		if (!in_array(strtolower($engine), $supported))
		{
			throw new UnsupportedEngineException(sprintf(
				'Unsupported engine type of "%s" specified. Engine type must be one of %s',
				$engine,
				implode(', ', $supported)
			));
		}

		$this->setQuery('ALTER TABLE ' . str_replace('#__', $this->tablePrefix, $this->quote($table, false)) . " ENGINE = " . $this->quote($engine));

		return $this->db->query();
	}

	/**
	 * Gets the database character set of the given table
	 *
	 * @param   string       $table  The table for which to retrieve the character set
	 * @param   string       $field  The field to check (optional)
	 * @return  string|bool
	 * @since   2.0.0
	 **/
	public function getCharacterSet($table, $field = null)
	{
		$create = $this->getTableCreate($table);

		if (isset($field))
		{
			preg_match('/' . $this->quoteName($field) . ' [[:alnum:]\(\)]* CHARACTER SET ([[:alnum:]]*)/', $create[$table], $matches);
		}
		else
		{
			preg_match('/CHARSET=([[:alnum:]]*)/', $create[$table], $matches);
		}

		return (isset($matches[1])) ? $matches[1] : false;
	}

	/**
	 * Gets the auto-increment value for the given table
	 *
	 * @param   string    $table  The table for which to retrieve the character set
	 * @return  int|bool
	 * @since   2.0.0
	 **/
	public function getAutoIncrement($table)
	{
		$create = $this->getTableCreate($table);

		preg_match('/AUTO_INCREMENT=([0-9]*)/', $create[$table], $matches);

		return (isset($matches[1])) ? $matches[1] : false;
	}

	/**
	 * Escapes a string for usage in an SQL statement
	 *
	 * In PDO, the quote method does both escaping and quoting, thus calls
	 * coming from the quote method need to have the leading and trailing
	 * quotes removed...otherwise it will be double-quoted.
	 *
	 * @FIXME: if escape is called directly, we shouldn't remove first and last char
	 *
	 * @param   string  $text   The string to be escaped
	 * @param   bool    $extra  Optional parameter to provide extra escaping
	 * @return  string
	 * @since   2.0.0
	 */
	public function escape($text, $extra = false)
	{
		$result = substr($this->connection->quote($text), 1, -1);

		if ($extra)
		{
			$result = addcslashes($result, '%_');
		}

		return $result;
	}

	/**
	 * Test to see if the PDO connector is available.
	 *
	 * @return  bool
	 * @since   2.0.0
	 */
	public static function test()
	{
		return (class_exists('\PDO'));
	}

	/**
	 * Determines if the connection to the server is active
	 *
	 * @return  bool
	 * @since   2.0.0
	 */
	public function connected()
	{
		if (is_object($this->connection))
		{
			return $this->connection->query("SELECT 1")->fetchAll()[0][1];
		}

		return false;
	}

	/**
	 * Gets the number of affected rows for the previous executed SQL statement
	 *
	 * @return  int
	 * @since   2.0.0
	 */
	public function getAffectedRows()
	{
		return $this->statement->rowCount();
	}

	/**
	 * Gets a new query for the current driver
	 *
	 * @param   bool   $legacy  Whether or not to return new query builder or legacy builder
	 * @return  Query
	 * @since   2.0.0
	 */
	public function getQuery($legacy = false)
	{
		if ($legacy)
		{
			return new \JDatabaseQueryPDOMySQL($this);
		}

		return new Query($this);
	}

	/**
	 * Gets the version of the database connector
	 *
	 * @return  string
	 * @since   2.0.0
	 */
	public function getVersion()
	{
		return $this->connection->query("SHOW VARIABLES LIKE '%version%'")->fetchAll()[3]['Value'];
	}

	/**
	 * Selects a database for use
	 *
	 * @param   string  $database  The name of the database to select for use
	 * @return  bool
	 * @since   2.0.0
	 */
	public function select($database)
	{
		if (empty($database))
		{
			return false;
		}

		$this->connection->exec('USE ' . $this->quoteName($database));

		$this->database = $database;

		return true;
	}

	/**
	 * Sets the connection to use UTF-8 character encoding
	 *
	 * This is already happening in the initial database connection for PDO.
	 *
	 * @return  bool
	 * @since   2.0.0
	 */
	public function setUTF()
	{
		return false;
	}

	/**
	 * Grabs the number of returned rows for the previous executed SQL statement
	 *
	 * @return  int
	 * @since   2.0.0
	 */
	public function getNumRows()
	{
		// @FIXME: this isn't guaranteed to work on select statements in mysql
		return $this->statement->rowCount();
	}

	/**
	 * Detects the driver syntax
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	protected function detectSyntax()
	{
		return $this->connection->getAttribute(\PDO::ATTR_DRIVER_NAME);
	}
}
