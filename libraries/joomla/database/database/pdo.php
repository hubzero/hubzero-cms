<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JDatabaseQueryPDOMySQL', dirname(__FILE__) . '/pdomysqlquery.php');
JLoader::register('JDatabaseExporterPDOMySQL', dirname(__FILE__) . '/pdomysqlexporter.php');
JLoader::register('JDatabaseImporterPDOMySQL', dirname(__FILE__) . '/pdomysqlimporter.php');

/**
 * PDO database driver
 *
 * @package     Joomla.Platform
 * @subpackage  Database
 * @see         http://dev.mysql.com/doc/
 * @since       11.1
 */
class JDatabasePDO extends JDatabase
{
	var $timer = 0;

	/**
	 * The name of the database driver.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $name = 'pdo';

	/**
	 * The character(s) used to quote SQL statement names such as table names or field names,
	 * etc. The child classes should define this as necessary.  If a single character string the
	 * same character is used for both sides of the quoted name, else the first character will be
	 * used for the opening quote and the second for the closing quote.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $nameQuote = '`';

	/**
	 * The null or zero representation of a timestamp for the database driver.  This should be
	 * defined in child classes to hold the appropriate value for the engine.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $nullDate = '0000-00-00 00:00:00';

	/**
	 * @var    string  The minimum supported database version.
	 * @since  12.1
	 */
	protected $dbMinimum = '5.0.4';

	/**
	 * Constructor.
	 *
	 * @param   array  $options  Array of database options with keys: host, user, password, database, select.
	 *
	 * @since   11.1
	 */
	protected function __construct($options)
	{
		// Get some basic values from the options.
		$options['host'] = (isset($options['host'])) ? $options['host'] : 'localhost';
		$options['user'] = (isset($options['user'])) ? $options['user'] : 'root';
		$options['password'] = (isset($options['password'])) ? $options['password'] : '';
		$options['database'] = (isset($options['database'])) ? $options['database'] : '';
		$options['select'] = (isset($options['select'])) ? (bool) $options['select'] : true;

		// Make sure the PDO MySQL extension for PHP is installed and enabled.
		if (!class_exists( 'PDO' )) 
		{

			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  12.1
			if (JError::$legacy)
			{
				$this->errorNum = 1;
				$this->errorMsg = 'The PDO adapter "pdo" is not available.';
				return;
			}
			else
			{
				throw new JDatabaseException('The PDO adapter "pdo" is not available.');
			}
		}

		// Attempt to connect to the server.
		if (!($this->connection = new PDO("mysql:host=${options['host']}", $options['user'], $options['password']))) 
		{

			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  12.1
			if (JError::$legacy)
			{
				$this->errorNum = 2;
				$this->errorMsg = JText::_('JLIB_DATABASE_ERROR_CONNECT_MYSQL');
				return;
			}
			else
			{
				throw new JDatabaseException(JText::_('JLIB_DATABASE_ERROR_CONNECT_MYSQL'));
			}
		}

		$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// Finalize initialisation
		parent::__construct($options);

		// Set sql_mode to non_strict mode
		// mysql_query("SET @@SESSION.sql_mode = '';", $this->connection);

		// If auto-select is enabled select the given database.
		if ($options['select'] && !empty($options['database']))
		{
			$this->select($options['database']);
		}
	}

	/**
	 * Destructor.
	 *
	 * @since   11.1
	 */
	public function __destruct()
	{
		if (is_object($this->connection))
		{
			$this->connection = null;
		}
	}

	/**
	 * Method to escape a string for usage in an SQL statement.
	 *
	 * @param   string   $text   The string to be escaped.
	 * @param   boolean  $extra  Optional parameter to provide extra escaping.
	 *
	 * @return  string  The escaped string.
	 *
	 * @since   11.1
	 */
	public function escape($text, $extra = false)
	{
		$result = $this->connection->quote($text);

		$result = substr($result,1,-1);

		if ($extra)
		{
			$result = addcslashes($result, '%_');
		}

		return $result;
	}

	/**
	 * Method to quote and optionally escape a string to database requirements for insertion into the database.
	 *
	 * @param   string   $text    The string to quote.
	 * @param   boolean  $escape  True (default) to escape the string, false to leave it unchanged.
	 *
	 * @return  string  The quoted input string.
	 *
	 * @since   11.1
	 */
	public function quote($text, $escape = true)
	{
		return $this->connection->quote($text);
	}

	/**
	 * Test to see if the MySQL connector is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public static function test()
	{
		return class_exists('PDO');
	}

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return  boolean  True if connected to the database engine.
	 *
	 * @since   11.1
	 */
	public function connected()
	{
		try
		{
			$this->connection->query('SELECT 1');
		}
		catch (PDOException $e)
		{
			return false;
		}

		return true;
	}

	/**
	 * Drops a table from the database.
	 *
	 * @param   string   $tableName  The name of the database table to drop.
	 * @param   boolean  $ifExists   Optionally specify that the table must exist before it is dropped.
	 *
	 * @return  JDatabaseMySQL  Returns this object to support chaining.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function dropTable($tableName, $ifExists = true)
	{
		$query = $this->getQuery(true);

		$this->setQuery('DROP TABLE ' . ($ifExists ? 'IF EXISTS ' : '') . $query->quoteName($tableName));

		$this->execute();

		return $this;
	}

	/**
	 * Get the number of affected rows for the previous executed SQL statement.
	 *
	 * @return  integer  The number of affected rows.
	 *
	 * @since   11.1
	 */
	public function getAffectedRows()
	{
		return $this->cursor->rowCount();
	}

	/**
	 * Method to get the database collation in use by sampling a text field of a table in the database.
	 *
	 * @return  mixed  The collation in use by the database (string) or boolean false if not supported.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function getCollation()
	{
		$this->setQuery('SHOW FULL COLUMNS FROM #__users');
		$array = $this->loadAssocList();
		return $array['2']['Collation'];
	}

	/**
	 * Gets an exporter class object.
	 *
	 * @return  JDatabaseExporterMySQL  An exporter object.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function getExporter()
	{
		// Make sure we have an exporter class for this driver.
		if (!class_exists('JDatabaseExporterPDOMySQL'))
		{
			throw new JDatabaseException(JText::_('JLIB_DATABASE_ERROR_MISSING_EXPORTER'));
		}

		$o = new JDatabaseExporterPDOMySQL;
		$o->setDbo($this);

		return $o;
	}

	/**
	 * Gets an importer class object.
	 *
	 * @return  JDatabaseImporterMySQL  An importer object.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function getImporter()
	{
		// Make sure we have an importer class for this driver.
		if (!class_exists('JDatabaseImporterPDOMySQL'))
		{
			throw new JDatabaseException(JText::_('JLIB_DATABASE_ERROR_MISSING_IMPORTER'));
		}

		$o = new JDatabaseImporterPDOMySQL;
		$o->setDbo($this);

		return $o;
	}

	/**
	 * Get the number of returned rows for the previous executed SQL statement.
	 *
	 * @param   resource  $cursor  An optional database cursor resource to extract the row count from.
	 *
	 * @return  integer   The number of returned rows.
	 *
	 * @since   11.1
	 */
	public function getNumRows($cursor = null)
	{
		return $cur ? $cur->rowCount() : $this->cursor->rowCount();
	}

	/**
	 * Get the current or query, or new JDatabaseQuery object.
	 *
	 * @param   boolean  $new  False to return the last query set, True to return a new JDatabaseQuery object.
	 *
	 * @return  mixed  The current value of the internal SQL variable or a new JDatabaseQuery object.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function getQuery($new = false)
	{
		if ($new)
		{
			// Make sure we have a query class for this driver.
			if (!class_exists('JDatabaseQueryMySQL'))
			{
				throw new JDatabaseException(JText::_('JLIB_DATABASE_ERROR_MISSING_QUERY'));
			}
			return new JDatabaseQueryPDOMySQL($this);
		}
		else
		{
			return $this->sql;
		}
	}

	/**
	 * Shows the table CREATE statement that creates the given tables.
	 *
	 * @param   mixed  $tables  A table name or a list of table names.
	 *
	 * @return  array  A list of the create SQL for the tables.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function getTableCreate($tables)
	{
		// Initialise variables.
		$result = array();

		// Sanitize input to an array and iterate over the list.
		settype($tables, 'array');
		foreach ($tables as $table)
		{
			// Set the query to get the table CREATE statement.
			$this->setQuery('SHOW CREATE table ' . $this->quoteName($this->escape($table)));
			$row = $this->loadRow();

			// Populate the result array based on the create statements.
			$result[$table] = $row[1];
		}

		return $result;
	}

	/**
	 * Retrieves field information about a given table.
	 *
	 * @param   string   $table     The name of the database table.
	 * @param   boolean  $typeOnly  True to only return field types.
	 *
	 * @return  array  An array of fields for the database table.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function getTableColumns($table, $typeOnly = true)
	{
		$result = array();

		// Set the query to get the table fields statement.
		$this->setQuery('SHOW FULL COLUMNS FROM ' . $this->quoteName($this->escape($table)));
		$fields = $this->loadObjectList();

		// If we only want the type as the value add just that to the list.
		if ($typeOnly)
		{
			foreach ($fields as $field)
			{
				$result[$field->Field] = preg_replace("/[(0-9)]/", '', $field->Type);
			}
		}
		// If we want the whole field data object add that to the list.
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
	 * Get the details list of keys for a table.
	 *
	 * @param   string  $table  The name of the table.
	 *
	 * @return  array  An array of the column specification for the table.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function getTableKeys($table)
	{
		// Get the details columns information.
		$this->setQuery('SHOW KEYS FROM ' . $this->quoteName($table));
		$keys = $this->loadObjectList();

		return $keys;
	}

	/**
	 * Method to get an array of all tables in the database.
	 *
	 * @return  array  An array of all the tables in the database.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function getTableList()
	{
		// Set the query to get the tables statement.
		$this->setQuery('SHOW TABLES');
		$tables = $this->loadColumn();

		return $tables;
	}

	/**
	 * Get the version of the database connector.
	 *
	 * @return  string  The database connector version.
	 *
	 * @since   11.1
	 */
	public function getVersion()
	{
		return $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION);
	}

	/**
	 * Determines if the database engine supports UTF-8 character encoding.
	 *
	 * @return  boolean  True if supported.
	 *
	 * @since   11.1
	 * @deprecated 12.1
	 */
	public function hasUTF()
	{
		JLog::add('JDatabaseMySQL::hasUTF() is deprecated.', JLog::WARNING, 'deprecated');
		return true;
	}

	/**
	 * Method to get the auto-incremented value from the last INSERT statement.
	 *
	 * @return  integer  The value of the auto-increment field from the last inserted row.
	 *
	 * @since   11.1
	 */
	public function insertid()
	{
		return $this->connection->lastInsertId();
	}

	/**
	 * Locks a table in the database.
	 *
	 * @param   string  $table  The name of the table to unlock.
	 *
	 * @return  JDatabaseMySQL  Returns this object to support chaining.
	 *
	 * @since   11.4
	 * @throws  JDatabaseException
	 */
	public function lockTable($table)
	{
		$this->setQuery('LOCK TABLES ' . $this->quoteName($table) . ' WRITE')->execute();

		return $this;
	}

	/**
	 * Execute the SQL statement.
	 *
	 * @return  mixed  A database cursor resource on success, boolean false on failure.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function execute()
	{
		if (!is_object($this->connection))
		{
			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  12.1
			if (JError::$legacy)
			{
				if ($this->debug)
				{
					JLog::add(JText::sprintf('JLIB_DATABASE_QUERY_FAILED', $this->errorNum, $this->errorMsg), JLog::ERROR, 'database');
					JError::raiseError(500, 'JDatabaseMySQL::query: ' . $this->errorNum . ' - ' . $this->errorMsg);
				}
				return false;
			}
			else
			{
				JLog::add(JText::sprintf('JLIB_DATABASE_QUERY_FAILED', $this->errorNum, $this->errorMsg), JLog::ERROR, 'database');
				throw new JDatabaseException($this->errorMsg, $this->errorNum);
			}
		}

		// Take a local copy so that we don't modify the original query and cause issues later
		$sql = $this->replacePrefix((string) $this->sql);
		if ($this->limit > 0 || $this->offset > 0)
		{
			$sql .= ' LIMIT ' . $this->offset . ', ' . $this->limit;
		}

		// START: HUBzero query timer

		$timer = 0;

		// Reset the error values.
 		$this->errorNum = 0;
 		$this->errorMsg = '';

		$starttime = microtime(true);
 		$this->cursor = $this->connection->query($sql);
		$endtime = microtime(true);

		// If debugging is enabled then let's log the query.
		if ($this->debug) {
			// Increment the query counter and add the query to the object queue.
			$this->count++;
			$timediff = ($endtime - $starttime);
			$this->timer += $timediff;
			if($timediff > 1000)
			{
				$timediff = "!!!!! ".$timediff;
			}
			$this->log[] = "(".$timediff."): ".$sql;

			JLog::add(str_replace("\n","",$sql), JLog::DEBUG, 'databasequery');
		}

		/*
		// If debugging is enabled then let's log the query.
		if ($this->debug)
		{
			// Increment the query counter and add the query to the object queue.
			$this->count++;
			$this->log[] = $sql;

			JLog::add(str_replace("\n","",$sql), JLog::DEBUG, 'databasequery');
		}

		// Reset the error values.
		$this->errorNum = 0;
		$this->errorMsg = '';

		// Execute the query.
		$this->cursor = $this->connection->query($sql);
		*/
		// END: HUBzero query timer

		// If an error occurred handle it.
		if (!$this->cursor)
		{
			$this->errorNum = (int) $this->connection->errorCode();
			$this->errorMsg = (string) $this->connection->errorInfo()." SQL=$sql";

			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  12.1
			if (JError::$legacy)
			{
				if ($this->debug)
				{
					JLog::add(JText::sprintf('JLIB_DATABASE_QUERY_FAILED', $this->errorNum, $this->errorMsg), JLog::ERROR, 'database');
					JError::raiseError(500, 'JDatabasePDO::query: ' . $this->errorNum . ' - ' . $this->errorMsg);
				}
				return false;
			}
			else
			{
				JLog::add(JText::sprintf('JLIB_DATABASE_QUERY_FAILED', $this->errorNum, $this->errorMsg), JLog::ERROR, 'database');
				throw new JDatabaseException($this->errorMsg, $this->errorNum);
			}
		}

		return $this->cursor;
	}

	/**
	 * Renames a table in the database.
	 *
	 * @param   string  $oldTable  The name of the table to be renamed
	 * @param   string  $newTable  The new name for the table.
	 * @param   string  $backup    Not used by MySQL.
	 * @param   string  $prefix    Not used by MySQL.
	 *
	 * @return  JDatabase  Returns this object to support chaining.
	 *
	 * @since   11.4
	 * @throws  JDatabaseException
	 */
	public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
	{
		$this->setQuery('RENAME TABLE ' . $oldTable . ' TO ' . $newTable)->execute();

		return $this;
	}

	/**
	 * Select a database for use.
	 *
	 * @param   string  $database  The name of the database to select for use.
	 *
	 * @return  boolean  True if the database was successfully selected.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function select($database)
	{
		if (!$database)
		{
			return false;
		}

		if ($this->connection->exec('USE `' . $database . '`') === false) 
		{
			// Legacy error handling switch based on the JError::$legacy switch.
			// @deprecated  12.1
			if (JError::$legacy)
			{
				$this->errorNum = 3;
				$this->errorMsg = JText::_('JLIB_DATABASE_ERROR_DATABASE_CONNECT');
				return false;
			}
			else
			{
				throw new JDatabaseException(JText::_('JLIB_DATABASE_ERROR_DATABASE_CONNECT'));
			}
		}

		return true;
	}

	/**
	 * Set the connection to use UTF-8 character encoding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function setUTF()
	{
		$this->connection->exec("SET NAMES 'utf8'");
	}

	/**
	 * Method to commit a transaction.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function transactionCommit()
	{
		$this->setQuery('COMMIT');
		$this->execute();
	}

	/**
	 * Method to roll back a transaction.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function transactionRollback()
	{
		$this->setQuery('ROLLBACK');
		$this->execute();
	}

	/**
	 * Method to initialize a transaction.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function transactionStart()
	{
		$this->setQuery('START TRANSACTION');
		$this->execute();
	}

	/**
	 * Method to fetch a row from the result set cursor as an array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   11.1
	 */
	protected function fetchArray($cursor = null)
	{
		return $cursor->fetch(PDO::FETCH_NUM);
	}

	/**
	 * Method to fetch a row from the result set cursor as an associative array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   11.1
	 */
	protected function fetchAssoc($cursor = null)
	{
		return $cursor->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @param   mixed   $cursor  The optional result set cursor from which to fetch the row.
	 * @param   string  $class   The class name to use for the returned row object.
	 *
	 * @return  mixed   Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   11.1
	 */
	protected function fetchObject($cursor = null, $class = 'stdClass')
	{
		return $cursor->fetch(PDO::FETCH_OBJ);
	}

	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function freeResult($cursor = null)
	{
		//  mysql_free_result($cursor ? $cursor : $this->cursor);
	}

	/**
	 * Diagnostic method to return explain information for a query.
	 *
	 * @return      string  The explain output.
	 *
	 * @since       11.1
	 * @deprecated  12.1
	 */
	public function explain()
	{
		// Deprecation warning.
		JLog::add('JDatabaseMySQL::explain() is deprecated.', JLog::WARNING, 'deprecated');

		// Backup the current query so we can reset it later.
		$backup = $this->sql;

		// Prepend the current query with EXPLAIN so we get the diagnostic data.
		$this->sql = 'EXPLAIN ' . $this->sql;

		// Execute the query and get the result set cursor.
		if (!($cursor = $this->execute()))
		{
			return null;
		}

		// Build the HTML table.
		$first = true;
		$buffer = '<table id="explain-sql">';
		$buffer .= '<thead><tr><td colspan="99">' . $this->getQuery() . '</td></tr>';
		while ($row = $cur->fetch(PDO::FETCH_ASSOC)) 
		{
			if ($first)
			{
				$buffer .= '<tr>';
				foreach ($row as $k => $v)
				{
					$buffer .= '<th>' . $k . '</th>';
				}
				$buffer .= '</tr></thead><tbody>';
				$first = false;
			}
			$buffer .= '<tr>';
			foreach ($row as $k => $v)
			{
				$buffer .= '<td>' . $v . '</td>';
			}
			$buffer .= '</tr>';
		}
		$buffer .= '</tbody></table>';
		$cur->closeCursor();

		// Restore the original query to its state before we ran the explain.
		$this->sql = $backup;

		// Free up system resources and return.
		$this->freeResult($cursor);

		return $buffer;
	}

	/**
	 * Execute a query batch.
	 *
	 * @param   boolean  $abortOnError     Abort on error.
	 * @param   boolean  $transactionSafe  Transaction safe queries.
	 *
	 * @return  mixed  A database resource if successful, false if not.
	 *
	 * @deprecated  12.1
	 * @since   11.1
	 */
	public function queryBatch($abortOnError = true, $transactionSafe = false)
	{
		// Deprecation warning.
		JLog::add('JDatabaseMySQL::queryBatch() is deprecated.', JLog::WARNING, 'deprecated');

		$sql = $this->replacePrefix((string) $this->sql);
		$this->errorNum = 0;
		$this->errorMsg = '';

		// If the batch is meant to be transaction safe then we need to wrap it in a transaction.
		if ($transactionSafe)
		{
			$this->sql = rtrim($this->sql, "; \t\r\n\0");
			$this->connection->beginTransaction();
		}
		$queries = $this->splitSql($sql);
		$error = 0;
		foreach ($queries as $query)
		{
			$query = trim($query);
			if ($query != '')
			{
				$this->cursor = $this->connection->query( $command_line );
				if ($this->debug)
				{
					$this->count++;
					$this->log[] = $query;
				}
				if (!$this->cursor)
				{
					$error = 1;
					$this->errorNum .= $this->connection->errorCode();
					$this->errorMsg .= $this->connection->errorInfo()." SQL=$query <br />";
					if ($abortOnError)
					{
						return $this->cursor;
					}
				}
			}
		}
		return $error ? false : true;
	}

	/**
	 * Unlocks tables in the database.
	 *
	 * @return  JDatabaseMySQL  Returns this object to support chaining.
	 *
	 * @since   11.4
	 * @throws  JDatabaseException
	 */
	public function unlockTables()
	{
		$this->setQuery('UNLOCK TABLES')->execute();

		return $this;
	}

	/**
	 * Check for the existance of a table
	 *
	 * @access	public
	 * @param 	string $table - table we're looking for
	 * @return 	bool
	 */
	public function tableExists($table)
	{
		$query = 'SHOW TABLES LIKE ' . str_replace('#__', $this->tablePrefix, $this->Quote($table, false));
		$this->setQuery($query);
		$this->query();

		return ($this->getAffectedRows() > 0) ? true : false;
	}

	/**
	 * Returns whether or not the given table has a given field
	 *
	 * @access	public
	 * @param 	string $table - A table name
	 * @param	string $field - A field name
	 * @return	bool          - true if table has field, false otherwise
	 */
	public function tableHasField($table, $field)
	{
		$this->setQuery( 'SHOW FIELDS FROM ' . $table );
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
	 * @access	public
	 * @param 	string $table - A table name
	 * @param	string $key   - A key name
	 * @return	bool          - true if table has key, false otherwise
	 */
	public function tableHaskey($table, $key)
	{
		$this->setQuery( 'SHOW KEYS FROM ' . $table );
		$keys = $this->loadObjectList('Key_name');

		if (!is_array($keys))
		{
			return false;
		}

		return (in_array($key, array_keys($keys))) ? true : false;
	}

	/**
	 * Get the primary key of a table
	 *
	 * @return (string) $Key
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
}
