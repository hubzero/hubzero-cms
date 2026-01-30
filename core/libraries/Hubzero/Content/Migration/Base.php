<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration;

require_once __DIR__ . '/helpers/queryAddColumnStatement.php';
require_once __DIR__ . '/helpers/queryDropColumnStatement.php';

use Hubzero\Content\Migration\Helpers\QueryAddColumnStatement;
use Hubzero\Content\Migration\Helpers\QueryDropColumnStatement;
use Hubzero\Database\Driver;
use Hubzero\System\PrivilegeManager;

/**
 * Base migration class
 **/
class Base
{
	/**
	 * Base database object (should have extensions and migrations log tables in it)
	 *
	 * @var  object
	 **/
	private $baseDb;

	/**
	 * Db object available to migrations
	 *
	 * @var  string
	 **/
	protected $db;

	/**
	 * Available callbacks
	 *
	 * @var  object
	 **/
	protected $callbacks = array();

	/**
	 * Options
	 *
	 * @var  array
	 **/
	protected $options = array();

	/**
	 * Errors
	 *
	 * @var  array
	 **/
	protected $errors = array();

	/**
	 * Whether or not we're running in protected mode
	 *
	 * @var  bool
	 **/
	private $protectedMode = true;

	/**
	 * Macros
	 *
	 * @var  array
	 **/
	protected static $macros = array();

	/**
	 * Registered list of namespaces in which to search for commands
	 *
	 * @var  array
	 **/
	protected static $macroNamespaces = array();

	/**
	 * Constructor
	 *
	 * @param   object  $db         Database object (primary)
	 * @param   array   $callbacks  Callbacks
	 * @param   object  $altDb      Alternate db
	 * @return  void
	 **/
	public function __construct($db, $callbacks=array(), $altDb=null)
	{
		$this->baseDb    = $db;
		$this->db        = (isset($altDb)) ? $altDb : $db;
		$this->callbacks = $callbacks;

		if (!isset($altDb))
		{
			$this->protectedMode = false;
		}

		if (empty(self::$macroNamespaces))
		{
			$this->registerMacroNamespace(__NAMESPACE__ . '\\Macros');
		}
	}

	/**
	 * Helper function for calling a given callback
	 *
	 * @param   string  $callback  Name of callback to use
	 * @param   string  $fund      Name of callback function to call
	 * @param   array   $args      Args to pass to callback function
	 * @return  mixed
	 **/
	public function callback($callback, $func, $args=array())
	{
		// Make sure the callback is set (this is protecting us when running in non-interactive mode and callbacks aren't set)
		if (!isset($this->callbacks[$callback]))
		{
			return false;
		}

		// Call function
		return call_user_func_array(array($this->callbacks[$callback], $func), $args);
	}

	/**
	 * Helper function for logging messages
	 *
	 * @param   string  $message
	 * @param   string  $type (info, warning, error, success)
	 * @return  void
	 **/
	public function log($message, $type='info')
	{
		$this->callback('migration', 'log', [
			'message' => $message,
			'type'    => $type
		]);
	}

	/**
	 * Get option - these are specified/overwritten by the individual migrations/hooks
	 *
	 * @param   string  $key
	 * @return  mixed
	 **/
	public function getOption($key)
	{
		return (isset($this->options[$key])) ? $this->options[$key] : false;
	}

	/**
	 * Return a middleware database object
	 *
	 * @return  object
	 */
	public function getMWDBO()
	{
		static $instance;

		if (!is_object($instance))
		{
			$config = $this->getParams('com_tools');

			$options['driver']   = 'pdo';
			$options['host']     = $config->get('mwDBHost');
			$options['port']     = $config->get('mwDBPort');
			$options['user']     = $config->get('mwDBUsername');
			$options['password'] = $config->get('mwDBPassword');
			$options['database'] = $config->get('mwDBDatabase');
			$options['prefix']   = $config->get('mwDBPrefix');

			if ((!isset($options['password']) || $options['password'] == '')
			 && (!isset($options['user'])     || $options['user'] == '')
			 && (!isset($options['database']) || $options['database'] == ''))
			{
				$instance = $this->db;
			}
			else
			{
				try
				{
					$instance = Driver::getInstance($options);
				}
				catch (\PDOException $e)
				{
					$instance = null;
					return false;
				}
			}

			// Test the connection
			if (!$instance->connected())
			{
				$instance = null;
				return false;
			}
		}

		return $instance;
	}

	/**
	 * Execute a shell command with escalated privileges if available
	 *
	 * This temporarily escalates to root (if running via sudo), executes the
	 * command, then drops back to the original user. Use this for system
	 * commands that require elevated privileges.
	 *
	 * @param   string  $command  The command to execute
	 * @return  string|null  Command output or null on failure
	 **/
	protected function executeWithEscalation($command)
	{
		return PrivilegeManager::getInstance()->withElevatedPrivileges(
			fn() => shell_exec($command)
		);
	}

	/**
	 * Try to run commands as MySQL root user
	 *
	 * Uses Unix socket authentication when running as system root.
	 * This requires:
	 *   1. Running muse via sudo (sudo muse migration run)
	 *   2. MySQL root user configured with auth_socket or unix_socket plugin
	 *   3. Database must be local (localhost, 127.0.0.1, or socket path)
	 *
	 * @return  bool  If successfully upgraded to root access
	 **/
	public function runAsRoot()
	{
		if ($this->protectedMode)
		{
			return false;
		}

		// Socket authentication only works for local databases
		$host = \Config::get('host', 'localhost');
		if (!$this->isLocalHost($host))
		{
			return false;
		}

		$pm = PrivilegeManager::getInstance();

		// Need to be running as root (or have escalation available)
		$escalated = $pm->escalate();
		$isRoot = (function_exists('posix_geteuid') && posix_geteuid() === 0);

		if (!$isRoot)
		{
			if ($escalated)
			{
				$pm->drop();
			}
			return false;
		}

		// Find MySQL socket
		$socketPaths = array(
			'/var/run/mysqld/mysqld.sock',
			'/var/lib/mysql/mysql.sock',
			'/tmp/mysql.sock',
		);

		$socket = null;
		foreach ($socketPaths as $path)
		{
			if (file_exists($path))
			{
				$socket = $path;
				break;
			}
		}

		if (!$socket)
		{
			if ($escalated)
			{
				$pm->drop();
			}
			return false;
		}

		// Try to connect as MySQL root using socket authentication
		try
		{
			$database = \Config::get('db');
			$dsn = "mysql:unix_socket={$socket};charset=utf8";
			if ($database)
			{
				$dsn .= ";dbname={$database}";
			}

			$db = Driver::getInstance(array(
				'driver'   => 'pdo',
				'dsn'      => $dsn,
				'user'     => 'root',
				'password' => '',
				'database' => $database,
				'prefix'   => \Config::get('dbprefix')
			));

			if ($db->connected())
			{
				$this->db = $db;
				// Keep escalated privileges while using root DB connection
				return true;
			}
		}
		catch (\Exception $e)
		{
			// Socket auth failed, connection error
		}

		if ($escalated)
		{
			$pm->drop();
		}

		return false;
	}

	/**
	 * Check if a host string refers to the local machine
	 *
	 * @param   string  $host  The host to check
	 * @return  bool    True if local, false if remote
	 **/
	private function isLocalHost($host)
	{
		$host = strtolower(trim($host));

		// Empty host typically means localhost
		if ($host === '')
		{
			return true;
		}

		// Common localhost identifiers
		if ($host === 'localhost' || $host === '127.0.0.1' || $host === '::1')
		{
			return true;
		}

		// Unix socket path (starts with /)
		if (str_starts_with($host, '/'))
		{
			return true;
		}

		return false;
	}

	/**
	 * Set an error
	 *
	 * @param   string  $message
	 * @param   string  $type
	 * @return  void
	 **/
	public function setError($message, $type='fatal')
	{
		$this->errors[] = array('type' => $type, 'message' => $message);
	}

	/**
	 * Get errors
	 *
	 * @return  array  Errors
	 **/
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Register a custom macro
	 *
	 * @param   string    $name
	 * @param   callable  $macro
	 * @return  void
	 */
	public static function macro($name, $macro)
	{
		static::$macros[$name] = $macro;
	}

	/**
	 * Checks if macro is registered
	 *
	 * @param   string   $name
	 * @return  boolean
	 */
	public static function hasMacro($name)
	{
		return isset(static::$macros[$name]);
	}

	/**
	 * Registers a location to look for commands
	 *
	 * @param   string  $namespace  The namespace location to use
	 * @return  $this
	 **/
	public static function registerMacroNamespace($namespace, $paths = array())
	{
		self::$macroNamespaces[$namespace] = (array)$paths;
	}

	/**
	 * Dynamically handle calls to the class.
	 *
	 * @param   string  $method
	 * @param   array   $parameters
	 * @return  mixed
	 * @throws  \BadMethodCallException
	 */
	public function __call($method, $parameters)
	{
		if (static::hasMacro($method))
		{
			$callback = static::$macros[$method]->setMigration($this);

			return call_user_func_array($callback, $parameters);
		}


		foreach (self::$macroNamespaces as $namespace => $paths)
		{
			$invokable = $namespace . '\\' . ucfirst($method);

			if (!class_exists($invokable))
			{
				foreach ($paths as $path)
				{
					include_once $path . DIRECTORY_SEPARATOR . $method . '.php';
				}
			}

			if (class_exists($invokable))
			{
				$callback = new $invokable();

				if ($callback instanceof Macro)
				{
					$callback->setMigration($this)->setDatabase($this->db);

					$this->macro($method, $callback);

					return call_user_func_array($callback, $parameters);
				}
			}
		}

		throw new \BadMethodCallException("Method {$method} does not exist.");
	}

	/**
	 * Generates ALTER TABLE SQL query to add columns absent from given table
	 *
	 * @param   string  $table    Given table
	 * @param   array   $columns  Columns to add to given table
	 * @return  string
	 **/
	protected function _generateSafeAddColumns($table, $columns)
	{
		$query = $this->_generateSafeAlterTableColumnOperation(
			$table, $columns, '_safeAddColumn'
		);

		return $query;
	}

	/**
	 * Generates ADD COLUMN SQL statement if column absent from given table
	 *
	 * @param   string  $table       Given table
	 * @param   array   $columnData  Data for column to be added
	 * @return  string
	 **/
	protected function _safeAddColumn($table, $columnData)
	{
		$columnName = $columnData['name'];
		$addColumnStatement = '';

		if (!$this->db->tableHasField($table, $columnName))
		{
			$addColumnStatement = (new QueryAddColumnStatement($columnData))
				->toString();
		}

		return $addColumnStatement;
	}

	/**
	 * Generates ALTER TABLE SQL query to drop columns present on given table
	 *
	 * @param   string  $table    Given table
	 * @param   array   $columns  Columns to drop from given table
	 * @return  string
	 **/
	protected function _generateSafeDropColumns($table, $columns)
	{
		$query = $this->_generateSafeAlterTableColumnOperation(
			$table, $columns, '_safeDropColumn'
		);

		return $query;
	}

	/**
	 * Generates DROP COLUMN SQL statement if column present on given table
	 *
	 * @param   string  $table       Given table
	 * @param   array   $columnData  Data for column to be dropped
	 * @return  string
	 **/
	protected function _safeDropColumn($table, $columnData)
	{
		$columnName = $columnData['name'];
		$dropColumnStatement = '';

		if ($this->db->tableHasField($table, $columnName))
		{
			$dropColumnStatement = with(new QueryDropColumnStatement($columnData))
				->toString();
		}

		return $dropColumnStatement;
	}

	/**
	 * Generates SQL statements to alter table for each column
	 *
	 * @param   string  $table         Given table
	 * @param   array   $columns       Columns to be affected by query
	 * @param   string  $functionName  Function to generate per column statements
	 * @return  string
	 **/
	protected function _generateSafeAlterTableColumnOperation($table, $columns, $functionName)
	{
		$query = "ALTER TABLE $table ";

		foreach ($columns as $columnData)
		{
			$query .= $this->$functionName($table, $columnData) . ',';
		}

		$query = rtrim($query, ',') . ';';

		return $query;
	}

	/**
	 * Executes given query if given table exists
	 *
	 * @param   string  $table  Given table
	 * @param   string  $query  Query to execute
	 * @return  void
	 **/
	protected function _queryIfTableExists($table, $query)
	{
		if ($this->db->tableExists($table))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
