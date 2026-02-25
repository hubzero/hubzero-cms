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
     * @public array
     **/
    private $searchPaths = [];

    /**
     * Array holding paths to migration scripts
     *
     * @public array
     **/
    private $files = [];

    /**
     * Array holding files affected during this migration (i.e. those that are/would be run)
     *
     * @public array
     **/
    private $affectedFiles = [];

    /**
     * Variable holding database object
     *
     * If an alternate db is given, this db will hold the connection to the
     * primary hub database where the extensions and logs tables are found
     *
     * @public string
     **/
    private $db = null;

    /**
     * Alternate db, passed to migrations if specified
     *
     * @public string
     **/
    private $runDb = null;

    /**
     * Log messages themselves (stored as array to return to browser, or other client)
     *
     * @public array
     **/
    private $log = [];

    /**
     * Array of callbacks
     *
     * @public array
     **/
    private $callbacks;

    /**
     * Table holding migration entries
     *
     * @public string
     **/
    private $tbl_name = '#__migrations';

    /**
     * Whether or not to ignore callbacks
     *
     * @public bool
     **/
    private $ignoreCallbacks = false;

    /**
     * All-or-nothing transaction mode
     *
     * When enabled, wraps all migrations in a single transaction.
     * If any migration fails, the entire batch is rolled back.
     *
     * @public bool
     **/
    private $allOrNothing = false;

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
        if (is_null($docroot)) {
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

            foreach ($nodes as $base) {
                if (!is_dir($base)) {
                    continue;
                }

                $directories = array_diff(scandir($base), ['.', '..']);

                foreach ($directories as $directory) {
                    if (!is_dir($base . DS . $directory)) {
                        continue;
                    }

                    // Does the directory conform to extension naming conventions?
                    if (strstr($directory, '.') || strstr($directory, ' ')) {
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

            foreach ($nodes as $base) {
                if (!is_dir($base)) {
                    continue;
                }

                $directories = array_diff(scandir($base), ['.', '..']);

                foreach ($directories as $directory) {
                    if (!is_dir($base . DS . $directory)) {
                        continue;
                    }

                    $subdirectories = array_diff(scandir($base . DS . $directory), ['.', '..']);

                    foreach ($subdirectories as $subdirectory) {
                        // Does the directory conform to extension naming conventions?
                        if (strstr($subdirectory, '.') || strstr($subdirectory, ' ')) {
                            continue;
                        }

                        $this->addSearchPath($base . DS . $directory . DS . $subdirectory);
                    }
                }
            }
        } else {
            $docroot = rtrim($docroot, DS);
            $this->addSearchPath($docroot);
        }

        // Setup the database connection
        if (!$this->db = $this->getDBO()) {
            $this->log('Error: database connection failed.', 'error');
            return false;
        }

        // This is the database that migrations will run against
        // This is used for super group migrations, that don't run against
        // the default database schema
        if (isset($runDb)) {
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
     * @param   string  $public the public to retrieve
     * @return  mixed
     **/
    public function get($var)
    {
        if (property_exists($this, $var)) {
            return $this->$var;
        } else {
            return false;
        }
    }

    /**
     * Enable or disable all-or-nothing transaction mode
     *
     * When enabled, wraps all migrations in a single transaction.
     * If any migration fails, the entire batch is rolled back.
     *
     * Note: MySQL DDL statements (CREATE TABLE, ALTER TABLE, etc.) cause
     * implicit commits and cannot be rolled back. This mode is most useful
     * for data migrations or with databases that support transactional DDL
     * (PostgreSQL, SQLite).
     *
     * @param   bool  $enabled  Whether to enable all-or-nothing mode
     * @return  $this
     **/
    public function setAllOrNothing($enabled = true)
    {
        $this->allOrNothing = (bool) $enabled;
        return $this;
    }

    /**
     * Check if all-or-nothing mode is enabled
     *
     * @return  bool
     **/
    public function isAllOrNothing()
    {
        return $this->allOrNothing;
    }

    /**
     * Setup database connect, test, return object
     *
     * @return  object
     **/
    public function getDBO()
    {
        $db = \Hubzero\Facades\App::get('db');

        // Test the connection
        if (!$db->connected()) {
            $this->log('PDO connection failed', 'error');
            return false;
        }

        // Check for the existance of the migrations table
        $tables = $db->getTableList();
        $prefix = $db->getPrefix();
        $tableset = false;

        if (in_array('migrations', $tables)) {
            $this->setTableName('migrations');
            $tableset = true;
        }

        if (in_array($prefix . 'migrations', $tables)) {
            if ($tableset) {
                $this->log('Tables `migrations` and `' . $prefix . 'migrations` both exist', 'error');
                return false;
            }

            $this->setTableName('#__migrations');
            $tableset = true;
        }

        if (!$tableset) {
            if ($this->createMigrationsTable($db) === false) {
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

        foreach ($this->searchPaths as $path) {
            if (!is_dir($path . DS . 'migrations')) {
                continue;
            }
            $found = array_diff(scandir($path . DS . 'migrations'), $exclude);

            foreach ($found as $f) {
                $files[$path . DS . 'migrations' . DS . $f] = $f;
            }
        }

        asort($files);

        if (!is_null($file)) {
            if (in_array($file, $files)) {
                $this->files[] = array_search($file, $files);
                return true;
            } else {
                $this->log("Provided file ({$file}) could not be found.", 'error');
                return false;
            }
        }

        if (!is_null($extension)) {
            $parts = explode('_', $extension);
            foreach ($parts as $part) {
                $ext .= ucfirst($part);
            }
        }

        foreach ($files as $path => $file) {
            // Make sure they have a php extension and proper filename format
            if (preg_match('/^Migration[0-9]{14}[[:alnum:]]+\.php$/', $file)) {
                // If an extension was provided...match against it...
                if (empty($ext) || (!empty($ext) && preg_match('/Migration[0-9]{14}' . $ext . '\.php/', $file))) {
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
     * @param   bool    $dryrun     Run the udpate, but only display what would be changed,
     *                              wihthout actually doing anything
     * @param   bool    $listAll    List all files found, not just those needing to be run
     * @param   bool    $logOnly    Run the update, and mark as run, but don't actually run sql
     *                              (usefully to mark changes that had already been made manually)
     * @return  bool
     **/
    public function migrate($direction = 'up', $force = false, $dryrun = false, $listAll = false, $logOnly = false)
    {
        // Make sure we have files
        if (empty($this->files)) {
            $this->log("There were no migrations to run");
            return true;
        }

        if (!$this->db) {
            return false;
        }

        // Notify if we're making a dry run
        if ($dryrun) {
            $this->log("Dry run: no changes will be made!");
        }

        // Notify if we're listing all files
        if ($listAll) {
            $this->log("List all: all found files will be listed!");
        }

        // Now, fire hooks
        if (!$dryrun && !$logOnly) {
            $this->fireHooks('onBeforeMigrate');
        }

        $hasStatus = $this->db->tableHasField($this->get('tbl_name'), 'status');

        // All-or-nothing mode: start batch transaction
        $runDb = $this->runDb ?? $this->db;
        $batchTransactionStarted = false;
        $completedMigrations = [];

        if ($this->allOrNothing && !$dryrun && !$logOnly) {
            $this->log("All-or-nothing mode: wrapping all migrations in a single transaction");
            $this->log("Warning: MySQL DDL statements cause implicit commits and cannot be rolled back", 'warning');
            $runDb->transactionStart();
            $batchTransactionStarted = true;
        }

        // Loop through files and run their '$direction' method
        foreach ($this->files as $fullpath) { //$file)
        // Get just the file
            $file = basename($fullpath);

            // Create a hash of the file (not using this at the moment)
            $hash = hash('md5', $file);

            // Get the file name
            $info = pathinfo($file);

            // Make sure the file exists
            // If it doesn't, there's no point going any further
            if (!is_file($fullpath)) {
                $this->log("{$fullpath} is not a valid file", 'warning');
                continue;
            }

            // Generate the scope
            // This will be the path to the migration, minus the document root
            // ex: "core/migrations" or "app/components/com_example/migrations"
            $scope = str_replace(PATH_ROOT . DS, '', dirname($fullpath));

            // Check to see if this file has already been run
            try {
                // Look to the database log to see the last run on this file
                $query = "SELECT `direction`";

                if ($this->db->tableHasField($this->get('tbl_name'), 'status')) {
                    $query .= ", `status`";
                }

                $query .= " FROM `{$this->get('tbl_name')}` WHERE `file` = " . $this->db->quote($file);

                if ($this->db->tableHasField($this->get('tbl_name'), 'scope')) {
                    if ($scope == 'core/migrations') {
                        $query .= " AND (`scope`='' OR `scope` IN (" .
                            $this->db->quote($scope) .
                            "," .
                            $this->db->quote('migrations') .
                            "))";
                    } else {
                        $query .= " AND `scope` = " . $this->db->quote($scope);
                    }
                }

                $query .= " ORDER BY `date` DESC LIMIT 1";

                $this->db->setQuery($query);
                $row = $this->db->loadObject();

                // Decide whether or not we want to show the file at all
                // If list all, then we just show everything
                // If force, we assume we have to show it
                if (!$listAll && !$force) {
                    // If we have a row (meaning it's been run at least once before),
                    // and the direction is the same as is being run now, then it's already been run
                    if ($row && $row->direction == $direction) {
                        // The last check is to make sure that the previous run we see was a success
                        // If we don't have a status line (which is an implicit success),
                        // or we do have a status and it was a success, then we can reasonably skip this entry
                        if (!$hasStatus || ($hasStatus && $row->status == 'success')) {
                            continue;
                        }
                    }
                }

                // Now, if we are showing the file, should it actually be run?
                if (!$force) {
                    // If we have no row at all
                    if (!$row && $direction == 'down') {
                        $this->log("Ignoring {$direction}() - you should run up first ({$scope}/{$file})");
                        continue;
                    } elseif ($row && $row->direction == $direction) {
                    // If the last run was the same direction as is currently being run, we shouldn't run it again
                        // Lastly, check status as well
                        if (!$hasStatus || ($hasStatus && $row->status == 'success')) {
                            if ($dryrun) {
                                $this->log("Would ignore {$direction}() {$scope}/{$file}");
                                continue;
                            } else {
                                $this->log("Ignoring {$direction}() {$scope}/{$file}");
                                continue;
                            }
                        }
                    }
                }
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Our query failed altogether...that's not good
                $this->log("Error: the check for preexisting migrations failed!", 'error');
                return false;
            }

            require_once $fullpath;

            // Set classname
            $classname = $info['filename'];

            // Make sure file and classname match
            // First try unqualified (backward compatible with non-namespaced migrations)
            if (!class_exists($classname)) {
                // Derive the expected namespace from the file path
                $namespace = $this->deriveNamespaceFromPath($fullpath);

                if ($namespace) {
                    $fqcn = $namespace . '\\' . $classname;
                    if (class_exists($fqcn)) {
                        $classname = $fqcn;
                    } else {
                        $this->log("{$info['filename']} class not found (expected {$fqcn})", 'warning');
                        continue;
                    }
                } else {
                    $this->log("{$info['filename']} does not have a class of the same name", 'warning');
                    continue;
                }
            }

            // We've made it this far, add this file to list of affected files
            $this->affectedFiles[] = $info['filename'];

            // Instantiate our class
            $class = new $classname($this->db, $this->callbacks, $this->runDb);

            // Check if we're making a dry run, or only logging changes
            if ($dryrun) {
                $this->log("Would run {$direction}() {$scope}/{$file}", 'success');
            } elseif ($logOnly) {
                $this->recordMigration($file, $scope, $hash, $direction);
                $this->log("Marking as run: {$direction}() in {$scope}/{$file}", 'success');
            } else {
                // Try running the '$direction' SQL
                if (method_exists($class, $direction)) {
                    // Determine if we should use transaction wrapping
                    // Migrations can opt-out by setting $useTransaction = false
                    // Skip per-migration transactions when in all-or-nothing mode
                    $useTransaction = $this->allOrNothing ? false : $this->shouldUseTransaction($class);

                    // Start transaction if enabled (per-migration, not batch)
                    if ($useTransaction) {
                        $runDb->transactionStart();
                    }

                    // Track execution time
                    $startTime = microtime(true);

                    try {
                        $result = $class->$direction();
                        $errors = $class->getErrors();
                        $status = 'success';

                        // Calculate execution time in milliseconds
                        $executionTime = (int) round((microtime(true) - $startTime) * 1000);

                        // Loop through errors if we have them
                        if ($errors && count($errors) > 0) {
                            foreach ($errors as $error) {
                                if ($error['type'] == 'fatal') {
                                    // Completely failed...rollback and stop immediately
                                    if ($this->allOrNothing && $batchTransactionStarted) {
                                        // Rollback entire batch
                                        $runDb->transactionRollback();
                                        $this->log(
                                            "All-or-nothing: rolling back entire batch due to fatal error",
                                            'error'
                                        );
                                    } elseif ($useTransaction) {
                                        $runDb->transactionRollback();
                                    }
                                    $this->log("Error: running {$direction}() resulted in a fatal error in
                                        {$scope}/{$file}: {$error['message']}", 'error');
                                    // Only record if not in all-or-nothing mode
                                    if (!$this->allOrNothing) {
                                        $this->recordMigration(
                                            $file,
                                            $scope,
                                            $hash,
                                            $direction,
                                            'fatal',
                                            $executionTime
                                        );
                                    }
                                    return false;
                                } elseif ($error['type'] == 'warning') {
                                    // Just a warning...display message and carry on (my wayward son)
                                    $this->log("Warning: running {$direction}() resulted in a non-fatal error in
                                        {$scope}/{$file}: {$error['message']}", 'warning');
                                    $status = 'warning';
                                    continue;
                                } elseif ($error['type'] == 'info') {
                                    // Informational error (is that a real thing?)
                                    $this->log("Info: running {$direction}() noted this in {$scope}/{$file}:
                                        {$error['message']}", 'info');
                                } elseif ($error['type'] == 'skipped') {
                                    // Migration chose to skip - will retry on next run
                                    $this->log(
                                        "Skipped {$direction}() in {$scope}/{$file}: {$error['message']}",
                                        'info'
                                    );
                                    $status = 'skipped';
                                }
                            }
                        }

                        // Commit transaction on success (non-fatal) - only for per-migration transactions
                        if ($useTransaction) {
                            $runDb->transactionCommit();
                        }

                        // Record or track the migration
                        if ($this->allOrNothing) {
                            // Track for batch recording after successful commit
                            $completedMigrations[] = [
                                'file' => $file,
                                'scope' => $scope,
                                'hash' => $hash,
                                'direction' => $direction,
                                'status' => $status,
                                'executionTime' => $executionTime
                            ];
                        } else {
                            $this->recordMigration($file, $scope, $hash, $direction, $status, $executionTime);
                        }

                        if ($status === 'skipped') {
                            // Don't log "Completed" for skipped migrations
                        } else {
                            $this->log("Completed {$direction}() in {$scope}/{$file} ({$executionTime}ms)", 'success');
                        }
                    } catch (Migration\SkipMigrationException $e) {
                        // Calculate execution time even for skipped migrations
                        $executionTime = (int) round((microtime(true) - $startTime) * 1000);

                        if ($this->allOrNothing && $batchTransactionStarted) {
                            // In all-or-nothing mode, a skip causes batch rollback
                            $runDb->transactionRollback();
                            $this->log("All-or-nothing: rolling back entire batch due to skip", 'warning');
                            $this->log("Skipped {$direction}() in {$scope}/{$file}: {$e->getMessage()}", 'info');
                            return false;
                        } elseif ($useTransaction) {
                            // Rollback per-migration transaction on skip
                            $runDb->transactionRollback();
                        }

                        // Only record if not in all-or-nothing mode
                        if (!$this->allOrNothing) {
                            $this->recordMigration($file, $scope, $hash, $direction, 'skipped', $executionTime);
                        }
                        $this->log("Skipped {$direction}() in {$scope}/{$file}: {$e->getMessage()}", 'info');
                    } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                        // Rollback transaction on failure
                        if ($this->allOrNothing && $batchTransactionStarted) {
                            $runDb->transactionRollback();
                            $this->log("All-or-nothing: rolling back entire batch due to query failure", 'error');
                        } elseif ($useTransaction) {
                            $runDb->transactionRollback();
                        }
                        $this->
                            log("Error: running {$direction}() resulted in\n\n{$e->
                            getMessage()}\n\nin {$scope}/{$file}", 'error');
                        return false;
                    } catch (\PDOException $e) {
                        // Rollback transaction on failure
                        if ($this->allOrNothing && $batchTransactionStarted) {
                            $runDb->transactionRollback();
                            $this->log("All-or-nothing: rolling back entire batch due to PDO exception", 'error');
                        } elseif ($useTransaction) {
                            $runDb->transactionRollback();
                        }
                        $this->
                            log("Error: running {$direction}() resulted in\n\n{$e->
                            getMessage()}\n\nin {$scope}/{$file}", 'error');
                        return false;
                    }
                }
            }
        }

        // All-or-nothing mode: commit batch transaction and record all migrations
        if ($this->allOrNothing && $batchTransactionStarted) {
            try {
                $runDb->transactionCommit();
                $this->log("All-or-nothing: batch transaction committed successfully", 'success');

                // Now record all completed migrations
                foreach ($completedMigrations as $migration) {
                    $this->recordMigration(
                        $migration['file'],
                        $migration['scope'],
                        $migration['hash'],
                        $migration['direction'],
                        $migration['status'],
                        $migration['executionTime']
                    );
                }

                if (count($completedMigrations) > 0) {
                    $this->log(
                        "All-or-nothing: recorded " . count($completedMigrations) . " migration(s)",
                        'success'
                    );
                }
            } catch (\Exception $e) {
                $this->log("All-or-nothing: failed to commit batch transaction: " . $e->getMessage(), 'error');
                return false;
            }
        }

        // Now, fire hooks
        if (!$dryrun && !$logOnly) {
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

        foreach ($this->searchPaths as $path) {
            // Make sure we have a hooks directroy
            if (is_dir($path . DS . 'migrations' . DS . 'hooks')) {
                $found = [];
                foreach (glob($path . DS . 'migrations' . DS . 'hooks' . DS . '*.php') as $hook) {
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

        if (count($hooks) > 0) {
            foreach ($hooks as $hook) {
                // Get the file name
                $fullpath = $hook['base'] . DS . $hook['name'];

                // Include the file
                if (is_file($fullpath)) {
                    require_once $fullpath;
                } else {
                    continue;
                }

                // Set classname
                $info      = pathinfo($hook['name']);
                $classname = $info['filename'];

                // Support namespaced hooks
                if (!class_exists($classname)) {
                    $namespace = $this->deriveNamespaceFromPath($fullpath);
                    if ($namespace) {
                        $fqcn = $namespace . '\\' . $classname;
                        if (class_exists($fqcn)) {
                            $classname = $fqcn;
                        } else {
                            continue;
                        }
                    } else {
                        continue;
                    }
                }

                // Instantiate our class
                $class = new $classname($this->db, $this->callbacks);
                $hookTiming = $class->getOption('timing');

                if ($hookTiming != $timing && $hookTiming != 'onAll') {
                    continue;
                }

                if (method_exists($class, 'fire')) {
                    $result = $class->fire();

                    if (is_array($result) && !$result['success']) {
                        // Just a warning...display message and carry on (my wayward son)
                        $message = (isset($result['message']) &&
                            !empty($result['message'])) ? $result['message'] : '[no message provided]';
                        $this->log(
                            "Warning: {$timing} hook '{$hook['name']}' resulted in an error: {$message}",
                            'warning'
                        );
                    }
                }
            }
        }
    }

    /**
     * Record migration in migrations table
     *
     * @param   string  $file           The path to file being recorded
     * @param   string  $scope          The folder of migration
     * @param   string  $hash           The hash of file
     * @param   string  $direction      Up or down
     * @param   string  $status         The status of the run
     * @param   int     $executionTime  Execution time in milliseconds
     * @return  bool
     **/
    public function recordMigration($file, $scope, $hash, $direction, $status = 'success', $executionTime = null)
    {
        // Catch instances where we don't have a status field yet
        // and mimic prior behavior where these runs were not logged
        if (!$this->db->tableHasField($this->get('tbl_name'), 'status') && $status != 'success') {
            return true;
        }

        // Try inserting a migration record into the database
        try {
            $date = new \Hubzero\Utility\Date();

            // Create our object to insert
            $obj = (object) array(
                'file'      => $file,
                'hash'      => $hash,
                'direction' => $direction,
                'date'      => $date->toSql(),
                'action_by' => (php_sapi_name() == 'cli') ? exec("whoami") : \Hubzero\Facades\User::get('id')
            );

            if ($this->db->tableHasField($this->get('tbl_name'), 'scope')) {
                $obj->scope = $scope;
            }

            if ($this->db->tableHasField($this->get('tbl_name'), 'status')) {
                $obj->status = $status;
            }

            if ($executionTime !== null && $this->db->tableHasField($this->get('tbl_name'), 'execution_time')) {
                $obj->execution_time = (int) $executionTime;
            }

            $this->db->insertObject($this->get('tbl_name'), $obj);
            return true;
        } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
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
        try {
            $query = "SELECT * FROM " . $this->db->quoteName($this->get('tbl_name'));
            $this->db->setQuery($query);
            $results = $this->db->loadObjectList();

            return $results;
        } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
            $this->log("Failed to retrieve history.", 'error');
            return false;
        }
    }

    /**
     * Mark a migration as executed without actually running it
     *
     * This is useful for:
     * - Fixing tracking table mismatches after manual database changes
     * - Recovering from partial failures
     * - Syncing tracking state with external changes
     *
     * @param   string  $file       The migration filename
     * @param   string  $direction  Direction ('up' or 'down')
     * @param   string  $extension  Optional extension filter to help locate the file
     * @return  bool    True on success, false on failure
     **/
    public function markMigration($file, $direction = 'up', $extension = null)
    {
        if (!$this->db) {
            $this->log("Database connection not available.", 'error');
            return false;
        }

        // Validate direction
        if (!in_array($direction, ['up', 'down'])) {
            $this->log("Invalid direction: must be 'up' or 'down'.", 'error');
            return false;
        }

        // Find the migration file to get its scope
        $this->files = [];
        if (!$this->find($extension, $file)) {
            $this->log("Migration file not found: {$file}", 'error');
            return false;
        }

        if (empty($this->files)) {
            $this->log("Migration file not found: {$file}", 'error');
            return false;
        }

        $fullpath = $this->files[0];
        $scope = str_replace(PATH_ROOT . DS, '', dirname($fullpath));
        $hash = hash('md5', $file);

        // Record the migration
        if ($this->recordMigration($file, $scope, $hash, $direction, 'success', null)) {
            $this->log("Marked {$file} as {$direction} (without executing)", 'success');
            return true;
        }

        return false;
    }

    /**
     * Remove a migration record from the tracking table
     *
     * This removes the most recent tracking entry for a migration,
     * effectively "unmarking" it so it appears as pending again.
     *
     * @param   string  $file       The migration filename
     * @param   string  $extension  Optional extension filter to help locate the file
     * @return  bool    True on success, false on failure
     **/
    public function unmarkMigration($file, $extension = null)
    {
        if (!$this->db) {
            $this->log("Database connection not available.", 'error');
            return false;
        }

        // Find the migration file to get its scope
        $this->files = [];
        if (!$this->find($extension, $file)) {
            $this->log("Migration file not found: {$file}", 'error');
            return false;
        }

        if (empty($this->files)) {
            $this->log("Migration file not found: {$file}", 'error');
            return false;
        }

        $fullpath = $this->files[0];
        $scope = str_replace(PATH_ROOT . DS, '', dirname($fullpath));

        try {
            // Find the most recent entry for this file/scope
            $query = "SELECT `id` FROM " . $this->db->quoteName($this->get('tbl_name'))
                   . " WHERE `file` = " . $this->db->quote($file);

            if ($this->db->tableHasField($this->get('tbl_name'), 'scope')) {
                if ($scope == 'core/migrations') {
                    $query .= " AND (`scope`='' OR `scope` IN ("
                            . $this->db->quote($scope) . ","
                            . $this->db->quote('migrations') . "))";
                } else {
                    $query .= " AND `scope` = " . $this->db->quote($scope);
                }
            }

            $query .= " ORDER BY `date` DESC LIMIT 1";

            $this->db->setQuery($query);
            $entry = $this->db->loadObject();

            if (!$entry) {
                $this->log("No tracking record found for {$file}", 'warning');
                return false;
            }

            // Delete the entry
            $deleteQuery = "DELETE FROM " . $this->db->quoteName($this->get('tbl_name'))
                         . " WHERE `id` = " . (int) $entry->id;

            $this->db->setQuery($deleteQuery);
            $this->db->query();

            $this->log("Removed tracking record for {$file}", 'success');
            return true;
        } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
            $this->log("Failed to remove tracking record: {$e->getMessage()}", 'error');
            return false;
        }
    }

    /**
     * Check if a migration has been executed
     *
     * @param   string  $file       The migration filename
     * @param   string  $extension  Optional extension filter
     * @return  array|false  Migration entry if executed, false if not found or not executed
     **/
    public function getMigrationStatus($file, $extension = null)
    {
        if (!$this->db) {
            return false;
        }

        // Find the migration file to get its scope
        $this->files = [];
        if (!$this->find($extension, $file)) {
            return false;
        }

        if (empty($this->files)) {
            return false;
        }

        $fullpath = $this->files[0];
        $scope = str_replace(PATH_ROOT . DS, '', dirname($fullpath));

        try {
            $query = "SELECT * FROM " . $this->db->quoteName($this->get('tbl_name'))
                   . " WHERE `file` = " . $this->db->quote($file);

            if ($this->db->tableHasField($this->get('tbl_name'), 'scope')) {
                if ($scope == 'core/migrations') {
                    $query .= " AND (`scope`='' OR `scope` IN ("
                            . $this->db->quote($scope) . ","
                            . $this->db->quote('migrations') . "))";
                } else {
                    $query .= " AND `scope` = " . $this->db->quote($scope);
                }
            }

            $query .= " ORDER BY `date` DESC LIMIT 1";

            $this->db->setQuery($query);
            $entry = $this->db->loadObject();

            return $entry ?: false;
        } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
            return false;
        }
    }

    /**
     * Get migration status summary
     *
     * Returns counts and details about pending, executed, and failed migrations.
     *
     * @param   string  $extension  Optional extension filter
     * @return  array|false  Status array or false on error
     **/
    public function getStatus($extension = null)
    {
        if (!$this->db) {
            return false;
        }

        // Find all migration files
        $this->find($extension);
        $allFiles = $this->files;

        // Get all executed migrations from database
        try {
            $query = "SELECT * FROM " . $this->db->quoteName($this->get('tbl_name'))
                   . " ORDER BY `date` DESC";
            $this->db->setQuery($query);
            $history = $this->db->loadObjectList();
        } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
            $this->log("Failed to retrieve migration history.", 'error');
            return false;
        }

        // Build lookup of executed migrations (most recent entry for each file/scope)
        $executed = [];
        $failed = [];
        $skipped = [];

        foreach ($history as $entry) {
            $key = $entry->scope . '/' . $entry->file;

            // Only track the most recent execution for each file
            if (!isset($executed[$key])) {
                $executed[$key] = $entry;

                // Track failed/skipped separately
                if (isset($entry->status)) {
                    if ($entry->status === 'fatal' || $entry->status === 'failed') {
                        $failed[] = $entry;
                    } elseif ($entry->status === 'skipped') {
                        $skipped[] = $entry;
                    }
                }
            }
        }

        // Determine pending migrations (files not executed or last run was 'down')
        $pending = [];
        foreach ($allFiles as $filepath) {
            $file = basename($filepath);
            $scope = str_replace(PATH_ROOT . DS, '', dirname($filepath));
            $key = $scope . '/' . $file;

            // Pending if: never run, or last run was down, or last run failed/skipped
            if (!isset($executed[$key])) {
                $pending[] = $file;
            } elseif ($executed[$key]->direction === 'down') {
                $pending[] = $file;
            } elseif (
                isset($executed[$key]->status) &&
                      in_array($executed[$key]->status, ['fatal', 'failed', 'skipped'])
            ) {
                $pending[] = $file;
            }
        }

        // Sort pending by filename (which includes timestamp)
        sort($pending);

        // Get last executed (successful up migration)
        $lastExecuted = null;
        foreach ($history as $entry) {
            if (
                $entry->direction === 'up' &&
                (!isset($entry->status) || $entry->status === 'success' || $entry->status === 'warning')
            ) {
                $lastExecuted = $entry;
                break;
            }
        }

        // Get recent history (last 10)
        $recent = array_slice($history, 0, 10);

        // Count successful executions (up migrations that succeeded)
        $executedCount = 0;
        foreach ($executed as $entry) {
            if (
                $entry->direction === 'up' &&
                (!isset($entry->status) || $entry->status === 'success' || $entry->status === 'warning')
            ) {
                $executedCount++;
            }
        }

        return [
            'counts' => [
                'available' => count($allFiles),
                'executed'  => $executedCount,
                'pending'   => count($pending),
                'failed'    => count($failed),
                'skipped'   => count($skipped),
            ],
            'pending'       => $pending,
            'failed'        => $failed,
            'skipped'       => $skipped,
            'last_executed' => $lastExecuted,
            'next_pending'  => !empty($pending) ? $pending[0] : null,
            'recent'        => $recent,
        ];
    }

    /**
     * Resolve a migration alias to a specific filename
     *
     * Supported aliases:
     * - 'first'   - The first (oldest) migration file
     * - 'prev'    - The migration before the last executed one
     * - 'current' - The last successfully executed migration
     * - 'next'    - The next pending migration
     * - 'latest'  - The latest (newest) migration file
     *
     * @param   string       $alias      The alias to resolve
     * @param   string|null  $extension  Optional extension filter
     * @return  array|false  Array with 'file' and 'info' keys, or false if not found
     **/
    public function resolveAlias($alias, $extension = null)
    {
        if (!$this->db) {
            return false;
        }

        $alias = strtolower($alias);
        $validAliases = ['first', 'prev', 'previous', 'current', 'next', 'latest', 'last'];

        if (!in_array($alias, $validAliases)) {
            return false;
        }

        // Normalize aliases
        if ($alias === 'previous') {
            $alias = 'prev';
        }
        if ($alias === 'last') {
            $alias = 'latest';
        }

        // Find all migration files (sorted by filename/timestamp)
        $this->files = [];
        $this->find($extension);
        $allFiles = $this->files;

        if (empty($allFiles)) {
            return false;
        }

        // Sort files by basename (which includes timestamp)
        usort($allFiles, function ($a, $b) {
            return strcmp(basename($a), basename($b));
        });

        // Get execution history
        try {
            $query = "SELECT * FROM " . $this->db->quoteName($this->get('tbl_name'))
                   . " WHERE `direction` = 'up'"
                   . " AND (`status` IS NULL OR `status` IN ('success', 'warning'))"
                   . " ORDER BY `date` DESC";
            $this->db->setQuery($query);
            $history = $this->db->loadObjectList();
        } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
            $this->log("Failed to retrieve migration history.", 'error');
            return false;
        }

        // Build list of successfully executed migrations
        $executed = [];
        foreach ($history as $entry) {
            $executed[$entry->file] = $entry;
        }

        // Get pending migrations (not executed or last run was down/failed)
        $pending = [];
        foreach ($allFiles as $filepath) {
            $file = basename($filepath);
            if (!isset($executed[$file])) {
                $pending[] = $filepath;
            }
        }

        // Resolve the alias
        switch ($alias) {
            case 'first':
                // First (oldest) migration file
                $filepath = $allFiles[0];
                return [
                    'file' => basename($filepath),
                    'path' => $filepath,
                    'info' => 'First migration (oldest)'
                ];

            case 'latest':
                // Latest (newest) migration file
                $filepath = $allFiles[count($allFiles) - 1];
                return [
                    'file' => basename($filepath),
                    'path' => $filepath,
                    'info' => 'Latest migration (newest)'
                ];

            case 'current':
                // Last successfully executed migration
                if (empty($history)) {
                    return [
                        'file' => null,
                        'path' => null,
                        'info' => 'No migrations have been executed yet'
                    ];
                }
                $current = $history[0];
                // Find the full path
                foreach ($allFiles as $filepath) {
                    if (basename($filepath) === $current->file) {
                        return [
                            'file' => $current->file,
                            'path' => $filepath,
                            'info' => 'Currently executed migration'
                        ];
                    }
                }
                return [
                    'file' => $current->file,
                    'path' => null,
                    'info' => 'Currently executed migration (file not found in search paths)'
                ];

            case 'next':
                // Next pending migration
                if (empty($pending)) {
                    return [
                        'file' => null,
                        'path' => null,
                        'info' => 'No pending migrations'
                    ];
                }
                $filepath = $pending[0];
                return [
                    'file' => basename($filepath),
                    'path' => $filepath,
                    'info' => 'Next pending migration'
                ];

            case 'prev':
                // Migration before the last executed one
                if (empty($history)) {
                    return [
                        'file' => null,
                        'path' => null,
                        'info' => 'No migrations have been executed yet'
                    ];
                }

                // Find the current migration in the sorted file list
                $currentFile = $history[0]->file;
                $currentIndex = null;
                foreach ($allFiles as $index => $filepath) {
                    if (basename($filepath) === $currentFile) {
                        $currentIndex = $index;
                        break;
                    }
                }

                if ($currentIndex === null || $currentIndex === 0) {
                    return [
                        'file' => null,
                        'path' => null,
                        'info' => 'No previous migration (already at first)'
                    ];
                }

                $filepath = $allFiles[$currentIndex - 1];
                return [
                    'file' => basename($filepath),
                    'path' => $filepath,
                    'info' => 'Previous migration'
                ];
        }

        return false;
    }

    /**
     * Get list of valid migration aliases
     *
     * @return  array
     **/
    public function getValidAliases()
    {
        return [
            'first'   => 'The first (oldest) migration file',
            'prev'    => 'The migration before the last executed one',
            'current' => 'The last successfully executed migration',
            'next'    => 'The next pending migration',
            'latest'  => 'The latest (newest) migration file',
        ];
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
     * @param   string  $type     Message type, can be one predefined values from output class (not
     *                            specified will default to 'normal' text)
     * @return  void
     **/
    public function log($message, $type = null)
    {
        $this->log[] = array('message' => $message, 'type' => $type);

        if (!$this->ignoreCallbacks && isset($this->callbacks['message']) && is_callable($this->callbacks['message'])) {
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
     * Derive the expected namespace from a migration file path
     *
     * @param   string  $path  Full path to migration file
     * @return  string|null  Expected namespace or null if cannot determine
     */
    private function deriveNamespaceFromPath($path)
    {
        // Get the path relative to PATH_ROOT
        $relativePath = str_replace(PATH_ROOT . DS, '', $path);
        $parts = explode(DS, $relativePath);

        // Skip core/app prefix
        $prefix = array_shift($parts);
        if (!in_array($prefix, ['core', 'app'])) {
            return null;
        }

        if (empty($parts)) {
            return null;
        }

        $namespace = [];
        $type = $parts[0];

        switch ($type) {
            case 'migrations':
                // Core/app migrations: core/migrations/MigrationXXX.php
                // Check if this is a hook
                if (isset($parts[1]) && $parts[1] === 'hooks') {
                    $namespace[] = 'Migrations';
                    $namespace[] = 'Hooks';
                } else {
                    $namespace[] = 'Migrations';
                }
                break;

            case 'components':
                // Component: core/components/com_blog/migrations/MigrationXXX.php
                $namespace[] = 'Components';
                if (isset($parts[1])) {
                    $name = preg_replace('/^com_/', '', $parts[1]);
                    $namespace[] = $this->studlyCase($name);
                }
                $namespace[] = 'Migrations';
                break;

            case 'modules':
                // Module: core/modules/mod_menu/migrations/MigrationXXX.php
                $namespace[] = 'Modules';
                if (isset($parts[1])) {
                    $name = preg_replace('/^mod_/', '', $parts[1]);
                    $namespace[] = $this->studlyCase($name);
                }
                $namespace[] = 'Migrations';
                break;

            case 'plugins':
                // Plugin: core/plugins/system/cache/migrations/MigrationXXX.php
                $namespace[] = 'Plugins';
                if (isset($parts[1])) {
                    $namespace[] = $this->studlyCase($parts[1]);
                }
                if (isset($parts[2])) {
                    $namespace[] = $this->studlyCase($parts[2]);
                }
                $namespace[] = 'Migrations';
                break;

            case 'templates':
                // Template: core/templates/protostar/migrations/MigrationXXX.php
                $namespace[] = 'Templates';
                if (isset($parts[1])) {
                    $namespace[] = $this->studlyCase($parts[1]);
                }
                $namespace[] = 'Migrations';
                break;

            default:
                return null;
        }

        return implode('\\', $namespace);
    }

    /**
     * Convert a string to StudlyCase
     *
     * @param   string  $value  The string to convert
     * @return  string
     */
    private function studlyCase($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return str_replace(' ', '', $value);
    }

    /**
     * Determine if a migration should use transaction wrapping
     *
     * By default, all migrations are wrapped in a transaction for safety.
     * Migrations can opt-out by setting a public $useTransaction = false property.
     *
     * Note: On MySQL/MariaDB, DDL statements (CREATE TABLE, ALTER TABLE, etc.)
     * cause an implicit commit, so transactions only protect DML operations.
     * On PostgreSQL and SQLite, DDL is fully transactional.
     *
     * @param   object  $class  The migration class instance
     * @return  bool
     **/
    private function shouldUseTransaction($class)
    {
        // Check if the migration explicitly opts out
        if (property_exists($class, 'useTransaction')) {
            return (bool) $class->useTransaction;
        }

        // Default: use transactions for safety
        return true;
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
        try {
            $db->setQuery($query);
            $db->query();
            $this->log('Migrations table successfully created');
            return true;
        } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
            $this->log('Unable to create needed migrations table', 'error');
            return false;
        }
    }
}
