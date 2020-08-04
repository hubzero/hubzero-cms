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
 * Postgres (Pdo) database driver
 */
class Pgsql extends PdoDriver
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
			$host = isset($options['host']) && $options['host'] ? "host={$options['host']};" : '';

			$options['dsn'] = "pgsql:{$host}dbname={$options['database']}";

			if (isset($options['port']))
			{
				$options['dsn'] .= ";port={$options['port']}";
			}

			foreach (['sslmode', 'sslcert', 'ssl_ca', 'sslkey', 'sslrootcert'] as $option)
			{
				if (isset($options[$option]))
				{
					$key = ($option == 'ssl_ca' ? 'sslcert' : $option);
					$val = $options[$option];

					$options['dsn'] .= ";{$key}={$val}";
				}
			}
		}

		if (substr($options['dsn'], 0, 6) != 'pgsql:')
		{
			throw new ConnectionFailedException('Postgres DSN for PDO connection does not appear to be valid.', 500);
		}

		// Call parent construct
		parent::__construct($options);
	}

	/**
	 * Gets the database collation in use
	 *
	 * @return  string|bool
	 * @since   2.2.15
	 */
	public function getCollation()
	{
		$this->setQuery('SHOW LC_COLLATE');
		$array = $this->loadAssocList();

		return $array[0]['lc_collate'];
	}

	/**
	 * Shows the table CREATE statement that creates the given tables
	 *
	 * This is unsuported by Postgres
	 *
	 * @param   string|array  $tables  A table name or a list of table names
	 * @return  array
	 * @since   2.2.15
	 */
	public function getTableCreate($tables)
	{
		// Initialise variables
		$result = [];

		return $result;
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
		$result = array();

		$tableSub = $this->replacePrefix($table);

		$this->setQuery('
			SELECT a.attname AS "column_name",
				pg_catalog.format_type(a.atttypid, a.atttypmod) as "type",
				CASE WHEN a.attnotnull IS TRUE
					THEN \'NO\'
					ELSE \'YES\'
				END AS "null",
				CASE WHEN pg_catalog.pg_get_expr(adef.adbin, adef.adrelid, true) IS NOT NULL
					THEN pg_catalog.pg_get_expr(adef.adbin, adef.adrelid, true)
				END as "Default",
				CASE WHEN pg_catalog.col_description(a.attrelid, a.attnum) IS NULL
					THEN \'\'
					ELSE pg_catalog.col_description(a.attrelid, a.attnum)
				END AS "comments"
			FROM pg_catalog.pg_attribute a
			LEFT JOIN pg_catalog.pg_attrdef adef ON a.attrelid=adef.adrelid AND a.attnum=adef.adnum
			LEFT JOIN pg_catalog.pg_type t ON a.atttypid=t.oid
			WHERE a.attrelid =
				(
					SELECT oid FROM pg_catalog.pg_class WHERE relname=' . $this->quote($tableSub) . '
					AND relnamespace = (SELECT oid FROM pg_catalog.pg_namespace WHERE nspname = \'public\')
				)
			AND a.attnum > 0 AND NOT a.attisdropped
			ORDER BY a.attnum'
		);

		$fields = $this->loadObjectList();

		if ($typeOnly)
		{
			foreach ($fields as $field)
			{
				$result[$field->column_name] = preg_replace('/[(0-9)]/', '', $field->type);
			}
		}
		else
		{
			foreach ($fields as $field)
			{
				if (stristr(strtolower($field->type), 'character varying'))
				{
					$field->Default = '';
				}

				if (stristr(strtolower($field->type), 'text'))
				{
					$field->Default = '';
				}

				// Normalize output
				$result[$field->column_name] = (object) array(
					'Default'     => $field->Default,
					'Comment'     => '',
					'Field'       => $field->column_name,
					'Type'        => $field->type,
					'Null'        => $field->null
				);
			}
		}

		// Change Postgres' NULL::* type with PHP's null one
		foreach ($fields as $field)
		{
			if (preg_match('/^NULL::*/', $field->Default))
			{
				$field->Default = null;
			}
		}

		return $result;
	}

	/**
	 * Get the details list of keys for a table.
	 *
	 * @param   string  $table  The name of the table.
	 * @return  array   An array of the column specification for the table.
	 * @since   2.2.15
	 */
	public function getTableKeys($table)
	{
		$this->connect();

		// To check if table exists and prevent SQL injection
		$tableList = $this->getTableList();

		if (in_array($table, $tableList, true))
		{
			// Get the details columns information.
			$this->setQuery('
				SELECT indexname AS "idxName", indisprimary AS "isPrimary", indisunique  AS "isUnique",
					CASE WHEN indisprimary = true
					THEN (
						SELECT \'ALTER TABLE \' || tablename || \' ADD \' || pg_catalog.pg_get_constraintdef(const.oid, true)
						FROM pg_constraint AS const WHERE const.conname= pgClassFirst.relname
					)
					ELSE pg_catalog.pg_get_indexdef(indexrelid, 0, true)
					END AS "Query"
				FROM pg_indexes
				LEFT JOIN pg_class AS pgClassFirst ON indexname=pgClassFirst.relname
				LEFT JOIN pg_index AS pgIndex ON pgClassFirst.oid=pgIndex.indexrelid
				WHERE tablename=' . $this->quote($table) . ' ORDER BY indkey'
			);

			return $this->loadObjectList('idxName');
		}

		return false;
	}

	/**
	 * Gets an array of all tables in the database
	 *
	 * @return  array
	 * @since   2.2.15
	 */
	public function getTableList()
	{
		// Set the query to get the tables statement
		$query = $this->getQuery()
			->select('table_name')
			->from('information_schema.tables')
			->whereEquals('table_type', 'BASE TABLE')
			->whereNotIn('table_schema', ['pg_catalog', 'information_schema'])
			->order('table_name', 'asc')
			->toString();

		$this->setQuery($query);
		$tables = $this->loadColumn();

		return $tables;
	}

	/**
	 * Locks a table in the database
	 *
	 * @param   string  $tableName  The name of the table to lock
	 * @return  $this
	 * @since   2.2.15
	 */
	public function lockTable($tableName)
	{
		$this->setQuery('LOCK TABLE ' . $this->quoteName($tableName) . ' IN ACCESS EXCLUSIVE MODE')->execute();

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
	 * @since   2.2.15
	 */
	public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
	{
		// To check if table exists and prevent SQL injection
		$tableList = $this->getTableList();

		// Origin Table does not exist
		if (!in_array($oldTable, $tableList, true))
		{
			throw new \RuntimeException('Table not found in Postgres database.');
		}

		// Rename indexes
		$subQuery = $this->getQuery()
			->select('indexrelid')
			->from('pg_index')
			->join('pg_class', 'pg_class.oid', 'pg_index.indrelid')
			->whereEquals('pg_class.relname', $oldTable)
			->toString();

		$this->setQuery(
			$this->getQuery()
				->select('relname')
				->from('pg_class')
				->whereRaw('oid IN (' . (string) $subQuery . ')')
				->toString()
		);

		$oldIndexes = $this->loadColumn();

		foreach ($oldIndexes as $oldIndex)
		{
			$changedIdxName = str_replace($oldTable, $newTable, $oldIndex);
			$this->setQuery('ALTER INDEX ' . $this->escape($oldIndex) . ' RENAME TO ' . $this->escape($changedIdxName))->execute();
		}

		// Rename sequences
		$subQuery = $this->getQuery()
			->select('oid')
			->from('pg_namespace')
			->whereNotLike('nspname', 'pg_%')
			->where('nspname', '!=', 'information_schema')
			->toString();

		$this->setQuery(
			$this->getQuery()
				->select('relname')
				->from('pg_class')
				->whereEquals('relkind', 'S')
				->whereRaw('relnamespace IN (' . (string) $subQuery . ')')
				->whereLike('relname', "%$oldTable%")
				->toString()
		);

		$oldSequences = $this->loadColumn();

		foreach ($oldSequences as $oldSequence)
		{
			$changedSequenceName = str_replace($oldTable, $newTable, $oldSequence);
			$this->setQuery('ALTER SEQUENCE ' . $this->escape($oldSequence) . ' RENAME TO ' . $this->escape($changedSequenceName))->execute();
		}

		// Rename table
		$this->setQuery('ALTER TABLE ' . $this->escape($oldTable) . ' RENAME TO ' . $this->escape($newTable))->execute();

		return true;
	}

	/**
	 * Selects a database for use
	 *
	 * This is unsuported by Postgres
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
		return 'MVCC';
	}

	/**
	 * Set the database engine of the given table
	 *
	 * This is unsuported by Postgres
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
		// Postgres only supports encodings for an entire database
		// Not encodings on a per-table or per-column level.
		$this->setQuery("SELECT * FROM information_schema.character_sets;");

		$result = $this->loadResult();

		return $result ? $result : false;
	}

	/**
	 * Gets the version of the database connector
	 *
	 * @return  string
	 * @since   2.2.15
	 */
	public function getVersion()
	{
		return $this->connection->getAttribute(\PDO::ATTR_SERVER_VERSION);
	}
}
