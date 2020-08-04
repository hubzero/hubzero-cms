<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Driver;

use Hubzero\Database\Driver\Pdo as PdoDriver;
use Hubzero\Database\Exception\ConnectionFailedException;
use Hubzero\Database\Exception\QueryFailedException;

/**
 * Sqlite (Pdo) database driver
 */
class Sqlite extends PdoDriver
{
	/**
	 * Constructs a new database object based on the given params
	 *
	 * @param   array  $options  The database connection params
	 * @return  void
	 */
	public function __construct($options)
	{
		// Add "extra" options as needed
		if (!isset($options['extras']))
		{
			$options['extras'] = [];
		}

		// Establish connection string
		if (!isset($options['dsn']))
		{
			$options['dsn'] = "sqlite:{$options['database']}";
		}

		if (substr($options['dsn'], 0, 7) != 'sqlite:')
		{
			throw new ConnectionFailedException('Sqlite DSN for PDO connection does not appear to be valid.', 500);
		}

		// Call parent construct
		parent::__construct($options);
	}

	/**
	 * Retrieves field information about the given table
	 *
	 * @param   string   $table     The name of the database table.
	 * @param   boolean  $typeOnly  True to only return field types.
	 * @return  array    An array of fields for the database table.
	 * @since   2.2.15
	 */
	public function getTableColumns($table, $typeOnly = true)
	{
		$columns = array();

		$fieldCasing = $this->connection->getAttribute(\PDO::ATTR_CASE);

		$this->connection->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_UPPER);

		$table = strtoupper($table);

		$this->setQuery('pragma table_info(' . $table . ')');
		$fields = $this->loadObjectList();

		if ($typeOnly)
		{
			foreach ($fields as $field)
			{
				$columns[$field->NAME] = $field->TYPE;
			}
		}
		else
		{
			foreach ($fields as $field)
			{
				// Normalize output
				$columns[$field->NAME] = (object) array(
					'Field'   => $field->NAME,
					'Type'    => $field->TYPE,
					'Null'    => ($field->NOTNULL == '1' ? 'NO' : 'YES'),
					'Default' => $field->DFLT_VALUE,
					'Key'     => ($field->PK != '0' ? 'PRI' : '')
				);
			}
		}

		$this->connection->setAttribute(\PDO::ATTR_CASE, $fieldCasing);

		return $columns;
	}

	/**
	 * Retrieves key information about the given tables
	 *
	 * @param   string|array  $tables  A table name or a list of table names
	 * @return  array
	 * @since   2.2.15
	 */
	public function getTableKeys($table)
	{
		$keys = array();

		$fieldCasing = $this->connection->getAttribute(\PDO::ATTR_CASE);

		$this->connection->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_UPPER);

		$table = strtoupper($table);

		$this->setQuery('pragma table_info( ' . $table . ')');
		$rows = $this->loadObjectList();

		foreach ($rows as $column)
		{
			if ($column->PK == 1)
			{
				$keys[$column->NAME] = $column;
			}
		}

		$this->connection->setAttribute(\PDO::ATTR_CASE, $fieldCasing);

		return $keys;
	}

	/**
	 * Gets an array of all tables in the database
	 *
	 * @return  array
	 * @since   2.2.15
	 */
	public function getTableList()
	{
		$query = $this->getQuery()
			->select('name')
			->from('sqlite_master')
			->whereEquals('type', 'table')
			->order('name', 'asc')
			->toString();

		$this->setQuery($query);

		return $this->loadColumn();
	}

	/**
	 * Locks a table in the database
	 *
	 * This is unsuported by SQLite
	 *
	 * @param   string  $tableName  The name of the table to lock
	 * @return  $this
	 * @since   2.2.15
	 */
	public function lockTable($tableName)
	{
		return $this;
	}

	/**
	 * Unlocks all tables in the database
	 *
	 * This is unsuported by SQLite
	 *
	 * @return  $this
	 * @since   2.0.0
	 */
	public function unlockTables()
	{
		return $this;
	}

	/**
	 * Selects a database for use
	 *
	 * This is unsuported by SQLite
	 *
	 * @param   string  $database  The name of the database to select for use
	 * @return  bool
	 * @since   2.2.15
	 */
	public function select($database)
	{
		return false;
	}

	/**
	 * Gets the database engine of the given table
	 *
	 * @param   string       $table  The table for which to retrieve the engine type
	 * @return  string|bool
	 * @since   2.2.15
	 **/
	public function getEngine($table)
	{
		return 'VDBE';
	}

	/**
	 * Set the database engine of the given table
	 *
	 * This is unsuported by SQLite
	 *
	 * @param   string  $table   The table for which to retrieve the engine type
	 * @param   string  $engine  The engine type to set
	 * @return  bool
	 * @since   2.2.15
	 **/
	public function setEngine($table, $engine)
	{
		return false;
	}

	/**
	 * Gets the database character set of the given table
	 *
	 * @param   string       $table  The table for which to retrieve the character set
	 * @param   string       $field  The field to check (optional)
	 * @return  string|bool
	 * @since   2.2.15
	 **/
	public function getCharacterSet($table, $field = null)
	{
		// SQLite only supports encodings for an entire database
		// Not encodings on a per-table or per-column level.
		$this->setQuery('pragma encoding;');

		return $this->loadResult();
	}

	/**
	 * Gets the version of the database connector
	 *
	 * @return  string
	 * @since   2.0.0
	 */
	public function getVersion()
	{
		$this->setQuery('SELECT sqlite_version()');

		return $this->loadResult();
	}
}
