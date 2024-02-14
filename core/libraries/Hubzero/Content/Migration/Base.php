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
use Hubzero\Config\Processor;
use Hubzero\Database\Driver;

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
	 * Try to get the root credentials from a variety of locations
	 *
	 * @return  mixed  Array of creds or false on failure
	 **/
	private function getRootCredentials()
	{
		$secrets   = DIRECTORY_SEPARATOR . 'etc'  . DIRECTORY_SEPARATOR . 'hubzero.secrets';
		$conf_file = DIRECTORY_SEPARATOR . 'root' . DIRECTORY_SEPARATOR . '.my.cnf';
		$hub_maint = DIRECTORY_SEPARATOR . 'etc'  . DIRECTORY_SEPARATOR . 'mysql' . DIRECTORY_SEPARATOR . 'hubmaint.cnf';
		$deb_maint = DIRECTORY_SEPARATOR . 'etc'  . DIRECTORY_SEPARATOR . 'mysql' . DIRECTORY_SEPARATOR . 'debian.cnf';

		if (is_file($secrets) && is_readable($secrets))
		{
			$conf = Processor::instance('ini')->parse($secrets);
			$user = (isset($conf['DEFAULT']['MYSQL-ROOT-USER'])) ? $conf['DEFAULT']['MYSQL-ROOT-USER'] : 'root';
			$pw   = (isset($conf['DEFAULT']['MYSQL-ROOT'])) ? $conf['DEFAULT']['MYSQL-ROOT'] : false;

			if ($user && $pw)
			{
				return array('user' => $user, 'password' => $pw);
			}
		}

		if (is_file($conf_file) && is_readable($conf_file))
		{
			$conf = Processor::instance('ini')->parse($conf_file, true);
			$user = (isset($conf['client']['user'])) ? $conf['client']['user'] : false;
			$pw   = (isset($conf['client']['password'])) ? $conf['client']['password'] : false;

			if ($user && $pw)
			{
				return array('user' => $user, 'password' => $pw);
			}
		}

		if (is_file($hub_maint) && is_readable($hub_maint))
		{
			$conf = Processor::instance('ini')->parse($hub_maint, true);
			$user = (isset($conf['client']['user'])) ? $conf['client']['user'] : false;
			$pw   = (isset($conf['client']['password'])) ? $conf['client']['password'] : false;

			if ($user && $pw)
			{
				return array('user' => $user, 'password' => $pw);
			}
		}

		if (is_file($deb_maint) && is_readable($deb_maint))
		{
			$conf = Processor::instance('ini')->parse($deb_maint, true);
			$user = (isset($conf['client']['user'])) ? $conf['client']['user'] : false;
			$pw   = (isset($conf['client']['password'])) ? $conf['client']['password'] : false;

			if ($user && $pw)
			{
				return array('user' => $user, 'password' => $pw);
			}
		}

		return false;
	}

	/**
	 * Try to run commands as MySql root user
	 *
	 * @return  bool  If successfully upgraded to root access
	 **/
	public function runAsRoot()
	{
		if ($this->protectedMode)
		{
			return false;
		}

		if ($creds = $this->getRootCredentials())
		{
			$db = Driver::getInstance(
				array(
					'driver'   => (\Config::get('dbtype') == 'mysql') ? 'pdo' : \Config::get('dbtype'),
					'host'     => \Config::get('host'),
					'user'     => $creds['user'],
					'password' => $creds['password'],
					'database' => \Config::get('db'),
					'prefix'   => \Config::get('dbprefix')
				)
			);

			// Test the connection
			if (!$db->connected())
			{
				return false;
			}
			else
			{
				$this->db = $db;
				return true;
			}
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
