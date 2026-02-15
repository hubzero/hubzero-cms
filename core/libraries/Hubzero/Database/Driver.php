<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

use Hubzero\Utility\Str;
use Hubzero\Error\Exception\RuntimeException;
use Hubzero\Error\Exception\BadMethodCallException;
use Hubzero\Database\Exception\ConnectionFailedException;
use Hubzero\Database\Exception\QueryFailedException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Base database driver
 *
 * This is the abstract base class for all database drivers. It provides
 * connection management and query execution mechanics. SQL dialect logic
 * lives in canonical `Drivers` classes.
 *
 * ```
 * Driver (abstract base - connection + query execution)
 *   └── Drivers\Base\BasePdoDriver
 *         └── Drivers\Base\BaseSqlDriver
 *               ├── Drivers\Mysql\MysqlDriver
 *               │     ├── Drivers\Mariadb\MariadbDriver
 *               │     └── Drivers\Percona\PerconaDriver
 *               ├── Drivers\Cubrid\CubridDriver
 *               ├── Drivers\Pgsql\PgsqlDriver
 *               ├── Drivers\Sqlite\SqliteDriver
 *               ├── Drivers\Firebird\FirebirdDriver
 *               ├── Drivers\Informix\InformixDriver
 *               ├── Drivers\Db2\Db2Driver
 *               ├── Drivers\Oci\OciDriver
 *               └── Drivers\Sqlsrv\SqlsrvDriver
 * ```
 *
 * ARCHITECTURE NOTES:
 * - Driver handles connection management: connect, disconnect, prepare, bind, execute, fetch
 * - Connections are injected via setConnection(ConnectionInterface)
 * - Drivers\Base\BaseSqlDriver defines the SQL contract as abstract methods
 * - Each dialect driver implements SQL syntax specific to that database
 * - Connections are lazy — PdoConnection auto-connects on first use
 *
 * When adding new database introspection or DDL methods:
 * 1. Add abstract method declaration to Drivers\Base\BaseSqlDriver
 * 2. Implement in each concrete dialect driver class
 *    (e.g. Drivers\Mysql\MysqlDriver, Drivers\Pgsql\PgsqlDriver).
 *
 * ## PSR-3 Logging
 *
 * The driver implements `Psr\Log\LoggerAwareInterface` for standardized logging.
 * By default, a `NullLogger` is used (no logging). Inject a logger to enable:
 *
 * ```php
 * $db = App::get('db');
 * $db->setLogger($monolog);
 * $db->setSlowQueryThreshold(0.5); // Log queries taking > 0.5 seconds as warnings
 * $db->enableDebugging();          // Log all queries at DEBUG level
 * ```
 *
 * Log levels:
 * - ERROR: Database errors (failed queries, connection issues)
 * - WARNING: Slow queries exceeding the threshold
 * - DEBUG: All queries (when debugging is enabled)
 */
abstract class Driver implements LoggerAwareInterface
{
    /**
     * The connection instances factory
     *
     * @public array
     **/
    protected static $instances = [];

    /**
     * The cumulative query timer (in miliseconds)
     *
     * @public int
     */
    protected $timer = 0;

    /**
     * The database connection
     *
     * This holds the connection adapter that implements ConnectionInterface.
     * For PDO-based drivers, this will be a PdoConnection instance.
     * For other drivers, this may be a different implementation.
     *
     * @var ConnectionInterface|object|resource
     */
    protected $connection;

    /**
     * The incremental count of executed queries
     *
     * @public int
     */
    protected $count = 0;

    /**
     * The database connection statement
     *
     * For prepared statements, this would the be actual statement class, for non-prepared
     * statements, this will simply be the last executed or upcoming query.
     *
     * @public object|string
     */
    protected $statement;

    /**
     * The prepared statement bindings
     *
     * @public array
     */
    protected $bindings = [];

    /**
     * The internal query log
     *
     * For prepared statements, we'll try to interpolate prepared statement into basic strings,
     * even though this isn't really an accurate representation of the query.
     *
     * @public array
     */
    protected $log = [];

    /**
     * The character(s) used to quote items such as table names or field names
     *
     * Default uses SQL standard double-quote identifiers. MySQL and SQLite
     * override this to use backticks.
     *
     * @public string
     */
    protected $wrapper = '"%s"';

    /**
     * The null or zero representation of a timestamp for the database driver
     *
     * @public string
     */
    protected $nullDate = '0000-00-00 00:00:00';

    /**
     * The common database table prefix
     *
     * @public string
     */
    protected $tablePrefix = 'jos_';

    /**
     * The state of debugging
     *
     * @public bool
     */
    protected $debug = false;

    /**
     * The name of the database
     *
     * @public string
     */
    protected $database;

    /**
     * The database driver syntax
     *
     * @public string
     **/
    protected $syntax = null;

    /**
     * The schema manager instance
     *
     * @var SchemaManager|null
     */
    protected $schema = null;

    /**
     * PSR-3 logger instance
     *
     * @var LoggerInterface|null
     */
    protected ?LoggerInterface $logger = null;

    /**
     * Slow query threshold in seconds
     *
     * Queries taking longer than this threshold will be logged as warnings.
     * Set to 0 to disable slow query logging.
     *
     * @var float
     */
    protected float $slowQueryThreshold = 1.0;

    /**
     * Raw query mode controls how direct setQuery() calls are handled
     *
     * When code outside the Database framework calls setQuery() with raw SQL,
     * this mode determines the behavior:
     *
     * - 'permissive': No checks (default, current behavior)
     * - 'log': Allow but log caller file/line via PSR-3 logger at NOTICE level
     * - 'strict': Throw RuntimeException for non-framework callers
     *
     * Internal framework calls (from Hubzero\Database\* classes) are always
     * allowed regardless of mode, since they already generate portable SQL
     * through the Query builder and Syntax layer.
     *
     * @var string
     */
    protected string $rawQueryMode = 'permissive';

    /**
     * Flag to prevent re-entrant raw query mode checks
     *
     * When a driver subclass overrides setQuery() and calls parent::setQuery(),
     * this flag ensures enforcement runs only once for the outermost call.
     *
     * @var bool
     */
    protected bool $rawQueryModeEnforced = false;

    /**
     * Unique identifier for this connection instance
     *
     * This UUID is generated when the connection is established and is used
     * for cache isolation. Each connection gets a unique ID, ensuring that
     * query caches are properly isolated between different connections, even
     * when connecting to the same database.
     *
     * @var string|null
     */
    protected ?string $connectionId = null;

    /**
     * Constructs a new object, setting some class properties
     *
     * @param  array  $options  List of options used to configure the connection
     */
    protected function __construct($options)
    {
        $this->tablePrefix = (isset($options['prefix']))   ? $options['prefix']   : $this->tablePrefix;
        $this->database    = (isset($options['database'])) ? $options['database'] : $this->database;
        $this->logger      = new NullLogger();

        if (isset($options['raw_query_mode'])) {
            $this->setRawQueryMode($options['raw_query_mode']);
        }

        $this->setUTF();
    }

    /**
     * Provides alias support for quote() and quoteName()
     *
     * @param       string  $method  The called method name
     * @param       array   $args    The array of arguments passed to the method
     * @return      string
     * @deprecated  2.0.0
     * @throws      \Hubzero\Error\Exception\BadMethodCallException
     */
    public function __call($method, $args)
    {
        // We have to have args
        if (empty($args)) {
            return;
        }

        switch ($method) {
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
     *  * The 'driver' option defines which driver class is used for the connection -- the default is 'mysql'.
     *  * The 'database' option determines which database is to be used for the connection.
     *  * The 'select' option determines whether the connector should automatically select the chosen database.
     *
     * Legacy alias support:
     *  * 'pdo' is normalized to 'mysql' for backward compatibility.
     *
     * @param   array   $options   Parameters to construct the database driver requested
     * @return  static
     * @throws  \Hubzero\Error\Exception\RuntimeException
     */
    public static function getInstance($options = [])
    {
        // Sanitize the database connector options
        $options['driver']   = (isset($options['driver']))   ? preg_replace(
            '/[^A-Z0-9_\.-]/i',
            '',
            $options['driver']
        ) : 'mysql';
        $options['database'] = (isset($options['database'])) ? $options['database']
            : null;
        $options['select']   = (isset($options['select']))   ? $options['select']
            : true;
        // Legacy alias retained for configuration compatibility.
        if ($options['driver'] === 'pdo') {
            $options['driver'] = 'mysql';
        }

        // Get the options signature for the database connector
        $signature = md5(serialize($options));

        // If we already have a database connector instance for these options, then just use that
        if (!isset(self::$instances[$signature])) {
            // Resolve canonical built-in class first
            $class = BackendRegistry::resolveDriverClassFor((string) $options['driver']);

            // Resolve canonical convention candidate for custom drivers
            if ($class === null) {
                $class = BackendRegistry::firstExistingClassCandidate(
                    BackendRegistry::conventionDriverClassCandidates((string) $options['driver'])
                );
            }

            // If the class doesn't exist we have a problem
            if (!class_exists($class)) {
                throw new RuntimeException('Database driver not available.', 500);
            }

            // Set the new connector to the global instances based on signature
            self::$instances[$signature] = new $class($options);
        }

        return self::$instances[$signature];
    }

    /**
     * Sets the connection
     *
     * This method is public because it can be helpful when testing.
     * You can ignore the constructor and just set the connection of
     * your choice.  We assume the person setting the connection
     * has done their checks to make sure it is valid.
     *
     * @param   object $connection the connection to set
     * @return  $this
     **/
    public function setConnection($connection)
    {
        $this->connection = $connection;
        $this->connectionId = $this->generateConnectionId();
        $this->setSyntax($this->detectSyntax());

        return $this;
    }

    /**
     * Opens the database connection
     *
     * For ConnectionInterface implementations, this delegates to connect().
     * For lazy connections (like PdoConnection), this triggers the actual
     * database connection to be established.
     *
     * @return  void
     * @throws  ConnectionFailedException
     */
    public function connect()
    {
        if ($this->connection instanceof ConnectionInterface) {
            $this->connection->connect();
        }
    }

    /**
     * Closes the database connection
     *
     * For ConnectionInterface implementations, this delegates to disconnect()
     * which releases the native connection but preserves connection parameters
     * for later reconnection. The ConnectionInterface wrapper is kept.
     *
     * For legacy connections, this nulls the connection entirely.
     *
     * @return  void
     */
    public function disconnect()
    {
        if ($this->connection instanceof ConnectionInterface) {
            $this->connection->disconnect();
        } else {
            $this->connection = null;
        }
    }

    /**
     * Destroys the connection entirely
     *
     * @return  void
     */
    public function __destruct()
    {
        if ($this->connection instanceof ConnectionInterface) {
            $this->connection->disconnect();
        }
        $this->connection = null;
    }

    /**
     * Sets the SQL statement string for later execution
     *
     * @param   string  $query  The SQL statement to set
     * @return  $this
     */
    public function setQuery($query)
    {
        if (
            $this->rawQueryMode !== 'permissive'
            && !$this->rawQueryModeEnforced
        ) {
            $this->rawQueryModeEnforced = true;
            try {
                $this->enforceRawQueryMode((string) $query);
            } finally {
                $this->rawQueryModeEnforced = false;
            }
        }

        $this->prepare((string) $query);

        return $this;
    }


    /**
     * Quotes and optionally escape a string to database requirements for insertion into the database
     *
     * @param   string  $text    The string to quote
     * @param   bool    $escape  True to escape the string, false to leave it unchanged
     * @return  string
     */
    public function quote($text, $escape = true)
    {
        return '\'' . ($escape ? $this->escape($text) : $text) . '\'';
    }

    /**
     * Wraps an SQL statement identifier name such as column, table or database names
     * in quotes to prevent injection risks and reserved word conflicts
     *
     * @param   string  $name  The identifier name to wrap in quotes, supporting dot-notation names
     * @param   string  $as    The AS query part associated to $name
     * @return  string
     */
    public function quoteName($name, $as = null)
    {
        $parts = (strpos($name, '.') !== false) ? explode('.', $name) : (array)$name;
        $bits  = array();

        foreach ($parts as $part) {
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
     * @param   string  $value  The table definition
     * @return  string
     */
    public function wrap($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        // Preserve derived-table expressions like "(SELECT ...)" as raw SQL.
        if ($value[0] === '(' && substr($value, -1) === ')') {
            return $value;
        }

        // Handle explicit aliases, including derived tables:
        // "(SELECT ... AS x ...) AS alias"
        if (preg_match('/^(.+)\s+as\s+([^\s]+)$/is', $value, $matches)) {
            return $this->wrap($matches[1]) . ' AS ' . $this->wrap($matches[2]);
        }

        // Handle shorthand alias format: "table alias" (without AS keyword)
        if (preg_match('/^(.+)\s+([^\s]+)$/s', $value, $matches)) {
            return $this->wrap($matches[1]) . ' AS ' . $this->wrap($matches[2]);
        }

        $quoted = [];
        $parts  = explode('.', $value);

        foreach ($parts as $part) {
            // Make sure it's not an *, which shouldn't be quoted
            $part = trim($part);
            $quoted[] = $part !== '*' ? sprintf($this->wrapper, $part) : $part;
        }

        // Put it back together and return
        return implode('.', $quoted);
    }

    /**
     * Creates a new Query builder instance bound to this connection
     *
     * This provides convenient access to the Query builder for performing
     * database operations through the higher-level Query API.
     *
     * Example:
     * ```php
     * $db->queryBuilder()->push('users', ['name' => 'John', 'email' => 'john@example.com']);
     * $db->queryBuilder()->pushObject('users', $userObject, 'id');
     * ```
     *
     * @return  Query  A new Query builder instance
     */
    public function queryBuilder()
    {
        return new Query($this);
    }

    /**
     * Gets the first row of the result set from the database query
     * as an associative array of type: ['field_name' => 'row_value']
     *
     * @return  array|null
     */
    public function loadAssoc()
    {
        // Initialise variables
        $return = null;

        // Execute the query
        if (!$this->execute()) {
            return null;
        }

        // Get the first row from the result set as an associative array
        if ($row = $this->fetchAssoc()) {
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
     * @param   string  $key     The name of a field on which to key the result array
     * @param   string  $column  Instead of the whole row, only this column value will be in the result array
     * @return  array|null
     */
    public function loadAssocList($key = null, $column = null)
    {
        // Initialise variables
        $array = [];

        // Execute the query
        if (!$this->execute()) {
            return null;
        }

        // Get all of the rows from the result set
        while ($row = $this->fetchAssoc()) {
            $value = ($column) ? (isset($row[$column]) ? $row[$column] : $row) : $row;
            if ($key) {
                $array[$row[$key]] = $value;
            } else {
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
     * @param   int  $offset  The row offset to use to build the result array
     * @return  array|null
     */
    public function loadColumn($offset = 0)
    {
        // Initialise variables
        $column = [];

        // Execute the query
        if (!$this->execute()) {
            return null;
        }

        // Get all of the rows from the result set as arrays
        while ($row = $this->fetchArray()) {
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
     * @param   string  $class  The class name to use for the returned row object
     * @return  object|bool
     */
    public function loadNextObject($class = 'stdClass')
    {
        // Get the next row from the result set as an object of type $class
        if ($row = $this->fetchObject($class)) {
            return $row;
        }

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
     * @return  array|bool
     */
    public function loadNextRow()
    {
        // Get the next row from the result set as an array
        if ($row = $this->fetchArray()) {
            return $row;
        }

        // Free up system resources and return
        $this->freeResult();

        return false;
    }

    /**
     * Gets the first row of the result set from the database query as an object
     *
     * @param   string  $class  The class name to use for the returned row object
     * @return  object|null
     */
    public function loadObject($class = 'stdClass')
    {
        // Initialise variables
        $return = null;

        // Execute the query
        if (!$this->execute()) {
            return null;
        }

        // Get the first row from the result set as an object of type $class
        if ($row = $this->fetchObject($class)) {
            $return = $row;
        }

        // Free up system resources and return
        $this->freeResult();

        return $return;
    }

    /**
     * Gets the first field of the first row of the result set from the database query
     *
     * @return  string|null
     */
    public function loadResult()
    {
        // Initialise variables
        $return = null;

        // Execute the query
        if (!$this->execute()) {
            return null;
        }

        // Get the first row from the result set as an array
        if ($row = $this->fetchArray()) {
            $return = $row[0];
        }

        // Free up system resources and return
        $this->freeResult();

        return $return;
    }

    /**
     * Gets the first row of the result set from the database query as an array
     *
     * @return  array|null
     */
    public function loadRow()
    {
        // Initialise variables
        $return = null;

        // Execute the query
        if (!$this->execute()) {
            return null;
        }

        // Get the first row from the result set as an array
        if ($row = $this->fetchArray()) {
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
     * @param   string  $key  The name of a field on which to key the result array
     * @return  array|null
     */
    public function loadRowList($key = null)
    {
        // Initialise variables
        $rows = [];

        // Execute the query
        if (!$this->execute()) {
            return null;
        }

        // Get all of the rows from the result set as arrays
        while ($row = $this->fetchArray()) {
            if ($key !== null) {
                $rows[$row[$key]] = $row;
            } else {
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
     * @param   string  $key    The name of the field on which to key the result array
     * @param   string  $class  The class name to use for the returned row objects
     * @return  array
     */
    public function loadObjectList($key = '', $class = 'stdClass')
    {
        $rows = [];

        // Execute the query
        if (!$this->execute()) {
            return null;
        }

        // Get all of the rows from the result set as objects of type $class
        while ($row = $this->fetchObject($class)) {
            if ($key) {
                $rows[$row->$key] = $row;
            } else {
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
     * @return  array
     */
    public static function getConnectors()
    {
        // Instantiate variables
        $connectors = [];

        // Canonical built-ins from backend registry
        $types = array_keys(BackendRegistry::driverClassMap());

        // Loop through the types and find the ones that are available
        foreach ($types as $type) {
            $class = BackendRegistry::resolveDriverClassFor($type);

            // If the class doesn't exist...these are not the droids you're looking for...
            if (!$class || !class_exists($class)) {
                continue;
            }

            // Our class exists, so now we just need to know if it passes it's test method
            if (call_user_func_array(array($class, 'test'), array())) {
                // Preserve historical connector naming style (ucfirst)
                $connectors[] = ucfirst($type);
            }
        }

        return $connectors;
    }

    /**
     * Replaces a string placeholder with the string held in the class variable
     *
     * @param   string  $sql     The SQL statement to prepare
     * @param   string  $prefix  The common table prefix
     * @return  string
     */
    public function replacePrefix($sql, $prefix = '#__')
    {
        // As we replace strings of different lengths, subsequent prefix positions will become invalid.
        // Thus, we track that differential here to account for the shifting locations
        $differential = strlen($this->tablePrefix) - strlen($prefix);
        $count        = 0;

        foreach (Str::findLiteral($prefix, $sql) as $prefixPosition) {
            $sql = substr_replace(
                $sql,
                $this->tablePrefix,
                $prefixPosition + ($differential * $count),
                strlen($prefix)
            );
            $count++;
        }

        return $sql;
    }

    /**
     * Splits a string of multiple queries into an array of individual queries
     *
     * @param   string  $sql  Input SQL string from which to split into individual queries
     * @return  array
     */
    public static function splitSql($sql)
    {
        $start   = 0;
        $length  = strlen($sql);
        $queries = [];

        foreach (Str::findLiteral(';', $sql) as $queryEndPosition) {
            $queries[] = trim(substr($sql, $start, $queryEndPosition - $start + 1));
            $start     = $queryEndPosition + 1;
        }

        // Grab the last query in case it doesn't end with a ';'
        if ($end = trim(substr($sql, $start))) {
            $queries[] = $end;
        }

        return $queries;
    }

    /**
     * Logs the current sql statement
     *
     * Logs to:
     * - Internal log array (always)
     * - Event system (always)
     * - PSR-3 logger at WARNING level if slow query threshold exceeded
     * - PSR-3 logger at DEBUG level if debugging is enabled
     *
     * @param   float  $time  The time elapsed during query execution (in seconds)
     * @return  $this
     **/
    protected function log($time)
    {
        // Build the actual query
        $query = $this->toString();

        // Convert time to milliseconds for the detailed log
        $timeMs = $time * 1000;

        // Only trigger event if Event facade is available (allows standalone usage)
        if (class_exists('Event', false)) {
            try {
                \Event::trigger('database_query', [
                    'query' => $query,
                    'time'  => $time
                ]);
            } catch (\Throwable $e) {
                // Silently continue if Event facade not configured
            }
        }

        // Add it to the internal logs
        $this->log[] = $query;
        $this->count++;
        $this->timer += $time;

        // Add to detailed query log if enabled
        $this->logQuery($query, $this->bindings, $timeMs);

        // PSR-3 logging: slow queries as warnings
        if ($this->slowQueryThreshold > 0 && $time > $this->slowQueryThreshold) {
            $this->getLogger()->warning('Slow database query', [
                'sql'       => $query,
                'time'      => round($time, 4),
                'threshold' => $this->slowQueryThreshold,
            ]);
        } elseif ($this->debug) {
            // Normal queries at debug level when debugging is enabled
            $this->getLogger()->debug('Database query executed', [
                'sql'  => $query,
                'time' => round($time, 4),
            ]);
        }

        return $this;
    }

    /**
     * Logs a database error
     *
     * @param   \Throwable  $exception  The exception that occurred
     * @param   string|null $sql        The SQL that caused the error (optional)
     * @return  void
     **/
    protected function logError(\Throwable $exception, ?string $sql = null): void
    {
        $this->getLogger()->error('Database error', [
            'message' => $exception->getMessage(),
            'code'    => $exception->getCode(),
            'sql'     => $sql ?? $this->toString(),
        ]);
    }

    /**
     * Gets the string version of the query
     *
     * @return  string
     **/
    public function toString()
    {
        if (is_object($this->statement)) {
            return $this->interpolate($this->statement->queryString, $this->bindings);
        }

        return $this->statement;
    }

    /**
     * Builds a string version of the prepared statement
     *
     * @param   string  $query     The query string to use as the base
     * @param   array   $bindings  The bindings to interpolate in
     * @return  string
     **/
    private function interpolate($query, $bindings)
    {
        $offset = 0;
        $index  = 0;

        foreach (Str::findLiteral('?', $query) as $placeholder) {
            $sub     = (is_null($bindings[$index])) ? 'NULL' : $this->quote($bindings[$index]);
            $query   = substr_replace($query, $sub, $placeholder + $offset, 1);
            $offset += (strlen($sub) - 1);
            $index++;
        }

        return $query;
    }

    /**
     * Executes the SQL statement (basically an alias for execute())
     *
     * @return  static
     */
    public function query()
    {
        return $this->execute();
    }

    /**
     * Sets the database debugging state for the driver
     *
     * @param   bool   $level  True to enable debugging
     * @return  $this
     */
    public function setDebug($level)
    {
        $this->debug = (bool) $level;

        return $this;
    }

    /**
     * Enables debugging
     *
     * @return  $this
     */
    public function enableDebugging()
    {
        return $this->setDebug(true);
    }

    /**
     * Disables debugging
     *
     * @return  $this
     */
    public function disableDebugging()
    {
        return $this->setDebug(false);
    }

    /**
     * Sets a PSR-3 logger instance on the driver
     *
     * When set, queries will be logged at DEBUG level (when $debug is true),
     * slow queries at WARNING level, and errors at ERROR level.
     *
     * @param   LoggerInterface  $logger  The PSR-3 logger instance
     * @return  void
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Gets the current PSR-3 logger instance
     *
     * Returns a NullLogger if no logger has been set.
     *
     * @return  LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * Sets the slow query threshold in seconds
     *
     * Queries taking longer than this threshold will be logged as warnings.
     * Set to 0 to disable slow query logging.
     *
     * @param   float  $seconds  The threshold in seconds
     * @return  $this
     */
    public function setSlowQueryThreshold(float $seconds)
    {
        $this->slowQueryThreshold = $seconds;

        return $this;
    }

    /**
     * Gets the slow query threshold in seconds
     *
     * @return  float
     */
    public function getSlowQueryThreshold(): float
    {
        return $this->slowQueryThreshold;
    }

    /**
     * Sets the raw query mode
     *
     * Controls how direct setQuery() calls from outside the Database
     * framework are handled:
     *
     * - 'permissive': No checks (default)
     * - 'log': Allow but log caller info at NOTICE level
     * - 'strict': Throw RuntimeException
     *
     * @param   string  $mode  One of 'permissive', 'log', 'strict'
     * @return  $this
     * @throws  \InvalidArgumentException  If mode is not valid
     */
    public function setRawQueryMode(string $mode): self
    {
        $valid = ['permissive', 'log', 'strict'];

        if (!in_array($mode, $valid, true)) {
            throw new \InvalidArgumentException(
                "Invalid raw query mode '{$mode}'. "
                . "Must be one of: " . implode(', ', $valid)
            );
        }

        $this->rawQueryMode = $mode;

        return $this;
    }

    /**
     * Gets the current raw query mode
     *
     * @return  string  One of 'permissive', 'log', 'strict'
     */
    public function getRawQueryMode(): string
    {
        return $this->rawQueryMode;
    }

    /**
     * Enforces raw query mode restrictions
     *
     * Called from setQuery() when mode is not 'permissive'. Uses
     * debug_backtrace to identify the caller. Internal framework
     * calls (from Hubzero\Database\* classes) are always allowed.
     *
     * @param   string  $sql  The SQL being set
     * @return  void
     * @throws  \RuntimeException  In 'strict' mode for external callers
     */
    protected function enforceRawQueryMode(string $sql): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 8);

        // Walk past internal setQuery() overrides in driver subclasses.
        // Frame 0 = enforceRawQueryMode(), Frame 1 = Driver::setQuery(),
        // Frame 2+ = possibly Firebird/Oracle setQuery() that called
        // parent::setQuery(). Skip those to find the REAL caller.
        $caller = null;
        for ($i = 2, $len = count($trace); $i < $len; $i++) {
            $fn = $trace[$i]['function'] ?? '';
            $cls = $trace[$i]['class'] ?? '';

            // Skip setQuery() calls within the Database namespace
            if (
                $fn === 'setQuery'
                && $cls !== ''
                && strncmp($cls, 'Hubzero\\Database\\', 17) === 0
            ) {
                continue;
            }

            $caller = $trace[$i];
            break;
        }

        if ($caller === null) {
            return;
        }

        $callerClass = $caller['class'] ?? '';

        // Internal Database framework calls are always allowed
        if (
            $callerClass !== ''
            && strncmp(
                $callerClass,
                'Hubzero\\Database\\',
                17
            ) === 0
        ) {
            return;
        }

        $file = $caller['file'] ?? 'unknown';
        $line = $caller['line'] ?? 0;
        $function = $caller['function'] ?? '';
        $sqlPreview = substr($sql, 0, 200);

        if ($this->rawQueryMode === 'log') {
            $this->getLogger()->notice(
                'Raw SQL via setQuery()',
                [
                    'sql_preview' => $sqlPreview,
                    'file'        => $file,
                    'line'        => $line,
                    'class'       => $callerClass,
                    'function'    => $function,
                ]
            );
            return;
        }

        // strict mode
        throw new \RuntimeException(
            "setQuery() called with raw SQL in strict mode. "
            . "Use the Query builder for portable queries. "
            . "Called from {$file}:{$line}"
            . ($callerClass ? " ({$callerClass}::{$function})" : '')
        );
    }

    /**
     * Grabs the connection adapter
     *
     * Returns the ConnectionInterface implementation used by this driver.
     * For most operations, use the Driver methods directly instead.
     *
     * @return  ConnectionInterface|mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Gets the unique identifier for this connection instance
     *
     * This UUID is used for query cache isolation. Each connection gets a unique
     * ID, ensuring that caches are properly separated between different connections.
     * The ID is generated lazily on first access and persists for the connection's
     * lifetime. If the connection is replaced via setConnection(), a new ID is generated.
     *
     * @return  string  A unique identifier for this connection instance
     */
    public function getConnectionId(): string
    {
        if ($this->connectionId === null) {
            $this->connectionId = $this->generateConnectionId();
        }

        return $this->connectionId;
    }

    /**
     * Generates a new unique connection identifier
     *
     * Creates a cryptographically secure random identifier for this connection.
     * This is called automatically when a connection is established or replaced.
     *
     * @return  string  A 32-character hexadecimal UUID
     */
    protected function generateConnectionId(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Gets the native/underlying database connection
     *
     * This provides an escape hatch for cases where direct access to the
     * underlying connection is needed (e.g., PDO, mysqli, or proprietary
     * driver-specific features like PostgreSQL's lo_* methods).
     *
     * Example:
     * ```php
     * $pdo = $db->getNativeConnection();
     * $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
     * ```
     *
     * @return  mixed  The native connection (PDO, mysqli, etc.)
     */
    public function getNativeConnection()
    {
        if ($this->connection instanceof ConnectionInterface) {
            return $this->connection->getNativeConnection();
        }

        // Fallback for legacy drivers that don't use ConnectionInterface
        return $this->connection;
    }

    /**
     * Grabs the syntax
     *
     * @return  string
     */
    public function getSyntax()
    {
        return $this->syntax;
    }

    /**
     * Sets the syntax
     *
     * @param   string  $syntax  The syntax being used based on the connection
     * @return  $this
     */
    public function setSyntax($syntax)
    {
        $this->syntax = $syntax;

        return $this;
    }

    /**
     * Gets the total number of SQL statements executed by the database driver
     *
     * @return  int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Gets the name of the database in use by this connection
     *
     * @return  string
     */
    protected function getDatabase()
    {
        return $this->database;
    }

    /**
     * Returns a PHP date() function compliant date format for the database driver
     *
     * @return  string
     */
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s';
    }

    /**
     * Gets the database driver SQL statement log
     *
     * @return  array
     */
    public function getLog()
    {
        return $this->log;
    }

    // =========================================================================
    // Query Logging
    // =========================================================================
    //
    // Record all executed queries with timing information for debugging
    // and performance analysis. Disabled by default for performance.
    //
    // | Method                | Returns | Description                          |
    // |-----------------------|---------|--------------------------------------|
    // | enableQueryLog()      | $this   | Start recording queries              |
    // | disableQueryLog()     | $this   | Stop recording queries               |
    // | isQueryLogEnabled()   | bool    | Check if logging is enabled          |
    // | getQueryLog()         | array   | Get all logged queries               |
    // | flushQueryLog()       | $this   | Clear the query log                  |
    // | getQueryLogCount()    | int     | Number of logged queries             |
    // | getQueryLogTotalTime()| float   | Total execution time (ms)            |
    //
    // Example:
    // ```php
    // $db = App::get('db');
    // $db->enableQueryLog();
    //
    // // Run your queries...
    // $users = User::all()->rows();
    // $articles = Article::whereEquals('status', 1)->rows();
    //
    // // Analyze queries
    // foreach ($db->getQueryLog() as $query) {
    //     echo $query['sql'] . "\n";
    //     echo "Bindings: " . json_encode($query['bindings']) . "\n";
    //     echo "Time: " . $query['time'] . "ms\n\n";
    // }
    //
    // echo "Total queries: " . $db->getQueryLogCount() . "\n";
    // echo "Total time: " . $db->getQueryLogTotalTime() . "ms\n";
    // ```
    // =========================================================================

    /**
     * Whether detailed query logging is enabled
     *
     * @var bool
     */
    protected static $queryLoggingEnabled = false;

    /**
     * Detailed query log with timing information
     *
     * @var array
     */
    protected static $queryLog = [];

    /**
     * Enable detailed query logging
     *
     * When enabled, all executed queries are logged with their SQL,
     * bindings, and execution time. This is useful for debugging
     * and performance analysis.
     *
     * Example:
     * ```php
     * $db = App::get('db');
     * $db->enableQueryLog();
     *
     * // ... run queries ...
     *
     * $queries = $db->getQueryLog();
     * foreach ($queries as $query) {
     *     echo $query['sql'] . ' (' . $query['time'] . 'ms)';
     * }
     * ```
     *
     * @return  $this
     */
    public function enableQueryLog()
    {
        static::$queryLoggingEnabled = true;
        return $this;
    }

    /**
     * Disable detailed query logging
     *
     * @return  $this
     */
    public function disableQueryLog()
    {
        static::$queryLoggingEnabled = false;
        return $this;
    }

    /**
     * Check if query logging is enabled
     *
     * @return  bool
     */
    public function isQueryLogEnabled()
    {
        return static::$queryLoggingEnabled;
    }

    /**
     * Get the detailed query log
     *
     * Returns an array of queries with their SQL, bindings, and execution time.
     * Each entry contains:
     * - sql: The SQL query string
     * - bindings: Array of bound parameters
     * - time: Execution time in milliseconds
     *
     * Example:
     * ```php
     * $queries = $db->getQueryLog();
     * // [
     * //   ['sql' => 'SELECT * FROM users WHERE id = ?', 'bindings' => [1], 'time' => 2.5],
     * //   ['sql' => 'UPDATE users SET name = ?', 'bindings' => ['John'], 'time' => 1.2],
     * // ]
     * ```
     *
     * @return  array
     */
    public function getQueryLog()
    {
        return static::$queryLog;
    }

    /**
     * Clear the query log
     *
     * @return  $this
     */
    public function flushQueryLog()
    {
        static::$queryLog = [];
        return $this;
    }

    /**
     * Get the total execution time of all logged queries
     *
     * @return  float  Total time in milliseconds
     */
    public function getQueryLogTotalTime()
    {
        return array_sum(array_column(static::$queryLog, 'time'));
    }

    /**
     * Get the number of queries in the log
     *
     * @return  int
     */
    public function getQueryLogCount()
    {
        return count(static::$queryLog);
    }

    /**
     * Log a query to the detailed query log
     *
     * This is called internally during query execution when logging is enabled.
     *
     * @param   string  $sql       The SQL query
     * @param   array   $bindings  The query bindings
     * @param   float   $time      Execution time in milliseconds
     * @return  void
     */
    protected function logQuery($sql, array $bindings, $time)
    {
        if (static::$queryLoggingEnabled) {
            static::$queryLog[] = [
                'sql' => $sql,
                'bindings' => $bindings,
                'time' => round($time, 2),
            ];
        }
    }

    /**
     * Gets the database timer
     *
     * @return  int
     */
    public function getTimer()
    {
        return $this->timer;
    }

    /**
     * Gets the null or zero representation of a timestamp for the database driver
     *
     * @return  string
     */
    public function getNullDate()
    {
        return $this->nullDate;
    }

    /**
     * Gets the common table prefix for the database driver
     *
     * @return  string
     */
    public function getPrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * Sets the common table prefix for the database driver
     *
     * @param   string  $prefix  The prefix to use
     * @return  $this
     */
    public function setPrefix($prefix)
    {
        $this->tablePrefix = $prefix;

        return $this;
    }

    /**
     * Gets the driver type identifier
     *
     * Returns the database driver type (e.g., 'mysql', 'sqlite', 'pgsql').
     * This is useful for debugging, logging, and tests that need to check
     * driver-specific behavior without using instanceof or is*() methods.
     *
     * @return  string  The driver type identifier
     */
    public function getDriverType(): string
    {
        return $this->name ?? 'unknown';
    }

    /**
     * Gets the current sql statement
     *
     * @return  string
     */
    public function getStatement()
    {
        return (is_object($this->statement))
                ? $this->interpolate($this->statement->queryString, $this->bindings)
                : $this->statement;
    }

    // =========================================================================
    // Connection Mechanics (absorbed from Pdo)
    //
    // These methods handle query preparation, execution, and result fetching.
    // They delegate to the ConnectionInterface implementation, with fallback
    // to raw PDO for backward compatibility.
    // =========================================================================

    /**
     * Fetches a row from the result set cursor as an object
     *
     * @param   string       $class  The class name to use for the returned row object
     * @return  object|null
     */
    protected function fetchObject($class = 'stdClass')
    {
        if ($this->connection instanceof ConnectionInterface) {
            return $this->connection->fetchObject($this->statement, $class);
        }

        // Backward compatibility: raw PDO - statement is a PDOStatement
        return $this->statement->fetchObject($class);
    }

    /**
     * Fetches a row from the result set as an array
     *
     * @return  array|null
     */
    protected function fetchArray()
    {
        if ($this->connection instanceof ConnectionInterface) {
            return $this->connection->fetchArray($this->statement);
        }

        // Backward compatibility: raw PDO - statement is a PDOStatement
        return $this->statement->fetch(\PDO::FETCH_NUM);
    }

    /**
     * Fetches a row from the result set as an associative array
     *
     * @return  array|null
     */
    protected function fetchAssoc()
    {
        if ($this->connection instanceof ConnectionInterface) {
            return $this->connection->fetchAssoc($this->statement);
        }

        // Backward compatibility: raw PDO - statement is a PDOStatement
        return $this->statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Detects the driver syntax
     *
     * Supports both ConnectionInterface and raw PDO for backward compatibility.
     *
     * @return  string
     **/
    protected function detectSyntax()
    {
        if ($this->connection instanceof ConnectionInterface) {
            return $this->connection->getDriverName();
        }

        // Backward compatibility: raw PDO object
        if ($this->connection instanceof \PDO) {
            return $this->connection->getAttribute(\PDO::ATTR_DRIVER_NAME);
        }

        return '';
    }

    /**
     * Frees up the memory used for the result set
     *
     * @return  $this
     */
    protected function freeResult()
    {
        if ($this->connection instanceof ConnectionInterface) {
            $this->connection->freeResult($this->statement);
        } else {
            // Backward compatibility: raw PDO - statement is a PDOStatement
            $this->statement->closeCursor();
        }

        $this->statement = null;

        return $this;
    }

    /**
     * Sets the error reporting mode to throw exceptions
     *
     * @return  $this
     **/
    public function throwExceptions()
    {
        if ($this->connection instanceof ConnectionInterface) {
            $this->connection->setExceptionMode();
        } elseif ($this->connection instanceof \PDO) {
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return $this;
    }

    /**
     * Sets the error reporting mode to return errors
     *
     * @return  $this
     **/
    public function returnErrors()
    {
        if ($this->connection instanceof ConnectionInterface) {
            $this->connection->setSilentMode();
        } elseif ($this->connection instanceof \PDO) {
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        }

        return $this;
    }

    /**
     * Checks for a database connection
     *
     * @return  $this
     * @throws  ConnectionFailedException  If no database connection exists
     **/
    public function hasConnectionOrFail()
    {
        if (!$this->connection) {
            throw new ConnectionFailedException('No database connection.', 500);
        }

        if ($this->connection instanceof ConnectionInterface) {
            if (!$this->connection->isConnected()) {
                throw new ConnectionFailedException('No database connection.', 500);
            }
        } elseif (!is_object($this->connection)) {
            throw new ConnectionFailedException('No database connection.', 500);
        }

        return $this;
    }

    /**
     * Prepares a query for binding
     *
     * @param   string  $statement  The statement to prepare
     * @return  $this
     **/
    public function prepare($statement)
    {
        $sql = $this->replacePrefix($statement);

        $this->bindings = [];

        // Close previous statement to avoid cursor issues
        if ($this->statement) {
            try {
                $this->statement->closeCursor();
            } catch (\PDOException $e) {
                // Ignore - statement might already be closed
            }
            $this->statement = null;
        }

        if ($this->connection instanceof ConnectionInterface) {
            $this->statement = $this->connection->prepare($sql);
        } elseif ($this->connection instanceof \PDO) {
            // Backward compatibility: raw PDO
            $this->statement = $this->connection->prepare($sql);
        }

        return $this;
    }

    /**
     * Explicitly translate generic type to driver specific types
     *
     * @param   string  $type  The variable type (bool, null, int, str)
     * @return  int
     **/
    private function translateType($type)
    {
        return constant('\PDO::PARAM_' . strtoupper($type));
    }

    /**
     * Infers the variable type from the variable itself
     *
     * @param   mixed  $binding  The binding to infer from
     * @return  int
     **/
    private function inferType($binding)
    {
        if (is_bool($binding)) {
            $type = \PDO::PARAM_BOOL;
        } elseif (is_null($binding)) {
            $type = \PDO::PARAM_NULL;
        } elseif (is_int($binding)) {
            $type = \PDO::PARAM_INT;
        } else {
            $type = \PDO::PARAM_STR;
        }

        return $type;
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
     **/
    public function bind($bindings, $type = [])
    {
        $idx = 1;

        $this->bindings = $bindings;

        foreach ($bindings as $binding) {
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
     * Gets the auto-incremented value from the last INSERT statement
     *
     * @return  int
     */
    public function insertid()
    {
        if ($this->connection instanceof ConnectionInterface) {
            return $this->connection->lastInsertId();
        }

        // Backward compatibility: raw PDO
        return $this->connection->lastInsertId();
    }

    /**
     * Sets the connection to use UTF-8 character encoding
     *
     * @return  bool
     */
    abstract public function setUTF();

    /**
     * Initializes a transaction
     *
     * @return  void
     */
    abstract public function transactionStart();

    /**
     * Rolls back a transaction
     *
     * @return  void
     */
    abstract public function transactionRollback();

    /**
     * Commits a transaction
     *
     * @return  void
     */
    abstract public function transactionCommit();

    /**
     * Execute a callback within a database transaction
     *
     * This method provides a convenient way to wrap database operations in a transaction.
     * The transaction is automatically committed on success or rolled back on exception.
     *
     * Example usage:
     * ```php
     * $result = $db->transaction(function($db) {
     *     $db->setQuery("INSERT INTO users (name) VALUES ('John')")->execute();
     *     $db->setQuery("UPDATE accounts SET balance = balance - 100")->execute();
     *     return $db->insertid();
     * });
     * ```
     *
     * With retry on deadlock:
     * ```php
     * $result = $db->transaction(function($db) {
     *     // operations that might deadlock
     * }, 3); // Retry up to 3 times
     * ```
     *
     * @param   callable  $callback  The callback to execute within the transaction.
     *                               Receives the driver instance as first argument.
     * @param   int       $attempts  Number of attempts before giving up (for deadlock retry).
     *                               Default is 1 (no retry).
     * @return  mixed     The return value of the callback
     * @throws  \Throwable  Re-throws the exception after rollback if all attempts fail
     */
    public function transaction(callable $callback, int $attempts = 1)
    {
        if ($attempts < 1) {
            $attempts = 1;
        }

        $lastException = null;

        for ($currentAttempt = 1; $currentAttempt <= $attempts; $currentAttempt++) {
            $this->transactionStart();

            try {
                $result = $callback($this);
                $this->transactionCommit();
                return $result;
            } catch (\Throwable $e) {
                $this->transactionRollback();
                $lastException = $e;

                // Only retry on deadlock-related exceptions
                if ($currentAttempt < $attempts && $this->isDeadlockException($e)) {
                    // Brief pause before retry (exponential backoff)
                    usleep(min(100000 * pow(2, $currentAttempt - 1), 1000000));
                    continue;
                }

                throw $e;
            }
        }

        // This should never be reached, but just in case
        if ($lastException) {
            throw $lastException;
        }
    }

    /**
     * Determine if an exception is caused by a deadlock or lock timeout
     *
     * Override in driver-specific classes for more accurate detection.
     *
     * @param   \Throwable  $e  The exception to check
     * @return  bool
     */
    protected function isDeadlockException(\Throwable $e): bool
    {
        $message = strtolower($e->getMessage());

        return strpos($message, 'deadlock') !== false
            || strpos($message, 'lock wait timeout') !== false
            || strpos($message, 'serialization failure') !== false;
    }

    /**
     * Check if the connection is currently within a transaction
     *
     * @return  bool
     */
    public function inTransaction(): bool
    {
        return $this->getTransactionLevel() > 0;
    }

    /**
     * Get the current transaction nesting level
     *
     * Returns 0 if not in a transaction, 1 for the main transaction,
     * and higher values for nested transactions (savepoints).
     *
     * @return  int
     */
    public function getTransactionLevel(): int
    {
        return $this->transactionDepth ?? 0;
    }

    /**
     * Unlocks all tables in the database
     *
     * @return  $this
     */
    abstract public function unlockTables();

    /**
     * Locks a table in the database
     *
     * @param   string  $tableName  The name of the table to lock
     * @return  $this
     */
    abstract public function lockTable($tableName);

    /**
     * Executes the SQL statement
     *
     * @return  $this|int
     * @throws  QueryFailedException
     */
    public function execute()
    {
        $this->hasConnectionOrFail();

        $start = microtime(true);

        try {
            $this->statement->execute();
        } catch (\PDOException $e) {
            throw new QueryFailedException($e->getMessage(), 500, $e);
        }

        if ($this->debug || $this->slowQueryThreshold > 0) {
            $this->log(microtime(true) - $start);
        }

        // For DELETE, UPDATE, INSERT queries, return affected row count
        $sql = $this->statement->queryString ?? '';
        $type = strtoupper(substr(trim($sql), 0, 6));
        if ($type === 'DELETE' || $type === 'UPDATE' || $type === 'INSERT') {
            return $this->getAffectedRows();
        }

        return $this;
    }

    /**
     * Executes a raw SQL statement directly without prepared statements
     *
     * This is useful for DDL statements, PRAGMA commands, SET statements,
     * and other SQL that doesn't need parameter binding or return results.
     *
     * @param   string  $statement  The SQL statement to execute
     * @return  int     The number of affected rows
     * @throws  QueryFailedException
     */
    public function exec($statement)
    {
        // Check connection
        $this->hasConnectionOrFail();

        // Replace table prefix
        $sql = $this->replacePrefix($statement);

        // Capture the start time
        $start = microtime(true);

        try {
            if ($this->connection instanceof ConnectionInterface) {
                $result = $this->connection->exec($sql);
            } elseif ($this->connection instanceof \PDO) {
                // Backward compatibility: raw PDO
                $result = $this->connection->exec($sql);
            } else {
                $result = 0;
            }
        } catch (\PDOException $e) {
            throw new QueryFailedException($e->getMessage(), 500, $e);
        }

        // Log query if debugging is enabled or slow query logging is configured
        if ($this->debug || $this->slowQueryThreshold > 0) {
            $this->log(microtime(true) - $start);
        }

        return $result === false ? 0 : $result;
    }

    /**
     * Determine whether an exception represents a duplicate key violation.
     *
     * Default implementation uses common SQLSTATE/error-message patterns
     * shared by PDO drivers. Dialect drivers can override this for
     * backend-specific behavior.
     *
     * @param   \Throwable  $e
     * @return  bool
     */
    public function isDuplicateKeyException(\Throwable $e): bool
    {
        // Exception may be wrapped (e.g. QueryFailedException).
        $pdoException = ($e instanceof \PDOException) ? $e : $e->getPrevious();
        if (!$pdoException instanceof \PDOException) {
            return false;
        }

        $sqlState = (string) $pdoException->getCode();
        $errorMessage = $pdoException->getMessage();

        // SQLSTATE 23000 = integrity constraint violation (includes duplicate key)
        // SQLSTATE 23505 = unique violation (PostgreSQL, DB2)
        // SQLSTATE HY000 with -803 = Firebird duplicate key error
        // SQLSTATE HY000 with ORA-00001 = Oracle unique constraint violated
        // ASE/PDO_DBLIB: "duplicate key row" in error message
        return in_array($sqlState, ['23000', '23505'], true) ||
            ($sqlState === 'HY000' && (
                strpos($errorMessage, '-803') !== false ||
                strpos($errorMessage, 'violation of PRIMARY or UNIQUE KEY') !== false ||
                strpos($errorMessage, 'UNIQUE KEY constraint') !== false ||
                strpos($errorMessage, 'ORA-00001') !== false
            )) ||
            strpos($errorMessage, 'duplicate key row') !== false;
    }

    /**
     * Get the schema manager instance
     *
     * The schema manager provides a unified interface for all DDL operations:
     * - Creating and altering tables (fluent builders)
     * - Dropping tables
     * - Table introspection (columns, keys, existence checks)
     * - Index management
     *
     * Usage:
     *   $db->schema()->createTable('#__users')
     *       ->id()
     *       ->string('name', 255)
     *       ->execute();
     *
     *   if ($db->schema()->tableExists('#__users')) { ... }
     *
     *   $columns = $db->schema()->getTableColumns('#__users');
     *
     * @return  SchemaManager
     */
    public function schema(): SchemaManager
    {
        if ($this->schema === null) {
            $this->schema = new SchemaManager($this);
        }

        return $this->schema;
    }

    /**
     * Get a Table gateway for fluent table operations
     *
     * Convenience method that returns a Table instance for the specified table.
     * The Table class provides fluent methods for create(), alter(), drop(), etc.
     *
     * @param   string  $table  Table name (can include prefix placeholder #__)
     * @return  \Hubzero\Database\Schema\Table
     */
    public function table(string $table): \Hubzero\Database\Schema\Table
    {
        return $this->schema()->table($table);
    }

    /**
     * Renames a table in the database
     *
     * @param   string  $oldTable  The name of the table to be renamed
     * @param   string  $newTable  The new name for the table
     * @param   string  $backup    Table prefix
     * @param   string  $prefix    For the table - used to rename constraints in non-mysql databases
     * @return  $this
     */
    abstract public function renameTable($oldTable, $newTable, $backup = null, $prefix = null);

    /**
     * Selects a database for use
     *
     * @param   string  $database  The name of the database to select for use
     * @return  bool
     */
    abstract public function select($database);

    /**
     * Gets a new query for the current driver
     *
     * @return  Query
     */
    abstract public function getQuery();

    /**
     * Retrieves field information about the given tables
     *
     * @param   string  $table     The name of the database table
     * @param   bool    $typeOnly  True (default) to only return field types
     * @return  array
     */
    abstract public function getTableColumns($table, $typeOnly = true);

    /**
     * Shows the table CREATE statement that creates the given tables
     *
     * @param   string|array  $tables  A table name or a list of table names
     * @return  array
     */
    abstract public function getTableCreate($tables);

    /**
     * Retrieves key information about the given tables
     *
     * @param   string|array  $tables  A table name or a list of table names
     * @return  array
     */
    abstract public function getTableKeys($tables);

    /**
     * Gets an array of all tables in the database
     *
     * @return  array
     */
    abstract public function getTableList();

    /**
     * Gets the version of the database connector
     *
     * @return  string
     */
    abstract public function getVersion();

    /**
     * Determines if the connection to the server is active
     *
     * @return  bool
     */
    public function connected()
    {
        if (!$this->connection) {
            return false;
        }

        if ($this->connection instanceof ConnectionInterface) {
            return $this->connection->isConnected();
        }

        // Backward compatibility: raw PDO
        if ($this->connection instanceof \PDO) {
            try {
                $this->connection->query('SELECT 1');
                return true;
            } catch (\PDOException $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Drops a table from the database
     *
     * @param   string  $table     The name of the database table to drop
     * @param   bool    $ifExists  Optionally specify that the table must exist before it is dropped
     * @return  $this
     */
    abstract public function dropTable($table, $ifExists = true);

    /**
     * Truncates a table (removes all rows and resets auto-increment)
     *
     * Emulates MySQL TRUNCATE semantics across all drivers:
     * 1. Deletes all rows from the table
     * 2. Resets auto-increment/identity counter to $nextId
     * 3. Always commits (MySQL TRUNCATE is DDL and auto-commits)
     * 4. Never cascades to other tables
     *
     * @param   string  $table   The name of the database table to truncate
     * @param   int     $nextId  The next auto-increment value (default 1)
     * @return  $this
     */
    abstract public function truncateTable($table, int $nextId = 1);

    /**
     * Escapes a string for usage in an SQL statement
     *
     * @param   string   $text   The string to be escaped
     * @param   boolean  $extra  Optional parameter to provide extra escaping
     * @return  string
     */
    public function escape($text, $extra = false)
    {
        if ($this->connection instanceof ConnectionInterface) {
            return $this->connection->escape($text ?? '', $extra);
        }

        // Backward compatibility: raw PDO
        $result = substr($this->connection->quote($text ?? ''), 1, -1);

        if ($extra) {
            $result = addcslashes($result, '%_');
        }

        return $result;
    }

    /**
     * Gets the number of affected rows for the previous executed SQL statement
     *
     * @return  int
     */
    public function getAffectedRows()
    {
        if ($this->connection instanceof ConnectionInterface) {
            return $this->connection->affectedRows($this->statement);
        }

        // Backward compatibility: raw PDO - statement is a PDOStatement
        return $this->statement->rowCount();
    }

    /**
     * Gets the database collation in use by sampling a text field of a table in the database
     *
     * @return  string|bool
     */
    abstract public function getCollation();

    /**
     * Grabs the number of returned rows for the previous executed SQL statement
     *
     * @return  int
     */
    public function getNumRows()
    {
        // @FIXME: this isn't guaranteed to work on select statements in mysql
        if ($this->connection instanceof ConnectionInterface) {
            return $this->connection->affectedRows($this->statement);
        }

        // Backward compatibility: raw PDO - statement is a PDOStatement
        return $this->statement->rowCount();
    }

    /**
     * Checks for the existance of a table
     *
     * @param   string  $table  The table we're looking for
     * @return  bool
     */
    abstract public function tableExists($table);

    /**
     * Returns whether or not the given table has a given field
     *
     * @param   string  $table  A table name
     * @param   string  $field  A field name
     * @return  bool
     */
    abstract public function tableHasField($table, $field);

    /**
     * Returns whether or not the given table has a given key
     *
     * Note: Method name uses capital 'K' in 'Key' (tableHasKey).
     * PHP methods are case-insensitive, so existing code using
     * tableHaskey (lowercase k) will continue to work.
     *
     * @param   string  $table  A table name
     * @param   string  $key    A key name
     * @return  bool
     */
    abstract public function tableHasKey($table, $key);

    /**
     * Gets the primary key of a table
     *
     * @return  string
     **/
    abstract public function getPrimaryKey($table);

    /**
     * Get primary key column names for a table
     *
     * Default implementation returns a single column from getPrimaryKey().
     * Drivers with composite primary key support should override.
     *
     * @param   string  $table  The table name
     * @return  array
     */
    public function getPrimaryKeyColumns($table): array
    {
        $pk = $this->getPrimaryKey($table);
        return $pk ? [$pk] : [];
    }

    /**
     * Gets foreign key constraints for a table
     *
     * Returns an array of foreign key constraint objects, each containing:
     * - name: The constraint name
     * - columns: Array of local column names
     * - foreign_table: The referenced table name
     * - foreign_columns: Array of referenced column names
     * - on_update: The ON UPDATE action (CASCADE, SET NULL, RESTRICT, NO ACTION)
     * - on_delete: The ON DELETE action (CASCADE, SET NULL, RESTRICT, NO ACTION)
     *
     * @param   string  $table  The table name
     * @return  array   Array of foreign key constraint objects
     */
    abstract public function getForeignKeys($table);

    /**
     * Checks if a table has a specific foreign key constraint
     *
     * @param   string  $table  The table name
     * @param   string  $name   The foreign key constraint name
     * @return  bool
     */
    public function hasForeignKey($table, $name)
    {
        $foreignKeys = $this->getForeignKeys($table);

        foreach ($foreignKeys as $fk) {
            if ($fk->name === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the database engine of the given table
     *
     * @param   string       $table  The table for which to retrieve the engine type
     * @return  string|bool
     **/
    abstract public function getEngine($table);

    /**
     * Set the database engine of the given table
     *
     * @param   string  $table   The table for which to retrieve the engine type
     * @param   string  $engine  The engine type to set
     * @return  bool
     **/
    abstract public function setEngine($table, $engine);

    /**
     * Gets the database character set of the given table
     *
     * @param   string  $table  The table for which to retrieve the character set
     * @param   string  $field  The field to check (optional)
     * @return  string|bool
     **/
    abstract public function getCharacterSet($table, $field = null);

    /**
     * Converts a table to the specified character set
     *
     * This converts all text columns in the table to the new character set.
     * On SQLite this is a no-op as SQLite handles encoding at the database level.
     *
     * @param   string       $table    The table to convert
     * @param   string       $charset  The character set (e.g., 'utf8', 'utf8mb4')
     * @param   string|null  $collate  Optional collation (e.g., 'utf8mb4_unicode_ci')
     * @return  bool
     **/
    abstract public function convertToCharset($table, $charset, $collate = null);

    /**
     * Gets the auto-increment value for the given table
     *
     * @param   string    $table  The table for which to retrieve the character set
     * @return  int|bool
     **/
    abstract public function getAutoIncrement($table);

    /**
     * Sets the auto-increment starting value for the given table
     *
     * On SQLite this is a no-op as SQLite manages auto-increment automatically
     * through the sqlite_sequence table. Use this for MySQL/PostgreSQL.
     *
     * @param   string  $table  The table name
     * @param   int     $value  The auto-increment starting value
     * @return  bool
     **/
    abstract public function setAutoIncrement($table, $value);

    // =========================================================================
    // ENUM Column Management
    // =========================================================================
    // Abstract methods for ENUM column operations. MySQL has native ENUM support,
    // while SQLite stores ENUM as TEXT with no enforcement.

    /**
     * Get the allowed values for an ENUM column
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @return  array   Array of allowed values, empty array if not an ENUM or on SQLite
     **/
    abstract public function getEnumValues($table, $column);

    /**
     * Add a value to an ENUM column
     *
     * On SQLite this is a no-op since ENUM columns are stored as TEXT.
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @param   string  $value   The value to add
     * @return  bool
     **/
    abstract public function addEnumValue($table, $column, $value);

    /**
     * Remove a value from an ENUM column
     *
     * On SQLite this is a no-op since ENUM columns are stored as TEXT.
     * Warning: Existing rows with this value will become invalid on MySQL.
     *
     * @param   string  $table   The table name
     * @param   string  $column  The column name
     * @param   string  $value   The value to remove
     * @return  bool
     **/
    abstract public function removeEnumValue($table, $column, $value);

    // =========================================================================
    // View Management
    // =========================================================================
    // Abstract methods for database view operations that differ between
    // MySQL and SQLite.

    /**
     * Creates or replaces a database view
     *
     * MySQL uses CREATE OR REPLACE VIEW with ALGORITHM, DEFINER, and SQL SECURITY clauses.
     * SQLite requires DROP then CREATE (no security concepts).
     * PostgreSQL uses CREATE OR REPLACE VIEW.
     *
     * Table prefixes (#__) are replaced in both the view name and the SELECT SQL.
     *
     * Options (MySQL-specific, ignored on SQLite/PostgreSQL):
     * - algorithm: UNDEFINED, MERGE, or TEMPTABLE (default: UNDEFINED)
     * - definer: User who owns the view (default: CURRENT_USER)
     * - security: DEFINER or INVOKER (default: INVOKER)
     *
     * @param   string  $name       The view name (with or without prefix)
     * @param   string  $selectSql  The SELECT statement for the view (prefixes will be replaced)
     * @param   array   $options    MySQL-specific view options (algorithm, definer, security)
     * @return  bool
     **/
    abstract public function createOrReplaceView($name, $selectSql, array $options = []);

    /**
     * Drops a database view
     *
     * @param   string  $name      The view name (with or without prefix)
     * @param   bool    $ifExists  Whether to use IF EXISTS clause
     * @return  bool
     **/
    abstract public function dropView($name, $ifExists = true);

    /**
     * Checks if a view exists in the database
     *
     * @param   string  $name  The view name (with or without prefix)
     * @return  bool
     **/
    abstract public function viewExists($name);

    /**
     * Returns a list of all views in the current database
     *
     * @return  array  Array of view names
     **/
    abstract public function getViews();

    /**
     * Returns a list of all database names on the server
     *
     * Note: SQLite is file-based and does not support this concept,
     * so it returns an array containing only the current database name.
     *
     * @return  array  Array of database names
     **/
    abstract public function getDatabaseNames();

    /**
     * Returns a list of all sequences in the current database
     *
     * Sequences are supported by PostgreSQL, Oracle, and SQL Server (2012+).
     * MySQL and SQLite do not support sequences and will return an empty array.
     *
     * @return  array  Array of SequenceInfo objects
     **/
    abstract public function getSequences();

    /**
     * Creates a new sequence
     *
     * @param   string  $name       The sequence name
     * @param   int     $start      Starting value (default: 1)
     * @param   int     $increment  Increment value (default: 1)
     * @param   array   $options    Additional options (min, max, cycle, cache)
     * @return  bool
     * @throws  \RuntimeException  If sequences are not supported
     **/
    abstract public function createSequence($name, $start = 1, $increment = 1, array $options = []);

    /**
     * Drops a sequence
     *
     * @param   string  $name      The sequence name
     * @param   bool    $ifExists  Whether to use IF EXISTS clause
     * @return  bool
     * @throws  \RuntimeException  If sequences are not supported
     **/
    abstract public function dropSequence($name, $ifExists = true);

    /**
     * Checks if a sequence exists
     *
     * @param   string  $name  The sequence name
     * @return  bool
     **/
    abstract public function sequenceExists($name);

    /**
     * Gets the next value from a sequence
     *
     * @param   string  $name  The sequence name
     * @return  int
     * @throws  \RuntimeException  If sequences are not supported
     **/
    abstract public function nextSequenceValue($name);

    /**
     * Gets the current value of a sequence (without incrementing)
     *
     * @param   string  $name  The sequence name
     * @return  int
     **/
    abstract public function currentSequenceValue($name);

    /**
     * Check if this driver supports sequences
     *
     * Drivers with native sequence support (PostgreSQL, Firebird,
     * Informix, MariaDB 10.3+, Oracle) return true. Drivers with
     * table-based emulation (MySQL, Percona, SQLite) also return true.
     *
     * @return  bool
     **/
    abstract public function supportsSequences(): bool;

    /**
     * Check whether this driver emulates sequences via a backing table.
     *
     * Drivers with native sequence objects should return false.
     * Drivers that implement sequence behavior through a `_sequences`
     * table (or equivalent emulation) should override and return true.
     *
     * @return  bool
     */
    public function usesSequenceEmulation(): bool
    {
        return false;
    }

    /**
     * Get the appropriate column type for binary JSON storage
     *
     * PostgreSQL has native JSONB support. Other databases fall back to TEXT.
     * This allows schema builders to request JSONB-like functionality in a
     * database-agnostic way.
     *
     * @return  string  The column type (e.g., 'JSONB' for PostgreSQL, 'TEXT' for others)
     */
    public function getJsonBinaryColumnType(): string
    {
        return 'TEXT';
    }

    /**
     * Get the SQL CAST keyword for integer conversion.
     *
     * Most drivers use `INTEGER`. Drivers with different CAST syntax
     * should override this method.
     *
     * @return  string
     */
    public function getIntegerCastKeyword(): string
    {
        return 'INTEGER';
    }

    /**
     * Whether identifier quoting wraps names with quote characters.
     *
     * Most drivers quote identifiers (e.g. "name", `name`, [name]).
     * Drivers that intentionally use bare identifiers should override.
     *
     * @return  bool
     */
    public function usesQuotedIdentifiers(): bool
    {
        return true;
    }

    /**
     * Build a column definition string for ALTER TABLE operations
     *
     * This method generates database-specific column definition SQL for use in
     * ALTER TABLE ADD COLUMN or ALTER TABLE MODIFY COLUMN statements.
     *
     * The definition array contains:
     * - 'type': The column type (e.g., 'INT', 'VARCHAR(255)', 'TEXT')
     * - 'modifiers': Array of column modifiers:
     *   - 'nullable': bool - Whether column allows NULL
     *   - 'default': mixed - Default value
     *   - 'autoIncrement': bool - Whether column is auto-increment
     *   - 'unsigned': bool - Whether numeric column is unsigned (MySQL-specific)
     *   - 'comment': string - Column comment (MySQL-specific)
     *   - 'after': string - Place column after this column (MySQL-specific)
     *   - 'first': bool - Place column first in table (MySQL-specific)
     *
     * @param   string  $name        The column name
     * @param   array   $definition  The column definition
     * @return  string  The SQL column definition string
     */
    public function buildAlterColumnDefinition(string $name, array $definition): string
    {
        $type = $definition['type'];
        $modifiers = $definition['modifiers'] ?? [];

        // Clean up type string
        $type = preg_replace('/\s*AUTO_INCREMENT\s*/i', ' ', $type);
        $type = trim($type);

        // Translate zero-date default to NULL
        if (
            array_key_exists('default', $modifiers)
            && \Hubzero\Database\Drivers\Base\BaseSqlDriver::isZeroDate($modifiers['default'])
        ) {
            $modifiers['nullable'] = true;
            $modifiers['default'] = null;
        }

        $parts = [$this->quoteName($name), $type];

        // NULL / NOT NULL
        if (isset($modifiers['nullable'])) {
            $parts[] = $modifiers['nullable'] ? 'NULL' : 'NOT NULL';
        }

        // DEFAULT value
        if (array_key_exists('default', $modifiers)) {
            $default = $modifiers['default'];
            if ($default === null) {
                $parts[] = 'DEFAULT NULL';
            } elseif (is_bool($default)) {
                $parts[] = 'DEFAULT ' . ($default ? '1' : '0');
            } elseif (is_numeric($default)) {
                $parts[] = 'DEFAULT ' . $default;
            } elseif ($default === 'CURRENT_TIMESTAMP') {
                $parts[] = 'DEFAULT CURRENT_TIMESTAMP';
            } else {
                $parts[] = "DEFAULT '" . addslashes($default) . "'";
            }
        }

        return implode(' ', $parts);
    }

    /**
     * Get the mapping of abstract column types to database-specific SQL types
     *
     * Maps abstract column types (e.g., 'integer', 'string', 'boolean') to
     * database-specific SQL types. Drivers override this to provide their
     * specific type mappings.
     *
     * Default implementation provides basic SQL-92 compatible types.
     *
     * @return  array  Mapping of abstract type => SQL type
     */
    public function getAbstractTypeMap(): array
    {
        // Default: SQL-92 compatible types
        return [
            'tinyInteger' => 'SMALLINT',
            'smallInteger' => 'SMALLINT',
            'mediumInteger' => 'INTEGER',
            'integer' => 'INTEGER',
            'bigInteger' => 'BIGINT',
            'boolean' => 'BOOLEAN',
            'string' => 'VARCHAR',
            'char' => 'CHAR',
            'tinyText' => 'TEXT',
            'text' => 'TEXT',
            'mediumText' => 'TEXT',
            'longText' => 'TEXT',
            'float' => 'REAL',
            'double' => 'DOUBLE PRECISION',
            'decimal' => 'DECIMAL',
            'date' => 'DATE',
            'time' => 'TIME',
            'datetime' => 'TIMESTAMP',
            'timestamp' => 'TIMESTAMP',
            'timestampTz' => 'TIMESTAMP',
            'year' => 'INTEGER',
            'binary' => 'BLOB',
            'json' => 'TEXT',
            'uuid' => 'CHAR(36)',
            'ulid' => 'CHAR(26)',
            'ipAddress' => 'VARCHAR(45)',
            'macAddress' => 'VARCHAR(17)',
        ];
    }

    /**
     * Check if this database requires length parameters for string types
     *
     * Most databases require VARCHAR(length) and CHAR(length).
     * SQLite is an exception - it treats all text as TEXT regardless of length.
     *
     * @return  bool  True if length parameters are required
     */
    public function requiresStringLength(): bool
    {
        // Default: yes, most databases require length
        return true;
    }

    /**
     * Check if this database supports precision parameters for float/double types
     *
     * Most databases support FLOAT(precision) and DOUBLE(precision).
     * PostgreSQL is an exception - REAL and DOUBLE PRECISION are fixed-size.
     *
     * @return  bool  True if precision parameters are supported
     */
    public function supportsFloatPrecision(): bool
    {
        // Default: yes, most databases support precision
        return true;
    }

    /**
     * Check if this database supports UNSIGNED modifier on integer columns
     *
     * MySQL and MariaDB support UNSIGNED on integer types.
     * PostgreSQL, SQLite, and SQL Server do not.
     *
     * @return  bool  True if UNSIGNED is supported
     */
    public function supportsUnsigned(): bool
    {
        // Default: no, UNSIGNED is MySQL-specific
        return false;
    }

    /**
     * Check if explicit NULL keyword is supported in DDL
     *
     * Most databases support explicitly specifying NULL for nullable columns,
     * but some (like Firebird) do not. For those databases, nullable columns
     * are created by omitting NOT NULL rather than adding NULL.
     *
     * @return  bool  True if explicit NULL is supported
     */
    public function supportsExplicitNull(): bool
    {
        // Default: yes, most databases support explicit NULL
        return true;
    }

    /**
     * Check whether upsert supports selective update-column lists.
     *
     * Some engines' native upsert syntax always updates all provided columns.
     * Drivers for those engines should override and return false.
     *
     * @return  bool
     */
    public function supportsSelectiveUpsertUpdateColumns(): bool
    {
        return true;
    }

    /**
     * Return the SQL expression for CURRENT_TIMESTAMP default values
     *
     * Most databases use CURRENT_TIMESTAMP. Informix uses CURRENT YEAR TO SECOND.
     *
     * @return  string  SQL expression for current timestamp
     */
    public function currentTimestampDefault(): string
    {
        return 'CURRENT_TIMESTAMP';
    }

    /**
     * Check if IF NOT EXISTS clause is supported in CREATE TABLE
     *
     * Most databases support IF NOT EXISTS, but some (like Firebird) do not.
     *
     * @return  bool  True if IF NOT EXISTS is supported
     */
    public function supportsIfNotExists(): bool
    {
        // Default: yes, most modern databases support IF NOT EXISTS
        return true;
    }

    /**
     * Check if this database supports length parameters for integer types
     *
     * MySQL and MariaDB support INT(length).
     * PostgreSQL, SQLite, and most others do not.
     *
     * @return  bool  True if integer length parameters are supported
     */
    public function supportsIntegerLength(): bool
    {
        // Default: no, only MySQL supports this
        return false;
    }

    /**
     * Get the schema grammar instance for this driver
     *
     * Schema grammars handle database-specific DDL syntax for CREATE TABLE,
     * ALTER TABLE, and other schema operations. Each driver returns its
     * appropriate grammar implementation.
     *
     * @return  \Hubzero\Database\Drivers\Base\BaseSchemaGrammar
     */
    abstract public function getSchemaGrammar();
}
