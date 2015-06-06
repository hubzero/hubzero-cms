<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Content;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
* HUBzero Database migrations class
*
* @TODO: add flag to ignore development scripts?
*/
class Migration
{
	/**
	 * Document root in which to search for migration scripts
	 *
	 * @var string
	 **/
	private $docroot = '';

	/**
	 * Array holding paths to migration scripts
	 *
	 * @var array
	 **/
	private $files = array();

	/**
	 * Array holding files affected during this migration (i.e. those that are/would be run)
	 *
	 * @var array
	 **/
	private $affectedFiles = array();

	/**
	 * Variable holding database object
	 *
	 * If an alternate db is given, this db will hold the connection to the
	 * primary joomla database where the extensions and logs tables are found
	 *
	 * @var string
	 **/
	private $db = null;

	/**
	 * Alternate db, passed to migrations if specified
	 *
	 * @var string
	 **/
	private $altDb = null;

	/**
	 * Log messages themselves (stored as array to return to browser, or other client)
	 *
	 * @var array
	 **/
	private $log = array();

	/**
	 * Date of last migrations run - implicit by last log entry
	 *
	 * @var array
	 **/
	private $last_run = array('up'=>null, 'down'=>null);

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
	 * Keep track of query scope (just so we don't have to compute multiple times)
	 *
	 * @var string
	 **/
	private $queryScope = '';

	/**
	 * Whether or not to ignore callbacks
	 *
	 * @var bool
	 **/
	private $ignoreCallbacks = false;

	/**
	 * Constructor
	 *
	 * @param $docroot - default null, which should then resolve to hub docroot
	 * @return void
	 **/
	public function __construct($docroot=null, $db=null)
	{
		// Try to determine the document root if none provided
		if (is_null($docroot))
		{
			$this->docroot = JPATH_ROOT;
		}
		else
		{
			$this->docroot = rtrim($docroot, '/');
		}

		// Setup the database connection
		if (!$this->db = $this->getDBO())
		{
			$this->log('Error: database connection failed.', 'error');
			return false;
		}

		if (isset($db))
		{
			$this->altDb = $db;
		}

		// Try to figure out the date of the last file run
		try
		{
			$scope = '';

			if ($this->db->tableHasField($this->get('tbl_name'), 'scope'))
			{
				// Scope could potentially be with or without document root
				$scopes = array(
					$this->db->quote($this->docroot . DS . 'migrations'),
					$this->db->quote(str_replace(JPATH_ROOT . DS, '', $this->docroot . DS . 'migrations'))
				);

				$scope = ' AND (`scope` = ' . implode(' OR `scope` = ', $scopes) . ')';
			}

			$this->queryScope = $scope;

			$this->db->setQuery('SELECT `file` FROM `'.$this->get('tbl_name').'` WHERE `direction` = \'up\'' . $this->queryScope . ' ORDER BY `file` DESC LIMIT 1');
			$rowup = $this->db->loadAssoc();

			$this->db->setQuery('SELECT `file` FROM `'.$this->get('tbl_name').'` WHERE `direction` = \'down\'' . $this->queryScope . ' ORDER BY `file` DESC LIMIT 1');
			$rowdown = $this->db->loadAssoc();

			if (count($rowup) > 0)
			{
				$this->last_run['up'] = substr($rowup['file'], 9, 14);
			}
			if (count($rowdown) > 0)
			{
				$this->last_run['down'] = substr($rowdown['file'], 9, 14);
			}
		}
		catch (\PDOException $e)
		{
			$this->log('Error: failed to look up last migrations log entry.', 'error');
			return false;
		}
	}

	/**
	 * Getter for class private variables
	 *
	 * @param $var - var to retrieve
	 * @return class var
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
	 * @return database object
	 **/
	public function getDBO()
	{
		// Include the joomla configuration file
		if (file_exists($this->docroot . '/configuration.php'))
		{
			// If there's one in the provided doc root, use that
			require_once $this->docroot . '/configuration.php';
		}
		else
		{
			if (file_exists(JPATH_ROOT . '/configuration.php'))
			{
				// If there's one in the provided doc root, use that
				require_once JPATH_ROOT . '/configuration.php';
			}
			else
			{
				$this->log('Error: document root does not contain a configuration file', 'error');
				return false;
			}
		}

		// Instantiate a config object
		$config = new \JConfig();

		$db = \JDatabase::getInstance(
			array(
				'driver'   => 'pdo',
				'host'     => $config->host,
				'user'     => $config->user,
				'password' => $config->password,
				'database' => $config->db,
				'prefix'   => 'jos_'
			)
		);

		// Test the connection
		if (!$db->connected())
		{
			$this->log('PDO connection failed', 'error');
			return false;
		}

		// Check for the existance of the migrations table
		$tables = $db->getTableList();
		$prefix = $db->getPrefix();

		if (in_array('migrations', $tables))
		{
			$this->setTableName('migrations');
		}
		else if (in_array($prefix . 'migrations', $tables))
		{
			$this->setTableName('#__migrations');
		}
		else if ($this->createMigrationsTable($db) === false)
		{
			return false;
		}

		// Add a callback so that a migration can update $this in real time if necessary
		$this->registerCallback('migration', $this);

		return $db;
	}

	/**
	 * Find all migration scripts
	 *
	 * @param $extension - only look for migrations for this extension
	 * @param $file      - specific file to run
	 * @return array of file paths
	 **/
	public function find($extension=null, $file=null)
	{
		// Exclude certain thiings from our search
		$exclude = array(".", "..");

		$files = array_diff(scandir($this->docroot . DS . 'migrations'), $exclude);
		$ext   = '';

		if (!is_null($file))
		{
			if (in_array($file, $files))
			{
				$this->files[] = $file;
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

		foreach ($files as $file)
		{
			// Make sure they have a php extension and proper filename format
			if (preg_match('/^Migration[0-9]{14}[[:alnum:]]+\.php$/', $file))
			{
				// If an extension was provided...match against it...
				if (empty($ext) || (!empty($ext) && preg_match('/Migration[0-9]{14}'.$ext.'\.php/', $file)))
				{
					$this->files[] = $file;
				}
			}
		}

		return true;
	}

	/**
	 * Migrate up/down on all files gathered via 'find'
	 *
	 * @param $direction   - direction to migrate (up or down)
	 * @param $force       - run the update, even if the database says it's already been run
	 * @param $dryrun      - run the udpate, but only display what would be changed, wihthout actually doing anything
	 * @param $ignoreDates - run the update on all files found lacking entries in the db, not just those after the last run date
	 * @param $logOnly     - run the update, and mark as run, but don't actually run sql (usefully to mark changes that had already been made manually)
	 * @return bool - success
	 **/
	public function migrate($direction='up', $force=false, $dryrun=false, $ignoreDates=false, $logOnly=false)
	{
		// Make sure we have files
		if (empty($this->files))
		{
			$this->log("There were no migrations to run");
			return true;
		}

		// Notify if we're making a dry run
		if ($dryrun)
		{
			$this->log("Dry run: no changes will be made!");
		}

		// Notify if we're ignoring dates
		if ($ignoreDates)
		{
			$this->log("Ignore dates: all eligible files will be included!");
		}

		// Now, fire hooks
		if (!$dryrun && !$logOnly)
		{
			$this->fireHooks('onBeforeMigrate');
		}

		// Loop through files and run their '$direction' method
		foreach ($this->files as $file)
		{
			// Create a hash of the file (not using this at the moment)
			$hash = hash('md5', $file);

			// Don't compare dates if we're doing a full scan
			if (!$ignoreDates)
			{
				// Get date from file (would use last modified date, but git changes that)
				// Running up, down, and up again won't do anything, because the file is now older than the last run.
				// Currently, one should run $force or $ignoreDates and include an extension to override that behavior
				$date = substr($file, 9, 14);
				if (is_numeric($date))
				{
					if (is_numeric($this->last_run[$direction]) && $date <= $this->last_run[$direction] && !$force)
					{
						// This migration is older than the current, but let's see if we should inform that it should still be run
						$this->db->setQuery("SELECT `direction` FROM `{$this->get('tbl_name')}` WHERE `file` = " . $this->db->Quote($file) . "{$this->queryScope} ORDER BY `date` DESC LIMIT 1");
						$row = $this->db->loadResult();

						// Check if last run was either not in the current direction we're going,
						// or if it hasn't been run at all and we're not going down
						if ($row != $direction || (!$row && $direction != 'down'))
						{
							$this->log("Migration {$direction}() in {$file} has not been run and should be (by using the -i option)", 'warning');
						}

						continue;
					}
				}
				else
				{
					// Filename did not contain a valid date
					$this->log("File did not contain a valid date ({$file}).", 'warning');
					continue;
				}
			}

			// Initialize ignore
			$ignore = false;

			// Check to see if this file has already been run
			try
			{
				// Look to the database log to see the last run on this file
				$this->db->setQuery("SELECT `direction` FROM `{$this->get('tbl_name')}` WHERE `file` = " . $this->db->Quote($file) . "{$this->queryScope} ORDER BY `date` DESC LIMIT 1");
				$row = $this->db->loadResult();

				// If the last migration for this file doesn't exist, or, it was the opposite of $direction, we can go ahead and run it.
				// But, if the last run was the same direction as is currently being run, we shouldn't run it again
				// Also, don't run down first...it's should come after up
				if ($row == $direction || (!$row && $direction == 'down'))
				{
					$ignore = true;
				}
			}
			catch (\PDOException $e)
			{
				$ignore = false;
			}

			// Ignore this file, if we've already run it (unless it's being forced)
			if ($ignore && !$force)
			{
				if ($dryrun)
				{
					$this->log("Would ignore {$direction}() {$file}");
					continue;
				}
				elseif (!$row)
				{
					$this->log("Ignoring {$direction}() - you should run up first ({$file})");
					continue;
				}
				else
				{
					$this->log("Ignoring {$direction}() {$file}");
					continue;
				}
			}

			// Get the file name
			$info = pathinfo($file);

			$fullpath = $this->docroot . DS . 'migrations' . DS . $file;

			// Include the file
			if (!is_file($fullpath))
			{
				$this->log("{$fullpath} is not a valid file", 'warning');
				continue;
			}
			else
			{
				require_once $fullpath;
			}

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
			$class = new $classname($this->db, $this->callbacks, $this->altDb);

			// Check if we're making a dry run, or only logging changes
			if ($dryrun || $logOnly)
			{
				if ($dryrun)
				{
					$this->log("Would run {$direction}() {$file}", 'success');
				}
				elseif ($logOnly)
				{
					$this->recordMigration($file, str_replace(JPATH_ROOT . DS, '', $this->docroot . DS . 'migrations'), $hash, $direction);
					$this->log("Marking as run: {$direction}() in {$file}", 'success');
				}
			}
			else
			{
				// Check and run 'pre' if necessary
				if (method_exists($class, 'pre'))
				{
					try
					{
						if ($class->pre() === false)
						{
							$this->log("Running pre() returned false {$file}", 'warning');
							continue;
						}

						$this->log("Running pre() {$file}");
					}
					catch (\PDOException $e)
					{
						$this->log("Error: running pre() resulted in\n\n{$e}\n\nin {$file}", 'error');
						return false;
					}
				}

				// Try running the '$direction' SQL
				if (method_exists($class, $direction))
				{
					try
					{
						$result = $class->$direction();
						$errors = $class->getErrors();

						// Loop through errors if we have them
						if ($errors && count($errors) > 0)
						{
							// Track whether we should log this as completed or continue to next file
							$log = true;
							foreach ($errors as $error)
							{
								if ($error['type'] == 'fatal')
								{
									// Completely failed...stop immediately
									$this->log("Error: running {$direction}() resulted in a fatal error in {$file}: {$error['message']}", 'error');
									return false;
								}
								else if ($error['type'] == 'warning')
								{
									// Just a warning...display message and carry on (my wayward son)
									$this->log("Warning: running {$direction}() resulted in a non-fatal error in {$file}: {$error['message']}", 'warning');
									// Continue...i.e. don't log that this migration was run, so it shows up again on the next run
									$log = false;
									continue;
								}
								else if ($error['type'] == 'info')
								{
									// Informational error (is that a real thing?)
									$this->log("Info: running {$direction}() noted this in {$file}: {$error['message']}", 'info');
								}
							}

							// Now check if we're logging this file
							if (!$log)
							{
								continue;
							}
						}

						$this->recordMigration($file, str_replace(JPATH_ROOT . DS, '', $this->docroot . DS . 'migrations'), $hash, $direction);
						$this->log("Completed {$direction}() in {$file}", 'success');
					}
					catch (\PDOException $e)
					{
						$this->log("Error: running {$direction}() resulted in\n\n{$e}\n\nin {$file}", 'error');
						return false;
					}
				}

				// Check and run 'post'
				if (method_exists($class, 'post'))
				{
					try
					{
						if ($class->post() === false)
						{
							$this->log("Running post() returned false {$file}", 'warning');
							continue;
						}

						$this->log("Running post() {$file}");
					}
					catch (\PDOException $e)
					{
						$this->log("Error: running post() resulted in\n\n{$e}\n\nin {$file}", 'error');
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
	 * @param  (string) $timing - which hooks to fire
	 * @return void
	 **/
	private function fireHooks($timing)
	{
		$exclude   = array(".", "..");
		$directory = $this->docroot . DS . 'migrations' . DS . 'hooks';

		// Make sure we have a hooks directroy
		if (!is_dir($directory))
		{
			return;
		}

		$hooks = array_diff(scandir($directory), $exclude);

		if (count($hooks) > 0)
		{
			foreach ($hooks as $hook)
			{
				// Get the file name
				$info     = pathinfo($hook);
				$fullpath = $this->docroot . DS . 'migrations' . DS . 'hooks' . DS . $hook;

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
						$this->log("Warning: {$timing} hook '{$hook}' resulted in an error: {$message}", 'warning');
					}
				}
			}
		}
	}

	/**
	 * Record migration in migrations table
	 *
	 * @param $file  - path to file being recorded
	 * @param $scope - folder of migration
	 * @param $hash  - hash of file
	 * @param $direction - up or down
	 * @return void
	 **/
	public function recordMigration($file, $scope, $hash, $direction)
	{
		// Try inserting a migration record into the database
		try
		{
			$date = new \JDate();

			// Craete our object to insert
			$obj = (object) array(
					'file'      => $file,
					'hash'      => $hash,
					'direction' => $direction,
					'date'      => $date->toSql(),
					'action_by' => (php_sapi_name() == 'cli') ? exec("whoami") : \JFactory::getUser()->get('id')
				);

			if ($this->db->tableHasField($this->get('tbl_name'), 'scope'))
			{
				$obj->scope = $scope;
			}

			$this->db->insertObject($this->get('tbl_name'), $obj);
		}
		catch (\PDOException $e)
		{
			$this->log("Failed inserting migration record: {$e}", 'error');
			return false;
		}
	}

	/**
	 * Return migration run history
	 *
	 * @return (array)
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
		catch (\PDOException $e)
		{
			$this->log("Failed to retrieve history.", 'error');
			return false;
		}
	}

	/**
	 * Set ignore callbacks to true
	 *
	 * @return void
	 **/
	public function ignoreCallbacks()
	{
		$this->ignoreCallbacks = true;
	}

	/**
	 * Set ignore callbacks to false
	 *
	 * @return void
	 **/
	public function honorCallbacks()
	{
		$this->ignoreCallbacks = false;
	}

	/**
	 * Logging mechanism
	 *
	 * @param $message - message to log
	 * @param $type    - message type, can be one predefined values from output class (not specified will default to 'normal' text)
	 * @return log messages
	 **/
	public function log($message, $type=null)
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
	 * @return void
	 **/
	public function setTableName($tbl_name)
	{
		$this->tbl_name = $tbl_name;
	}

	/**
	 * Register a callback
	 *
	 * @param  (string)  $name - callback name
	 * @param  (closure) $callback - function to run
	 * @return void
	 **/
	public function registerCallback($name, $callback)
	{
		$this->callbacks[$name] = $callback;
	}

	/**
	 * Attempt to create needed migrations table
	 *
	 * @return bool
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
		catch (\PDOException $e)
		{
			$this->log('Unable to create needed migrations table', 'error');
			return false;
		}
	}
}
