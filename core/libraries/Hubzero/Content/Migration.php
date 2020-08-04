<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content;

/**
 * HUBzero Database migrations class
 *
 * @TODO: add flag to ignore development scripts?
 */
class Migration
{
	/**
	 * Paths in which to search for migration scripts
	 *
	 * @var array
	 **/
	private $searchPaths = [];

	/**
	 * Array holding paths to migration scripts
	 *
	 * @var array
	 **/
	private $files = [];

	/**
	 * Array holding files affected during this migration (i.e. those that are/would be run)
	 *
	 * @var array
	 **/
	private $affectedFiles = [];

	/**
	 * Variable holding database object
	 *
	 * If an alternate db is given, this db will hold the connection to the
	 * primary hub database where the extensions and logs tables are found
	 *
	 * @var string
	 **/
	private $db = null;

	/**
	 * Alternate db, passed to migrations if specified
	 *
	 * @var string
	 **/
	private $runDb = null;

	/**
	 * Log messages themselves (stored as array to return to browser, or other client)
	 *
	 * @var array
	 **/
	private $log = [];

	/**
	 * Array of callbacks
	 *
	 * @var array
	 **/
	private $callbacks;

	/**
	 * Table holding migration entries
	 *
	 * @var string
	 **/
	private $tbl_name = '#__migrations';

	/**
	 * Whether or not to ignore callbacks
	 *
	 * @var bool
	 **/
	private $ignoreCallbacks = false;

	/**
	 * Constructor
	 *
	 * @param   object  $docroot  Defaults to null, which should then resolve to the hub docroot
	 * @param   object  $runDb    The db that migrations will actually run against
	 * @return  void
	 **/
	public function __construct($docroot = null, $runDb = null)
	{
		// Try to determine the document root if none provided
		if (is_null($docroot))
		{
			$this->addSearchPath(PATH_CORE)
			     ->addSearchPath(PATH_APP);

			$nodes = array(
				PATH_CORE . DS . 'templates',
				PATH_APP . DS . 'templates',
				PATH_CORE . DS . 'components',
				PATH_APP . DS . 'components',
				PATH_CORE . DS . 'modules',
				PATH_APP . DS . 'modules'
			);

			foreach ($nodes as $base)
			{
				$directories = array_diff(scandir($base), ['.', '..']);

				foreach ($directories as $directory)
				{
					if (!is_dir($base . DS . $directory))
					{
						continue;
					}

					// Does the directory conform to extension naming conventions?
					if (strstr($directory, '.') || strstr($directory, ' '))
					{
						continue;
					}

					$this->addSearchPath($base . DS . $directory);
				}
			}

			// Plugins have one extra level of directories
			$nodes = array(
				PATH_CORE . DS . 'plugins',
				PATH_APP . DS . 'plugins'
			);

			foreach ($nodes as $base)
			{
				$directories = array_diff(scandir($base), ['.', '..']);

				foreach ($directories as $directory)
				{
					if (!is_dir($base . DS . $directory))
					{
						continue;
					}

					$subdirectories = array_diff(scandir($base . DS . $directory), ['.', '..']);

					foreach ($subdirectories as $subdirectory)
					{
						// Does the directory conform to extension naming conventions?
						if (strstr($subdirectory, '.') || strstr($subdirectory, ' '))
						{
							continue;
						}

						$this->addSearchPath($base . DS . $directory . DS . $subdirectory);
					}
				}
			}
		}
		else
		{
			$docroot = rtrim($docroot, DS);
			$this->addSearchPath($docroot);
		}

		// Setup the database connection
		if (!$this->db = $this->getDBO())
		{
			$this->log('Error: database connection failed.', 'error');
			return false;
		}

		// This is the database that migrations will run against
		// This is used for super group migrations, that don't run against
		// the default database schema
		if (isset($runDb))
		{
			$this->runDb = $runDb;
		}
	}

	/**
	 * Adds a search path to the migration
	 *
	 * @param   string  $path  The path to add
	 * @return  $this
	 **/
	public function addSearchPath($path)
	{
		$this->searchPaths[] = $path;

		return $this;
	}

	/**
	 * Getter for class private variables
	 *
	 * @param   string  $var  the var to retrieve
	 * @return  mixed
	 **/
	public function get($var)
	{
		if (property_exists($this, $var))
		{
			return $this->$var;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Setup database connect, test, return object
	 *
	 * @return  object
	 **/
	public function getDBO()
	{
		$db = \App::get('db');

		// Test the connection
		if (!$db->connected())
		{
			$this->log('PDO connection failed', 'error');
			return false;
		}

		// Check for the existance of the migrations table
		$tables = $db->getTableList();
		$prefix = $db->getPrefix();
		$tableset = false;

		if (in_array('migrations', $tables))
		{
			$this->setTableName('migrations');
			$tableset = true;
		}

		if (in_array($prefix . 'migrations', $tables))
		{
			if ($tableset)
			{
				$this->log('Tables `migrations` and `' . $prefix . 'migrations` both exist', 'error');
				return false;
			}

			$this->setTableName('#__migrations');
			$tableset = true;
		}

		if (!$tableset)
		{
			if ($this->createMigrationsTable($db) === false)
			{
				return false;
			}
		}

		// Add a callback so that a migration can update $this in real time if necessary
		$this->registerCallback('migration', $this);

		return $db;
	}

	/**
	 * Find all migration scripts
	 *
	 * @param   string  $extension  Only look for migrations for this extension
	 * @param   string  $file       The specific file to run
	 * @return  array
	 **/
	public function find($extension = null, $file = null)
	{
		// Exclude certain thiings from our search
		$exclude = array(".", "..");
		$files   = [];
		$ext     = '';

		foreach ($this->searchPaths as $path)
		{
			if (!is_dir($path . DS . 'migrations'))
			{
				continue;
			}
			$found = array_diff(scandir($path . DS . 'migrations'), $exclude);

			foreach ($found as $f)
			{
				$files[$path . DS . 'migrations' . DS . $f] = $f;
			}
		}

		asort($files);

		if (!is_null($file))
		{
			if (in_array($file, $files))
			{
				$this->files[] = array_search($file, $files);
				return true;
			}
			else
			{
				$this->log("Provided file ({$file}) could not be found.", 'error');
				return false;
			}
		}

		if (!is_null($extension))
		{
			$parts = explode('_', $extension);
			foreach ($parts as $part)
			{
				$ext .= ucfirst($part);
			}
		}

		foreach ($files as $path => $file)
		{
			// Make sure they have a php extension and proper filename format
			if (preg_match('/^Migration[0-9]{14}[[:alnum:]]+\.php$/', $file))
			{
				// If an extension was provided...match against it...
				if (empty($ext) || (!empty($ext) && preg_match('/Migration[0-9]{14}'.$ext.'\.php/', $file)))
				{
					$this->files[] = $path;
				}
			}
		}

		return true;
	}

	/**
	 * Migrate up/down on all files gathered via 'find'
	 *
	 * @param   string  $direction  Direction to migrate (up or down)
	 * @param   bool    $force      Run the update, even if the database says it's already been run
	 * @param   bool    $dryrun     Run the udpate, but only display what would be changed, wihthout actually doing anything
	 * @param   bool    $listAll    List all files found, not just those needing to be run
	 * @param   bool    $logOnly    Run the update, and mark as run, but don't actually run sql (usefully to mark changes that had already been made manually)
	 * @return  bool
	 **/
	public function migrate($direction = 'up', $force = false, $dryrun = false, $listAll = false, $logOnly = false)
	{
		// Make sure we have files
		if (empty($this->files))
		{
			$this->log("There were no migrations to run");
			return true;
		}

		if (!$this->db)
		{
			return false;
		}

		// Notify if we're making a dry run
		if ($dryrun)
		{
			$this->log("Dry run: no changes will be made!");
		}

		// Notify if we're listing all files
		if ($listAll)
		{
			$this->log("List all: all found files will be listed!");
		}

		// Now, fire hooks
		if (!$dryrun && !$logOnly)
		{
			$this->fireHooks('onBeforeMigrate');
		}

		$hasStatus = $this->db->tableHasField($this->get('tbl_name'), 'status');

		// Loop through files and run their '$direction' method
		foreach ($this->files as $fullpath) //$file)
		{
			// Get just the file
			$file = basename($fullpath);

			// Create a hash of the file (not using this at the moment)
			$hash = hash('md5', $file);

			// Get the file name
			$info = pathinfo($file);

			// Make sure the file exists
			// If it doesn't, there's no point going any further
			if (!is_file($fullpath))
			{
				$this->log("{$fullpath} is not a valid file", 'warning');
				continue;
			}

			// Generate the scope
			// This will be the path to the migration, minus the document root
			// ex: "core/migrations" or "app/components/com_example/migrations"
			$scope = str_replace(PATH_ROOT . DS, '', dirname($fullpath));

			// Check to see if this file has already been run
			try
			{
				// Look to the database log to see the last run on this file
				$query = "SELECT `direction`";

				if ($this->db->tableHasField($this->get('tbl_name'), 'status'))
				{
					$query .= ", `status`";
				}

				$query .= " FROM `{$this->get('tbl_name')}` WHERE `file` = " . $this->db->quote($file);

				if ($this->db->tableHasField($this->get('tbl_name'), 'scope'))
				{
					if ($scope == 'core/migrations')
					{
						$query .= " AND (`scope`='' OR `scope` IN (" . $this->db->quote($scope) . "," . $this->db->quote('migrations') . "))";
					}
					else
					{
						$query .= " AND `scope` = " . $this->db->quote($scope);
					}
				}

				$query .= " ORDER BY `date` DESC LIMIT 1";

				$this->db->setQuery($query);
				$row = $this->db->loadObject();

				// Decide whether or not we want to show the file at all
				// If list all, then we just show everything
				// If force, we assume we have to show it
				if (!$listAll && !$force)
				{
					// If we have a row (meaning it's been run at least once before),
					// and the direction is the same as is being run now, then it's already been run
					if ($row && $row->direction == $direction)
					{
						// The last check is to make sure that the previous run we see was a success
						// If we don't have a status line (which is an implicit success),
						// or we do have a status and it was a success, then we can reasonably skip this entry
						if (!$hasStatus || ($hasStatus && $row->status == 'success'))
						{
							continue;
						}
					}
				}

				// Now, if we are showing the file, should it actually be run?
				if (!$force)
				{
					// If we have no row at all
					if (!$row && $direction == 'down')
					{
						$this->log("Ignoring {$direction}() - you should run up first ({$scope}/{$file})");
						continue;
					}
					// If the last run was the same direction as is currently being run, we shouldn't run it again
					else if ($row && $row->direction == $direction)
					{
						// Lastly, check status as well
						if (!$hasStatus || ($hasStatus && $row->status == 'success'))
						{
							if ($dryrun)
							{
								$this->log("Would ignore {$direction}() {$scope}/{$file}");
								continue;
							}
							else
							{
								$this->log("Ignoring {$direction}() {$scope}/{$file}");
								continue;
							}
						}
					}
				}
			}
			catch (\Hubzero\Database\Exception\QueryFailedException $e)
			{
				// Our query failed altogether...that's not good
				$this->log("Error: the check for preexisting migrations failed!", 'error');
				return false;
			}

			require_once $fullpath;

			// Set classname
			$classname = $info['filename'];

			// Make sure file and classname match
			if (!class_exists($classname))
			{
				$this->log("{$info['filename']} does not have a class of the same name", 'warning');
				continue;
			}

			// We've made it this far, add this file to list of affected files
			$this->affectedFiles[] = $info['filename'];

			// Instantiate our class
			$class = new $classname($this->db, $this->callbacks, $this->runDb);

			// Check if we're making a dry run, or only logging changes
			if ($dryrun)
			{
				$this->log("Would run {$direction}() {$scope}/{$file}", 'success');
			}
			else if ($logOnly)
			{
				$this->recordMigration($file, $scope, $hash, $direction);
				$this->log("Marking as run: {$direction}() in {$scope}/{$file}", 'success');
			}
			else
			{
				// Try running the '$direction' SQL
				if (method_exists($class, $direction))
				{
					try
					{
						$result = $class->$direction();
						$errors = $class->getErrors();
						$status = 'success';

						// Loop through errors if we have them
						if ($errors && count($errors) > 0)
						{
							foreach ($errors as $error)
							{
								if ($error['type'] == 'fatal')
								{
									// Completely failed...log and stop immediately
									$this->log("Error: running {$direction}() resulted in a fatal error in {$scope}/{$file}: {$error['message']}", 'error');
									$this->recordMigration($file, $scope, $hash, $direction, 'fatal');
									return false;
								}
								else if ($error['type'] == 'warning')
								{
									// Just a warning...display message and carry on (my wayward son)
									$this->log("Warning: running {$direction}() resulted in a non-fatal error in {$scope}/{$file}: {$error['message']}", 'warning');
									$status = 'warning';
									continue;
								}
								else if ($error['type'] == 'info')
								{
									// Informational error (is that a real thing?)
									$this->log("Info: running {$direction}() noted this in {$scope}/{$file}: {$error['message']}", 'info');
								}
							}
						}

						$this->recordMigration($file, $scope, $hash, $direction, $status);
						$this->log("Completed {$direction}() in {$scope}/{$file}", 'success');
					}
					catch (\Hubzero\Database\Exception\QueryFailedException $e)
					{
						$this->log("Error: running {$direction}() resulted in\n\n{$e->getMessage()}\n\nin {$scope}/{$file}", 'error');
						return false;
					}
					catch (\PDOException $e)
					{
						$this->log("Error: running {$direction}() resulted in\n\n{$e->getMessage()}\n\nin {$scope}/{$file}", 'error');
						return false;
					}
				}
			}
		}

		// Now, fire hooks
		if (!$dryrun && !$logOnly)
		{
			$this->fireHooks('onAfterMigrate');
		}

		return true;
	}

	/**
	 * Fire migration pre/post hooks
	 *
	 * @param   string  $timing  Which hooks to fire
	 * @return  void
	 **/
	private function fireHooks($timing)
	{
		$exclude = array('.', '..');
		$hooks   = [];

		foreach ($this->searchPaths as $path)
		{
			// Make sure we have a hooks directroy
			if (is_dir($path . DS . 'migrations' . DS . 'hooks'))
			{
				$found = [];
				foreach (glob($path . DS . 'migrations' . DS . 'hooks' . DS . '*.php') as $hook)
				{
					// We just want the filename, so strip the path off
					$hook = str_replace($path . DS . 'migrations' . DS . 'hooks' . DS, '', $hook);

					$found[] = [
						'base' => $path . DS . 'migrations' . DS . 'hooks',
						'name' => $hook
					];
				}

				$hooks = array_merge($hooks, $found);
			}
		}

		if (count($hooks) > 0)
		{
			foreach ($hooks as $hook)
			{
				// Get the file name
				$fullpath = $hook['base'] . DS . $hook['name'];

				// Include the file
				if (is_file($fullpath))
				{
					require_once $fullpath;
				}
				else
				{
					continue;
				}

				// Set classname
				$info      = pathinfo($hook['name']);
				$classname = $info['filename'];

				// Instantiate our class
				$class = new $classname($this->db, $this->callbacks);
				$hookTiming = $class->getOption('timing');

				if ($hookTiming != $timing && $hookTiming != 'onAll')
				{
					continue;
				}

				if (method_exists($class, 'fire'))
				{
					$result = $class->fire();

					if (is_array($result) && !$result['success'])
					{
						// Just a warning...display message and carry on (my wayward son)
						$message = (isset($result['message']) && !empty($result['message'])) ? $result['message'] : '[no message provided]';
						$this->log("Warning: {$timing} hook '{$hook['name']}' resulted in an error: {$message}", 'warning');
					}
				}
			}
		}
	}

	/**
	 * Record migration in migrations table
	 *
	 * @param   string  $file       The path to file being recorded
	 * @param   string  $scope      The folder of migration
	 * @param   string  $hash       The hash of file
	 * @param   string  $direction  Up or down
	 * @param   string  $status     The status of the run
	 * @return  bool
	 **/
	public function recordMigration($file, $scope, $hash, $direction, $status = 'success')
	{
		// Catch instances where we don't have a status field yet
		// and mimic prior behavior where these runs were not logged
		if (!$this->db->tableHasField($this->get('tbl_name'), 'status') && $status != 'success')
		{
			return true;
		}

		// Try inserting a migration record into the database
		try
		{
			$date = new \Hubzero\Utility\Date();

			// Craete our object to insert
			$obj = (object) array(
				'file'      => $file,
				'hash'      => $hash,
				'direction' => $direction,
				'date'      => $date->toSql(),
				'action_by' => (php_sapi_name() == 'cli') ? exec("whoami") : \User::get('id')
			);

			if ($this->db->tableHasField($this->get('tbl_name'), 'scope'))
			{
				$obj->scope = $scope;
			}

			if ($this->db->tableHasField($this->get('tbl_name'), 'status'))
			{
				$obj->status = $status;
			}

			$this->db->insertObject($this->get('tbl_name'), $obj);
			return true;
		}
		catch (\Hubzero\Database\Exception\QueryFailedException $e)
		{
			$this->log("Failed inserting migration record: {$e->getMessage()}", 'error');
			return false;
		}
	}

	/**
	 * Return migration run history
	 *
	 * @return  mixed  False on error, array on success
	 **/
	public function history()
	{
		try
		{
			$query = "SELECT * FROM " . $this->db->quoteName($this->get('tbl_name'));
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			return $results;
		}
		catch (\Hubzero\Database\Exception\QueryFailedException $e)
		{
			$this->log("Failed to retrieve history.", 'error');
			return false;
		}
	}

	/**
	 * Set ignore callbacks to true
	 *
	 * @return  void
	 **/
	public function ignoreCallbacks()
	{
		$this->ignoreCallbacks = true;
	}

	/**
	 * Set ignore callbacks to false
	 *
	 * @return  void
	 **/
	public function honorCallbacks()
	{
		$this->ignoreCallbacks = false;
	}

	/**
	 * Logging mechanism
	 *
	 * @param   string  $message  Message to log
	 * @param   string  $type     Message type, can be one predefined values from output class (not specified will default to 'normal' text)
	 * @return  void
	 **/
	public function log($message, $type = null)
	{
		$this->log[] = array('message' => $message, 'type' => $type);

		if (!$this->ignoreCallbacks && isset($this->callbacks['message']) && is_callable($this->callbacks['message']))
		{
			$this->callbacks['message']($message, $type);
		}
	}

	/**
	 * Set the table name used for internal logging of migrations
	 *
	 * @param   string  $tbl_name  The table name to set
	 * @return  void
	 **/
	public function setTableName($tbl_name)
	{
		$this->tbl_name = $tbl_name;
	}

	/**
	 * Register a callback
	 *
	 * @param   string   $name      The callback name
	 * @param   closure  $callback  The function to run
	 * @return  void
	 **/
	public function registerCallback($name, $callback)
	{
		$this->callbacks[$name] = $callback;
	}

	/**
	 * Attempt to create needed migrations table
	 *
	 * @param   object  $db  The database connection object
	 * @return  bool
	 **/
	private function createMigrationsTable($db)
	{
		$this->log('Migrations table did not exist...attempting to create it now');

		$query = "CREATE TABLE `{$this->get('tbl_name')}` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`file` varchar(255) NOT NULL DEFAULT '',
					`scope` varchar(255) NOT NULL,
					`hash` char(32) NOT NULL DEFAULT '',
					`direction` varchar(10) NOT NULL DEFAULT '',
					`date` datetime NOT NULL,
					`action_by` varchar(255) NOT NULL DEFAULT '',
					`status` varchar(255) NOT NULL DEFAULT '',
					PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		// Try creating the migrations table now
		try
		{
			$db->setQuery($query);
			$db->query();
			$this->log('Migrations table successfully created');
			return true;
		}
		catch (\Hubzero\Database\Exception\QueryFailedException $e)
		{
			$this->log('Unable to create needed migrations table', 'error');
			return false;
		}
	}
}
