<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

use App;

/**
 * Database query class
 *
 * @uses \Hubzero\Database\Row for results returned from queries
 */
class Query
{
    /**
     * Known syntax name -> syntax class map.
     *
     * This is the primary resolution path used by reset().
     *
     * @var array<string, string>
     */
    private const SYNTAX_CLASS_MAP = [
        'mysql'    => '\\Hubzero\\Database\\Syntax\\Mysql',
        'mariadb'  => '\\Hubzero\\Database\\Syntax\\Mariadb',
        'percona'  => '\\Hubzero\\Database\\Syntax\\Percona',
        'cubrid'   => '\\Hubzero\\Database\\Syntax\\Cubrid',
        'pgsql'    => '\\Hubzero\\Database\\Syntax\\Pgsql',
        'sqlite'   => '\\Hubzero\\Database\\Syntax\\Sqlite',
        'firebird' => '\\Hubzero\\Database\\Syntax\\Firebird',
        'informix' => '\\Hubzero\\Database\\Syntax\\Informix',
        'sqlsrv'   => '\\Hubzero\\Database\\Syntax\\Sqlsrv',
        'db2'      => '\\Hubzero\\Database\\Syntax\\Db2',
        'oci'      => '\\Hubzero\\Database\\Syntax\\Oci',
    ];

    /**
     * Driver-name aliases mapped to syntax names.
     *
     * Maps PDO driver names (PDO::ATTR_DRIVER_NAME) to syntax class keys when
     * they differ. For example, pdo_ibm reports driver name "ibm" but uses "db2" syntax.
     *
     * @var array<string, string>
     */
    private const SYNTAX_ALIASES = [
        'ibm' => 'db2',   // pdo_ibm reports driver name "ibm"; uses DB2 syntax
    ];

    /**
     * The actual database connection object
     *
     * @var  object
     **/
    private $connection = null;

    /**
     * The query syntax
     *
     * @var  object
     **/
    protected $syntax = null;

    /**
     * The query results cache (in-memory)
     *
     * This is a key value dictionary of query md5 hash and query results.
     *
     * @var  array
     **/
    private static $cache = array();

    /**
     * The injected persistent cache store
     *
     * This can be any object that implements get(), put(), forget(), and has() methods
     * compatible with Hubzero\Cache\Storage interface. When set, it will be used
     * for persistent caching across requests.
     *
     * @var  object|null
     **/
    private static $cacheStore = null;

    /**
     * Tracks whether any SELECT columns have been set
     *
     * Used to provide implicit SELECT * when none are specified.
     *
     * @var  bool
     **/
    private $hasSelect = false;

    /**
     * Cache TTL in minutes (0 = no persistent caching)
     *
     * @var  int
     **/
    private $cacheTtl = 0;

    /**
     * Whether to cache forever (no expiration)
     *
     * @var  bool
     **/
    private $cacheForever = false;

    /**
     * Cache key prefix for persistent cache
     *
     * @var  string
     **/
    private $cachePrefix = 'hubzero_query_';

    /**
     * The database query type constants
     **/
    public const ROW    = 'loadObject';
    public const ROWS   = 'loadObjectList';
    public const COLUMN = 'loadColumn';

    /**
     * The elements of a basic select statement
     *
     * @var  array
     **/
    private $select = array(
        'select',
        'from',
        'join',
        'where',
        'group',
        'having',
        'union',
        'order',
        'limit'
    );

    /**
     * The elements of a basic insert statement
     *
     * @var  array
     **/
    private $insert = array(
        'insert',
        'values'
    );

    /**
     * The elements of a basic update statement
     *
     * @var  array
     **/
    private $update = array(
        'update',
        'set',
        'where'
    );

    /**
     * The elements of a basic delete statement
     *
     * @var  array
     **/
    private $delete = array(
        'delete',
        'where'
    );

    /**
     * The elements of an upsert statement
     *
     * @var  array
     **/
    private $upsert = array(
        'upsert'
    );

    /**
     * The elements of an insertMany (bulk insert) statement
     *
     * @var  array
     **/
    private $insertMany = array(
        'insertMany'
    );

    /**
     * The query type to be performed
     *
     * This is a silly way of tracking what type of query we think
     * we're going to execute. This is used by the execute method.
     *
     * @var  string
     **/
    protected $type = null;

    /**
     * Current condition group nesting depth
     *
     * Used by beginOrGroup()/beginAndGroup()/endGroup() to track
     * the current nesting level for WHERE clause grouping.
     *
     * @var  int
     **/
    protected $groupDepth = 0;

    /**
     * Pending group logical operator
     *
     * When a group is opened, this stores the logical operator (and/or)
     * that connects the GROUP to the previous condition. The first
     * condition inside the group will use this operator.
     *
     * @var  string|null
     **/
    protected $pendingGroupLogical = null;

    /**
     * Named parameters for fluent parameter binding
     *
     * Stores parameters set via setParameter() and setParameters() methods.
     * These are merged with inline bindings when building queries with
     * named placeholders (:name).
     *
     * @var  array
     **/
    protected $namedParameters = [];

    /**
     * Constructs a new query instance
     *
     * @param   object  $connect  The database connection to use in the query builder
     * @return  void
     **/
    public function __construct($connection = null)
    {
        $this->connection = $connection ?: App::get('db');
        $this->reset();
    }

    /**
     * Clones the query object, including its individual syntax elements
     *
     * We want to duplicate our syntax elements, as well as the overall query object,
     * hence the need for this. Otherwise, PHP would only provide references to the
     * syntax elements, which is counter productive in this instance.
     *
     * @return  void
     **/
    public function __clone()
    {
        $this->syntax = clone $this->syntax;
    }

    // =========================================================================
    // Query Caching (Public API)
    // =========================================================================
    //
    // Two-layer caching: in-memory (per-request) + persistent (across requests).
    // See "Persistent Cache Operations" section below for implementation details.
    //
    // Quick Reference:
    //   remember(60)       - Cache for 60 minutes
    //   rememberForever()  - Cache with no TTL
    //   forgetCached($key) - Invalidate specific cache entry
    //   setCacheStore($s)  - Set persistent backend (Redis, file, etc.)
    //   purgeCache()       - Clear in-memory cache
    //
    // Example: Article::all()->whereEquals('published', 1)->remember(30)->rows();
    //
    // =========================================================================

    /**
     * Purges the query cache (in-memory only)
     *
     * Note: This only clears the in-memory cache. Persistent cache entries
     * are managed by their TTL or must be cleared via the cache store directly.
     *
     * @return  void
     **/
    public static function purgeCache()
    {
        self::$cache = array();
    }

    /**
     * Set the persistent cache store for query caching
     *
     * The cache store should implement get(), put(), forget(), and has() methods.
     * Compatible with Hubzero\Cache\Storage classes or any duck-typed equivalent.
     *
     * Example with HubZero Cache:
     *   Query::setCacheStore(App::get('cache')->storage());
     *
     * Example with custom store:
     *   Query::setCacheStore(new MyRedisCache());
     *
     * @param   object|null  $store  Cache store instance or null to disable
     * @return  void
     **/
    public static function setCacheStore($store)
    {
        self::$cacheStore = $store;
    }

    /**
     * Get the persistent cache store
     *
     * @return  object|null
     **/
    public static function getCacheStore()
    {
        return self::$cacheStore;
    }

    /**
     * Cache the query results for a given number of minutes
     *
     * This enables persistent caching using either:
     * 1. An injected cache store (via setCacheStore)
     * 2. APCu if available (automatic fallback)
     * 3. In-memory cache only (when neither is available)
     *
     * Example:
     *   $users = $query->from('users')->whereEquals('active', 1)->remember(60)->fetch();
     *   // Results cached for 60 minutes
     *
     * @param   int     $minutes  Number of minutes to cache (default 60)
     * @param   string  $prefix   Optional custom cache key prefix
     * @return  $this
     **/
    public function remember(int $minutes = 60, ?string $prefix = null)
    {
        $this->cacheTtl = max(0, $minutes);
        $this->cacheForever = false;

        if ($prefix !== null) {
            $this->cachePrefix = $prefix;
        }

        return $this;
    }

    /**
     * Cache the query results forever (no expiration)
     *
     * Use with caution - cached data will persist until manually cleared
     * or the cache store evicts it.
     *
     * Example:
     *   $config = $query->from('config')->rememberForever()->fetch();
     *
     * @param   string  $prefix  Optional custom cache key prefix
     * @return  $this
     **/
    public function rememberForever(?string $prefix = null)
    {
        $this->cacheTtl = 0;
        $this->cacheForever = true;

        if ($prefix !== null) {
            $this->cachePrefix = $prefix;
        }

        return $this;
    }

    /**
     * Forget (invalidate) a cached query result by key
     *
     * Useful when you know data has changed and want to bust the cache.
     *
     * @param   string  $key  The cache key to forget
     * @return  bool    True on success
     **/
    public function forgetCached(string $key)
    {
        $fullKey = $this->cachePrefix . $key;

        // Clear from in-memory cache
        if (isset(self::$cache[$fullKey])) {
            unset(self::$cache[$fullKey]);
        }

        // Clear from persistent store
        return $this->persistentForget($fullKey);
    }

    /**
     * Empties a query clause of current values
     *
     * @param   string  $clause  [select, update, insert, delete, from, join, set, values, where, group, having, order]
     * @return  $this
     **/
    public function clear($clause = '')
    {
        if (!$clause) {
            $this->reset();
        } else {
            $clause = 'reset' . ucfirst(strtolower($clause));

            $this->syntax->$clause();
        }

        return $this;
    }

    /**
     * Empties a query of current select values
     *
     * @return  $this
     **/
    public function deselect()
    {
        $this->syntax->resetSelect();
        $this->hasSelect = false;
        return $this;
    }

    /**
     * Applies a select field to the pending query
     *
     * @param   string  $column  The column to select
     * @param   string  $as      What to call the return val
     * @param   bool    $count   Whether or not to count column
     * @return  $this
     **/
    public function select($column, $as = null, $count = false)
    {
        // TODO: Consider Laravel-style select('a', 'b', ...) support in the future.
        // Right now, select('a', 'b') is treated as column + alias for BC with existing code.
        // If we add variadic support, it must not break alias usage.
        if (is_array($column)) {
            foreach ($column as $key => $value) {
                if (is_array($value)) {
                    $col = $value[0] ?? null;
                    if ($col === null) {
                        continue;
                    }
                    $alias = $value[1] ?? null;
                    $cnt = $value[2] ?? false;
                    $this->syntax->setSelect($col, $alias, $cnt);
                } elseif (is_string($key)) {
                    $this->syntax->setSelect($key, $value);
                } else {
                    $this->syntax->setSelect($value);
                }
            }

            $this->type = 'select';
            $this->hasSelect = true;
            return $this;
        }

        $this->syntax->setSelect($column, $as, $count);
        $this->type = 'select';
        $this->hasSelect = true;
        return $this;
    }

    /**
     * Applies DISTINCT modifier to the SELECT query
     *
     * Forces the query to return only unique rows by eliminating duplicates.
     * Must be called after select() to have effect.
     *
     * Example:
     *   $query->select('category')->distinct()->from('products')
     *   // Generates: SELECT DISTINCT category FROM products
     *
     * @return  $this
     */
    public function distinct()
    {
        $this->syntax->setDistinct(true);
        return $this;
    }

    /**
     * Applies a subquery as a select field
     *
     * Example:
     *   selectSub(function($query) {
     *       $query->select('COUNT(*)')
     *             ->from('comments')
     *             ->whereRaw('comments.post_id = posts.id');
     *   }, 'comment_count')
     *   // Generates: (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comment_count
     *
     * @param   callable  $callback  Closure that builds the subquery
     * @param   string    $as        Alias for the subquery result (required)
     * @return  $this
     **/
    public function selectSub(callable $callback, string $as)
    {
        list($sql, $bindings) = $this->buildSubquery($callback);

        $this->syntax->setRawSelect($sql, $bindings, $as);
        $this->type = 'select';
        $this->hasSelect = true;
        return $this;
    }

    /**
     * Applies a raw select expression to the query
     *
     * Useful for database-specific functions, calculations, or expressions
     * that cannot be built with the standard select() method.
     *
     * Supports both positional (?) and named (:name) placeholders.
     * Named parameters can be passed inline or set via setParameter().
     *
     * Example with no parameters:
     *   selectRaw('COUNT(*) as total')
     *
     * Example with positional placeholders:
     *   selectRaw('COALESCE(price, ?) as display_price', [0], 'price')
     *
     * Example with named placeholders:
     *   selectRaw('price * :tax_rate as with_tax', ['tax_rate' => 1.08])
     *
     * Example with stored parameters:
     *   setParameter('tax_rate', 1.08)->selectRaw('price * :tax_rate as with_tax')
     *
     * @param   string       $expression  The raw SQL expression
     * @param   array        $bindings    Optional bindings (positional or named)
     * @param   string|null  $as          Optional alias for the expression
     * @return  $this
     **/
    public function selectRaw(string $expression, array $bindings = [], ?string $as = null)
    {
        // Process named placeholders if needed
        if ($this->shouldProcessNamedPlaceholders($expression, $bindings)) {
            list($expression, $bindings) = $this->convertNamedToPositional($expression, $bindings);
        }

        $this->syntax->setRawSelect($expression, $bindings, $as);
        $this->type = 'select';
        $this->hasSelect = true;
        return $this;
    }

    /**
     * Applies an insert statement to the pending query
     *
     * @param   string  $table   The table into which we will be inserting
     * @param   bool    $ignore  Whether or not to ignore errors produced related to things like duplicate keys
     * @return  $this
     **/
    public function insert($table, $ignore = false)
    {
        $this->syntax->setInsert($table, $ignore);
        $this->type = 'insert';
        return $this;
    }

    /**
     * Applies an INSERT IGNORE statement to the pending query
     *
     * This is a convenience method equivalent to insert($table, true).
     *
     * @param   string  $table  The table into which we will be inserting
     * @return  $this
     */
    public function insertIgnore($table)
    {
        return $this->insert($table, true);
    }

    /**
     * Applies an INSERT IGNORE statement to the pending query
     *
     * Alias for insertIgnore() to match common naming conventions.
     *
     * @param   string  $table  The table into which we will be inserting
     * @return  $this
     */
    public function insertOrIgnore($table)
    {
        return $this->insertIgnore($table);
    }




    /**
     * Specifies columns for an INSERT statement
     *
     * Used with fromSelect() to define which columns to insert into.
     * If not specified, the SELECT query determines the columns.
     *
     * Example:
     *   $query->insert('users')
     *       ->columns(['email', 'name'])
     *       ->fromSelect($selectQuery);
     *
     * @param   array  $columns  The column names
     * @return  $this
     **/
    public function columns(array $columns)
    {
        $this->syntax->setColumns($columns);
        return $this;
    }

    /**
     * Inserts using a SELECT query as the source
     *
     * Convenience wrapper for insert() + columns() + fromSelect().
     *
     * @param   string         $table    The target table
     * @param   Query|callable $source   Query object or closure that builds the SELECT query
     * @param   array          $columns  Optional list of target columns
     * @return  $this
     **/
    public function insertUsing(string $table, $source, array $columns = [])
    {
        $this->insert($table);

        if (!empty($columns)) {
            $this->columns($columns);
        }

        return $this->fromSelect($source);
    }

    /**
     * Uses a SELECT query as the source for an INSERT statement
     *
     * Enables INSERT ... SELECT patterns with the fluent API.
     * Works with both insert() and insertIgnore().
     *
     * Accepts two input types:
     * 1. Query object - Pass another Query instance (recommended)
     * 2. Closure - Build query inline
     *
     * Example with Query object (recommended):
     *   $selectQuery = $db->getQuery(true)
     *       ->select('uidNumber')
     *       ->select('userPassword')
     *       ->from('#__xprofiles');
     *
     *   $query->insertIgnore('users_password')
     *       ->columns(['user_id', 'passhash'])
     *       ->fromSelect($selectQuery);
     *
     * Example with closure:
     *   $query->insertIgnore('users_password')
     *       ->columns(['user_id', 'passhash'])
     *       ->fromSelect(function($q) {
     *           $q->select('uidNumber')
     *             ->select('userPassword')
     *             ->from('#__xprofiles');
     *       });
     *
     * Database-specific behavior:
     * - MySQL/MariaDB: INSERT IGNORE INTO ... SELECT ...
     * - PostgreSQL: INSERT INTO ... SELECT ... ON CONFLICT DO NOTHING
     * - SQLite: INSERT OR IGNORE INTO ... SELECT ...
     * - SQL Server/Oracle/DB2: Uses appropriate INSERT ... SELECT syntax
     *
     * @param   Query|callable  $source  Query object or closure that builds the SELECT query
     * @return  $this
     **/
    public function fromSelect($source)
    {
        if ($source instanceof Query) {
            // Query object passed - extract SQL with placeholders and bindings
            $sql = $source->toSql('select');
            $bindings = $source->getBindings();
            $this->syntax->setInsertSelect($sql, $bindings);
        } elseif (is_callable($source)) {
            // Closure passed - build subquery
            list($sql, $bindings) = $this->buildSubquery($source);
            $this->syntax->setInsertSelect($sql, $bindings);
        } else {
            throw new \InvalidArgumentException(
                'fromSelect() expects a Query object or closure. ' .
                'For raw SQL, use fromSelectRaw() instead.'
            );
        }

        return $this;
    }

    /**
     * Uses raw SQL SELECT as the source for an INSERT statement
     *
     * For simple cases or when you need full control over the SELECT query.
     * Use fromSelect() with a Query object for better type safety and portability.
     *
     * Example:
     *   $query->insertIgnore('users_password')
     *       ->columns(['user_id', 'passhash'])
     *       ->fromSelectRaw('SELECT uidNumber, userPassword FROM #__xprofiles WHERE active = ?', [1]);
     *
     * @param   string  $sql       The raw SELECT SQL
     * @param   array   $bindings  Optional parameter bindings
     * @return  $this
     **/
    public function fromSelectRaw($sql, array $bindings = [])
    {
        $this->syntax->setInsertSelect($sql, $bindings);
        return $this;
    }

    /**
     * Applies an update statement to the pending query
     *
     * @param   string  $table  The table whose fields will be updated
     * @return  $this
     **/
    public function update($table)
    {
        $this->syntax->setUpdate($table);
        $this->type = 'update';
        return $this;
    }

    /**
     * Applies a delete statement to the pending query
     *
     * @param   string  $table  The table whose row will be deleted
     * @return  $this
     **/
    public function delete($table)
    {
        $this->syntax->setDelete($table);
        $this->type = 'delete';
        return $this;
    }

    /**
     * Defines the table from which data should be retrieved
     *
     * @param   string  $table  The table of interest
     * @param   string  $as     What to call the table
     * @return  $this
     **/
    public function from($table, $as = null)
    {
        $this->syntax->setFrom($table, $as);
        return $this;
    }

    /**
     * Defines a subquery as the source table (derived table)
     *
     * Example:
     *   fromSub(function($query) {
     *       $query->select('user_id')
     *             ->select('COUNT(*)', 'post_count')
     *             ->from('posts')
     *             ->group('user_id');
     *   }, 'user_posts')
     *   // Generates: FROM (SELECT user_id, COUNT(*) AS post_count FROM posts GROUP BY user_id) AS user_posts
     *
     * Note: An alias is required for derived tables in most SQL databases.
     *
     * @param   callable  $callback  Closure that builds the subquery
     * @param   string    $as        Alias for the derived table (required)
     * @return  $this
     **/
    public function fromSub(callable $callback, string $as)
    {
        list($sql, $bindings) = $this->buildSubquery($callback);

        $this->syntax->setRawFrom($sql, $bindings, $as);
        return $this;
    }

    /**
     * Defines a table join to be performed for the query
     *
     * @param   string  $table     The table join
     * @param   string  $leftKey   The left side of the join condition
     * @param   string  $rightKey  The right side of the join condition
     * @param   string  $type      The join type to perform
     * @return  $this
     **/
    public function join($table, $leftKey = null, $rightKey = null, $type = 'inner')
    {
        if (is_callable($leftKey)) {
            $callback = $leftKey;
            $joinType = $rightKey ?? $type;
            $builder = new JoinBuilder($this, $table, $joinType);
            $callback($builder);
            $builder->end();
            return $this;
        }

        $this->syntax->setJoin($table, $leftKey, $rightKey, $type);
        return $this;
    }

    /**
     * Begin a fluent join predicate builder.
     *
     * @param   string  $table
     * @param   string  $type
     * @return  JoinBuilder
     */
    public function joinBuilder($table, $type = 'inner')
    {
        return new JoinBuilder($this, $table, $type);
    }

    /**
     * Defines a table join with multiple ON predicates
     *
     * Example:
     *   joinOn('posts', [
     *       ['users.id', '=', 'posts.user_id'],
     *       ['users.type', '=', 'posts.user_type'],
     *   ])
     *
     *   joinOn('posts', [
     *       ['users.id', '=', 'posts.user_id'],
     *       ['left' => 'users.name', 'operator' => '=', 'value' => 'Sam Wilson'],
     *       ['left_value' => 1, 'operator' => '=', 'right' => 'posts.user_id'],
     *       ['left' => 'users.id', 'operator' => 'in', 'value' => [1, 2, 3]],
     *       ['left' => 'users.id', 'operator' => 'between', 'value' => [1, 5]],
     *   ])
     *
     * @param   string  $table
     * @param   array   $conditions
     * @param   string  $type
     * @return  $this
     */
    public function joinOn($table, array $conditions, $type = 'inner')
    {
        $this->syntax->setJoinOn($table, $conditions, $type);
        return $this;
    }

    /**
     * Defines a table join to be performed for the query using a raw expression
     *
     * @param   string  $table  The table join
     * @param   string  $raw    The join clause (anything after the ON keyword)
     * @param   string  $type   The join type to perform
     * @return  $this
     **/
    public function joinRaw($table, $raw, $type = 'inner')
    {
        $this->syntax->setRawJoin($table, $raw, $type);
        return $this;
    }

    /**
     * Defines a table INNER join to be performed for the query
     *
     * @param   string  $table     The table join
     * @param   string  $leftKey   The left side of the join condition
     * @param   string  $rightKey  The right side of the join condition
     * @return  $this
     **/
    public function innerJoin($table, $leftKey, $rightKey = null)
    {
        if (is_callable($leftKey)) {
            return $this->join($table, $leftKey, 'inner');
        }

        $this->syntax->setJoin($table, $leftKey, $rightKey, 'inner');
        return $this;
    }

    /**
     * Defines a table INNER join with multiple ON predicates
     *
     * @param   string  $table
     * @param   array   $conditions
     * @return  $this
     */
    public function innerJoinOn($table, array $conditions)
    {
        return $this->joinOn($table, $conditions, 'inner');
    }

    /**
     * Defines a table FULL OUTER join to be performed for the query
     *
     * Note: FULL JOIN support is limited to a single FULL JOIN between the
     * base FROM table and one joined table. Additional JOINs are allowed
     * only if they are INNER or LEFT joins that reference tables introduced
     * earlier in the join order. More complex layouts will throw
     * UnsupportedSyntaxException to keep behavior consistent across drivers.
     *
     * @param   string  $table     The table join
     * @param   string  $leftKey   The left side of the join condition
     * @param   string  $rightKey  The right side of the join condition
     * @return  $this
     **/
    public function fullJoin($table, $leftKey, $rightKey = null)
    {
        if (is_callable($leftKey)) {
            return $this->join($table, $leftKey, 'full');
        }

        $this->syntax->setJoin($table, $leftKey, $rightKey, 'full');
        return $this;
    }

    /**
     * Defines a table FULL OUTER join with multiple ON predicates
     *
     * Note: FULL JOIN support requires a single equality predicate.
     *
     * @param   string  $table
     * @param   array   $conditions
     * @return  $this
     **/
    public function fullJoinOn($table, array $conditions)
    {
        return $this->joinOn($table, $conditions, 'full');
    }

    /**
     * Defines a table LEFT join to be performed for the query
     *
     * @param   string  $table     The table join
     * @param   string  $leftKey   The left side of the join condition
     * @param   string  $rightKey  The right side of the join condition
     * @return  $this
     **/
    public function leftJoin($table, $leftKey, $rightKey = null)
    {
        if (is_callable($leftKey)) {
            return $this->join($table, $leftKey, 'left');
        }

        $this->syntax->setJoin($table, $leftKey, $rightKey, 'left');
        return $this;
    }

    /**
     * Defines a table LEFT join with multiple ON predicates
     *
     * @param   string  $table
     * @param   array   $conditions
     * @return  $this
     **/
    public function leftJoinOn($table, array $conditions)
    {
        return $this->joinOn($table, $conditions, 'left');
    }

    /**
     * Defines a table RIGHT join to be performed for the query
     *
     * @param   string  $table     The table join
     * @param   string  $leftKey   The left side of the join condition
     * @param   string  $rightKey  The right side of the join condition
     * @return  $this
     **/
    public function rightJoin($table, $leftKey, $rightKey = null)
    {
        if (is_callable($leftKey)) {
            return $this->join($table, $leftKey, 'right');
        }

        $this->syntax->setJoin($table, $leftKey, $rightKey, 'right');
        return $this;
    }

    /**
     * Defines a table RIGHT join with multiple ON predicates
     *
     * @param   string  $table
     * @param   array   $conditions
     * @return  $this
     **/
    public function rightJoinOn($table, array $conditions)
    {
        return $this->joinOn($table, $conditions, 'right');
    }

    /**
     * Defines an INNER join with a subquery (derived table)
     *
     * Example:
     *   joinSub(function($query) {
     *       $query->select('user_id')
     *             ->select('MAX(created)', 'latest_post')
     *             ->from('posts')
     *             ->group('user_id');
     *   }, 'recent_posts', 'users.id', 'recent_posts.user_id')
     *   // Generates: INNER JOIN (SELECT user_id, MAX(created) AS latest_post
     *   //   FROM posts GROUP BY user_id) AS recent_posts
     *   //   ON users.id = recent_posts.user_id
     *
     * @param   callable  $callback   Closure that builds the subquery
     * @param   string    $as         Alias for the derived table
     * @param   string    $leftKey    The left side of the join condition
     * @param   string    $rightKey   The right side of the join condition
     * @return  $this
     **/
    public function joinSub(callable $callback, string $as, $leftKey, $rightKey)
    {
        return $this->subqueryJoin($callback, $as, $leftKey, $rightKey, 'inner');
    }

    /**
     * Defines a LEFT join with a subquery (derived table)
     *
     * @param   callable  $callback   Closure that builds the subquery
     * @param   string    $as         Alias for the derived table
     * @param   string    $leftKey    The left side of the join condition
     * @param   string    $rightKey   The right side of the join condition
     * @return  $this
     **/
    public function leftJoinSub(callable $callback, string $as, $leftKey, $rightKey)
    {
        return $this->subqueryJoin($callback, $as, $leftKey, $rightKey, 'left');
    }

    /**
     * Defines a RIGHT join with a subquery (derived table)
     *
     * @param   callable  $callback   Closure that builds the subquery
     * @param   string    $as         Alias for the derived table
     * @param   string    $leftKey    The left side of the join condition
     * @param   string    $rightKey   The right side of the join condition
     * @return  $this
     **/
    public function rightJoinSub(callable $callback, string $as, $leftKey, $rightKey)
    {
        return $this->subqueryJoin($callback, $as, $leftKey, $rightKey, 'right');
    }

    /**
     * Internal helper for subquery joins
     *
     * @param   callable  $callback   Closure that builds the subquery
     * @param   string    $as         Alias for the derived table
     * @param   string    $leftKey    The left side of the join condition
     * @param   string    $rightKey   The right side of the join condition
     * @param   string    $type       The join type (inner, left, right)
     * @return  $this
     **/
    protected function subqueryJoin(callable $callback, string $as, $leftKey, $rightKey, $type = 'inner')
    {
        list($sql, $bindings) = $this->buildSubquery($callback);

        $this->syntax->setSubqueryJoin($sql, $bindings, $as, $leftKey, $rightKey, $type);
        return $this;
    }

    /**
     * Applies a where clause to the pending query
     *
     * When inside a condition group (via beginAndGroup/beginOrGroup),
     * the depth is automatically managed. Explicit depth values take
     * precedence for backwards compatibility.
     *
     * @param   string  $column    The column to which the clause will apply
     * @param   string  $operator  The operation that will compare column to value
     * @param   string  $value     The value to which the column will be evaluated
     * @param   string  $logical   The operator between multiple clauses
     * @param   int     $depth     The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function where($column, $operator, $value, $logical = 'and', $depth = 0)
    {
        // Use effective depth (from group stack if no explicit depth)
        $effectiveDepth = $this->getEffectiveDepth($depth);

        // Use pending group logical for the first condition in a group
        $effectiveLogical = $this->consumePendingLogical($logical);

        $this->syntax->setWhere($column, $operator, $value, $effectiveLogical, $effectiveDepth);
        return $this;
    }

    /**
     * Applies a where clause to the pending query
     *
     * @param   string  $column    The column to which the clause will apply
     * @param   string  $operator  The operation that will compare column to value
     * @param   string  $value     The value to which the column will be evaluated
     * @param   int     $depth     The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhere($column, $operator, $value, $depth = 0)
    {
        $this->where($column, $operator, $value, 'or', $depth);
        return $this;
    }

    /**
     * Applies a simple where equals clause to the pending query
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   string  $value   The value to which the column will be evaluated
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereEquals($column, $value, $depth = 0)
    {
        $this->where($column, '=', $value, 'and', $depth);
        return $this;
    }

    /**
     * Applies a simple where equals clause to the pending query
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   string  $value   The value to which the column will be evaluated
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereEquals($column, $value, $depth = 0)
    {
        $this->where($column, '=', $value, 'or', $depth);
        return $this;
    }

    /**
     * Add a WHERE clause comparing two columns
     *
     * Compares two database columns directly without parameter binding,
     * which is necessary when comparing column values to each other.
     *
     * Example:
     *   // Find records where updated_at is after created_at
     *   $query->whereColumn('updated_at', '>', 'created_at');
     *
     *   // Find records where first_name equals last_name
     *   $query->whereColumn('first_name', '=', 'last_name');
     *
     *   // Shorthand for equals comparison
     *   $query->whereColumn('start_date', 'end_date');
     *
     * @param   string  $first     The first column name
     * @param   string  $operator  The comparison operator (=, !=, <, >, <=, >=) or second column if using = default
     * @param   string  $second    The second column name (optional if $operator is the second column)
     * @param   int     $depth     The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereColumn($first, $operator, $second = null, $depth = 0)
    {
        // If only two arguments, assume equals comparison
        if ($second === null) {
            $second = $operator;
            $operator = '=';
        }

        // Use whereRaw to compare columns directly without binding
        // whereRaw defaults to AND logic
        $this->whereRaw("{$first} {$operator} {$second}", [], $depth);
        return $this;
    }

    /**
     * Add a WHERE clause comparing two columns with OR logic
     *
     * Example:
     *   $query->whereColumn('col1', '=', 'col2')
     *         ->orWhereColumn('col3', '>', 'col4');
     *
     * @param   string  $first     The first column name
     * @param   string  $operator  The comparison operator or second column
     * @param   string  $second    The second column name (optional)
     * @param   int     $depth     The depth level of the clause
     * @return  $this
     **/
    public function orWhereColumn($first, $operator, $second = null, $depth = 0)
    {
        // If only two arguments, assume equals comparison
        if ($second === null) {
            $second = $operator;
            $operator = '=';
        }

        // Use orWhereRaw for OR logic
        $this->orWhereRaw("{$first} {$operator} {$second}", [], $depth);
        return $this;
    }

    /**
     * Applies a simple where in clause to the pending query
     *
     * Accepts either an array of values or a closure for subqueries.
     *
     * Example with array:
     *   whereIn('id', [1, 2, 3])
     *
     * Example with subquery:
     *   whereIn('user_id', function($query) {
     *       $query->select('id')->from('users')->whereEquals('active', 1);
     *   })
     *
     * @param   string          $column  The column to which the clause will apply
     * @param   array|callable  $values  The values or a closure that builds a subquery
     * @param   int             $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereIn($column, $values, $depth = 0)
    {
        if (is_callable($values)) {
            return $this->whereInSub($column, $values, 'and', false, $depth);
        }

        $this->where($column, 'IN', $values, 'and', $depth);
        return $this;
    }

    /**
     * Applies a simple where in clause to the pending query with OR logic
     *
     * Accepts either an array of values or a closure for subqueries.
     *
     * @param   string          $column  The column to which the clause will apply
     * @param   array|callable  $values  The values or a closure that builds a subquery
     * @param   int             $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereIn($column, $values, $depth = 0)
    {
        if (is_callable($values)) {
            return $this->whereInSub($column, $values, 'or', false, $depth);
        }

        $this->where($column, 'IN', $values, 'or', $depth);
        return $this;
    }

    /**
     * Applies a simple where not in clause to the pending query
     *
     * Accepts either an array of values or a closure for subqueries.
     *
     * Example with subquery:
     *   whereNotIn('user_id', function($query) {
     *       $query->select('id')->from('banned_users');
     *   })
     *
     * @param   string          $column  The column to which the clause will apply
     * @param   array|callable  $values  The values or a closure that builds a subquery
     * @param   int             $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereNotIn($column, $values, $depth = 0)
    {
        if (is_callable($values)) {
            return $this->whereInSub($column, $values, 'and', true, $depth);
        }

        $this->where($column, 'NOT IN', $values, 'and', $depth);
        return $this;
    }

    /**
     * Applies a simple where not in clause to the pending query with OR logic
     *
     * Accepts either an array of values or a closure for subqueries.
     *
     * @param   string          $column  The column to which the clause will apply
     * @param   array|callable  $values  The values or a closure that builds a subquery
     * @param   int             $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereNotIn($column, $values, $depth = 0)
    {
        if (is_callable($values)) {
            return $this->whereInSub($column, $values, 'or', true, $depth);
        }

        $this->where($column, 'NOT IN', $values, 'or', $depth);
        return $this;
    }

    /**
     * Internal helper for where in subquery clauses
     *
     * @param   string    $column    The column to which the clause will apply
     * @param   callable  $callback  Closure that builds the subquery
     * @param   string    $logical   The logical operator (and/or)
     * @param   bool      $not       Whether to use NOT IN
     * @param   int       $depth     The depth level of the clause
     * @return  $this
     **/
    protected function whereInSub($column, callable $callback, $logical = 'and', $not = false, $depth = 0)
    {
        list($sql, $bindings) = $this->buildSubquery($callback);

        $operator = $not ? 'NOT IN' : 'IN';
        $raw = $column . ' ' . $operator . ' (' . $sql . ')';

        $this->syntax->setRawWhere($raw, $bindings, $logical, $depth);
        return $this;
    }

    /**
     * Add a WHERE BETWEEN clause to the query
     *
     * Filters results where the column value falls between two values (inclusive).
     *
     * Example:
     *   whereBetween('price', [10, 100])
     *   // Generates: WHERE price BETWEEN 10 AND 100
     *
     *   whereBetween('created_at', ['2024-01-01', '2024-12-31'])
     *   // Generates: WHERE created_at BETWEEN '2024-01-01' AND '2024-12-31'
     *
     * @param   string  $column  The column to filter
     * @param   array   $values  Array with exactly 2 values [min, max]
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereBetween($column, array $values, $depth = 0)
    {
        return $this->addWhereBetween($column, $values, 'and', false, $depth);
    }

    /**
     * Add a WHERE BETWEEN clause with OR logic
     *
     * @param   string  $column  The column to filter
     * @param   array   $values  Array with exactly 2 values [min, max]
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereBetween($column, array $values, $depth = 0)
    {
        return $this->addWhereBetween($column, $values, 'or', false, $depth);
    }

    /**
     * Add a WHERE NOT BETWEEN clause to the query
     *
     * Filters results where the column value falls outside two values.
     *
     * Example:
     *   whereNotBetween('price', [10, 100])
     *   // Generates: WHERE price NOT BETWEEN 10 AND 100
     *
     * @param   string  $column  The column to filter
     * @param   array   $values  Array with exactly 2 values [min, max]
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereNotBetween($column, array $values, $depth = 0)
    {
        return $this->addWhereBetween($column, $values, 'and', true, $depth);
    }

    /**
     * Add a WHERE NOT BETWEEN clause with OR logic
     *
     * @param   string  $column  The column to filter
     * @param   array   $values  Array with exactly 2 values [min, max]
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereNotBetween($column, array $values, $depth = 0)
    {
        return $this->addWhereBetween($column, $values, 'or', true, $depth);
    }

    /**
     * Internal helper for BETWEEN clauses
     *
     * @param   string  $column   The column to filter
     * @param   array   $values   Array with exactly 2 values [min, max]
     * @param   string  $logical  The logical operator (and/or)
     * @param   bool    $not      Whether to use NOT BETWEEN
     * @param   int     $depth    The depth level of the clause
     * @return  $this
     **/
    protected function addWhereBetween($column, array $values, $logical = 'and', $not = false, $depth = 0)
    {
        if (count($values) !== 2) {
            throw new \InvalidArgumentException('whereBetween requires exactly 2 values [min, max]');
        }

        $values = array_values($values); // Ensure numeric keys
        $operator = $not ? 'NOT BETWEEN' : 'BETWEEN';
        $raw = $column . ' ' . $operator . ' ? AND ?';

        $effectiveDepth = $this->getEffectiveDepth($depth);
        $effectiveLogical = $this->consumePendingLogical($logical);

        $this->syntax->setRawWhere($raw, $values, $effectiveLogical, $effectiveDepth);
        return $this;
    }

    /**
     * Add a WHERE column BETWEEN two columns clause
     *
     * Filters results where a column value falls between two other column values.
     * This compares columns directly without parameter binding.
     *
     * Example:
     *   whereBetweenColumns('price', ['min_price', 'max_price'])
     *   // Generates: WHERE price BETWEEN min_price AND max_price
     *
     *   whereBetweenColumns('order_date', ['project_start', 'project_end'])
     *   // Generates: WHERE order_date BETWEEN project_start AND project_end
     *
     * @param   string  $column   The column to filter
     * @param   array   $columns  Array with exactly 2 column names [min_column, max_column]
     * @param   int     $depth    The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereBetweenColumns($column, array $columns, $depth = 0)
    {
        return $this->addWhereBetweenColumns($column, $columns, 'and', false, $depth);
    }

    /**
     * Add a WHERE column BETWEEN two columns clause with OR logic
     *
     * @param   string  $column   The column to filter
     * @param   array   $columns  Array with exactly 2 column names [min_column, max_column]
     * @param   int     $depth    The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereBetweenColumns($column, array $columns, $depth = 0)
    {
        return $this->addWhereBetweenColumns($column, $columns, 'or', false, $depth);
    }

    /**
     * Add a WHERE column NOT BETWEEN two columns clause
     *
     * Filters results where a column value falls outside two other column values.
     *
     * Example:
     *   whereNotBetweenColumns('price', ['min_price', 'max_price'])
     *   // Generates: WHERE price NOT BETWEEN min_price AND max_price
     *
     * @param   string  $column   The column to filter
     * @param   array   $columns  Array with exactly 2 column names [min_column, max_column]
     * @param   int     $depth    The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereNotBetweenColumns($column, array $columns, $depth = 0)
    {
        return $this->addWhereBetweenColumns($column, $columns, 'and', true, $depth);
    }

    /**
     * Add a WHERE column NOT BETWEEN two columns clause with OR logic
     *
     * @param   string  $column   The column to filter
     * @param   array   $columns  Array with exactly 2 column names [min_column, max_column]
     * @param   int     $depth    The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereNotBetweenColumns($column, array $columns, $depth = 0)
    {
        return $this->addWhereBetweenColumns($column, $columns, 'or', true, $depth);
    }

    /**
     * Internal helper for BETWEEN columns clauses
     *
     * @param   string  $column   The column to filter
     * @param   array   $columns  Array with exactly 2 column names
     * @param   string  $logical  The logical operator (and/or)
     * @param   bool    $not      Whether to use NOT BETWEEN
     * @param   int     $depth    The depth level of the clause
     * @return  $this
     **/
    protected function addWhereBetweenColumns($column, array $columns, $logical = 'and', $not = false, $depth = 0)
    {
        if (count($columns) !== 2) {
            throw new \InvalidArgumentException(
                'whereBetweenColumns requires exactly 2 column names'
                . ' [min_column, max_column]'
            );
        }

        $columns = array_values($columns); // Ensure numeric keys
        $operator = $not ? 'NOT BETWEEN' : 'BETWEEN';
        $raw = $column . ' ' . $operator . ' ' . $columns[0] . ' AND ' . $columns[1];

        $effectiveDepth = $this->getEffectiveDepth($depth);
        $effectiveLogical = $this->consumePendingLogical($logical);

        // No bindings needed - columns are compared directly
        $this->syntax->setRawWhere($raw, [], $effectiveLogical, $effectiveDepth);
        return $this;
    }

    /**
     * Applies a WHERE EXISTS clause to the pending query
     *
     * EXISTS checks if the subquery returns any rows.
     *
     * Example:
     *   whereExists(function($query) {
     *       $query->select('1')
     *             ->from('comments')
     *             ->whereRaw('comments.post_id = posts.id');
     *   })
     *   // Generates: WHERE EXISTS (SELECT 1 FROM comments WHERE comments.post_id = posts.id)
     *
     * @param   callable  $callback  Closure that builds the subquery
     * @param   int       $depth     The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereExists(callable $callback, $depth = 0)
    {
        return $this->addWhereExists($callback, 'and', false, $depth);
    }

    /**
     * Applies a WHERE EXISTS clause with OR logic
     *
     * @param   callable  $callback  Closure that builds the subquery
     * @param   int       $depth     The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereExists(callable $callback, $depth = 0)
    {
        return $this->addWhereExists($callback, 'or', false, $depth);
    }

    /**
     * Applies a WHERE NOT EXISTS clause to the pending query
     *
     * NOT EXISTS checks if the subquery returns no rows.
     *
     * Example:
     *   whereNotExists(function($query) {
     *       $query->select('1')
     *             ->from('spam_reports')
     *             ->whereRaw('spam_reports.user_id = users.id');
     *   })
     *   // Generates: WHERE NOT EXISTS (SELECT 1 FROM spam_reports WHERE spam_reports.user_id = users.id)
     *
     * @param   callable  $callback  Closure that builds the subquery
     * @param   int       $depth     The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereNotExists(callable $callback, $depth = 0)
    {
        return $this->addWhereExists($callback, 'and', true, $depth);
    }

    /**
     * Applies a WHERE NOT EXISTS clause with OR logic
     *
     * @param   callable  $callback  Closure that builds the subquery
     * @param   int       $depth     The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereNotExists(callable $callback, $depth = 0)
    {
        return $this->addWhereExists($callback, 'or', true, $depth);
    }

    /**
     * Internal helper for where exists subquery clauses
     *
     * @param   callable  $callback  Closure that builds the subquery
     * @param   string    $logical   The logical operator (and/or)
     * @param   bool      $not       Whether to use NOT EXISTS
     * @param   int       $depth     The depth level of the clause
     * @return  $this
     **/
    protected function addWhereExists(callable $callback, $logical = 'and', $not = false, $depth = 0)
    {
        list($sql, $bindings) = $this->buildSubquery($callback);

        $operator = $not ? 'NOT EXISTS' : 'EXISTS';
        $raw = $operator . ' (' . $sql . ')';

        $this->syntax->setRawWhere($raw, $bindings, $logical, $depth);
        return $this;
    }

    /**
     * Applies a simple where like clause to the pending query
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   string  $value   The value to which the column will be evaluated
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereLike($column, $value, $depth = 0)
    {
        $this->where($column, 'LIKE', "%{$value}%", 'and', $depth);
        return $this;
    }

    /**
     * Applies a simple where like clause to the pending query
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   string  $value   The value to which the column will be evaluated
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereLike($column, $value, $depth = 0)
    {
        $this->where($column, 'LIKE', "%{$value}%", 'or', $depth);
        return $this;
    }

    /**
     * Applies a NOT LIKE clause to the pending query
     *
     * Filters results where the column does NOT contain the given value.
     * The value is automatically wrapped with % wildcards for substring matching.
     *
     * Example:
     *   whereNotLike('email', 'spam')
     *   // Generates: WHERE email NOT LIKE '%spam%'
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   string  $value   The value to search for (will be wrapped with %)
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereNotLike($column, $value, $depth = 0)
    {
        $this->where($column, 'NOT LIKE', "%{$value}%", 'and', $depth);
        return $this;
    }

    /**
     * Applies a NOT LIKE clause with OR logic to the pending query
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   string  $value   The value to search for (will be wrapped with %)
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereNotLike($column, $value, $depth = 0)
    {
        $this->where($column, 'NOT LIKE', "%{$value}%", 'or', $depth);
        return $this;
    }

    /**
     * Add a WHERE NOT clause that wraps conditions in negation
     *
     * Wraps a group of conditions in NOT (...) to negate the entire group.
     * The callback receives a query builder to define the conditions to negate.
     *
     * Example:
     *   whereNot(function($query) {
     *       $query->whereEquals('status', 'archived')
     *             ->whereEquals('type', 'draft');
     *   })
     *   // Generates: WHERE NOT (status = 'archived' AND type = 'draft')
     *
     *   whereNot(function($query) {
     *       $query->whereEquals('role', 'guest')
     *             ->orWhereEquals('role', 'banned');
     *   })
     *   // Generates: WHERE NOT (role = 'guest' OR role = 'banned')
     *
     * @param   callable  $callback  Closure that builds the conditions to negate
     * @param   int       $depth     The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereNot(callable $callback, $depth = 0)
    {
        return $this->addWhereNot($callback, 'and', $depth);
    }

    /**
     * Add a WHERE NOT clause with OR logic
     *
     * Example:
     *   whereEquals('active', 1)
     *   ->orWhereNot(function($query) {
     *       $query->whereEquals('role', 'guest');
     *   })
     *   // Generates: WHERE active = 1 OR NOT (role = 'guest')
     *
     * @param   callable  $callback  Closure that builds the conditions to negate
     * @param   int       $depth     The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereNot(callable $callback, $depth = 0)
    {
        return $this->addWhereNot($callback, 'or', $depth);
    }

    /**
     * Internal helper for WHERE NOT clauses
     *
     * @param   callable  $callback  Closure that builds the conditions to negate
     * @param   string    $logical   The logical operator (and/or)
     * @param   int       $depth     The depth level of the clause
     * @return  $this
     **/
    protected function addWhereNot(callable $callback, $logical = 'and', $depth = 0)
    {
        // Build the subquery to get its SQL
        list($sql, $bindings) = $this->buildSubquery($callback);

        // Wrap in NOT (...)
        // Remove the outer SELECT wrapper and any leading WHERE if present
        // The subquery builder returns "SELECT * FROM table WHERE conditions"
        // We need just the conditions part
        if (preg_match('/WHERE\s+(.+)$/is', $sql, $matches)) {
            $conditions = $matches[1];
        } else {
            // If no WHERE clause found, use the whole SQL as conditions
            $conditions = $sql;
        }

        $raw = 'NOT (' . $conditions . ')';

        $effectiveDepth = $this->getEffectiveDepth($depth);
        $effectiveLogical = $this->consumePendingLogical($logical);

        $this->syntax->setRawWhere($raw, $bindings, $effectiveLogical, $effectiveDepth);
        return $this;
    }

    /**
     * Add a full-text search WHERE clause (MySQL/MariaDB)
     *
     * Uses MySQL's MATCH...AGAINST syntax for full-text searching.
     * Requires a FULLTEXT index on the specified columns.
     *
     * Example:
     *   whereFullText('content', 'search terms')
     *   // Generates: WHERE MATCH(content) AGAINST('search terms')
     *
     *   whereFullText(['title', 'body'], 'search terms')
     *   // Generates: WHERE MATCH(title, body) AGAINST('search terms')
     *
     *   whereFullText('content', 'search terms', ['mode' => 'boolean'])
     *   // Generates: WHERE MATCH(content) AGAINST('search terms' IN BOOLEAN MODE)
     *
     * Options:
     *   - mode: 'natural' (default), 'boolean', 'expansion' (query expansion)
     *   - strategy: 'auto' (default), 'portable', 'native'
     *       - portable: force LIKE fallback semantics
     *       - native: use driver-native fulltext where supported
     *       - auto: backend default strategy
     *
     * @param   string|array  $columns  Column name(s) to search
     * @param   string        $value    The search term(s)
     * @param   array         $options  Search options (mode, etc.)
     * @param   int           $depth    The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereFullText($columns, $value, array $options = [], $depth = 0)
    {
        return $this->addWhereFullText($columns, $value, $options, 'and', $depth);
    }

    /**
     * Add a full-text search WHERE clause with OR logic
     *
     * @param   string|array  $columns  Column name(s) to search
     * @param   string        $value    The search term(s)
     * @param   array         $options  Search options (mode, etc.)
     * @param   int           $depth    The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereFullText($columns, $value, array $options = [], $depth = 0)
    {
        return $this->addWhereFullText($columns, $value, $options, 'or', $depth);
    }

    /**
     * Internal helper for full-text search clauses
     *
     * @param   string|array  $columns  Column name(s) to search
     * @param   string        $value    The search term(s)
     * @param   array         $options  Search options
     * @param   string        $logical  The logical operator (and/or)
     * @param   int           $depth    The depth level of the clause
     * @return  $this
     **/
    protected function addWhereFullText($columns, $value, array $options, $logical = 'and', $depth = 0)
    {
        // Normalize columns to array
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        // Check if driver supports native fulltext search (MySQL/MariaDB/Informix BTS)
        $driverName = strtolower($this->connection->getName() ?? '');
        $isInformix = $driverName === 'informix'
            || get_class($this->connection) === \Hubzero\Database\Driver\Informix::class;
        $supportsFulltext = in_array($driverName, ['mysql', 'mariadb']);

        if ($supportsFulltext) {
            // Use native MATCH...AGAINST syntax
            $columnList = implode(', ', $columns);

            // Determine search mode
            $mode = $options['mode'] ?? 'natural';
            $modeClause = '';

            switch (strtolower($mode)) {
                case 'boolean':
                    $modeClause = ' IN BOOLEAN MODE';
                    break;
                case 'expansion':
                case 'query_expansion':
                    $modeClause = ' WITH QUERY EXPANSION';
                    break;
                case 'natural':
                default:
                    // Natural language mode is the default, no clause needed
                    $modeClause = '';
                    break;
            }

            $raw = 'MATCH(' . $columnList . ') AGAINST(?' . $modeClause . ')';
            $bindings = [$value];
        } elseif ($isInformix) {
            // Informix fulltext modes:
            // - like   (default): portable LIKE fallback for all columns
            // - hybrid: single column uses BTS; multi-column uses LIKE fallback
            // - bts   : force BTS for first column, LIKE for remaining columns
            $informixMode = $this->resolveInformixFulltextMode($options);
            $useBtsSingle = in_array($informixMode, ['hybrid', 'bts'], true);

            if ($useBtsSingle && count($columns) === 1) {
                $quoted = $this->connection->quoteName($columns[0]);
                $raw = "bts_contains({$quoted}, ?)";
                $bindings = [$value];
            } elseif ($informixMode === 'bts' && count($columns) > 1) {
                // Multi-column BTS OR predicates are fragile in this runtime.
                // Use BTS on the first column and LIKE fallback on the rest.
                $clauses = [];
                $bindings = [];

                $firstQuoted = $this->connection->quoteName($columns[0]);
                $clauses[] = "bts_contains({$firstQuoted}, ?)";
                $bindings[] = $value;

                for ($i = 1; $i < count($columns); $i++) {
                    $clauses[] = $columns[$i] . ' LIKE ?';
                    $bindings[] = '%' . $value . '%';
                }

                $raw = '(' . implode(' OR ', $clauses) . ')';
            } else {
                $likeClauses = [];
                $bindings = [];
                foreach ($columns as $column) {
                    $likeClauses[] = $column . ' LIKE ?';
                    $bindings[] = '%' . $value . '%';
                }
                $raw = count($likeClauses) > 1
                    ? '(' . implode(' OR ', $likeClauses) . ')'
                    : $likeClauses[0];
            }
        } else {
            // Fallback for databases without fulltext support (e.g., SQLite)
            // Use LIKE clauses with OR logic between columns
            $likeClauses = [];
            $bindings = [];

            foreach ($columns as $column) {
                $likeClauses[] = $column . ' LIKE ?';
                $bindings[] = '%' . $value . '%';
            }

            // Wrap multiple LIKE clauses in parentheses if more than one column
            if (count($likeClauses) > 1) {
                $raw = '(' . implode(' OR ', $likeClauses) . ')';
            } else {
                $raw = $likeClauses[0];
            }
        }

        $effectiveDepth = $this->getEffectiveDepth($depth);
        $effectiveLogical = $this->consumePendingLogical($logical);

        $this->syntax->setRawWhere($raw, $bindings, $effectiveLogical, $effectiveDepth);
        return $this;
    }

    /**
     * Resolve Informix fulltext execution mode from generic and legacy options.
     *
     * Generic option:
     *   strategy: auto|portable|native
     *
     * Legacy option:
     *   informix_mode: like|hybrid|bts
     *
     * @param   array  $options
     * @return  string
     */
    protected function resolveInformixFulltextMode(array $options): string
    {
        if (isset($options['strategy'])) {
            switch (strtolower((string) $options['strategy'])) {
                case 'portable':
                    return 'like';
                case 'native':
                    return 'bts';
                case 'auto':
                default:
                    return 'hybrid';
            }
        }

        if (isset($options['fulltext_strategy'])) {
            switch (strtolower((string) $options['fulltext_strategy'])) {
                case 'portable':
                    return 'like';
                case 'native':
                    return 'bts';
                case 'auto':
                default:
                    return 'hybrid';
            }
        }

        // Backward-compatibility shim.
        if (isset($options['informix_mode'])) {
            return strtolower((string) $options['informix_mode']);
        }

        return 'like';
    }

    /**
     * Applies an AND where is null clause to the pending query
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereIsNull($column, $depth = 0)
    {
        $this->where($column, 'IS', null, 'and', $depth);
        return $this;
    }

    /**
     * Applies a OR where is null clause to the pending query
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereIsNull($column, $depth = 0)
    {
        $this->where($column, 'IS', null, 'or', $depth);
        return $this;
    }

    /**
     * Applies an AND where is not null clause to the pending query
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereIsNotNull($column, $depth = 0)
    {
        $this->where($column, 'IS NOT', null, 'and', $depth);
        return $this;
    }

    /**
     * Applies a OR where is not null clause to the pending query
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereIsNotNull($column, $depth = 0)
    {
        $this->where($column, 'IS NOT', null, 'or', $depth);
        return $this;
    }

    /**
     * Applies a raw where clause to the pending query
     *
     * Supports both positional (?) and named (:name) placeholders.
     * Named placeholders are automatically detected by checking if bindings
     * array has string keys, and converted to positional internally.
     *
     * Named parameters can also be set via setParameter()/setParameters():
     *   $query->setParameter('status', 1)
     *         ->whereRaw('status = :status')
     *
     * Example with positional placeholders:
     *   whereRaw('status = ? AND type = ?', [1, 'article'])
     *
     * Example with named placeholders (inline):
     *   whereRaw('status = :status AND type = :type', ['status' => 1, 'type' => 'article'])
     *
     * Example with named placeholders (stored):
     *   setParameter('user', 42)->whereRaw('created_by = :user OR modified_by = :user')
     *
     * @param   string  $string    The raw where clause
     * @param   array   $bindings  Bindings array (positional or named)
     * @param   int     $depth     The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereRaw($string, $bindings = [], $depth = 0)
    {
        // Process named placeholders if:
        // 1. Bindings are associative (named inline), OR
        // 2. No bindings passed but SQL has :placeholders and we have stored parameters
        if ($this->shouldProcessNamedPlaceholders($string, $bindings)) {
            list($string, $bindings) = $this->convertNamedToPositional($string, $bindings);
        }

        // Use effective depth (from group stack if no explicit depth)
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $effectiveLogical = $this->consumePendingLogical('and');

        $this->syntax->setRawWhere($string, $bindings, $effectiveLogical, $effectiveDepth);
        return $this;
    }

    /**
     * Applies a raw where clause to the pending query with OR logic
     *
     * Supports both positional (?) and named (:name) placeholders.
     * See whereRaw() for examples.
     *
     * @param   string  $string    The raw where clause
     * @param   array   $bindings  Bindings array (positional or named)
     * @param   int     $depth     The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereRaw($string, $bindings = [], $depth = 0)
    {
        // Process named placeholders if needed
        if ($this->shouldProcessNamedPlaceholders($string, $bindings)) {
            list($string, $bindings) = $this->convertNamedToPositional($string, $bindings);
        }

        // Use effective depth (from group stack if no explicit depth)
        $effectiveDepth = $this->getEffectiveDepth($depth);
        // Note: OR logical is always 'or', no pending consumption
        // (if inside a group, the group opener consumed the pending)

        $this->syntax->setRawWhere($string, $bindings, 'or', $effectiveDepth);
        return $this;
    }

    /**
     * Applies a WHERE clause that extracts a value from a JSON column at a given path
     *
     * The path should use dot notation for nested keys (e.g., "user.name" or "items.0.price").
     * Array indices can be used for JSON arrays.
     *
     * Database-specific implementations:
     * - MySQL: Uses JSON_EXTRACT() with JSON path syntax
     * - PostgreSQL: Uses the #>> operator for text extraction
     * - SQLite: Uses json_extract() function
     * - SQL Server: Uses JSON_VALUE() function
     *
     * Example:
     *   whereJsonPath('settings', 'theme.color', '=', 'blue')
     *   // Checks if settings->"$.theme.color" equals 'blue'
     *
     * @param   string  $column    The JSON column name
     * @param   string  $path      The dot-notation path to the value (e.g., "user.name")
     * @param   string  $operator  Comparison operator (=, !=, <, >, <=, >=, LIKE, etc.)
     * @param   mixed   $value     The value to compare against
     * @param   int     $depth     The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereJsonPath($column, $path, $operator, $value, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $effectiveLogical = $this->consumePendingLogical('and');
        $this->syntax->setJsonPathWhere($column, $path, $operator, $value, $effectiveLogical, $effectiveDepth);
        return $this;
    }

    /**
     * Applies a WHERE clause with OR logic that extracts a value from a JSON column
     *
     * @param   string  $column    The JSON column name
     * @param   string  $path      The dot-notation path to the value
     * @param   string  $operator  Comparison operator
     * @param   mixed   $value     The value to compare against
     * @param   int     $depth     The depth level of the clause
     * @return  $this
     **/
    public function orWhereJsonPath($column, $path, $operator, $value, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $this->syntax->setJsonPathWhere($column, $path, $operator, $value, 'or', $effectiveDepth);
        return $this;
    }

    /**
     * Applies a WHERE clause that checks if a JSON array contains a value
     *
     * This is useful for checking membership in JSON arrays stored in a column.
     * Optionally specify a path to check a nested array.
     *
     * Database-specific implementations:
     * - MySQL: Uses JSON_CONTAINS() function
     * - PostgreSQL: Uses the @> operator (containment)
     * - SQLite: Uses json_each() with EXISTS subquery
     * - SQL Server: Uses OPENJSON() with EXISTS subquery
     *
     * Example:
     *   whereJsonContains('tags', 'php')
     *   // Checks if tags array contains "php"
     *
     *   whereJsonContains('data', 'admin', 'roles')
     *   // Checks if data->"$.roles" array contains "admin"
     *
     * @param   string       $column  The JSON column name
     * @param   mixed        $value   The value to search for in the array
     * @param   string|null  $path    Optional dot-notation path to a nested array
     * @param   int          $depth   The depth level of the clause
     * @return  $this
     **/
    public function whereJsonContains($column, $value, $path = null, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $effectiveLogical = $this->consumePendingLogical('and');
        $this->syntax->setJsonContainsWhere($column, $value, $path, $effectiveLogical, $effectiveDepth);
        return $this;
    }

    /**
     * Applies a WHERE clause with OR logic that checks if a JSON array contains a value
     *
     * @param   string       $column  The JSON column name
     * @param   mixed        $value   The value to search for
     * @param   string|null  $path    Optional dot-notation path to a nested array
     * @param   int          $depth   The depth level of the clause
     * @return  $this
     **/
    public function orWhereJsonContains($column, $value, $path = null, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $this->syntax->setJsonContainsWhere($column, $value, $path, 'or', $effectiveDepth);
        return $this;
    }

    /**
     * Applies a WHERE clause that checks the length of a JSON array
     *
     * This is useful for filtering by the number of elements in a JSON array.
     * Optionally specify a path to check a nested array's length.
     *
     * Database-specific implementations:
     * - MySQL: Uses JSON_LENGTH() function
     * - PostgreSQL: Uses jsonb_array_length() function
     * - SQLite: Uses json_array_length() function
     * - SQL Server: Uses COUNT from OPENJSON()
     *
     * Example:
     *   whereJsonLength('tags', '>=', 3)
     *   // Checks if tags array has 3 or more elements
     *
     *   whereJsonLength('data', '=', 0, 'items')
     *   // Checks if data->"$.items" array is empty
     *
     * @param   string       $column    The JSON column name
     * @param   string       $operator  Comparison operator (=, !=, <, >, <=, >=)
     * @param   int          $value     The length value to compare against
     * @param   string|null  $path      Optional dot-notation path to a nested array
     * @param   int          $depth     The depth level of the clause
     * @return  $this
     **/
    public function whereJsonLength($column, $operator, $value, $path = null, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $effectiveLogical = $this->consumePendingLogical('and');
        $this->syntax->setJsonLengthWhere($column, $operator, $value, $path, $effectiveLogical, $effectiveDepth);
        return $this;
    }

    /**
     * Applies a WHERE clause with OR logic that checks the length of a JSON array
     *
     * @param   string       $column    The JSON column name
     * @param   string       $operator  Comparison operator
     * @param   int          $value     The length value to compare against
     * @param   string|null  $path      Optional dot-notation path to a nested array
     * @param   int          $depth     The depth level of the clause
     * @return  $this
     **/
    public function orWhereJsonLength($column, $operator, $value, $path = null, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $this->syntax->setJsonLengthWhere($column, $operator, $value, $path, 'or', $effectiveDepth);
        return $this;
    }

    /**
     * Resets the depth of a nested statement back down to a given level
     *
     * @param   int  $depth  The depth to set to
     * @return  $this
     **/
    public function resetDepth($depth = 0)
    {
        $this->syntax->resetDepth($depth);
        return $this;
    }

    // =========================================================================
    // Condition Groups (AND/OR Nesting)
    // =========================================================================
    //
    // These methods provide a fluent API for creating nested condition groups
    // without needing closures or manual depth tracking.
    //
    // Example: WHERE status = 1 AND (featured = 1 OR sticky = 1)
    //
    //   $query->whereEquals('status', 1)
    //         ->beginAndGroup()
    //             ->whereEquals('featured', 1)
    //             ->orWhereEquals('sticky', 1)
    //         ->endGroup();
    //
    // Example: WHERE published = 1 OR (author_id = 5 AND created > '2024-01-01')
    //
    //   $query->whereEquals('published', 1)
    //         ->beginOrGroup()
    //             ->whereEquals('author_id', 5)
    //             ->where('created', '>', '2024-01-01')
    //         ->endGroup();
    //
    // Groups can be nested:
    //
    //   $query->whereEquals('status', 1)
    //         ->beginAndGroup()
    //             ->whereEquals('type', 'post')
    //             ->beginOrGroup()
    //                 ->whereEquals('featured', 1)
    //                 ->whereEquals('sticky', 1)
    //             ->endGroup()
    //         ->endGroup();
    //
    // =========================================================================

    /**
     * Begin a condition group connected with AND
     *
     * All conditions added after this call (until endGroup()) will be
     * wrapped in parentheses. The group as a whole is connected to
     * previous conditions with AND.
     *
     * Example:
     *   $query->whereEquals('status', 1)
     *         ->beginAndGroup()
     *             ->whereEquals('featured', 1)
     *             ->orWhereEquals('sticky', 1)
     *         ->endGroup();
     *   // Generates: WHERE status = 1 AND (featured = 1 OR sticky = 1)
     *
     * @return  $this
     **/
    public function beginAndGroup()
    {
        return $this->beginGroup('and');
    }

    /**
     * Begin a condition group connected with OR
     *
     * All conditions added after this call (until endGroup()) will be
     * wrapped in parentheses. The group as a whole is connected to
     * previous conditions with OR.
     *
     * Example:
     *   $query->whereEquals('published', 1)
     *         ->beginOrGroup()
     *             ->whereEquals('author_id', 5)
     *             ->where('created', '>', '2024-01-01')
     *         ->endGroup();
     *   // Generates: WHERE published = 1 OR (author_id = 5 AND created > '2024-01-01')
     *
     * @return  $this
     **/
    public function beginOrGroup()
    {
        return $this->beginGroup('or');
    }

    /**
     * Begin a condition group with the specified logical operator
     *
     * @param   string  $logical  The logical operator connecting this group ('and' or 'or')
     * @return  $this
     **/
    public function beginGroup($logical = 'and')
    {
        $this->groupDepth++;
        $this->pendingGroupLogical = $logical;
        return $this;
    }

    /**
     * End the current condition group
     *
     * Closes the parentheses opened by beginAndGroup() or beginOrGroup().
     * Must be called once for each begin*Group() call.
     *
     * @return  $this
     * @throws  \LogicException  If called without a matching begin*Group()
     **/
    public function endGroup()
    {
        if ($this->groupDepth <= 0) {
            throw new \LogicException('endGroup() called without matching beginGroup()');
        }

        $this->groupDepth--;
        $this->syntax->resetDepth($this->groupDepth);
        return $this;
    }

    /**
     * Get the current group depth
     *
     * Useful for debugging or for advanced manual depth control.
     *
     * @return  int
     **/
    public function getGroupDepth()
    {
        return $this->groupDepth;
    }

    /**
     * Determine the effective depth for a where clause
     *
     * If a depth is explicitly provided (non-zero), use it.
     * Otherwise, use the current group depth.
     *
     * @param   int  $depth  The explicitly provided depth
     * @return  int
     **/
    protected function getEffectiveDepth($depth)
    {
        return ($depth !== 0) ? $depth : $this->groupDepth;
    }

    /**
     * Consume the pending group logical operator
     *
     * When a group is opened, the first condition uses the group's
     * logical operator. Subsequent conditions use their own logical.
     *
     * @param   string  $default  The default logical operator
     * @return  string
     **/
    protected function consumePendingLogical($default)
    {
        if ($this->pendingGroupLogical !== null) {
            $logical = $this->pendingGroupLogical;
            $this->pendingGroupLogical = null;
            return $logical;
        }
        return $default;
    }

    // =========================================================================
    // Named Parameter Binding
    // =========================================================================
    //
    // Fluent API for binding named parameters (:name placeholders).
    // Parameters set here are automatically merged with inline bindings
    // when using whereRaw(), selectRaw(), havingRaw(), etc.
    //
    // Example:
    //   $query->whereRaw('status = :status AND type = :type')
    //         ->setParameter('status', 1)
    //         ->setParameter('type', 'article')
    //         ->fetch();
    //
    // =========================================================================

    /**
     * Set a named parameter value
     *
     * Binds a value to a named placeholder (:name) for use in raw query methods.
     * Named parameters can be reused multiple times in the same query.
     *
     * Example:
     * ```php
     * $query->whereRaw('created_by = :user OR modified_by = :user')
     *       ->setParameter('user', $userId)
     *       ->fetch();
     * ```
     *
     * @param   string  $name   Parameter name (with or without leading colon)
     * @param   mixed   $value  The value to bind
     * @param   string  $type   Optional type hint (not used, for interface compatibility)
     * @return  $this
     **/
    public function setParameter(string $name, $value, ?string $type = null)
    {
        // Remove leading colon if present for consistency
        $name = ltrim($name, ':');
        $this->namedParameters[$name] = $value;
        return $this;
    }

    /**
     * Set multiple named parameters at once
     *
     * Example:
     * ```php
     * $query->whereRaw('status = :status AND category = :category')
     *       ->setParameters([
     *           'status' => 'published',
     *           'category' => 'news'
     *       ])
     *       ->fetch();
     * ```
     *
     * @param   array  $parameters  Associative array of name => value pairs
     * @return  $this
     **/
    public function setParameters(array $parameters)
    {
        foreach ($parameters as $name => $value) {
            $this->setParameter($name, $value);
        }
        return $this;
    }

    /**
     * Get all currently set named parameters
     *
     * @return  array  Associative array of name => value pairs
     **/
    public function getNamedParameters(): array
    {
        return $this->namedParameters;
    }

    /**
     * Check if a named parameter has been set
     *
     * @param   string  $name  Parameter name (with or without leading colon)
     * @return  bool
     **/
    public function hasParameter(string $name): bool
    {
        $name = ltrim($name, ':');
        return array_key_exists($name, $this->namedParameters);
    }

    /**
     * Clear all named parameters
     *
     * @return  $this
     **/
    public function clearParameters()
    {
        $this->namedParameters = [];
        return $this;
    }

    /**
     * Applies 'order by' clause
     *
     * @param   string  $column  The column to which the order by will apply
     * @param   string  $dir     The direction in which the results will be ordered
     * @return  $this
     **/
    public function order($column, $dir)
    {
        $this->syntax->setOrder($column, $dir);
        return $this;
    }

    /**
     * Removes 'order by' clause
     *
     * @return  $this
     **/
    public function unorder()
    {
        $this->syntax->resetOrder();
        return $this;
    }

    /**
     * Sets query offset to start at a certain position
     *
     * @param   int    $start  Position to start from
     * @return  $this
     **/
    public function start($start)
    {
        $this->syntax->setStart((int)$start);
        return $this;
    }

    /**
     * Limits query results returned to a certain number
     *
     * @param   int    $limit  Number of results to return on next query
     * @return  $this
     **/
    public function limit($limit)
    {
        $this->syntax->setLimit((int)$limit);
        return $this;
    }

    // =========================================================================
    // Union Query Methods
    // =========================================================================

    /**
     * Adds a UNION clause to the query
     *
     * UNION combines result sets from multiple SELECT statements and removes
     * duplicate rows. Use unionAll() if you want to include duplicates.
     *
     * You can pass either a Query object or a closure that receives a Query:
     *
     * ```php
     * // Using a Query object
     * $activeUsers = User::all()->whereEquals('active', 1);
     * $recentUsers = User::all()
     *     ->where('created', '>', Date::of('-30 days')->toSql())
     *     ->union($activeUsers);
     *
     * // Using a closure
     * $users = User::all()
     *     ->whereEquals('type', 'admin')
     *     ->union(function($query) {
     *         $query->from('users')
     *               ->select('*')
     *               ->whereEquals('type', 'moderator');
     *     });
     * ```
     *
     * @param   Query|callable  $query  The query to union with, or a closure
     * @return  $this
     **/
    public function union($query)
    {
        return $this->addUnion($query, false);
    }

    /**
     * Adds a UNION ALL clause to the query
     *
     * UNION ALL combines result sets from multiple SELECT statements and
     * keeps all rows including duplicates. This is faster than UNION when
     * you don't need duplicate removal.
     *
     * ```php
     * // Combine all log entries from both tables, including duplicates
     * $logs = Log::all()
     *     ->whereEquals('level', 'error')
     *     ->unionAll(function($query) {
     *         $query->from('archived_logs')
     *               ->select('*')
     *               ->whereEquals('level', 'error');
     *     });
     * ```
     *
     * @param   Query|callable  $query  The query to union with, or a closure
     * @return  $this
     **/
    public function unionAll($query)
    {
        return $this->addUnion($query, true);
    }

    /**
     * Internal method to add a union query
     *
     * @param   Query|callable  $query  The query to union with, or a closure
     * @param   bool            $all    Whether to use UNION ALL (true) or UNION (false)
     * @return  $this
     **/
    protected function addUnion($query, $all = false)
    {
        // If a closure was passed, execute it with a fresh Query
        if (is_callable($query)) {
            $newQuery = new self($this->connection);
            $query($newQuery);
            $query = $newQuery;
        }

        // Get the query string and bindings from the Query object
        if ($query instanceof self) {
            // Build only core SELECT elements — ORDER BY and LIMIT
            // within a UNION member are ignored per SQL standard and
            // cause errors on strict implementations (e.g. Firebird).
            $sql = $query->buildUnionMemberQuery();
            $bindings = $query->syntax->getBindings();

            $this->syntax->setUnion($sql, $bindings, $all);
        }

        return $this;
    }

    /**
     * Sets the values to be inserted into the database
     *
     * @param   array  $data  The data to be inserted
     * @return  $this
     **/
    public function values($data)
    {
        $this->syntax->setValues($data);
        return $this;
    }

    /**
     * Performs an upsert (insert or update on duplicate key)
     *
     * This is an atomic operation that inserts a new row if it doesn't exist,
     * or updates the existing row if a duplicate key conflict occurs.
     *
     * The conflict is determined by:
     * - MySQL/MariaDB: PRIMARY KEY or UNIQUE index
     * - PostgreSQL: Specified conflict columns or constraint
     * - SQLite: PRIMARY KEY or UNIQUE constraint
     *
     * @param   string      $table          The table into which to upsert
     * @param   array       $values         Key-value pairs of data to insert
     * @param   array|null  $updateColumns  Columns to update on conflict (null = all columns)
     * @param   array|null  $conflictColumns  Columns that define the conflict (for PostgreSQL)
     * @return  $this
     **/
    public function upsert(string $table, array $values, ?array $updateColumns = null, ?array $conflictColumns = null)
    {
        $this->syntax->setUpsert($table, $values, $updateColumns, $conflictColumns);
        $this->type = 'upsert';
        return $this;
    }

    /**
     * Sets the values to be modified in the database
     *
     * @param   array  $data  The data to be modified
     * @return  $this
     **/
    public function set($data)
    {
        $this->syntax->setSet($data);
        return $this;
    }

    /**
     * Sets a raw value expression for an update statement
     *
     * Use this for atomic operations like incrementing:
     * ->setRaw('hits', 'hits + 1')
     *
     * @param   string  $column      The column to update
     * @param   string  $expression  The raw SQL expression
     * @return  $this
     */
    public function setRaw(string $column, string $expression)
    {
        $this->syntax->setSet([
            $column => new Value\Raw($expression)
        ]);
        return $this;
    }

    /**
     * Sets a column reference for an update statement
     *
     * Use this when updating one column to the value of another:
     * ->setColumn('finished_at', 'created_at')
     *
     * @param   string  $column        The column to update
     * @param   string  $targetColumn  The column to copy the value from
     * @return  $this
     */
    public function setColumn(string $column, string $targetColumn)
    {
        $this->syntax->setSet([
            $column => Expression::column($targetColumn)
        ]);
        return $this;
    }

    /**
     * Generate a database-agnostic CONCAT expression
     *
     * @param   array  $parts  Array of column names or quoted strings
     * @return  string
     */
    public function concat(array $parts)
    {
        return $this->syntax->buildConcat($parts);
    }

    /**
     * Generate a database-agnostic REPLACE expression
     *
     * @param   string  $column   The column name
     * @param   string  $search   The search string
     * @param   string  $replace  The replacement string
     * @return  string
     */
    public function replace($column, $search, $replace)
    {
        return $this->syntax->buildReplace($column, $search, $replace);
    }

    /**
     * Generate a database-agnostic LOWER expression
     *
     * @param   string  $column  The column name
     * @return  string
     */
    public function lower($column)
    {
        return $this->syntax->buildLower($column);
    }

    /**
     * Generate a database-agnostic UPPER expression
     *
     * @param   string  $column  The column name
     * @return  string
     */
    public function upper($column)
    {
        return $this->syntax->buildUpper($column);
    }

    /**
     * Increment a column's value by a given amount
     *
     * This is a convenience method that builds and executes an UPDATE
     * statement to atomically increment a column value. The WHERE clause
     * from the current query is applied.
     *
     * Example:
     *   // Increment views by 1
     *   $query->from('articles')->whereEquals('id', 5)->increment('views');
     *
     *   // Increment score by 10 and update modified timestamp
     *   $query->from('users')
     *         ->whereEquals('id', 1)
     *         ->increment('score', 10, ['updated_at' => date('Y-m-d H:i:s')]);
     *
     * @param   string  $column  The column to increment
     * @param   int     $amount  The amount to increment by (default 1)
     * @param   array   $extra   Additional columns to update
     * @return  int     The number of affected rows
     **/
    public function increment(string $column, int $amount = 1, array $extra = []): int
    {
        $table = $this->syntax->getFromTable();

        if ($table === null) {
            throw new \RuntimeException('Cannot increment: no table specified. Use from() or update() first.');
        }

        // Switch to update mode
        $this->syntax->setUpdate($table);
        $this->type = 'update';

        // Build the data array with increment expression
        $quotedColumn = $this->connection->quoteName($column);
        $data = [
            $column => new Value\Raw("{$quotedColumn} + {$amount}")
        ];

        // Merge any extra columns
        if (!empty($extra)) {
            $data = array_merge($data, $extra);
        }

        $this->syntax->setSet($data);
        $this->execute();
        return $this->connection->getAffectedRows();
    }

    /**
     * Decrement a column's value by a given amount
     *
     * This is a convenience method that builds and executes an UPDATE
     * statement to atomically decrement a column value. The WHERE clause
     * from the current query is applied.
     *
     * Example:
     *   // Decrement stock by 1
     *   $query->from('products')->whereEquals('id', 5)->decrement('stock');
     *
     *   // Decrement balance by 50 and log the transaction
     *   $query->from('accounts')
     *         ->whereEquals('id', 1)
     *         ->decrement('balance', 50, ['last_withdrawal' => date('Y-m-d H:i:s')]);
     *
     * @param   string  $column  The column to decrement
     * @param   int     $amount  The amount to decrement by (default 1)
     * @param   array   $extra   Additional columns to update
     * @return  int     The number of affected rows
     **/
    public function decrement(string $column, int $amount = 1, array $extra = []): int
    {
        $table = $this->syntax->getFromTable();

        if ($table === null) {
            throw new \RuntimeException('Cannot decrement: no table specified. Use from() or update() first.');
        }

        // Switch to update mode
        $this->syntax->setUpdate($table);
        $this->type = 'update';

        // Build the data array with decrement expression
        $quotedColumn = $this->connection->quoteName($column);
        $data = [
            $column => new Value\Raw("{$quotedColumn} - {$amount}")
        ];

        // Merge any extra columns
        if (!empty($extra)) {
            $data = array_merge($data, $extra);
        }

        $this->syntax->setSet($data);
        $this->execute();
        return $this->connection->getAffectedRows();
    }

    /**
     * Sets the group by element on the query
     *
     * @param   string  $column  The column on which to apply the group by
     * @return  $this
     **/
    public function group($column)
    {
        $this->syntax->setGroup($column);
        return $this;
    }

    /**
     * Sets the having element on the query
     *
     * @param   string  $column    The column to which the clause will apply
     * @param   string  $operator  The operation that will compare column to value
     * @param   string  $value     The value to which the column will be evaluated
     * @return  $this
     **/
    public function having($column, $operator, $value)
    {
        $this->syntax->setHaving($column, $operator, $value);
        return $this;
    }

    /**
     * Applies a raw having clause to the pending query
     *
     * Supports both positional (?) and named (:name) placeholders.
     * Named parameters can be passed inline or set via setParameter().
     *
     * Example with positional placeholders:
     *   havingRaw('COUNT(*) > ?', [10])
     *
     * Example with named placeholders:
     *   havingRaw('SUM(amount) >= :min_total', ['min_total' => 1000])
     *
     * Example with stored parameters:
     *   setParameter('min_count', 5)->havingRaw('COUNT(*) > :min_count')
     *
     * @param   string  $string    The raw having clause
     * @param   array   $bindings  Bindings array (positional or named)
     * @param   string  $logical   The logical operator (and/or)
     * @return  $this
     **/
    public function havingRaw($string, $bindings = [], $logical = 'and')
    {
        // Process named placeholders if needed
        if ($this->shouldProcessNamedPlaceholders($string, $bindings)) {
            list($string, $bindings) = $this->convertNamedToPositional($string, $bindings);
        }

        $this->syntax->setRawHaving($string, $bindings, $logical);
        return $this;
    }

    /**
     * Applies a raw having clause with OR logic
     *
     * @param   string  $string    The raw having clause
     * @param   array   $bindings  Bindings array (positional or named)
     * @return  $this
     **/
    public function orHavingRaw($string, $bindings = [])
    {
        return $this->havingRaw($string, $bindings, 'or');
    }

    // =========================================================================
    // Aggregate Helpers
    // =========================================================================

    /**
     * Get the sum of a column's values
     *
     * Example:
     *   $total = $query->from('orders')->sum('amount');
     *   // Returns: 1500.50
     *
     * @param   string  $column  The column to sum
     * @return  mixed   The sum value (int, float, or null if no rows)
     **/
    public function sum($column)
    {
        return $this->aggregate('SUM', $column);
    }

    /**
     * Get the average of a column's values
     *
     * Example:
     *   $avg = $query->from('products')->avg('price');
     *   // Returns: 29.99
     *
     * @param   string  $column  The column to average
     * @return  mixed   The average value (float or null if no rows)
     **/
    public function avg($column)
    {
        return $this->aggregate('AVG', $column);
    }

    /**
     * Get the minimum value of a column
     *
     * Example:
     *   $oldest = $query->from('users')->min('created_at');
     *   // Returns: '2020-01-01 00:00:00'
     *
     * @param   string  $column  The column to find minimum
     * @return  mixed   The minimum value (or null if no rows)
     **/
    public function min($column)
    {
        return $this->aggregate('MIN', $column);
    }

    /**
     * Get the maximum value of a column
     *
     * Example:
     *   $newest = $query->from('users')->max('created_at');
     *   // Returns: '2024-12-15 14:30:00'
     *
     * @param   string  $column  The column to find maximum
     * @return  mixed   The maximum value (or null if no rows)
     **/
    public function max($column)
    {
        return $this->aggregate('MAX', $column);
    }

    /**
     * Get the count of rows matching the query
     *
     * Example:
     *   $total = $query->from('users')->whereEquals('active', 1)->count();
     *   // Returns: 42
     *
     *   $unique = $query->from('orders')->count('DISTINCT customer_id');
     *   // Returns: 15
     *
     * @param   string  $column  The column to count (default '*' for all rows)
     * @return  int     The count
     **/
    public function count($column = '*')
    {
        $result = $this->aggregate('COUNT', $column);
        return (int) ($result ?? 0);
    }

    /**
     * Determine if any rows exist for the current query
     *
     * More readable alternative to `count() > 0` or `total() > 0`.
     *
     * Example:
     *   if ($query->from('users')->whereEquals('email', $email)->exists()) {
     *       // Email is already taken
     *   }
     *
     *   if (User::all()->whereEquals('active', 1)->exists()) {
     *       // At least one active user exists
     *   }
     *
     * @return  bool  True if at least one row exists, false otherwise
     **/
    public function exists(): bool
    {
        $query = clone $this;
        $query->deselect()
            ->selectRaw('1')
            ->limit(1);

        return $query->fetch('row', true) !== null;
    }

    /**
     * Determine if no rows exist for the current query
     *
     * The inverse of exists(). More readable alternative to `count() === 0`.
     *
     * Example:
     *   if ($query->from('posts')->whereEquals('author_id', $userId)->doesntExist()) {
     *       // User has no posts
     *   }
     *
     *   if (Order::all()->whereEquals('status', 'pending')->doesntExist()) {
     *       // No pending orders
     *   }
     *
     * @return  bool  True if no rows exist, false otherwise
     **/
    public function doesntExist(): bool
    {
        return !$this->exists();
    }

    /**
     * Execute an aggregate function and return the result
     *
     * @param   string  $function  The aggregate function (SUM, AVG, MIN, MAX, COUNT)
     * @param   string  $column    The column to aggregate
     * @return  mixed   The aggregate result
     **/
    protected function aggregate($function, $column)
    {
        // Store current select and replace with aggregate
        $this->syntax->resetSelect();
        $this->syntax->setRawSelect("{$function}({$column})", [], 'aggregate');
        $this->type = 'select';
        $this->hasSelect = true;

        // Execute and get the single value
        $result = $this->fetch('row');

        return $result ? $result->aggregate : null;
    }

    // =========================================================================
    // Date/Time Where Clauses
    // =========================================================================

    /**
     * Add a WHERE clause comparing only the date part of a datetime column
     *
     * Extracts only the date (YYYY-MM-DD) from a datetime/timestamp column
     * for comparison, ignoring the time component.
     *
     * Database-specific implementations:
     * - MySQL: DATE(column)
     * - PostgreSQL: column::date
     * - SQLite: date(column)
     * - SQL Server: CAST(column AS DATE)
     *
     * Example:
     *   whereDate('created_at', '=', '2024-01-15')
     *   whereDate('published_at', '>=', '2024-01-01')
     *
     * @param   string  $column    The datetime column name
     * @param   string  $operator  Comparison operator (=, !=, <, >, <=, >=)
     * @param   string  $value     The date value (YYYY-MM-DD format recommended)
     * @param   int     $depth     The depth level of the clause
     * @return  $this
     **/
    public function whereDate($column, $operator, $value, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $effectiveLogical = $this->consumePendingLogical('and');
        $this->syntax->setDateWhere($column, $operator, $value, 'date', $effectiveLogical, $effectiveDepth);
        return $this;
    }

    /**
     * Add a WHERE clause with OR logic comparing only the date part
     *
     * @param   string  $column    The datetime column name
     * @param   string  $operator  Comparison operator
     * @param   string  $value     The date value
     * @param   int     $depth     The depth level of the clause
     * @return  $this
     **/
    public function orWhereDate($column, $operator, $value, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $this->syntax->setDateWhere($column, $operator, $value, 'date', 'or', $effectiveDepth);
        return $this;
    }

    /**
     * Add a WHERE clause comparing only the month of a datetime column
     *
     * Extracts only the month (1-12) from a datetime/timestamp column.
     *
     * Database-specific implementations:
     * - MySQL: MONTH(column)
     * - PostgreSQL: EXTRACT(MONTH FROM column)
     * - SQLite: CAST(strftime('%m', column) AS INTEGER)
     * - SQL Server: MONTH(column)
     *
     * Example:
     *   whereMonth('created_at', '=', 12)  // December
     *   whereMonth('birthday', '>=', 6)    // June or later
     *
     * @param   string  $column    The datetime column name
     * @param   string  $operator  Comparison operator
     * @param   int     $value     The month value (1-12)
     * @param   int     $depth     The depth level of the clause
     * @return  $this
     **/
    public function whereMonth($column, $operator, $value, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $effectiveLogical = $this->consumePendingLogical('and');
        $this->syntax->setDateWhere($column, $operator, (int) $value, 'month', $effectiveLogical, $effectiveDepth);
        return $this;
    }

    /**
     * Add a WHERE clause with OR logic comparing only the month
     *
     * @param   string  $column    The datetime column name
     * @param   string  $operator  Comparison operator
     * @param   int     $value     The month value (1-12)
     * @param   int     $depth     The depth level of the clause
     * @return  $this
     **/
    public function orWhereMonth($column, $operator, $value, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $this->syntax->setDateWhere($column, $operator, (int) $value, 'month', 'or', $effectiveDepth);
        return $this;
    }

    /**
     * Add a WHERE clause comparing only the year of a datetime column
     *
     * Extracts only the year from a datetime/timestamp column.
     *
     * Database-specific implementations:
     * - MySQL: YEAR(column)
     * - PostgreSQL: EXTRACT(YEAR FROM column)
     * - SQLite: CAST(strftime('%Y', column) AS INTEGER)
     * - SQL Server: YEAR(column)
     *
     * Example:
     *   whereYear('created_at', '=', 2024)
     *   whereYear('published_at', '>=', 2020)
     *
     * @param   string  $column    The datetime column name
     * @param   string  $operator  Comparison operator
     * @param   int     $value     The year value (e.g., 2024)
     * @param   int     $depth     The depth level of the clause
     * @return  $this
     **/
    public function whereYear($column, $operator, $value, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $effectiveLogical = $this->consumePendingLogical('and');
        $this->syntax->setDateWhere($column, $operator, (int) $value, 'year', $effectiveLogical, $effectiveDepth);
        return $this;
    }

    /**
     * Add a WHERE clause with OR logic comparing only the year
     *
     * @param   string  $column    The datetime column name
     * @param   string  $operator  Comparison operator
     * @param   int     $value     The year value
     * @param   int     $depth     The depth level of the clause
     * @return  $this
     **/
    public function orWhereYear($column, $operator, $value, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $this->syntax->setDateWhere($column, $operator, (int) $value, 'year', 'or', $effectiveDepth);
        return $this;
    }

    /**
     * Add a WHERE clause comparing only the time part of a datetime column
     *
     * Extracts only the time (HH:MM:SS) from a datetime/timestamp column
     * for comparison, ignoring the date component.
     *
     * Database-specific implementations:
     * - MySQL: TIME(column)
     * - PostgreSQL: column::time
     * - SQLite: time(column)
     * - SQL Server: CAST(column AS TIME)
     *
     * Example:
     *   whereTime('created_at', '>=', '09:00:00')  // After 9 AM
     *   whereTime('event_time', '<', '17:00:00')   // Before 5 PM
     *
     * @param   string  $column    The datetime column name
     * @param   string  $operator  Comparison operator
     * @param   string  $value     The time value (HH:MM:SS format)
     * @param   int     $depth     The depth level of the clause
     * @return  $this
     **/
    public function whereTime($column, $operator, $value, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $effectiveLogical = $this->consumePendingLogical('and');
        $this->syntax->setDateWhere($column, $operator, $value, 'time', $effectiveLogical, $effectiveDepth);
        return $this;
    }

    /**
     * Add a WHERE clause with OR logic comparing only the time
     *
     * @param   string  $column    The datetime column name
     * @param   string  $operator  Comparison operator
     * @param   string  $value     The time value (HH:MM:SS format)
     * @param   int     $depth     The depth level of the clause
     * @return  $this
     **/
    public function orWhereTime($column, $operator, $value, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $this->syntax->setDateWhere($column, $operator, $value, 'time', 'or', $effectiveDepth);
        return $this;
    }

    /**
     * Add a WHERE clause comparing only the day of a datetime column
     *
     * Extracts only the day of month (1-31) from a datetime/timestamp column.
     *
     * Example:
     *   whereDay('created_at', '=', 15)   // 15th of any month
     *   whereDay('birthday', '<=', 7)     // First week of month
     *
     * @param   string  $column    The datetime column name
     * @param   string  $operator  Comparison operator
     * @param   int     $value     The day value (1-31)
     * @param   int     $depth     The depth level of the clause
     * @return  $this
     **/
    public function whereDay($column, $operator, $value, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $effectiveLogical = $this->consumePendingLogical('and');
        $this->syntax->setDateWhere($column, $operator, (int) $value, 'day', $effectiveLogical, $effectiveDepth);
        return $this;
    }

    /**
     * Add a WHERE clause with OR logic comparing only the day
     *
     * @param   string  $column    The datetime column name
     * @param   string  $operator  Comparison operator
     * @param   int     $value     The day value (1-31)
     * @param   int     $depth     The depth level of the clause
     * @return  $this
     **/
    public function orWhereDay($column, $operator, $value, $depth = 0)
    {
        $effectiveDepth = $this->getEffectiveDepth($depth);
        $this->syntax->setDateWhere($column, $operator, (int) $value, 'day', 'or', $effectiveDepth);
        return $this;
    }

    // =========================================================================
    // Conditional WHERE Methods (Filter-Aware)
    // =========================================================================
    //
    // These methods only add WHERE conditions if the value is not "empty".
    // Useful for building search forms where users may or may not fill in fields.
    //
    // Empty values (ignored): null, '', [], ['']
    // Non-empty values (applied): 0, '0', false (intentional values)
    //
    // Example:
    //   $query->whereEqualsIfNotEmpty('status', $request->get('status'))
    //         ->whereLikeIfNotEmpty('title', $request->get('search'))
    //         ->whereInIfNotEmpty('category_id', $request->get('categories'));
    //
    // =========================================================================

    /**
     * Check if a value is considered "empty" for filtering purposes
     *
     * Empty values that cause the condition to be skipped:
     * - null
     * - '' (empty string)
     * - [] (empty array)
     * - [''] (array containing only empty string)
     *
     * Non-empty values that ARE applied (intentional values):
     * - 0 (zero integer)
     * - '0' (zero string)
     * - false (boolean false)
     *
     * @param   mixed  $value  The value to check
     * @return  bool   True if the value should be considered empty
     **/
    protected function isFilterValueEmpty($value): bool
    {
        // Null is empty
        if ($value === null) {
            return true;
        }

        // Empty string is empty
        if ($value === '') {
            return true;
        }

        // Empty array is empty
        if (is_array($value)) {
            // Completely empty array
            if (count($value) === 0) {
                return true;
            }

            // Array containing only empty strings or nulls
            $filtered = array_filter($value, function ($v) {
                return $v !== '' && $v !== null;
            });

            return count($filtered) === 0;
        }

        // 0, '0', false are NOT empty (they're intentional filter values)
        return false;
    }

    /**
     * Applies a where clause only if the value is not empty
     *
     * This is useful for building dynamic queries from form input where
     * some fields may be left blank. Skips adding the condition if the
     * value is null, '', [], or [''].
     *
     * Example:
     *   $query->whereIfNotEmpty('status', '=', $request->get('status'))
     *         ->whereIfNotEmpty('type', '=', $request->get('type'));
     *
     * @param   string  $column    The column to which the clause will apply
     * @param   string  $operator  The operation that will compare column to value
     * @param   mixed   $value     The value to which the column will be evaluated
     * @param   int     $depth     The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereIfNotEmpty($column, $operator, $value, $depth = 0)
    {
        if (!$this->isFilterValueEmpty($value)) {
            $this->where($column, $operator, $value, 'and', $depth);
        }

        return $this;
    }

    /**
     * Applies a where clause with OR logic only if the value is not empty
     *
     * @param   string  $column    The column to which the clause will apply
     * @param   string  $operator  The operation that will compare column to value
     * @param   mixed   $value     The value to which the column will be evaluated
     * @param   int     $depth     The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereIfNotEmpty($column, $operator, $value, $depth = 0)
    {
        if (!$this->isFilterValueEmpty($value)) {
            $this->where($column, $operator, $value, 'or', $depth);
        }

        return $this;
    }

    /**
     * Applies a simple where equals clause only if the value is not empty
     *
     * Example:
     *   $query->whereEqualsIfNotEmpty('status', $filters['status'])
     *         ->whereEqualsIfNotEmpty('category_id', $filters['category']);
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   mixed   $value   The value to which the column will be evaluated
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereEqualsIfNotEmpty($column, $value, $depth = 0)
    {
        if (!$this->isFilterValueEmpty($value)) {
            $this->whereEquals($column, $value, $depth);
        }

        return $this;
    }

    /**
     * Applies a simple where equals clause with OR logic only if the value is not empty
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   mixed   $value   The value to which the column will be evaluated
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereEqualsIfNotEmpty($column, $value, $depth = 0)
    {
        if (!$this->isFilterValueEmpty($value)) {
            $this->orWhereEquals($column, $value, $depth);
        }

        return $this;
    }

    /**
     * Applies a where LIKE clause only if the value is not empty
     *
     * Automatically wraps the value in % wildcards for partial matching.
     *
     * Example:
     *   $query->whereLikeIfNotEmpty('title', $searchTerm)
     *         ->whereLikeIfNotEmpty('description', $searchTerm);
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   mixed   $value   The search term (will be wrapped with %)
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereLikeIfNotEmpty($column, $value, $depth = 0)
    {
        if (!$this->isFilterValueEmpty($value)) {
            $this->whereLike($column, $value, $depth);
        }

        return $this;
    }

    /**
     * Applies a where LIKE clause with OR logic only if the value is not empty
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   mixed   $value   The search term (will be wrapped with %)
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereLikeIfNotEmpty($column, $value, $depth = 0)
    {
        if (!$this->isFilterValueEmpty($value)) {
            $this->orWhereLike($column, $value, $depth);
        }

        return $this;
    }

    /**
     * Applies a where IN clause only if the values array is not empty
     *
     * Example:
     *   $query->whereInIfNotEmpty('category_id', $selectedCategories)
     *         ->whereInIfNotEmpty('tag_id', $selectedTags);
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   array   $values  The array of values to check against
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereInIfNotEmpty($column, $values, $depth = 0)
    {
        if (!$this->isFilterValueEmpty($values)) {
            $this->whereIn($column, $values, $depth);
        }

        return $this;
    }

    /**
     * Applies a where IN clause with OR logic only if the values array is not empty
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   array   $values  The array of values to check against
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereInIfNotEmpty($column, $values, $depth = 0)
    {
        if (!$this->isFilterValueEmpty($values)) {
            $this->orWhereIn($column, $values, $depth);
        }

        return $this;
    }

    /**
     * Applies a where NOT IN clause only if the values array is not empty
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   array   $values  The array of values to exclude
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereNotInIfNotEmpty($column, $values, $depth = 0)
    {
        if (!$this->isFilterValueEmpty($values)) {
            $this->whereNotIn($column, $values, $depth);
        }

        return $this;
    }

    /**
     * Applies a where NOT IN clause with OR logic only if the values array is not empty
     *
     * @param   string  $column  The column to which the clause will apply
     * @param   array   $values  The array of values to exclude
     * @param   int     $depth   The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function orWhereNotInIfNotEmpty($column, $values, $depth = 0)
    {
        if (!$this->isFilterValueEmpty($values)) {
            $this->orWhereNotIn($column, $values, $depth);
        }

        return $this;
    }

    // =========================================================================
    // Conditional Query Methods (*When / *Unless)
    // =========================================================================

    /**
     * Apply an ORDER BY clause only when the condition is truthy
     *
     * Unlike Laravel's closure-based when() method, this provides a simpler
     * approach that doesn't require closures for common conditional operations.
     *
     * Example:
     *   // Apply ordering only if $sortField is set
     *   $query->orderWhen($sortField, $sortField, $direction);
     *
     *   // Apply ordering based on user permission
     *   $query->orderWhen($user->canSort(), 'priority', 'desc');
     *
     * @param   mixed   $condition  The condition to evaluate (truthy/falsy)
     * @param   string  $column     The column to order by
     * @param   string  $direction  The direction to order (asc or desc)
     * @return  $this
     **/
    public function orderWhen($condition, $column, $direction = 'asc')
    {
        if ($condition) {
            $this->order($column, $direction);
        }

        return $this;
    }

    /**
     * Apply an ORDER BY clause only when the condition is falsy
     *
     * Inverse of orderWhen() - applies the clause when condition is false/empty.
     *
     * Example:
     *   // Apply default ordering unless custom sort is specified
     *   $query->orderUnless($customSort, 'created', 'desc');
     *
     * @param   mixed   $condition  The condition to evaluate (truthy/falsy)
     * @param   string  $column     The column to order by
     * @param   string  $direction  The direction to order (asc or desc)
     * @return  $this
     **/
    public function orderUnless($condition, $column, $direction = 'asc')
    {
        if (!$condition) {
            $this->order($column, $direction);
        }

        return $this;
    }

    /**
     * Apply a LIMIT clause only when the condition is truthy
     *
     * Example:
     *   // Limit results only if pagination is enabled
     *   $query->limitWhen($paginate, $perPage);
     *
     *   // Limit only for non-admin users
     *   $query->limitWhen(!$user->isAdmin(), 100);
     *
     * @param   mixed  $condition  The condition to evaluate (truthy/falsy)
     * @param   int    $limit      The limit to apply
     * @return  $this
     **/
    public function limitWhen($condition, $limit)
    {
        if ($condition) {
            $this->limit($limit);
        }

        return $this;
    }

    /**
     * Apply a LIMIT clause only when the condition is falsy
     *
     * Example:
     *   // Apply default limit unless unlimited is requested
     *   $query->limitUnless($unlimited, 50);
     *
     * @param   mixed  $condition  The condition to evaluate (truthy/falsy)
     * @param   int    $limit      The limit to apply
     * @return  $this
     **/
    public function limitUnless($condition, $limit)
    {
        if (!$condition) {
            $this->limit($limit);
        }

        return $this;
    }

    /**
     * Apply an OFFSET (start) clause only when the condition is truthy
     *
     * Example:
     *   // Apply offset only if on page 2+
     *   $query->startWhen($page > 1, ($page - 1) * $perPage);
     *
     * @param   mixed  $condition  The condition to evaluate (truthy/falsy)
     * @param   int    $start      The offset to apply
     * @return  $this
     **/
    public function startWhen($condition, $start)
    {
        if ($condition) {
            $this->start($start);
        }

        return $this;
    }

    /**
     * Apply an OFFSET (start) clause only when the condition is falsy
     *
     * @param   mixed  $condition  The condition to evaluate (truthy/falsy)
     * @param   int    $start      The offset to apply
     * @return  $this
     **/
    public function startUnless($condition, $start)
    {
        if (!$condition) {
            $this->start($start);
        }

        return $this;
    }

    /**
     * Apply a GROUP BY clause only when the condition is truthy
     *
     * Example:
     *   // Group by category only if grouping is enabled
     *   $query->groupWhen($groupByCategory, 'category_id');
     *
     * @param   mixed   $condition  The condition to evaluate (truthy/falsy)
     * @param   string  $column     The column to group by
     * @return  $this
     **/
    public function groupWhen($condition, $column)
    {
        if ($condition) {
            $this->group($column);
        }

        return $this;
    }

    /**
     * Apply a GROUP BY clause only when the condition is falsy
     *
     * @param   mixed   $condition  The condition to evaluate (truthy/falsy)
     * @param   string  $column     The column to group by
     * @return  $this
     **/
    public function groupUnless($condition, $column)
    {
        if (!$condition) {
            $this->group($column);
        }

        return $this;
    }

    /**
     * Apply a JOIN clause only when the condition is truthy
     *
     * Example:
     *   // Join user details only if needed
     *   $query->joinWhen($includeUserDetails, '#__users', 'posts.user_id', 'users.id');
     *
     *   // Left join for optional relationship
     *   $query->joinWhen($withComments, '#__comments', 'posts.id', 'comments.post_id', 'left');
     *
     * @param   mixed   $condition   The condition to evaluate (truthy/falsy)
     * @param   string  $table       The table to join
     * @param   string  $localKey    The local key for the join condition
     * @param   string  $foreignKey  The foreign key for the join condition
     * @param   string  $type        The type of join (inner, left, right, outer)
     * @return  $this
     **/
    public function joinWhen($condition, $table, $localKey, $foreignKey, $type = 'inner')
    {
        if ($condition) {
            $this->join($table, $localKey, $foreignKey, $type);
        }

        return $this;
    }

    /**
     * Apply a JOIN clause only when the condition is falsy
     *
     * @param   mixed   $condition   The condition to evaluate (truthy/falsy)
     * @param   string  $table       The table to join
     * @param   string  $localKey    The local key for the join condition
     * @param   string  $foreignKey  The foreign key for the join condition
     * @param   string  $type        The type of join (inner, left, right, outer)
     * @return  $this
     **/
    public function joinUnless($condition, $table, $localKey, $foreignKey, $type = 'inner')
    {
        if (!$condition) {
            $this->join($table, $localKey, $foreignKey, $type);
        }

        return $this;
    }

    /**
     * Apply SELECT columns only when the condition is truthy
     *
     * Optionally specify fallback columns to use when condition is falsy.
     *
     * Example:
     *   // Select detailed columns only for admins
     *   $query->selectWhen($isAdmin, ['*'], ['id', 'name', 'status']);
     *
     *   // Include sensitive fields conditionally
     *   $query->selectWhen($canViewPrivate, ['id', 'name', 'email', 'phone'], ['id', 'name']);
     *
     * @param   mixed  $condition  The condition to evaluate (truthy/falsy)
     * @param   array  $columns    The columns to select when condition is truthy
     * @param   array  $fallback   Optional columns to select when condition is falsy
     * @return  $this
     **/
    public function selectWhen($condition, array $columns, ?array $fallback = null)
    {
        if ($condition) {
            foreach ($columns as $column) {
                $this->select($column);
            }
        } elseif ($fallback !== null) {
            foreach ($fallback as $column) {
                $this->select($column);
            }
        }

        return $this;
    }

    /**
     * Apply a WHERE clause only when the condition is truthy
     *
     * Example:
     *   // Filter by status only if status filter is set
     *   $query->whereWhen($statusFilter, 'status', '=', $statusFilter);
     *
     *   // Apply date filter only if date is provided
     *   $query->whereWhen($startDate, 'created', '>=', $startDate);
     *
     * @param   mixed   $condition  The condition to evaluate (truthy/falsy)
     * @param   string  $column     The column to constrain
     * @param   string  $operator   The comparison operator
     * @param   mixed   $value      The value to compare against
     * @param   int     $depth      The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereWhen($condition, $column, $operator, $value, $depth = 0)
    {
        if ($condition) {
            $this->where($column, $operator, $value, 'and', $depth);
        }

        return $this;
    }

    /**
     * Apply a WHERE clause only when the condition is falsy
     *
     * Example:
     *   // Show only published unless user is admin
     *   $query->whereUnless($isAdmin, 'status', '=', 'published');
     *
     * @param   mixed   $condition  The condition to evaluate (truthy/falsy)
     * @param   string  $column     The column to constrain
     * @param   string  $operator   The comparison operator
     * @param   mixed   $value      The value to compare against
     * @param   int     $depth      The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereUnless($condition, $column, $operator, $value, $depth = 0)
    {
        if (!$condition) {
            $this->where($column, $operator, $value, 'and', $depth);
        }

        return $this;
    }

    /**
     * Apply a WHERE IN clause only when the condition is truthy
     *
     * Example:
     *   // Filter by categories only if filter array is provided
     *   $query->whereInWhen($categoryIds, 'category_id', $categoryIds);
     *
     * @param   mixed   $condition  The condition to evaluate (truthy/falsy)
     * @param   string  $column     The column to constrain
     * @param   array   $values     The array of values
     * @param   int     $depth      The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereInWhen($condition, $column, array $values, $depth = 0)
    {
        if ($condition) {
            $this->whereIn($column, $values, $depth);
        }

        return $this;
    }

    /**
     * Apply a WHERE IN clause only when the condition is falsy
     *
     * @param   mixed   $condition  The condition to evaluate (truthy/falsy)
     * @param   string  $column     The column to constrain
     * @param   array   $values     The array of values
     * @param   int     $depth      The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereInUnless($condition, $column, array $values, $depth = 0)
    {
        if (!$condition) {
            $this->whereIn($column, $values, $depth);
        }

        return $this;
    }

    /**
     * Apply a WHERE NOT IN clause only when the condition is truthy
     *
     * @param   mixed   $condition  The condition to evaluate (truthy/falsy)
     * @param   string  $column     The column to constrain
     * @param   array   $values     The array of values to exclude
     * @param   int     $depth      The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereNotInWhen($condition, $column, array $values, $depth = 0)
    {
        if ($condition) {
            $this->whereNotIn($column, $values, $depth);
        }

        return $this;
    }

    /**
     * Apply a WHERE NOT IN clause only when the condition is falsy
     *
     * @param   mixed   $condition  The condition to evaluate (truthy/falsy)
     * @param   string  $column     The column to constrain
     * @param   array   $values     The array of values to exclude
     * @param   int     $depth      The depth level of the clause, for sub clauses
     * @return  $this
     **/
    public function whereNotInUnless($condition, $column, array $values, $depth = 0)
    {
        if (!$condition) {
            $this->whereNotIn($column, $values, $depth);
        }

        return $this;
    }

    /**
     * Apply a HAVING clause only when the condition is truthy
     *
     * Example:
     *   // Filter by count only if minimum is specified
     *   $query->havingWhen($minCount, 'count', '>=', $minCount);
     *
     * @param   mixed   $condition  The condition to evaluate (truthy/falsy)
     * @param   string  $column     The column/aggregate to constrain
     * @param   string  $operator   The comparison operator
     * @param   mixed   $value      The value to compare against
     * @return  $this
     **/
    public function havingWhen($condition, $column, $operator, $value)
    {
        if ($condition) {
            $this->having($column, $operator, $value);
        }

        return $this;
    }

    /**
     * Apply a HAVING clause only when the condition is falsy
     *
     * @param   mixed   $condition  The condition to evaluate (truthy/falsy)
     * @param   string  $column     The column/aggregate to constrain
     * @param   string  $operator   The comparison operator
     * @param   mixed   $value      The value to compare against
     * @return  $this
     **/
    public function havingUnless($condition, $column, $operator, $value)
    {
        if (!$condition) {
            $this->having($column, $operator, $value);
        }

        return $this;
    }

    // =========================================================================
    // Table Operations
    // =========================================================================

    /**
     * Truncate a table, removing all rows
     *
     * TRUNCATE is faster than DELETE for removing all rows because it doesn't
     * log individual row deletions. It also resets auto-increment counters.
     *
     * Database-specific implementations:
     * - MySQL: TRUNCATE TABLE table
     * - PostgreSQL: TRUNCATE TABLE table RESTART IDENTITY
     * - SQLite: DELETE FROM table (plus reset sequence)
     * - SQL Server: TRUNCATE TABLE table
     *
     * WARNING: This operation cannot be rolled back in most databases.
     *
     * Example:
     *   $query->truncate('temp_data');
     *   $query->truncate('session_logs');
     *
     * @param   string  $table  The table to truncate
     * @return  bool    True on success
     **/
    public function truncate($table)
    {
        $statements = $this->syntax->getTruncateStatements($table);

        foreach ($statements as $sql) {
            $this->connection->setQuery($sql)->query();
        }

        return true;
    }

    /**
     * Retrieves all applicable data
     *
     * Supports three levels of caching:
     * 1. In-memory cache (default) - caches results for the duration of the request
     * 2. Persistent cache via injected store - uses setCacheStore() with remember()
     * 3. APCu fallback - automatic when APCu is available and remember() is called
     *
     * @FIXME: this could result in slightly odd behavior if you call the same query
     *         twice, but for some reason want differing structures of the returned data.
     *
     * @param   string  $structure  The structure of the item(s) returned (if applicable)
     * @param   bool    $noCache    Whether or not to check cache for results
     * @return  mixed
     **/
    public function fetch($structure = 'rows', $noCache = false)
    {
        if (!$this->hasSelect) {
            $this->select('*');
        }

        // Build and hash query
        $query = $this->buildQuery();
        $connectionId = $this->connection ? $this->connection->getConnectionId() : '';
        $key   = hash('md5', $connectionId . $structure . $query . serialize($this->syntax->getBindings()));

        // Handle persistent caching if enabled via remember() or rememberForever()
        if ($this->isPersistentCacheEnabled() && $this->hasPersistentCache()) {
            $persistentKey = $this->cachePrefix . $key;

            // Check persistent cache first (unless noCache)
            if (!$noCache) {
                $cached = $this->persistentGet($persistentKey);
                if ($cached !== null) {
                    // Also populate in-memory cache for this request
                    self::$cache[$key] = $cached;

                    // Clear elements and reset cache settings
                    $this->resetCacheSettings();
                    $this->reset();

                    return $cached;
                }
            }

            // Execute query and cache result
            $result = $this->query($query, $structure);

            // Store in persistent cache
            $ttl = $this->cacheForever ? 0 : $this->cacheTtl;
            $this->persistentPut($persistentKey, $result, $ttl);

            // Also store in in-memory cache
            self::$cache[$key] = $result;

            // Clear elements and reset cache settings
            $this->resetCacheSettings();
            $this->reset();

            return $result;
        }

        // Standard in-memory cache behavior
        if ($noCache || !isset(self::$cache[$key])) {
            self::$cache[$key] = $this->query($query, $structure);
        }

        // Clear elements
        $this->reset();

        return self::$cache[$key];
    }

    /**
     * Reset cache settings after a query is executed
     *
     * @return  void
     **/
    private function resetCacheSettings()
    {
        $this->cacheTtl = 0;
        $this->cacheForever = false;
        $this->cachePrefix = 'hubzero_query_';
    }

    /**
     * Get the next value from a sequence as a raw SQL value
     *
     * Returns a Value\Raw object suitable for use in INSERT data arrays.
     * On databases with native sequences (PostgreSQL, Firebird, etc.),
     * this emits inline SQL. On MySQL/SQLite, it resolves via the
     * table-based emulation and returns the literal integer.
     *
     * ```php
     * $query->push('invoices', [
     *     'id'    => $query->nextVal('invoice_seq'),
     *     'title' => 'New Invoice',
     * ]);
     * ```
     *
     * @param   string  $sequence  The sequence name
     * @return  Value\Raw
     **/
    public function nextVal(string $sequence): Value\Raw
    {
        $expr = Expression::nextVal($sequence);
        return new Value\Raw($expr->build($this->syntax));
    }

    /**
     * Inserts a new row using data provided into given table
     *
     * @param   string    $table   The table name into which the data should be inserted
     * @param   array     $data    An associative array of data to insert
     * @param   bool      $ignore  Whether or not to perform an insert ignore
     * @return  bool|int
     **/
    public function push($table, $data, $ignore = false)
    {
        // Add insert statement
        $this->insert($table, $ignore)
             ->values($data);

        $result = $this->execute();

        // Return the inserted data
        return !$result ?: $this->connection->insertid();
    }

    /**
     * Inserts a new row or updates an existing row on conflict
     *
     * This is an auto-executing convenience method that mirrors push() but
     * uses upsert semantics. If the row doesn't exist (based on primary key
     * or unique index), it inserts. If it exists, it updates the specified
     * columns.
     *
     * @param   string      $table           The table name
     * @param   array       $data            Key-value pairs to insert/update
     * @param   array|null  $updateColumns   Columns to update on conflict
     *                                       (null = all)
     * @param   array|null  $conflictColumns Columns defining conflict
     *                                       (for PG/SQLite/Firebird)
     * @return  bool|int
     **/
    public function pushOrUpdate(
        string $table,
        array $data,
        ?array $updateColumns = null,
        ?array $conflictColumns = null
    ) {
        $this->upsert($table, $data, $updateColumns, $conflictColumns);

        $result = $this->execute();

        return !$result ?: $this->connection->insertid();
    }

    /**
     * Inserts a new row using an object's properties
     *
     * This method provides compatibility with legacy code that uses objects
     * instead of arrays for database operations. It extracts public properties
     * from the object (skipping nulls, arrays, objects, and underscore-prefixed
     * internal fields) and inserts them into the specified table.
     *
     * If a primary key field name is provided and the insert succeeds, the
     * generated ID will be set back on the object.
     *
     * @param   string  $table   The table name into which the data should be inserted
     * @param   object  &$object A reference to an object whose public properties match table fields
     * @param   string  $key     Optional primary key field name (to set generated ID on object)
     * @param   bool    $ignore  Whether or not to perform an insert ignore
     * @return  bool
     */
    public function pushObject($table, &$object, $key = null, $ignore = false)
    {
        // Extract object properties, filtering out non-insertable values
        $data = [];
        foreach (get_object_vars($object) as $k => $v) {
            // Only process non-null scalars
            if (is_array($v) || is_object($v) || $v === null) {
                continue;
            }

            // Ignore any internal fields (underscore-prefixed)
            if ($k[0] === '_') {
                continue;
            }

            $data[$k] = $v;
        }

        // Perform the insert using the existing push method
        $result = $this->push($table, $data, $ignore);

        // Update the primary key on the object if it exists
        if ($result && $key) {
            $object->$key = $result;
        }

        return $result !== false;
    }

    /**
     * Insert multiple rows in a single query
     *
     * This method is significantly faster than inserting rows one at a time,
     * as it reduces network round-trips and query parsing overhead.
     *
     * Example:
     * ```php
     * $query->insertMany('users', [
     *     ['name' => 'Alice', 'email' => 'alice@example.com'],
     *     ['name' => 'Bob', 'email' => 'bob@example.com'],
     * ]);
     * ```
     *
     * For very large datasets, use chunking to avoid packet size limits:
     * ```php
     * $query->insertMany('users', $thousandsOfRecords, chunkSize: 500);
     * ```
     *
     * @param   string  $table      The table name into which to insert
     * @param   array   $rows       Array of associative arrays (each row is a key-value pair)
     * @param   bool    $ignore     Whether to use INSERT IGNORE (skip duplicates)
     * @param   int     $chunkSize  Rows per batch (0 = no chunking, default 1000)
     * @return  int     Number of rows inserted
     **/
    public function insertMany(string $table, array $rows, bool $ignore = false, int $chunkSize = 1000): int
    {
        if (empty($rows)) {
            return 0;
        }

        // Ensure all rows have the same keys (use first row as template)
        $columns = array_keys($rows[0]);

        // Validate that all rows have the same structure
        foreach ($rows as $index => $row) {
            if (array_keys($row) !== $columns) {
                throw new \InvalidArgumentException(
                    "Row {$index} has different columns than the first row. All rows must have the same structure."
                );
            }
        }

        $totalInserted = 0;

        // Chunk the rows if needed
        if ($chunkSize > 0 && count($rows) > $chunkSize) {
            $chunks = array_chunk($rows, $chunkSize);
        } else {
            $chunks = [$rows];
        }

        foreach ($chunks as $chunk) {
            // Build and execute the multi-row insert
            $this->syntax->setInsertMany($table, $chunk, $ignore);
            $this->type = 'insert';

            // Check if database supports bulk insert (syntax returns non-empty SQL)
            $this->syntax->clearBindings();
            $sql = $this->syntax->buildInsertMany();

            if (empty($sql)) {
                // Fall back to individual inserts for databases that don't support bulk insert
                foreach ($chunk as $row) {
                    if ($this->push($table, $row, $ignore)) {
                        $totalInserted++;
                    }
                }
            } else {
                // Use bulk insert
                if ($this->query($sql)) {
                    $totalInserted += count($chunk);
                    self::purgeCache();
                }
            }

            // Reset for next chunk
            $this->reset();
        }

        return $totalInserted;
    }

    /**
     * Insert multiple rows using INSERT IGNORE semantics
     *
     * Alias for insertMany() with ignore=true.
     *
     * @param   string  $table      The table name into which to insert
     * @param   array   $rows       Array of associative arrays (each row is a key-value pair)
     * @param   int     $chunkSize  Rows per batch (0 = no chunking, default 1000)
     * @return  int     Number of rows inserted
     **/
    public function insertManyIgnore(string $table, array $rows, int $chunkSize = 1000): int
    {
        return $this->insertMany($table, $rows, true, $chunkSize);
    }

    /**
     * Upserts multiple rows in a single query (or chunked queries)
     *
     * For databases that support multi-row upsert syntax (MySQL,
     * PostgreSQL, SQLite, Oracle, DB2, SQL Server), this builds a
     * single efficient statement. For databases that don't (Firebird,
     * Informix), it falls back to individual upsert calls.
     *
     * @param   string      $table           The table name
     * @param   array       $rows            Array of associative arrays
     * @param   array|null  $updateColumns   Columns to update on
     *                                       conflict (null = all)
     * @param   array|null  $conflictColumns Columns defining conflict
     * @param   int         $chunkSize       Rows per batch (default 1000)
     * @return  int         Number of rows affected
     * @throws  \InvalidArgumentException  If rows have inconsistent columns
     */
    public function upsertMany(
        string $table,
        array $rows,
        ?array $updateColumns = null,
        ?array $conflictColumns = null,
        int $chunkSize = 1000
    ): int {
        if (empty($rows)) {
            return 0;
        }

        // Validate all rows have same column structure
        $columns = array_keys($rows[0]);
        foreach ($rows as $index => $row) {
            if (array_keys($row) !== $columns) {
                throw new \InvalidArgumentException(
                    "Row {$index} has different columns than the"
                    . " first row. All rows must have identical"
                    . " column structure for bulk upsert."
                );
            }
        }

        $totalAffected = 0;

        // Chunk large batches
        if ($chunkSize > 0 && count($rows) > $chunkSize) {
            $chunks = array_chunk($rows, $chunkSize);
        } else {
            $chunks = [$rows];
        }

        foreach ($chunks as $chunk) {
            $this->syntax->setUpsertMany(
                $table,
                $chunk,
                $updateColumns,
                $conflictColumns
            );
            $this->type = 'upsert';

            $this->syntax->clearBindings();
            $sql = $this->syntax->buildUpsertMany();

            if (empty($sql)) {
                // Fall back to individual upserts
                foreach ($chunk as $row) {
                    $this->pushOrUpdate(
                        $table,
                        $row,
                        $updateColumns,
                        $conflictColumns
                    );
                    $totalAffected++;
                }
            } else {
                if ($this->query($sql)) {
                    $totalAffected += count($chunk);
                    self::purgeCache();
                }
            }

            $this->reset();
        }

        return $totalAffected;
    }

    /**
     * Updates an existing item in the database using the provided data
     *
     * @param   string  $table    The table to update
     * @param   string  $pkField  The table field serving as primary key
     * @param   mixed   $pkValue  The primary key value
     * @param   array   $data     The data to update in the database
     * @return  bool
     **/
    public function alter($table, $pkField, $pkValue, $data)
    {
        // Add insert statement
        $this->update($table)
             ->set($data);

        // Where primary key is...
        $this->whereEquals($pkField, $pkValue);

        // Return the result of the query
        return $this->execute();
    }

    /**
     * Updates a row in a table based on an object's properties
     *
     * This method provides compatibility with legacy code that uses objects
     * instead of arrays for database operations. It extracts public properties
     * from the object (skipping arrays, objects, and underscore-prefixed internal
     * fields) and updates them in the specified table.
     *
     * The primary key field is used for the WHERE clause and is not updated.
     *
     * @param   string  $table   The name of the database table to update
     * @param   object  &$object A reference to an object whose public properties match table fields
     * @param   string  $key     The name of the primary key field
     * @param   bool    $nulls   True to update null fields, false to ignore them
     * @return  bool
     */
    public function alterObject($table, &$object, $key, $nulls = false)
    {
        // Extract the primary key value
        $pkValue = null;
        if (isset($object->$key)) {
            $pkValue = $object->$key;
        }

        // Build the data array from object properties
        $data = [];
        foreach (get_object_vars($object) as $k => $v) {
            // Skip arrays and objects
            if (is_array($v) || is_object($v)) {
                continue;
            }

            // Skip internal fields (underscore-prefixed)
            if ($k[0] === '_') {
                continue;
            }

            // Skip the primary key field (it goes in WHERE, not SET)
            if ($k === $key) {
                continue;
            }

            // Handle null values based on $nulls parameter
            if ($v === null && !$nulls) {
                continue;
            }

            $data[$k] = $v;
        }

        // Nothing to update
        if (empty($data)) {
            return true;
        }

        // Perform the update using the existing alter method
        return $this->alter($table, $key, $pkValue, $data);
    }

    /**
     * Removes a record by its primary key
     *
     * @param   string  $table    The table to update
     * @param   string  $pkField  The table field serving as primary key
     * @param   mixed   $pkValue  The primary key value
     * @return  bool
     **/
    public function remove($table, $pkField, $pkValue)
    {
        // Make sure we have an id (i.e. don't delete everything in the table!)
        if (is_null($pkValue) || empty($pkValue)) {
            return false;
        }

        // Add delete statement
        $this->delete($table)
             ->whereEquals($pkField, $pkValue);

        // Return result of the query
        return $this->execute();
    }

    /**
     * Builds and executes the current query based on the elements present
     *
     * This is a fairly 'dumb' function, in that it just looks for whichever type was
     * most recently set by one of the primary functions (select, insert, update, delete).
     * Fetch should still be used for select queries as it offers result caching.
     *
     * @FIXME: maybe this should be combined with fetch?
     *
     * @return  mixed
     **/
    public function execute()
    {
        // For databases without native INSERT IGNORE (e.g. Firebird), INSERT ... SELECT
        // fails entirely on the first duplicate. Break into per-row inserts so non-duplicate
        // rows still get inserted while duplicates are silently skipped.
        if ($this->type === 'insert' && $this->syntax->needsRowByRowInsertIgnore()) {
            return $this->executeRowByRowInsertIgnore();
        }

        try {
            $result = $this->query($this->buildQuery($this->type));
        } catch (\Exception $e) {
            // For INSERT IGNORE, silently ignore duplicate key errors
            // (databases without native INSERT IGNORE syntax like Firebird, Oracle, DB2, SQL Server, Informix)
            if ($this->type === 'insert' && $this->syntax->isIgnore()) {
                // Check if this is a PDOException (may be wrapped in QueryFailedException)
                $pdoException = ($e instanceof \PDOException) ? $e : $e->getPrevious();

                if ($pdoException instanceof \PDOException) {
                    $sqlState = $pdoException->getCode();
                    $errorMessage = $pdoException->getMessage();

                    // SQLSTATE 23000 = integrity constraint violation (includes duplicate key)
                    // SQLSTATE 23505 = unique violation (PostgreSQL, DB2)
                    // SQLSTATE HY000 with -803 = Firebird duplicate key error
                    // SQLSTATE HY000 with ORA-00001 = Oracle unique constraint violated
                    $isDuplicateKey = in_array($sqlState, ['23000', '23505']) ||
                        ($sqlState === 'HY000' && (
                            strpos($errorMessage, '-803') !== false ||
                            strpos($errorMessage, 'violation of PRIMARY or UNIQUE KEY') !== false ||
                            strpos($errorMessage, 'UNIQUE KEY constraint') !== false ||
                            strpos($errorMessage, 'ORA-00001') !== false
                        ));

                    if ($isDuplicateKey) {
                        // Silently ignore the duplicate - clear state and return success
                        $this->reset();
                        return true;
                    }
                }
            }
            // Re-throw if not an ignorable duplicate key error
            throw $e;
        }

        // For DELETE, UPDATE, INSERT queries, purge the cache since data has changed
        // This prevents stale cached SELECT results from being returned
        if (in_array($this->type, ['delete', 'update', 'insert', 'upsert'])) {
            self::purgeCache();
        }

        // Clear elements
        $this->reset();

        // Return result of the query
        return $result;
    }

    /**
     * Execute INSERT IGNORE ... SELECT row by row
     *
     * For databases without native INSERT IGNORE syntax, a single
     * INSERT ... SELECT fails entirely on the first duplicate key.
     * This method runs the SELECT first, then inserts each row
     * individually, catching and ignoring duplicate key errors.
     *
     * @return bool
     */
    private function executeRowByRowInsertIgnore(): bool
    {
        $table = $this->syntax->getInsertTable();
        $columns = $this->syntax->getInsertColumns();
        $selectSql = $this->syntax->getInsertSelectQuery();
        $selectBindings = $this->syntax->getInsertSelectBindings();

        // Execute the SELECT to get the rows
        $this->connection->prepare($selectSql)->bind($selectBindings);
        $rows = $this->connection->loadObjectList();

        if (empty($rows)) {
            $this->reset();
            return true;
        }

        // Build quoted column list
        $quotedCols = [];
        foreach ($columns as $col) {
            $quotedCols[] = $this->connection->quoteName($col);
        }
        $colList = implode(', ', $quotedCols);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $quotedTable = $this->connection->quoteName($table);

        $insertSql = "INSERT INTO {$quotedTable} ({$colList}) VALUES ({$placeholders})";

        foreach ($rows as $row) {
            $rowArray = (array) $row;
            $values = array_values($rowArray);

            try {
                $this->connection->prepare($insertSql)->bind($values);
                $this->connection->query();
            } catch (\Exception $e) {
                // Check if this is a duplicate key error — if so, skip silently
                $pdoException = ($e instanceof \PDOException) ? $e : $e->getPrevious();

                if ($pdoException instanceof \PDOException) {
                    $sqlState = $pdoException->getCode();
                    $errorMessage = $pdoException->getMessage();

                    $isDuplicateKey = in_array($sqlState, ['23000', '23505']) ||
                        ($sqlState === 'HY000' && (
                            strpos($errorMessage, '-803') !== false ||
                            strpos($errorMessage, 'violation of PRIMARY or UNIQUE KEY') !== false ||
                            strpos($errorMessage, 'UNIQUE KEY constraint') !== false ||
                            strpos($errorMessage, 'ORA-00001') !== false
                        ));

                    if ($isDuplicateKey) {
                        continue;
                    }
                }

                // Not a duplicate key error — re-throw
                throw $e;
            }
        }

        self::purgeCache();
        $this->reset();
        return true;
    }

    /**
     * Gets the first row of the result set from the database query
     * as an associative array of type: ['field_name' => 'row_value']
     *
     * @return  array|null
     */
    public function loadAssoc()
    {
        return $this->connection
                    ->prepare($this->buildQuery($this->type))
                    ->bind($this->syntax->getBindings())
                    ->loadAssoc();
    }

    /**
     * Gets an array of the result set rows from the database query where each row is an associative array
     * of ['field_name' => 'row_value'].
     *
     * @param   string  $key     The name of a field on which to key the result array
     * @param   string  $column  Instead of the whole row, only this column value will be in the result array
     * @return  array|null
     */
    public function loadAssocList($key = null, $column = null)
    {
        return $this->connection
                    ->prepare($this->buildQuery($this->type))
                    ->bind($this->syntax->getBindings())
                    ->loadAssocList($key, $column);
    }

    /**
     * Gets an array of values from the offset field in each row of the result set from the database query
     *
     * @param   int  $offset  The row offset to use to build the result array
     * @return  array|null
     */
    public function loadColumn($offset = 0)
    {
        return $this->connection
                    ->prepare($this->buildQuery($this->type))
                    ->bind($this->syntax->getBindings())
                    ->loadColumn($offset);
    }

    /**
     * Gets the next row in the result set from the database query as an object
     *
     * @param   string  $class  The class name to use for the returned row object
     * @return  object|bool
     */
    public function loadNextObject($class = 'stdClass')
    {
        return $this->connection
                    ->prepare($this->buildQuery($this->type))
                    ->bind($this->syntax->getBindings())
                    ->loadNextObject($class);
    }

    /**
     * Gets the next row in the result set from the database query as an array
     *
     * @return  array|bool
     */
    public function loadNextRow()
    {
        return $this->connection
                    ->prepare($this->buildQuery($this->type))
                    ->bind($this->syntax->getBindings())
                    ->loadNextRow();
    }

    /**
     * Gets the first row of the result set from the database query as an object
     *
     * @param   string  $class  The class name to use for the returned row object
     * @return  object|null
     */
    public function loadObject($class = 'stdClass')
    {
        return $this->connection
                    ->prepare($this->buildQuery($this->type))
                    ->bind($this->syntax->getBindings())
                    ->loadObject($class);
    }

    /**
     * Gets an array of the result set rows from the database query where each row is an object.
     *
     * @param   string  $key    The name of the field on which to key the result array
     * @param   string  $class  The class name to use for the returned row objects
     * @return  array|null
     */
    public function loadObjectList($key = '', $class = 'stdClass')
    {
        return $this->connection
                    ->prepare($this->buildQuery($this->type))
                    ->bind($this->syntax->getBindings())
                    ->loadObjectList($key, $class);
    }

    /**
     * Gets the first field of the first row of the result set from the database query
     *
     * @return  string|null
     */
    public function loadResult()
    {
        return $this->connection
                    ->prepare($this->buildQuery($this->type))
                    ->bind($this->syntax->getBindings())
                    ->loadResult();
    }

    /**
     * Retrieve the first row from the query
     *
     * @param   string|array|null  $columns  Optional columns to select
     * @return  object|null
     **/
    public function first($columns = null)
    {
        $query = clone $this;

        if ($columns !== null) {
            $query->deselect();

            if (is_array($columns)) {
                foreach ($columns as $column) {
                    $query->select($column);
                }
            } else {
                $query->select($columns);
            }
        }

        $query->limit(1);
        return $query->fetch('row', true);
    }

    /**
     * Retrieve a single column value from the first row
     *
     * @param   string  $column  Column name or expression
     * @return  mixed
     **/
    public function value(string $column)
    {
        $query = clone $this;
        $query->deselect()
            ->select($column)
            ->limit(1);

        $row = $query->fetch('row', true);
        $query->reset();

        if (!$row) {
            return null;
        }

        if (is_object($row)) {
            $vars = get_object_vars($row);
            return $vars ? reset($vars) : null;
        }

        if (is_array($row)) {
            return $row ? reset($row) : null;
        }

        return $row;
    }

    /**
     * Retrieve a list of values from a single column
     *
     * Optionally key the results by another column.
     *
     * @param   string       $column  Column to return
     * @param   string|null  $key     Optional key column
     * @return  array
     **/
    public function pluck(string $column, ?string $key = null): array
    {
        $query = clone $this;
        $query->deselect();

        if ($key !== null) {
            $query->select($key);
        }

        $query->select($column);

        $rows = $query->fetch('rows', true);
        $query->reset();

        if (!$rows) {
            return [];
        }

        $results = [];
        foreach ($rows as $row) {
            if (is_object($row)) {
                $value = $row->{$column} ?? null;
                if ($key !== null) {
                    $results[$row->{$key}] = $value;
                } else {
                    $results[] = $value;
                }
            } else {
                $value = $row[$column] ?? null;
                if ($key !== null) {
                    $results[$row[$key]] = $value;
                } else {
                    $results[] = $value;
                }
            }
        }

        return $results;
    }

    /**
     * Retrieve rows keyed by a specific column
     *
     * @param   string  $column  Column to key results by
     * @return  array
     **/
    public function keyBy(string $column): array
    {
        $query = clone $this;
        if ($query->type !== 'select') {
            $query->select('*');
        }

        $rows = $query->fetch('rows', true);
        $query->reset();

        if (!$rows) {
            return [];
        }

        $results = [];
        foreach ($rows as $row) {
            if (is_object($row)) {
                $results[$row->{$column}] = $row;
            } else {
                $results[$row[$column]] = $row;
            }
        }

        return $results;
    }

    /**
     * Gets the first row of the result set from the database query as an array
     *
     * @return  array|null
     */
    public function loadRow()
    {
        return $this->connection
                    ->prepare($this->buildQuery($this->type))
                    ->bind($this->syntax->getBindings())
                    ->loadRow();
    }

    /**
     * Gets an array of the result set rows from the database query where each row is an array.
     *
     * @param   string  $key  The name of a field on which to key the result array
     * @return  array|null
     */
    public function loadRowList($key = null)
    {
        return $this->connection
                    ->prepare($this->buildQuery($this->type))
                    ->bind($this->syntax->getBindings())
                    ->loadRowList($key);
    }

    /**
     * Performs the actual query and returns the results
     *
     * @param   string  $query      The query to perform
     * @param   string  $structure  The structure of the item(s) returned (if applicable)
     * @return  mixed
     **/
    public function query($query, $structure = null)
    {
        // Check the type of query to decide what to return
        list($type) = explode(' ', $query, 2);
        $type       = strtolower($type);

        // Default structure if needed
        if ($type == 'select' && is_null($structure)) {
            $structure = 'rows';
        }

        $this->connection->prepare($query)->bind($this->syntax->getBindings());

        $result = (isset($structure))
                ? $this->connection->{constant('self::' . strtoupper($structure))}()
                : $this->connection->query();

        return $result;
    }

    /**
     * Retrieves the current query as a string (without executing it)
     *
     * @return  string
     **/
    public function toString()
    {
        return $this->connection
                    ->prepare($this->buildQuery($this->type))
                    ->bind($this->syntax->getBindings())
                    ->toString();
    }

    /**
     * Retrieves the current query as a string (without executing it)
     *
     * @return  string
     **/
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Builds query based on the current query elements established
     *
     * @param   string  $type  The type of query to build
     * @return  string
     **/
    private function buildQuery($type = 'select')
    {
        // Clear bindings before building to prevent duplicates when buildQuery is called multiple times
        $this->syntax->clearBindings();

        $pieces = array();

        // Loop through query elements
        foreach ($this->$type as $piece) {
            // If we have one of these elements, get its string value
            if ($element = $this->syntax->build($piece)) {
                $pieces[] = $element;
            }
        }

        return implode("\n", $pieces);
    }

    /**
     * Builds a query for use as a UNION member
     *
     * Excludes ORDER BY and LIMIT clauses which have no effect
     * within a UNION member per the SQL standard and cause syntax
     * errors on strict SQL implementations like Firebird.
     *
     * @return  string
     */
    private function buildUnionMemberQuery()
    {
        $this->syntax->clearBindings();

        $pieces = [];
        $elements = ['select', 'from', 'join', 'where', 'group', 'having'];

        foreach ($elements as $piece) {
            if ($element = $this->syntax->build($piece)) {
                $pieces[] = $element;
            }
        }

        return implode("\n", $pieces);
    }

    /**
     * Resets the query elements
     *
     * @return  void
     **/
    private function reset()
    {
        // Reset the syntax element
        $syntaxClass  = $this->resolveSyntaxClass((string) $this->connection->getSyntax());
        $this->syntax = new $syntaxClass($this->connection);

        // Reset condition group tracking
        $this->groupDepth = 0;
        $this->pendingGroupLogical = null;

        // Reset named parameters
        $this->namedParameters = [];
        $this->hasSelect = false;
    }

    /**
     * Resolve and validate the concrete syntax class for the current connection.
     *
     * @param   string  $syntaxName
     * @return  string
     */
    private function resolveSyntaxClass(string $syntaxName): string
    {
        $key = strtolower(trim($syntaxName));

        if ($key === '') {
            throw new \InvalidArgumentException(
                'Database syntax is empty. Ensure the driver returns a valid syntax name.'
            );
        }

        if (isset(self::SYNTAX_ALIASES[$key])) {
            $key = self::SYNTAX_ALIASES[$key];
        }

        if (isset(self::SYNTAX_CLASS_MAP[$key])) {
            return $this->validateSyntaxClass(self::SYNTAX_CLASS_MAP[$key], $syntaxName);
        }

        // Backward-compatible fallback for custom syntax classes that follow
        // the Hubzero\Database\Syntax\<Name> convention.
        $fallback = '\\Hubzero\\Database\\Syntax\\' . ucfirst($key);
        if (class_exists($fallback)) {
            return $this->validateSyntaxClass($fallback, $syntaxName);
        }

        throw new \InvalidArgumentException(
            'Unsupported database syntax "' . $syntaxName . '". Supported syntax names: '
            . implode(', ', array_keys(self::SYNTAX_CLASS_MAP))
        );
    }

    /**
     * Ensure a resolved syntax class is a valid Hubzero SQL syntax implementation.
     *
     * @param   string  $syntaxClass
     * @param   string  $originalName
     * @return  string
     */
    private function validateSyntaxClass(string $syntaxClass, string $originalName): string
    {
        if (!is_subclass_of($syntaxClass, '\\Hubzero\\Database\\Syntax\\Sql')) {
            throw new \InvalidArgumentException(
                'Invalid syntax class "' . $syntaxClass . '" resolved from syntax "'
                . $originalName . '". Class must extend Hubzero\\Database\\Syntax\\Sql.'
            );
        }

        return $syntaxClass;
    }

    // =========================================================================
    // Persistent Cache Operations
    // =========================================================================
    //
    // Query caching provides two layers: in-memory (single request) and
    // persistent (across requests). Use these methods to cache expensive
    // queries and reduce database load.
    //
    // ## Cache Layers
    //
    // | Layer      | Scope           | Backend                       | Cleared By           |
    // |------------|-----------------|-------------------------------|----------------------|
    // | In-memory  | Single request  | Static array ($cache)         | purgeCache()         |
    // | Persistent | Across requests | Injected store or APCu        | forgetCached(), TTL  |
    //
    // ## Public Methods
    //
    // | Method          | Returns  | Description                               |
    // |-----------------|----------|-------------------------------------------|
    // | remember($min)  | $this    | Cache results for N minutes               |
    // | rememberForever | $this    | Cache results with no expiration          |
    // | forgetCached()  | bool     | Invalidate a specific cache key           |
    // | setCacheStore() | void     | Set persistent cache backend (static)     |
    // | getCacheStore() | object   | Get current cache backend (static)        |
    // | purgeCache()    | void     | Clear in-memory cache only (static)       |
    //
    // ## Cache Resolution Order (Persistent)
    //
    // 1. Injected cache store (set via Query::setCacheStore())
    // 2. APCu extension (automatic fallback if available)
    // 3. In-memory only (no persistence when neither is available)
    //
    // ## Usage Examples
    //
    // Basic caching (60 minutes):
    //   $users = $query->from('users')->whereEquals('active', 1)->remember(60)->fetch();
    //
    // Cache forever (use sparingly):
    //   $config = $query->from('config')->rememberForever()->fetch();
    //
    // Invalidate cache:
    //   $query->forgetCached('users_active');
    //
    // Set custom cache backend:
    //   Query::setCacheStore(App::get('cache')->storage());
    //
    // Model-level caching (via Relational):
    //   Article::all()->whereEquals('published', 1)->remember(30)->rows();
    //
    // =========================================================================

    /**
     * Check if persistent caching is enabled for this query
     *
     * @return  bool
     **/
    private function isPersistentCacheEnabled(): bool
    {
        return $this->cacheTtl > 0 || $this->cacheForever;
    }

    /**
     * Check if persistent cache is available
     *
     * Cache resolution order:
     * 1. Injected cache store (if set via setCacheStore)
     * 2. APCu (if extension is loaded)
     *
     * @return  bool
     **/
    private function hasPersistentCache(): bool
    {
        // First check injected store
        if (self::$cacheStore !== null) {
            return true;
        }

        // Fall back to APCu
        return function_exists('apcu_fetch');
    }

    /**
     * Get a value from persistent cache
     *
     * @param   string  $key  The cache key
     * @return  mixed   The cached value or null if not found
     **/
    private function persistentGet(string $key)
    {
        // Try injected store first
        if (self::$cacheStore !== null) {
            $result = self::$cacheStore->get($key);
            // Most cache stores return false or null for misses
            return ($result !== false && $result !== null) ? $result : null;
        }

        // Fall back to APCu
        if (function_exists('apcu_fetch')) {
            $success = false;
            $result = apcu_fetch($key, $success);
            return $success ? $result : null;
        }

        return null;
    }

    /**
     * Store a value in persistent cache
     *
     * @param   string  $key      The cache key
     * @param   mixed   $value    The value to cache
     * @param   int     $minutes  TTL in minutes (0 = forever)
     * @return  bool    True on success
     **/
    private function persistentPut(string $key, $value, int $minutes)
    {
        $seconds = $minutes * 60;

        // Try injected store first
        if (self::$cacheStore !== null) {
            return (bool) self::$cacheStore->put($key, $value, $minutes);
        }

        // Fall back to APCu
        if (function_exists('apcu_store')) {
            return apcu_store($key, $value, $seconds);
        }

        return false;
    }

    /**
     * Remove a value from persistent cache
     *
     * @param   string  $key  The cache key
     * @return  bool    True on success
     **/
    private function persistentForget(string $key): bool
    {
        // Try injected store first
        if (self::$cacheStore !== null) {
            return (bool) self::$cacheStore->forget($key);
        }

        // Fall back to APCu
        if (function_exists('apcu_delete')) {
            return apcu_delete($key);
        }

        return false;
    }

    /**
     * Determine if we should process named placeholders in a SQL string
     *
     * Returns true if:
     * 1. Bindings array is associative (contains named parameters), OR
     * 2. SQL contains :name placeholders AND we have stored named parameters
     *
     * This allows two usage patterns:
     * - Inline: whereRaw('x = :x', ['x' => 1])
     * - Stored: setParameter('x', 1)->whereRaw('x = :x')
     *
     * @param   string  $sql       The SQL string to check
     * @param   array   $bindings  The bindings array passed to the method
     * @return  bool
     **/
    private function shouldProcessNamedPlaceholders(string $sql, array $bindings): bool
    {
        // Case 1: Bindings are associative (named inline)
        if (!empty($bindings) && $this->isAssociativeArray($bindings)) {
            return true;
        }

        // Case 2: No bindings but SQL has :placeholders and we have stored params
        if (empty($bindings) && !empty($this->namedParameters)) {
            // Check if SQL contains named placeholders
            return $this->containsNamedPlaceholders($sql);
        }

        return false;
    }

    /**
     * Check if a SQL string contains named placeholders (:name)
     *
     * @param   string  $sql  The SQL string to check
     * @return  bool
     **/
    private function containsNamedPlaceholders(string $sql): bool
    {
        return (bool) preg_match('/:([a-zA-Z_][a-zA-Z0-9_]*)/', $sql);
    }

    /**
     * Check if an array is associative (has string keys)
     *
     * Used to detect named parameter bindings vs positional bindings.
     * An associative array indicates named placeholders (:name).
     * A sequential/numeric array indicates positional placeholders (?).
     *
     * @param   array  $array  The array to check
     * @return  bool   True if associative (string keys), false if sequential
     **/
    private function isAssociativeArray(array $array): bool
    {
        if (empty($array)) {
            return false;
        }

        // Check if any key is a non-numeric string
        foreach (array_keys($array) as $key) {
            if (is_string($key) && !is_numeric($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert named placeholders to positional placeholders
     *
     * Transforms SQL with :named placeholders to use ? positional placeholders,
     * and returns the bindings in the correct order based on placeholder appearance.
     *
     * This allows the same named placeholder to be used multiple times in the query,
     * with the value automatically duplicated in the correct positions.
     *
     * Parameters are resolved in this order:
     * 1. Inline parameters passed to the method (highest priority)
     * 2. Parameters set via setParameter()/setParameters() (stored parameters)
     *
     * Example with inline parameters:
     *   whereRaw('status = :status', ['status' => 1])
     *
     * Example with stored parameters:
     *   setParameter('status', 1)->whereRaw('status = :status')
     *
     * Example mixing both (inline overrides stored):
     *   setParameter('status', 1)
     *     ->whereRaw('status = :status AND type = :type', ['type' => 'article'])
     *
     * @param   string  $sql     SQL string with :named placeholders
     * @param   array   $params  Associative array of placeholder => value (inline bindings)
     * @return  array   [converted_sql, ordered_bindings]
     * @throws  \InvalidArgumentException  If a named placeholder is missing from both sources
     **/
    private function convertNamedToPositional(string $sql, array $params): array
    {
        $bindings = [];

        // Merge stored parameters with inline params (inline takes priority)
        $mergedParams = array_merge($this->namedParameters, $params);

        // Find all :named placeholders and replace with ? while building bindings
        $converted = preg_replace_callback(
            '/:([a-zA-Z_][a-zA-Z0-9_]*)/',
            function ($matches) use ($mergedParams, &$bindings) {
                $name = $matches[1];

                if (!array_key_exists($name, $mergedParams)) {
                    throw new \InvalidArgumentException(
                        "Missing named parameter ':$name' in query bindings. " .
                        "Set it via setParameter('$name', \$value) or pass in the bindings array."
                    );
                }

                $bindings[] = $mergedParams[$name];
                return '?';
            },
            $sql
        );

        return [$converted, $bindings];
    }

    /**
     * Build a subquery from a closure
     *
     * Creates a fresh Query instance, passes it to the callback,
     * and returns the generated SQL and bindings.
     *
     * Example:
     *   $result = $this->buildSubquery(function($query) {
     *       $query->select('id')->from('users')->whereEquals('active', 1);
     *   });
     *   // Returns: ['SELECT id FROM users WHERE active = ?', [1]]
     *
     * @param   callable  $callback  Closure that receives a Query instance
     * @return  array     [sql, bindings]
     **/
    protected function buildSubquery(callable $callback): array
    {
        // Create a fresh query instance with the same connection
        $subquery = new self($this->connection);

        // Execute the callback to build the subquery
        $callback($subquery);

        // Build the SELECT query and get the SQL
        $sql = $subquery->buildQuery('select');

        // Get bindings from the subquery
        $bindings = $subquery->syntax->getBindings();

        return [$sql, $bindings];
    }

    // =========================================================================
    // Query Debugging
    // =========================================================================

    /**
     * Get the SQL representation of the query with placeholders
     *
     * Returns the SQL string with `?` placeholders for bound parameters.
     * This is useful for debugging and logging.
     *
     * Example:
     * ```php
     * $sql = Article::all()->whereEquals('status', 'published')->toSql();
     * // "SELECT * FROM `#__articles` WHERE `status` = ?"
     * ```
     *
     * @param   string  $type  The query type (select, insert, update, delete)
     * @return  string
     */
    public function toSql($type = null)
    {
        return $this->buildQuery($type ?: $this->type);
    }

    /**
     * Get the current query bindings
     *
     * Returns an array of values that will be bound to the query placeholders.
     *
     * Example:
     * ```php
     * $bindings = Article::all()->whereEquals('status', 'published')->getBindings();
     * // ['published']
     * ```
     *
     * @return  array
     */
    public function getBindings()
    {
        // Build the query first to ensure bindings are populated
        $this->buildQuery($this->type);
        return $this->syntax->getBindings();
    }

    /**
     * Get the raw SQL with bindings substituted
     *
     * Returns the SQL string with actual values substituted for placeholders.
     * This is for debugging only - never execute this string directly as it
     * may be vulnerable to SQL injection.
     *
     * Example:
     * ```php
     * $sql = Article::all()->whereEquals('status', 'published')->toRawSql();
     * // "SELECT * FROM `#__articles` WHERE `status` = 'published'"
     * ```
     *
     * @param   string  $type  The query type (select, insert, update, delete)
     * @return  string
     */
    public function toRawSql($type = 'select')
    {
        $sql = $this->toSql($type);
        $bindings = $this->getBindings();

        // Replace each placeholder with its corresponding binding
        foreach ($bindings as $binding) {
            if ($binding === null) {
                $value = 'NULL';
            } elseif (is_bool($binding)) {
                $value = $binding ? '1' : '0';
            } elseif (is_int($binding) || is_float($binding)) {
                $value = (string) $binding;
            } else {
                // Escape single quotes for string values
                $value = "'" . addslashes((string) $binding) . "'";
            }

            // Replace first occurrence of ?
            $pos = strpos($sql, '?');
            if ($pos !== false) {
                $sql = substr_replace($sql, $value, $pos, 1);
            }
        }

        return $sql;
    }

    /**
     * Dump the query SQL and bindings for debugging
     *
     * Outputs the SQL and bindings to the screen. Useful for quick debugging.
     * Returns $this for method chaining.
     *
     * Example:
     * ```php
     * Article::all()->whereEquals('status', 'published')->dump()->rows();
     * ```
     *
     * @param   string  $type  The query type (select, insert, update, delete)
     * @return  $this
     */
    public function dump($type = 'select')
    {
        $sql = $this->toSql($type);
        $bindings = $this->getBindings();
        $rawSql = $this->toRawSql($type);

        echo "\n";
        echo "=== Query Debug ===\n";
        echo "SQL: " . $sql . "\n";
        echo "Bindings: " . print_r($bindings, true);
        echo "Raw SQL: " . $rawSql . "\n";
        echo "===================\n";
        echo "\n";

        return $this;
    }

    /**
     * Dump the query SQL and bindings, then terminate execution
     *
     * Same as dump() but also calls exit(). Useful for quick debugging
     * when you want to see the query and stop execution.
     *
     * Example:
     * ```php
     * Article::all()->whereEquals('status', 'published')->dd();
     * ```
     *
     * @param   string  $type  The query type (select, insert, update, delete)
     * @return  never
     */
    public function dd($type = 'select')
    {
        $this->dump($type);
        exit(1);
    }

    /**
     * Get debug information about the query as an array
     *
     * Returns an associative array with query debug information.
     * Useful for logging or custom debug output.
     *
     * Example:
     * ```php
     * $debug = Article::all()->whereEquals('status', 'published')->getDebugInfo();
     * // ['sql' => '...', 'bindings' => [...], 'raw_sql' => '...']
     * ```
     *
     * @param   string  $type  The query type (select, insert, update, delete)
     * @return  array
     */
    public function getDebugInfo($type = 'select')
    {
        return [
            'sql' => $this->toSql($type),
            'bindings' => $this->getBindings(),
            'raw_sql' => $this->toRawSql($type),
        ];
    }
}
