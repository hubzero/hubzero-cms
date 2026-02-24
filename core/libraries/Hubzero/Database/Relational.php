<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

use Hubzero\Database\Relationship\BelongsToOne;
use Hubzero\Database\Relationship\OneToMany;
use Hubzero\Database\Relationship\ManyToMany;
use Hubzero\Database\Relationship\OneToManyThrough;
use Hubzero\Database\Relationship\OneToOneThrough;
use Hubzero\Database\Relationship\OneToOne;
use Hubzero\Database\Relationship\OneShiftsToMany;
use Hubzero\Database\Relationship\ManyShiftsToMany;
use Hubzero\Database\Relationship\MorphTo;
use Hubzero\Database\Relationship\MorphOne;
use Hubzero\Database\Relationship\MorphMany;
use Hubzero\Database\Relationship\MorphToMany;
use Hubzero\Database\Exception\BadMethodCallException;
use Hubzero\Database\Exception\RuntimeException;
use Closure;

/**
 * Database ORM base class
 *
 * This class provides ActiveRecord-style ORM functionality including relationships,
 * query building, and model lifecycle hooks.
 *
 * ## Query Scopes
 *
 * Models can define reusable query constraints as "scope" methods. Define a protected
 * method prefixed with `scope` that accepts the query as its first argument:
 *
 * ```php
 * protected function scopePublished($query)
 * {
 *     $query->whereEquals('state', 1);
 * }
 *
 * protected function scopeRecent($query, $days = 30)
 * {
 *     $query->where('created', '>=', date('Y-m-d H:i:s', strtotime('-' . $days . ' days')));
 * }
 * ```
 *
 * Then call the scope without the `scope` prefix:
 *
 * ```php
 * Entry::published()->recent(7)->rows();
 * ```
 *
 * ### IDE Support for Scopes
 *
 * Since scopes use magic methods, IDEs cannot autocomplete them by default.
 * Add `@method` annotations to your model's class docblock for IDE support:
 *
 * ```php
 * /**
 *  * @method static self published()
 *  * @method static self recent(int $days = 30)
 *  * /
 * class Entry extends Relational { ... }
 * ```
 *
 * ## Dirty Tracking
 *
 * Models automatically track which attributes have been modified since loading from
 * the database. This is useful for:
 * - Knowing which fields changed before saving
 * - Sending notifications only when specific fields change
 * - Logging changes for audit purposes
 *
 * ```php
 * $article = Article::oneOrFail(123);
 * $article->set('title', 'New Title');
 *
 * // Check if anything changed
 * $article->isDirty();              // true
 * $article->isDirty('title');       // true
 * $article->isDirty('created_at');  // false
 *
 * // Get all changed attributes
 * $article->getDirty();             // ['title' => 'New Title']
 *
 * // Get original value
 * $article->getOriginal('title');   // 'Old Title'
 *
 * // Discard changes
 * $article->discardChanges('title');  // Reverts title to original
 * $article->discardChanges();         // Reverts all changes
 *
 * // After saving
 * $article->save();
 * $article->wasChanged('title');    // true (what changed in last save)
 * $article->getChanges();           // ['title' => 'New Title']
 * $article->isDirty();              // false (synced after save)
 * ```
 *
 * ### Using in Event Handlers
 *
 * ```php
 * public function onContentSave($table, $model, $changes)
 * {
 *     // Only notify when status changes to published
 *     if ($model->wasChanged('status') && $model->get('status') == 'published') {
 *         $this->sendNotification($model);
 *     }
 * }
 * ```
 *
 * ## Global Scopes
 *
 * Global scopes are query constraints that are automatically applied to EVERY query
 * on a model. They're useful for:
 * - Multi-tenancy (always filter by tenant_id)
 * - Soft deletes (always exclude deleted_at IS NOT NULL)
 * - Active filtering (always filter by active = 1)
 *
 * ### Defining Global Scopes
 *
 * Override the static `boot()` method to register global scopes:
 *
 * ```php
 * class Article extends Relational
 * {
 *     protected static function boot()
 *     {
 *         parent::boot();
 *
 *         // Add a closure-based global scope
 *         static::addGlobalScope('active', function ($query) {
 *             $query->whereEquals('active', 1);
 *         });
 *     }
 * }
 * ```
 *
 * ### Bypassing Global Scopes
 *
 * Sometimes you need to query without certain (or all) global scopes:
 *
 * ```php
 * // Remove all global scopes
 * Article::withoutGlobalScopes()->rows();
 *
 * // Remove specific scopes
 * Article::withoutGlobalScopes(['active', 'published'])->rows();
 *
 * // Remove a single scope
 * Article::withoutGlobalScope('active')->rows();
 * ```
 *
 * //@FIXME: handle dates
 *
 * @uses  \Hubzero\Database\Exception\BadMethodCallException  to handle calls to undefined methods
 * @uses  \Hubzero\Database\Exception\RuntimeException        to handle scenarios with undefined rows
 */

/** @phpstan-consistent-constructor */
class Relational implements \IteratorAggregate, \ArrayAccess
{
    /*
     * Errors trait for error message handling
     **/
    use Traits\ErrorBag;

    /**
     * Database state constants
     **/
    public const STATE_UNPUBLISHED = 0;
    public const STATE_PUBLISHED   = 1;
    public const STATE_DELETED     = 2;

    /**
     * Database access constants
     **/
    public const ACCESS_PUBLIC     = 1;
    public const ACCESS_REGISTERED = 2;
    public const ACCESS_PRIVATE    = 4;

    /**
     * The database model name
     *
     * This will defined as the static/calling class' name.
     * It's used when building relationships between classes.
     *
     * @public string
     **/
    private $modelName = null;

    /**
     * The database model namespace
     *
     * @public string
     **/
    private $modelNamespace = null;

    /**
     * The internal array of methods of this model
     *
     * We do a lot of reflection checks on the model,
     * so this should save us some time by storing the results
     * for future reference.
     *
     * @public array
     **/
    private $methods = [];

    /**
     * The database query object
     *
     * @public \Hubzero\Database\Query
     **/
    private $query = null;

    /**
     * The database connection used by the query object
     *
     * @public \Hubzero\Database\Driver|object
     **/
    public static $connection = null;

    /**
     * Resolver for current user ID (callable returning int)
     *
     * @var callable|null
     */
    protected static $userIdResolver = null;

    /**
     * Resolver for user object by ID (callable accepting int, returning object|null)
     *
     * @var callable|null
     */
    protected static $userResolver = null;

    /**
     * Whether or not we're caching query results
     *
     * @public string
     **/
    private $noCache = false;

    /**
     * The relationships on this model
     *
     * @public array
     **/
    private $relationships = [];

    /**
     * Original IDs of related models for orphan removal tracking
     *
     * When a relationship with orphanRemoval enabled is loaded, we store
     * the original IDs here. On save, we compare current IDs to find orphans.
     *
     * @public array
     **/
    private $originalRelationshipIds = [];

    /**
     * Runtime relationship registry.
     *
     * @var RelationshipRegistry|null
     */
    protected static ?RelationshipRegistry $relationshipRegistry = null;

    /**
     * The forwards for the model (i.e. other places to look for attributes)
     *
     * @public array
     **/
    private $forwards = [];

    /**
     * The includes set on the model for eager loading
     *
     * @public array
     **/
    private $includes = [];

    /**
     * The model data returned as the result of a query, or set for saving
     *
     * @public array
     **/
    private $attributes = [];

    /**
     * The original attributes as loaded from the database
     *
     * Used for dirty tracking to determine which attributes have changed.
     *
     * @var array
     **/
    private $original = [];

    /**
     * The attributes that were changed during the last save operation
     *
     * Populated after save() so event handlers can see what changed.
     *
     * @var array
     **/
    private $changes = [];

    /**
     * The parent iterator if this model was retrieved as part of a larger rows collection
     *
     * @public \Hubzero\Database\Rows
     **/
    private $collection = null;

    /**
     * The table to which the class pertains
     *
     * This will default to #__{namespace}_{modelName} unless otherwise
     * overwritten by a given subclass. Definition of this property likely
     * indicates some derivation from standard naming conventions.
     *
     * @public string
     **/
    protected $table = null;

    /**
     * An alias to apply to the table for initial query building
     *
     * @public string
     **/
    protected $tableAlias = null;

    /**
     * The table namespace
     *
     * This is likely just the component name, and will most likely
     * be set by all subclasses. This follows the convention of
     * prefixing/namespacing database tables with #__componentname_*.
     *
     * @FIXME: could we infer this once our models are properly namespaced?
     *
     * @public string
     **/
    protected $namespace = null;

    /**
     * The table primary key name
     *
     * It defaults to 'id', but can be overwritten by a subclass.
     *
     * @public string
     **/
    protected $pk = 'id';

    /**
     * Fields that have content that can/should be parsed
     *
     * @public array
     **/
    protected $parsed = [];

    /**
     * Fields and their validation criteria
     *
     * Rules can be defined as:
     * - String: 'notempty|email' - applies to all scenarios (BC)
     * - Callable: function($data) { ... } - applies to all scenarios (BC)
     * - Array with 'rule' key: ['rule' => 'notempty', 'on' => 'register']
     *
     * The 'on' key specifies which scenario(s) the rule applies to:
     * - String: 'register' - only in register scenario
     * - Array: ['register', 'update'] - in multiple scenarios
     * - Omitted: applies to all scenarios
     *
     * @public array
     * @see  \Hubzero\Database\Rules
     **/
    protected $rules = [];

    /**
     * The current validation scenario
     *
     * Scenarios allow different validation rules to apply in different contexts.
     * For example, 'register' may require password confirmation while 'login' does not.
     *
     * @var string
     */
    protected $scenario = 'default';

    /**
     * Default order by for select queries
     *
     * This can be overwritten in a model or by calling
     * the order method on the query object.
     *
     * @public string
     **/
    public $orderBy = 'id';

    /**
     * Default order direction for select queries
     *
     * @public string
     **/
    public $orderDir = 'asc';

    /**
     * The pagination object
     *
     * This will also get set on the rows object if applicable.
     *
     * @public \Hubzero\Database\Pagination|null
     **/
    public $pagination = null;

    /**
     * Automatic fields to populate every time a row is touched
     *
     * @public array
     **/
    public $always = [];

    /**
     * Automatic fields to populate every time a row is created
     *
     * @public array
     **/
    public $initiate = [];

    /**
     * Automatic fields to populate every time a row is updated
     *
     * @public array
     **/
    public $renew = [];

    /**
     * Any associative elements
     *
     * @public object
     **/
    public $associated = null;

    /**
     * Cached list of class methods
     *
     * @public array
     **/
    private static $classMethods = [];

    /**
     * Cached introspected relationship names per model class
     *
     * @var array
     **/
    private static $introspectedRelationships = [];

    /**
     * Global scopes registered on each model class
     *
     * Indexed by fully-qualified class name, then by scope name.
     * Each scope is either a Closure or an object with an apply() method.
     *
     * @var array
     */
    protected static $globalScopes = [];

    /**
     * Tracks which model classes have been booted
     *
     * @var array
     */
    protected static $booted = [];

    /**
     * Global scopes that have been removed for this query instance
     *
     * @var array
     */
    protected $removedScopes = [];

    /**
     * Whether this model dispatches lifecycle events.
     *
     * Defaults to false for backwards compatibility. Models that want
     * Eloquent-style events (retrieved, creating, saving, etc.) should
     * set this to true.
     *
     * @var bool
     */
    protected $dispatchesModelEvents = false;

    /**
     * Whether to enable cascade relationship operations (save, delete, orphan removal).
     *
     * Defaults to false for backwards compatibility. Models that define
     * relationships with cascadeOnSave(), cascadeOnDelete(), or removeOrphans()
     * should set this to true.
     *
     * @var bool
     */
    protected $cascadeRelationships = false;

    /**
     * Model event callbacks registered on each model class
     *
     * Indexed by fully-qualified class name, then by event name.
     * Events: retrieved, creating, created, updating, updated, saving, saved, deleting, deleted
     *
     * @var array
     */
    protected static $modelEvents = [];

    /**
     * The registered morph type to class mappings
     *
     * Used by polymorphic relationships to map type identifiers (like 'post', 'video')
     * to their corresponding model classes. This allows using simple strings in the
     * database instead of fully-qualified class names.
     *
     * Register mappings using morphMap():
     * ```php
     * Relational::morphMap([
     *     'post'  => Post::class,
     *     'video' => Video::class,
     *     'photo' => Photo::class,
     * ]);
     * ```
     *
     * @var array
     */
    protected static $morphMap = [];

    /**
     * The attributes that should be cast to native types
     *
     * Define this property in your model to enable automatic type casting.
     * This provides Laravel-compatible attribute casting with the same syntax.
     *
     * ```php
     * protected $casts = [
     *     // Primitive types
     *     'is_active'    => 'boolean',       // 0/1, "true"/"false" <-> true/false
     *     'view_count'   => 'integer',       // String <-> int
     *     'rating'       => 'float',         // String <-> float
     *     'price'        => 'decimal:2',     // Fixed precision: "19.99" (avoids float issues)
     *
     *     // JSON types
     *     'settings'     => 'array',         // JSON string <-> array
     *     'metadata'     => 'object',        // JSON string <-> stdClass
     *     'items'        => 'collection',    // JSON string <-> ArrayObject
     *
     *     // Date/time types
     *     'published_at' => 'datetime',      // String <-> DateTime
     *     'expires_at'   => 'datetime:Y-m-d H:i', // DateTime with custom format
     *     'created_date' => 'date',          // String <-> DateTime (time = 00:00:00)
     *     'last_login'   => 'timestamp',     // String <-> Unix timestamp integer
     *
     *     // Custom cast classes (implement CastsAttributes interface)
     *     'options'      => \Hubzero\Database\Casts\AsJson::class,
     *     'tags'         => \Hubzero\Database\Casts\AsCollection::class,
     *     'secret'       => \Hubzero\Database\Casts\AsEncryptedString::class,
     * ];
     * ```
     *
     * Supported cast types:
     * - integer, int: Cast to PHP integer
     * - float, double, real: Cast to PHP float
     * - decimal:N: Cast to string with N decimal places (e.g., 'decimal:2' -> "19.99")
     * - boolean, bool: Cast to PHP boolean (handles 0/1, "0"/"1", "true"/"false")
     * - string: Cast to PHP string
     * - array: JSON decode to array, JSON encode when setting
     * - object: JSON decode to stdClass, JSON encode when setting
     * - collection: JSON decode to ArrayObject
     * - datetime: Parse to DateTime object, format to SQL datetime when setting
     * - datetime:format: DateTime with custom format (e.g., 'datetime:Y-m-d H:i')
     * - date: Parse to DateTime (midnight), format to SQL date when setting
     * - timestamp: Parse to Unix timestamp integer
     * - Custom class: Any class implementing Casts\CastsAttributes interface
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Cache for cast attribute values
     *
     * Prevents repeated casting of the same attribute during a single request.
     *
     * @var array
     */
    private $castCache = [];

    /**
     * Cache for custom cast class instances
     *
     * @var array
     */
    private static $customCasters = [];

    /**
     * Constructs an object instance
     *
     * @return  void
     **/
    public function __construct()
    {
        $r = new \ReflectionClass($this);

        // Set model name
        $this->modelName = $r->getShortName();
        $this->modelNamespace = $r->getNamespaceName();

        // If table name isn't explicitly set, build it
        $namespace   = (!$this->namespace ? '' : $this->namespace . '_');
        $plural      = static::pluralize(strtolower($this->getModelName()));
        $this->table = $this->table ?: '#__' . $namespace . $plural;

        // Note: Query object is now lazy-loaded to avoid database connection
        // during construction. Methods that need the query check for null
        // and call newQuery() if needed.

        // Store methods for later
        //
        // Here we store the methods per class name. This allows for quicker
        // lookup and less memory usage when dealing with multiple classes
        // of the same type (i.e., a listing of records).
        $key = $r->getName();
        if (!isset(self::$classMethods[$key])) {
            self::$classMethods[$key] = get_class_methods($this);

            $this->methods = self::$classMethods[$key];
        }
        $this->methods = self::$classMethods[$key];

        // Run extra setup. This is so subclasses don't have to overwrite
        // the constructor and then call parent::__construct().
        // They can instead just add a setup() method.
        $this->setup();
    }

    /**
     * Plural inflector rules for deriving table names from model names
     *
     * NOTE: Duplicated from Hubzero\Utility\Inflector to keep the Database
     * package free of external dependencies. Changes to these rules should
     * be kept in sync with Inflector::$plural_rules.
     *
     * @var array
     */
    private static $pluralRules = [
        '/^(ox)$/i'                 => '\1\2en',
        '/([m|l])ouse$/i'           => '\1ice',
        '/(matr|vert|ind)ix|ex$/i'  => '\1ices',
        '/(x|ch|ss|sh)$/i'          => '\1es',
        '/([^aeiouy]|qu)y$/i'       => '\1ies',
        '/(hive)$/i'                => '\1s',
        '/(?:([^f])fe|([lr])f)$/i'  => '\1\2ves',
        '/sis$/i'                   => 'ses',
        '/([ti])um$/i'              => '\1a',
        '/(p)erson$/i'              => '\1eople',
        '/(m)an$/i'                 => '\1en',
        '/(c)hild$/i'               => '\1hildren',
        '/(buffal|tomat)o$/i'       => '\1\2oes',
        '/(bu|campu)s$/i'           => '\1\2ses',
        '/(alias|status|virus)$/i'  => '\1es',
        '/(octop)us$/i'             => '\1i',
        '/(ax|cris|test)is$/i'      => '\1es',
        '/s$/'                      => 's',
        '/$/'                       => 's',
    ];

    /**
     * Words that do not have a plural form
     *
     * @var array
     */
    private static $uncountable = [
        'equipment', 'information', 'rice', 'money',
        'species', 'series', 'fish', 'meta', 'metadata',
        'buffalo', 'elk', 'rhinoceros', 'salmon',
        'bison', 'headquarters', 'moose',
    ];

    /**
     * Pluralize a word using English inflection rules
     *
     * @param   string  $word  The word to pluralize
     * @return  string
     */
    protected static function pluralize(string $word): string
    {
        if (in_array(strtolower($word), static::$uncountable)) {
            return $word;
        }

        foreach (static::$pluralRules as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }

    /**
     * Processes calls to inaccessible or undefined instance methods
     *
     * Checks for methods in this order:
     * 1. Helper methods (helper{Name})
     * 2. Transformer methods (transformer{Name})
     * 3. Parsable fields
     * 4. Query scope methods (scope{Name}) - reusable query constraints
     * 5. Query class methods (forwarded to the query builder)
     * 6. Dynamic relationship definitions (acquaintances)
     *
     * Query scopes allow defining reusable, chainable query constraints in models:
     * ```php
     * protected function scopePublished($query)
     * {
     *     $query->whereEquals('state', 1);
     * }
     *
     * protected function scopeRecent($query, $days = 30)
     * {
     *     $query->where('created', '>=', date('Y-m-d H:i:s', strtotime('-' . $days . ' days')));
     * }
     *
     * // Usage: Entry::published()->recent(7)->rows()
     * ```
     *
     * @param   string  $name       The method name being called
     * @param   array   $arguments  The method arguments provided
     * @return  mixed
     * @throws  \Hubzero\Database\Exception\BadMethodCallException  If called method does not exist in
     *                                                           this class or the query class, or
     *                                                           as a helper* method on the current class.
     **/
    public function __call($name, $arguments)
    {
        // See if method is available as a helper method on current class
        if ($this->hasHelper($name)) {
            return $this->callHelper($name, $arguments);
        }

        // See if method is available as a transformer on current class
        if ($this->hasTransformer($name)) {
            return $this->callTransformer($name, $arguments);
        }

        // Check if it is a parsable field (i.e. wiki/html)
        if ($this->isParsable($name)) {
            return $this->parse($name, (isset($arguments[0])) ? $arguments[0] : 'parsed');
        }

        // Check for a query scope method (e.g., calling published() looks for scopePublished())
        // Query scopes allow defining reusable query constraints in models.
        // Example: scopePublished($query) { $query->whereEquals('state', 1); }
        // Can then be called as: Entry::published()->rows()
        $scopeMethod = 'scope' . ucfirst($name);
        if (in_array($scopeMethod, $this->methods)) {
            if ($this->query === null) {
                $this->newQuery();
            }
            $this->$scopeMethod($this->query, ...$arguments);
            return $this;
        }

        // See if we need to call a query method (lazy-load query if needed)
        if ($this->query === null) {
            $this->newQuery();
        }
        if (in_array($name, get_class_methods($this->query))) {
            // @FIXME: hack to fully qualify field names in one location...is there a better way/location?
            if (
                (substr(
                    $name,
                    0,
                    5
                ) == 'where' || substr($name, 0, 7) == 'orWhere') && $name != 'whereRaw' && $name != 'orWhereRaw'
            ) {
                $arguments[0] = (strpos($arguments[0], '.') === false)
                                ? $this->getQualifiedFieldName($arguments[0])
                                : $arguments[0];
            }

            // Call method and get type of response
            $result = call_user_func_array(array($this->query, $name), $arguments);
            $class  = __NAMESPACE__ . '\\Query';
            // We never want to return an instance of the query class, because
            // we want to be able to chain methods together that are on the model
            // itself.  Plus we auto-forward calls to query functions, so they'll
            // get there eventually anyway.
            return ($result instanceof $class) ? $this : $result;
        }

        // Finally, check for a dynamic relationship definition
        $registry = static::getRelationshipRegistry();
        if ($registry->has(static::class, $name)) {
            $resolver = $registry->get(static::class, $name);
            return call_user_func_array($resolver, [$this]);
        }

        // This method doesn't exist
        throw new BadMethodCallException("'{$name}' method does not exist.", 500);
    }

    /**
     * Processes calls to inaccessible or undefined static methods
     *
     * This is here primarily so we can statically call query class
     * methods and scope methods directly on a newly created object.
     *
     * Examples:
     * - Model::whereEquals('field', 'yes')  // Query method
     * - Entry::published()->recent()->rows() // Scope methods
     *
     * @param   string  $name       The method name being called
     * @param   array   $arguments  The method arguments provided
     * @return  mixed
     **/
    public static function __callStatic($name, $arguments)
    {
        $lifecycleEvents = [
            'creating',
            'created',
            'updating',
            'updated',
            'saving',
            'saved',
            'deleting',
            'deleted',
            'retrieved',
        ];

        if (in_array($name, $lifecycleEvents, true)) {
            if (!isset($arguments[0]) || !is_callable($arguments[0])) {
                throw new BadMethodCallException(
                    "Lifecycle event registration '{$name}' requires a callable argument.",
                    500
                );
            }

            static::registerModelEvent($name, $arguments[0]);
            return;
        }

        return call_user_func_array(array(new static(), $name), $arguments);
    }

    /**
     * Gets attributes set on model dynmically
     *
     * @param   string  $name  The name of the public to retrieve
     * @return  mixed
     **/
    public function __get($name)
    {
        // First, see if a transformer is available on the model
        if ($this->hasTransformer($name)) {
            return $this->callTransformer($name);
        }

        // Check if it is a parsable field (i.e. wiki/html)
        if ($this->isParsable($name)) {
            return $this->parse($name);
        }

        // Next check for an attribute on the model
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        // Check forwarding
        if (!empty($this->forwards)) {
            foreach ($this->forwards as $forward) {
                // We take the first one we find, so in theory, if multiple forwards exist with
                // the same name, you'd have to prioritize them somehow.
                if ($public = $this->makeRelationship($forward)->getRelationship($forward)->$name) {
                    return $public;
                }
            }
        }

        // Now, we'll assume we're looking for a relationship
        if (in_array($name, $this->methods)) {
            return $this->makeRelationship($name)->getRelationship($name);
        }

        // Finally, check for a dynamic relationship definition
        if (static::getRelationshipRegistry()->has(static::class, $name)) {
            return $this->makeAcquaintance($name)->getRelationship($name);
        }
    }

    /**
     * Check if attributes (i.e. field) on the model is set
     *
     * @param   string  $name    The attribute to check if set
     * @return  boolean
     */
    public function __isset($name)
    {
        return $this->hasAttribute($name);
    }

    /**
     * Sets attributes (i.e. fields) on the model
     *
     * @param   array|string  $key    The key to set, or array of key/value pairs
     * @param   mixed         $value  The value to set if key is string
     * @return  self
     */
    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * Intercepts calls to copy the object so we can make a true clone of the attached query
     *
     * PHP, when cloning, does a shallow copy, hence the need for this intercept.
     *
     * @return  void
     **/
    public function __clone()
    {
        if ($this->query !== null) {
            $this->query = clone $this->query;
        }
    }

    /**
     * Serializes the model data for storage
     *
     * @return  string
     **/

    #[\ReturnTypeWillChange]
    public function serialize()
    {
        return serialize($this->__serialize());
    }

    /**
     * Serializes the model data for storage
     *
     * Includes both current attributes and original values for dirty tracking.
     *
     * @return  array
     **/
    #[\ReturnTypeWillChange]
    public function __serialize()
    {
        return [
            '__relational_version' => 2,
            'attributes' => $this->getAttributes(),
            'original' => $this->original,
        ];
    }

    /**
     * Unserializes the data into a new model
     *
     * @param   string  $data  The data to build from
     * @return  void
     **/

    #[\ReturnTypeWillChange]
    public function unserialize($data)
    {
        $this->__unserialize($data);
    }

    /**
     * Unserializes the data into a new model
     *
     * Handles both the new format (with 'attributes' and 'original' keys)
     * and the old format (flat attributes array) for backward compatibility.
     *
     * @param   array  $data  The data to build from
     * @return  void
     **/
    #[\ReturnTypeWillChange]
    public function __unserialize($data)
    {
        $this->__construct();
        if (is_string($data)) {
            // Restrict legacy string payloads to arrays/scalars only.
            // This keeps backward compatibility while preventing object hydration.
            $data = @unserialize($data, ['allowed_classes' => false]);
        }

        // Check if this is the new serialization format (v2+)
        if (is_array($data) && isset($data['__relational_version'])) {
            $this->set($data['attributes'] ?? []);
            $this->original = $data['original'] ?? [];
        } else {
            // Old format: data is just the attributes
            $this->set($data);
            // For old format, sync original so the model isn't dirty
            $this->syncOriginal();
        }
    }

    /**
     * Runs extra setup code when creating a new model
     *
     * @return  void
     **/
    public function setup()
    {
        // Overload in subclass to do something here...nothing by default
    }

    // =========================================================================
    // Global Scopes
    // =========================================================================

    /**
     * Boot the model if it hasn't been booted yet
     *
     * This method is called automatically before any query is built. It ensures
     * that the static boot() method is only called once per model class.
     *
     * @return  void
     **/
    protected function bootIfNotBooted()
    {
        $class = static::class;

        if (!isset(static::$booted[$class])) {
            static::$booted[$class] = true;
            static::bootTraits();
            static::boot();
        }
    }

    /**
     * Boot all of the bootable traits on the model
     *
     * Automatically calls boot{TraitName}() methods for any traits used by
     * the model. This allows traits like SoftDeletes to register their own
     * global scopes without requiring explicit calls in the model's boot().
     *
     * @return  void
     */
    protected static function bootTraits()
    {
        $class = static::class;
        $booted = [];

        // Get all traits used by this class and its parents recursively
        $traits = static::classUsesRecursive($class);

        foreach ($traits as $trait) {
            // Get just the trait name (without namespace)
            $parts = explode('\\', $trait);
            $traitName = end($parts);
            $method = 'boot' . $traitName;

            // Call the boot method if it exists and hasn't been called
            if (method_exists($class, $method) && !in_array($method, $booted)) {
                forward_static_call([$class, $method]);
                $booted[] = $method;
            }
        }
    }

    /**
     * Get all traits used by a class, including parent classes and nested traits
     *
     * @param   string|object  $class     Class name or object
     * @param   bool           $autoload  Whether to autoload classes
     * @return  array
     */
    protected static function classUsesRecursive($class, $autoload = true)
    {
        $results = [];

        // Get traits for the class and all its parents
        foreach (array_reverse(class_parents($class)) + [$class => $class] as $class) {
            $results = array_merge($results, static::traitUsesRecursive($class, $autoload));
        }

        return array_unique($results);
    }

    /**
     * Get all traits used by a trait and its nested traits
     *
     * @param   string  $trait     Trait name
     * @param   bool    $autoload  Whether to autoload classes
     * @return  array
     */
    protected static function traitUsesRecursive($trait, $autoload = true)
    {
        $traits = class_uses($trait, $autoload) ?: [];

        foreach ($traits as $nestedTrait) {
            $traits = array_merge($traits, static::traitUsesRecursive($nestedTrait, $autoload));
        }

        return $traits;
    }

    /**
     * Bootstrap the model
     *
     * Override this method to register global scopes or perform other one-time
     * static setup. This method is called once per model class, not per instance.
     *
     * Example:
     * ```php
     * protected static function boot()
     * {
     *     parent::boot();
     *
     *     // Add a global scope as a closure
     *     static::addGlobalScope('active', function ($query) {
     *         $query->whereEquals('active', 1);
     *     });
     *
     *     // Or use a scope class
     *     static::addGlobalScope(new ActiveScope());
     * }
     * ```
     *
     * @return  void
     **/
    protected static function boot()
    {
        // Override in subclasses to register global scopes
    }

    /**
     * Register a global scope on the model
     *
     * Global scopes are query constraints that are automatically applied to
     * every query on this model. They're useful for multi-tenancy, soft deletes,
     * or any default filtering that should always be applied.
     *
     * Example with closure:
     * ```php
     * static::addGlobalScope('active', function ($query) {
     *     $query->whereEquals('active', 1);
     * });
     * ```
     *
     * Example with scope object (must have apply($query, $model) method):
     * ```php
     * static::addGlobalScope(new TenantScope());
     * ```
     *
     * @param   string|object  $scope     Scope name (string) or scope object
     * @param   Closure|null   $callback  The scope callback (required if $scope is a string)
     * @return  void
     **/
    public static function addGlobalScope($scope, ?Closure $callback = null)
    {
        $class = static::class;

        if (!isset(static::$globalScopes[$class])) {
            static::$globalScopes[$class] = [];
        }

        if (is_string($scope) && $callback !== null) {
            // Named scope with closure
            static::$globalScopes[$class][$scope] = $callback;
        } elseif (is_object($scope)) {
            // Scope object (anonymous or class-based)
            $name = get_class($scope);
            static::$globalScopes[$class][$name] = $scope;
        } else {
            throw new \InvalidArgumentException(
                'Global scope must be a string name with callback, or an object with apply() method.'
            );
        }
    }

    /**
     * Get the global scopes for this model class
     *
     * @return  array
     **/
    public static function getGlobalScopes()
    {
        return static::$globalScopes[static::class] ?? [];
    }

    /**
     * Determine if a model has a global scope
     *
     * @param   string  $scope  The scope name or class name
     * @return  bool
     **/
    public static function hasGlobalScope($scope)
    {
        return isset(static::$globalScopes[static::class][$scope]);
    }

    /**
     * Remove all or specific global scopes from the current query
     *
     * This method allows you to bypass global scopes for a specific query.
     * It returns the model instance for chaining.
     *
     * Example - remove all global scopes:
     * ```php
     * Article::withoutGlobalScopes()->get();
     * ```
     *
     * Example - remove specific scopes:
     * ```php
     * Article::withoutGlobalScopes(['active', 'published'])->get();
     * ```
     *
     * @param   array|null  $scopes  Array of scope names to remove, or null for all
     * @return  $this
     **/
    public function withoutGlobalScopes(?array $scopes = null)
    {
        // Ensure model is booted so we know what scopes exist
        $this->bootIfNotBooted();

        $class = static::class;

        if ($scopes === null) {
            // Remove all scopes
            $this->removedScopes = array_keys(static::$globalScopes[$class] ?? []);
        } else {
            // Remove specific scopes
            $this->removedScopes = array_merge($this->removedScopes, $scopes);
        }

        return $this;
    }

    /**
     * Remove a single global scope from the current query
     *
     * Example:
     * ```php
     * Article::withoutGlobalScope('active')->get();
     * ```
     *
     * @param   string  $scope  The scope name to remove
     * @return  $this
     **/
    public function withoutGlobalScope($scope)
    {
        $this->removedScopes[] = $scope;
        return $this;
    }

    /**
     * Get the scopes that have been removed for this query
     *
     * @return  array
     **/
    public function getRemovedScopes()
    {
        return $this->removedScopes;
    }

    /**
     * Apply global scopes to the query
     *
     * This method is called internally by newQuery() to apply all registered
     * global scopes that haven't been removed for this query instance.
     *
     * @return  void
     **/
    protected function applyGlobalScopes()
    {
        $class = static::class;
        $scopes = static::$globalScopes[$class] ?? [];

        foreach ($scopes as $name => $scope) {
            // Skip removed scopes
            if (in_array($name, $this->removedScopes)) {
                continue;
            }

            if ($scope instanceof Closure) {
                // Closure-based scope
                $scope($this->query);
            } elseif (is_object($scope) && method_exists($scope, 'apply')) {
                // Object-based scope with apply() method
                $scope->apply($this->query, $this);
            }
        }
    }

    /**
     * Clear the booted state for a model (useful for testing)
     *
     * @return  void
     **/
    public static function clearBootedModels()
    {
        static::$booted = [];
        static::$globalScopes = [];
        static::$modelEvents = [];
    }

    /**
     * Remove all registered event listeners for this model
     *
     * This is primarily useful for testing, where you need to ensure a clean
     * state between test cases. Unlike clearBootedModels(), this only affects
     * the current model class.
     *
     * @return  void
     **/
    public static function flushEventListeners()
    {
        $class = static::class;
        static::$modelEvents[$class] = [];
    }

    // =========================================================================
    // Model Lifecycle Hooks
    // =========================================================================

    /**
     * Register a "creating" model event callback
     *
     * The callback receives the model instance and can return false to cancel the operation.
     * This event fires before a new record is inserted into the database.
     *
     * Example:
     * ```php
     * static::creating(function ($model) {
     *     $model->set('slug', Str::slug($model->get('title')));
     * });
     * ```
     *
     * @param   callable  $callback  The callback to execute
     * @return  void
     **/
    public static function onCreating(callable $callback)
    {
        static::registerModelEvent('creating', $callback);
    }

    /**
     * Register a "created" model event callback
     *
     * This event fires after a new record has been successfully inserted.
     *
     * @param   callable  $callback  The callback to execute
     * @return  void
     **/
    public static function onCreated(callable $callback)
    {
        static::registerModelEvent('created', $callback);
    }

    /**
     * Register an "updating" model event callback
     *
     * The callback receives the model instance and can return false to cancel the operation.
     * This event fires before an existing record is updated.
     *
     * @param   callable  $callback  The callback to execute
     * @return  void
     **/
    public static function onUpdating(callable $callback)
    {
        static::registerModelEvent('updating', $callback);
    }

    /**
     * Register an "updated" model event callback
     *
     * This event fires after an existing record has been successfully updated.
     *
     * @param   callable  $callback  The callback to execute
     * @return  void
     **/
    public static function onUpdated(callable $callback)
    {
        static::registerModelEvent('updated', $callback);
    }

    /**
     * Register a "saving" model event callback
     *
     * The callback receives the model instance and can return false to cancel the operation.
     * This event fires before both create and update operations.
     *
     * @param   callable  $callback  The callback to execute
     * @return  void
     **/
    public static function onSaving(callable $callback)
    {
        static::registerModelEvent('saving', $callback);
    }

    /**
     * Register a "saved" model event callback
     *
     * This event fires after both create and update operations complete successfully.
     *
     * @param   callable  $callback  The callback to execute
     * @return  void
     **/
    public static function onSaved(callable $callback)
    {
        static::registerModelEvent('saved', $callback);
    }

    /**
     * Register a "deleting" model event callback
     *
     * The callback receives the model instance and can return false to cancel the operation.
     * This event fires before a record is deleted (or soft deleted).
     *
     * @param   callable  $callback  The callback to execute
     * @return  void
     **/
    public static function onDeleting(callable $callback)
    {
        static::registerModelEvent('deleting', $callback);
    }

    /**
     * Register a "deleted" model event callback
     *
     * This event fires after a record has been deleted (or soft deleted).
     *
     * @param   callable  $callback  The callback to execute
     * @return  void
     **/
    public static function onDeleted(callable $callback)
    {
        static::registerModelEvent('deleted', $callback);
    }

    /**
     * Register a "retrieved" model event callback (afterFind equivalent)
     *
     * The callback is executed after a model is loaded from the database.
     * This is useful for:
     * - Post-processing data after retrieval
     * - Loading computed properties
     * - Decrypting sensitive data
     * - Triggering side effects (logging, caching)
     *
     * Note: This event fires for every model loaded, including during eager loading.
     * For performance-critical code, consider using $casts for simple transformations.
     *
     * Example:
     * ```php
     * Entry::retrieved(function ($entry) {
     *     $entry->set('computed_field', $entry->calculateSomething());
     * });
     * ```
     *
     * @param   callable  $callback  The callback to execute
     * @return  void
     **/
    public static function onRetrieved(callable $callback)
    {
        static::registerModelEvent('retrieved', $callback);
    }

    /**
     * Register an observer for this model
     *
     * Observers provide a clean way to group event handling logic for a model
     * into a dedicated class instead of using inline callbacks. This improves
     * code organization, testability, and maintainability.
     *
     * ## Creating an Observer
     *
     * Create a class with methods named after the events you want to handle:
     *
     * ```php
     * namespace Components\Blog\Observers;
     *
     * use Components\Blog\Models\Entry;
     *
     * class EntryObserver
     * {
     *     // Fires before a new record is inserted (can return false to cancel)
     *     public function creating(Entry $entry)
     *     {
     *         $entry->set('slug', preg_replace('/[^a-z0-9]+/', '-', strtolower($entry->get('title'))));
     *     }
     *
     *     // Fires after a new record is inserted
     *     public function created(Entry $entry)
     *     {
     *         Notification::send($entry->author, new EntryPublished($entry));
     *     }
     *
     *     // Fires before an existing record is updated (can return false to cancel)
     *     public function updating(Entry $entry)
     *     {
     *         $entry->set('modified_by', static::resolveCurrentUserId());
     *     }
     *
     *     // Fires before delete (can return false to cancel)
     *     public function deleting(Entry $entry)
     *     {
     *         // Clean up related data
     *         foreach ($entry->comments as $comment) {
     *             $comment->destroy();
     *         }
     *     }
     * }
     * ```
     *
     * ## Registering an Observer
     *
     * Register the observer in a service provider or bootstrap file:
     *
     * ```php
     * // Using class name (recommended)
     * Entry::observe(EntryObserver::class);
     *
     * // Using an instance
     * Entry::observe(new EntryObserver());
     * ```
     *
     * ## Available Events
     *
     * | Event       | When                        | Can Cancel |
     * |-------------|-----------------------------|------------|
     * | retrieved   | After loading from database | No         |
     * | creating    | Before insert               | Yes        |
     * | created     | After insert                | No         |
     * | updating    | Before update               | Yes        |
     * | updated     | After update                | No         |
     * | saving      | Before insert/update        | Yes        |
     * | saved       | After insert/update         | No         |
     * | deleting    | Before delete               | Yes        |
     * | deleted     | After delete                | No         |
     * | restoring*  | Before restore (SoftDeletes)| Yes        |
     * | restored*   | After restore (SoftDeletes) | No         |
     *
     * *Only available on models using the SoftDeletes trait.
     *
     * ## Canceling Operations
     *
     * For "ing" events (creating, updating, saving, deleting, restoring),
     * return `false` to cancel the operation:
     *
     * ```php
     * public function deleting(Entry $entry)
     * {
     *     // Prevent deletion of published entries
     *     if ($entry->get('state') == 1) {
     *         return false;  // Cancels the delete
     *     }
     * }
     * ```
     *
     * ## Multiple Observers
     *
     * You can register multiple observers for the same model:
     *
     * ```php
     * Entry::observe(EntryObserver::class);
     * Entry::observe(AuditObserver::class);
     * Entry::observe(SearchIndexObserver::class);
     * ```
     *
     * Observers are called in the order they were registered.
     *
     * @param   string|object  $observer  Observer class name or instance
     * @return  void
     **/
    public static function observe($observer)
    {
        // Instantiate if class name provided
        $instance = is_string($observer) ? new $observer() : $observer;

        // List of all possible model events
        $events = [
            'creating', 'created',
            'updating', 'updated',
            'saving', 'saved',
            'deleting', 'deleted',
            'restoring', 'restored',  // SoftDeletes events
        ];

        // Register each method that exists on the observer
        foreach ($events as $event) {
            if (method_exists($instance, $event)) {
                static::registerModelEvent($event, [$instance, $event]);
            }
        }
    }

    /**
     * Register a model event callback
     *
     * @param   string    $event     The event name
     * @param   callable  $callback  The callback to execute
     * @return  void
     **/
    protected static function registerModelEvent($event, callable $callback)
    {
        $class = static::class;

        if (!isset(static::$modelEvents[$class])) {
            static::$modelEvents[$class] = [];
        }

        if (!isset(static::$modelEvents[$class][$event])) {
            static::$modelEvents[$class][$event] = [];
        }

        static::$modelEvents[$class][$event][] = $callback;
    }

    /**
     * Get registered model event callbacks
     *
     * @param   string|null  $event  Specific event to get, or null for all
     * @return  array
     **/
    public static function getModelEvents($event = null)
    {
        $class = static::class;
        $events = static::$modelEvents[$class] ?? [];

        if ($event !== null) {
            return $events[$event] ?? [];
        }

        return $events;
    }

    /**
     * Fire a model event
     *
     * Executes all registered callbacks for the event and triggers a system event.
     * If any callback returns false, the operation will be halted.
     *
     * The system event name format is: model.{ClassName}.{event}
     * Example: model.Components\Blog\Models\Entry.creating
     *
     * @param   string  $event  The event name (creating, created, etc.)
     * @param   bool    $halt   Whether to halt on false return (for "ing" events)
     * @return  bool    False if any callback returned false, true otherwise
     **/
    protected function fireModelEvent($event, $halt = true)
    {
        if (!$this->dispatchesModelEvents) {
            return true;
        }

        $class = static::class;

        // Execute registered callbacks
        $callbacks = static::$modelEvents[$class][$event] ?? [];

        foreach ($callbacks as $callback) {
            $result = $callback($this);

            // For "ing" events, allow cancellation
            if ($halt && $result === false) {
                return false;
            }
        }

        // Also trigger through HubZero's Event system for plugin integration
        // Event name format: model.{ClassName}.{event}
        // Only trigger if Event facade is available (may not be in tests)
        if (class_exists('Event')) {
            $eventName = 'model.' . $class . '.' . $event;
            $results = \Event::trigger($eventName, [$this]);

            // Check if any plugin listener returned false
            if ($halt && in_array(false, $results, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the model has any registered listeners for an event
     *
     * @param   string  $event  The event name
     * @return  bool
     **/
    public static function hasModelEvent($event)
    {
        $class = static::class;
        return !empty(static::$modelEvents[$class][$event]);
    }

    /**
     * Sets the database connection to be used by the query builder
     *
     * @param   object  $connection  The connection to set
     * @return  void
     **/
    public static function setDefaultConnection($connection)
    {
        self::$connection = $connection;
    }

    /**
     * Set the resolver for the current user ID
     *
     * @param  callable|null  $resolver  fn(): int
     */
    public static function setUserIdResolver(?callable $resolver): void
    {
        static::$userIdResolver = $resolver;
    }

    /**
     * Set the resolver for looking up a user by ID
     *
     * @param  callable|null  $resolver  fn(int $id): ?object
     */
    public static function setUserResolver(?callable $resolver): void
    {
        static::$userResolver = $resolver;
    }

    /**
     * Resolve the current user ID
     *
     * @return int
     */
    public static function resolveCurrentUserId(): int
    {
        if (static::$userIdResolver !== null) {
            return (int) call_user_func(static::$userIdResolver);
        }

        return 0;
    }

    /**
     * Resolve a user by ID
     *
     * @param  int  $id
     * @return object|null
     */
    public static function resolveUser(int $id): ?object
    {
        if (static::$userResolver !== null) {
            return call_user_func(static::$userResolver, $id);
        }

        return null;
    }

    /**
     * Flush static runtime state for long-lived worker processes.
     *
     * Defaults clear all static model runtime state. Pass explicit false values
     * to opt out of individual reset categories.
     *
     * @param   array  $options  Supported keys:
     *                           - clear_columns (bool, default true)
     *                           - clear_query_cache (bool, default true)
     *                           - clear_connection (bool, default true)
     *                           - clear_relationships (bool, default true)
     *                           - clear_morph_map (bool, default true)
     *                           - clear_custom_casters (bool, default true)
     *                           - clear_booted_models (bool, default true)
     *                           - clear_resolvers (bool, default true)
     * @return  void
     */
    public static function flush(array $options = []): void
    {
        $clearColumns = $options['clear_columns'] ?? true;
        $clearQueryCache = $options['clear_query_cache'] ?? true;
        $clearConnection = $options['clear_connection'] ?? true;
        $clearRelationships = $options['clear_relationships'] ?? true;
        $clearMorphMap = $options['clear_morph_map'] ?? true;
        $clearCustomCasters = $options['clear_custom_casters'] ?? true;
        $clearBootedModels = $options['clear_booted_models'] ?? true;
        $clearClassMethods = $options['clear_class_methods'] ?? true;
        $clearResolvers = $options['clear_resolvers'] ?? true;

        if ($clearColumns) {
            static::clearTableColumnsCache();
        }

        if ($clearQueryCache) {
            Query::purgeCache();
        }

        if ($clearConnection) {
            static::$connection = null;
        }

        if ($clearRelationships) {
            static::getRelationshipRegistry()->clear();
        }

        if ($clearMorphMap) {
            static::clearMorphMap();
        }

        if ($clearCustomCasters) {
            static::$customCasters = [];
        }

        if ($clearBootedModels) {
            static::clearBootedModels();
        }

        if ($clearClassMethods) {
            static::$classMethods = [];
        }

        if ($clearResolvers) {
            static::$userIdResolver = null;
            static::$userResolver = null;
        }
    }

    // =========================================================================
    // Query Caching
    // =========================================================================
    //
    // Two-layer caching: in-memory (per-request) + persistent (across requests).
    // Persistent caching requires either an injected cache store or APCu extension.
    //
    // | Method            | Type      | Description                           |
    // |-------------------|-----------|---------------------------------------|
    // | disableCaching()  | instance  | Skip cache for this query             |
    // | enableCaching()   | instance  | Re-enable caching (default)           |
    // | purgeCache()      | instance  | Clear in-memory cache                 |
    // | remember($min)    | instance  | Cache results for N minutes           |
    // | rememberForever() | instance  | Cache results with no TTL             |
    // | setCacheStore()   | static    | Set persistent backend (Redis, etc.)  |
    //
    // Example:
    //   $articles = Article::all()->whereEquals('published', 1)->remember(30)->rows();
    //
    // @see \Hubzero\Database\Query for implementation details
    // =========================================================================

    /**
     * Disables query caching
     *
     * @return  self
     **/
    public function disableCaching()
    {
        $this->noCache = true;

        return $this;
    }

    /**
     * Enables query caching
     *
     * @return  self
     **/
    public function enableCaching()
    {
        $this->noCache = false;

        return $this;
    }

    /**
     * Purges the query cache
     *
     * @return  self
     **/
    public function purgeCache()
    {
        if ($this->query === null) {
            $this->newQuery();
        }
        $this->query::purgeCache();

        return $this;
    }

    /**
     * Cache the query results for a given number of minutes
     *
     * This enables persistent caching using either:
     * 1. An injected cache store (via Query::setCacheStore)
     * 2. APCu if available (automatic fallback)
     * 3. In-memory cache only (when neither is available)
     *
     * Example:
     *   $users = User::all()->remember(60)->rows();
     *   // Results cached for 60 minutes
     *
     *   $active = User::whereEquals('active', 1)->remember(30)->rows();
     *   // Conditional query cached for 30 minutes
     *
     * @param   int     $minutes  Number of minutes to cache (default 60)
     * @param   string  $prefix   Optional custom cache key prefix
     * @return  self
     **/
    public function remember(int $minutes = 60, ?string $prefix = null)
    {
        if ($this->query === null) {
            $this->newQuery();
        }
        $this->query->remember($minutes, $prefix);

        return $this;
    }

    /**
     * Cache the query results forever (no expiration)
     *
     * Use with caution - cached data will persist until manually cleared
     * or the cache store evicts it.
     *
     * Example:
     *   $config = Config::all()->rememberForever()->rows();
     *   // Configuration data cached indefinitely
     *
     * @param   string  $prefix  Optional custom cache key prefix
     * @return  self
     **/
    public function rememberForever(?string $prefix = null)
    {
        if ($this->query === null) {
            $this->newQuery();
        }
        $this->query->rememberForever($prefix);

        return $this;
    }

    /**
     * Set the persistent cache store for query caching
     *
     * This is a static convenience method that delegates to Query::setCacheStore().
     * The cache store should implement get(), put(), forget(), and has() methods.
     *
     * Example:
     *   Relational::setCacheStore(new MyRedisCache());
     *
     * @param   object|null  $store  Cache store instance or null to disable
     * @return  void
     **/
    public static function setCacheStore($store)
    {
        Query::setCacheStore($store);
    }

    /**
     * Gets an attribute by key
     *
     * This will not retrieve properties directly attached to the model,
     * even if they are public - those should be accessed directly!
     *
     * Also, make sure to access properties in transformers using the get method.
     * Otherwise you'll just get stuck in a loop!
     *
     * @param   string  $key      The attribute key to get
     * @param   mixed   $default  The value to provide, should the key be non-existent
     * @return  mixed
     **/
    public function get($key, $default = null)
    {
        if (!$this->hasAttribute($key)) {
            return $default;
        }

        $value = $this->attributes[$key];

        // Apply casting if defined for this attribute
        if ($this->hasCast($key)) {
            return $this->castAttribute($key, $value);
        }

        return $value;
    }

    /**
     * Sets attributes (i.e. fields) on the model
     *
     * This must be used when setting data to be saved. Otherwise, the properties
     * will be attached directly to the model itself and not included in the save.
     *
     * @param   array|string  $key    The key to set, or array of key/value pairs
     * @param   mixed         $value  The value to set if key is string
     * @return  self
     **/
    public function set($key, $value = null)
    {
        if (is_array($key) || is_object($key)) {
            foreach ($key as $k => $v) {
                $this->setAttribute($k, $v);
            }
        } else {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Set a single attribute value, applying casting if needed
     *
     * @param   string  $key    The attribute name
     * @param   mixed   $value  The value to set
     * @return  void
     */
    protected function setAttribute($key, $value)
    {
        // Clear cast cache for this attribute if it exists
        unset($this->castCache[$key]);

        // Apply storage casting if this attribute has a cast defined
        if ($this->hasCast($key)) {
            $value = $this->castAttributeForStorage($key, $value);
        }

        $this->attributes[$key] = $value;
    }

    /**
     * Returns a new empty model
     *
     * @return  static
     **/
    public static function blank()
    {
        return new static();
    }

    /**
     * Construct a new object instance, setting the passed in results on the object
     *
     * This method creates a model instance from database results and fires the
     * "retrieved" event, allowing post-load processing.
     *
     * @param   object  $results  The results to set on the new model
     * @return  static
     **/
    public static function newFromResults($results)
    {
        $instance = self::blank();

        // Set attributes directly without casting - database values are already in storage format
        // Applying storage casts here would double-encode JSON, double-hash encrypted data, etc.
        foreach ($results as $key => $value) {
            $instance->attributes[$key] = $value;
        }

        // Store the database values as original for dirty tracking
        $instance->syncOriginal();

        // Fire "retrieved" event (afterFind equivalent)
        $instance->fireModelEvent('retrieved', false);

        return $instance;
    }

    /**
     * Copies the current model (likely used to maintain query parameters between multiple queries)
     *
     * @return  self
     **/
    public function copy()
    {
        return clone $this;
    }

    /**
     * Outputs attributes in JSON encoded format
     *
     * @return  string
     **/
    public function toJson()
    {
        return json_encode($this->attributes);
    }

    /**
     * Outputs attributes as array
     *
     * @return  array
     **/
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Outputs attributes as object
     *
     * @return  object
     **/
    public function toObject()
    {
        return (object)$this->attributes;
    }

    // =========================================================================
    // Attribute Casting
    // =========================================================================
    //
    // Automatically convert database values to native PHP types when retrieved.
    // Define the $casts property in your model to enable:
    //
    // ```php
    // protected $casts = [
    //     'is_active'    => 'boolean',   // DB: 0/1 -> PHP: true/false
    //     'view_count'   => 'integer',   // DB: "42" -> PHP: 42
    //     'price'        => 'float',     // DB: "19.99" -> PHP: 19.99
    //     'settings'     => 'array',     // DB: JSON -> PHP: array
    //     'metadata'     => 'object',    // DB: JSON -> PHP: stdClass
    //     'tags'         => 'collection',// DB: JSON -> PHP: ArrayObject
    //     'published_at' => 'datetime',  // DB: string -> PHP: DateTime
    //     'birth_date'   => 'date',      // DB: string -> PHP: DateTime (midnight)
    //     'expires_at'   => 'timestamp', // DB: string -> PHP: Unix timestamp
    // ];
    // ```
    //
    // | Cast Type   | PHP Type    | Storage Format      | Notes                    |
    // |-------------|-------------|---------------------|--------------------------|
    // | integer     | int         | int                 |                          |
    // | float/real  | float       | float               |                          |
    // | boolean     | bool        | int (0/1)           | Handles "true"/"false"   |
    // | string      | string      | string              |                          |
    // | array       | array       | JSON string         | Bidirectional            |
    // | object      | stdClass    | JSON string         | Bidirectional            |
    // | collection  | ArrayObject | JSON string         | Bidirectional            |
    // | datetime    | DateTime    | Y-m-d H:i:s         | Null for invalid dates   |
    // | date        | DateTime    | Y-m-d               | Time set to 00:00:00     |
    // | timestamp   | int         | Y-m-d H:i:s         | Unix timestamp           |
    //
    // Use getRaw($key) to get the original uncasted value.
    // =========================================================================

    /**
     * Determine whether an attribute should be cast to a native type
     *
     * @param   string       $key   The attribute name
     * @param   array|null   $types Optional array of types to check against
     * @return  bool
     */
    public function hasCast($key, $types = null)
    {
        if (!array_key_exists($key, $this->casts)) {
            return false;
        }

        if ($types === null) {
            return true;
        }

        return in_array($this->getCastType($key), (array) $types, true);
    }

    /**
     * Get the casts array
     *
     * @return  array
     */
    public function getCasts()
    {
        return $this->casts;
    }

    /**
     * Get the type of cast for a model attribute
     *
     * @param   string  $key  The attribute name
     * @return  string|null
     */
    protected function getCastType($key)
    {
        if (!isset($this->casts[$key])) {
            return null;
        }

        $castType = $this->casts[$key];

        // Check if it's a custom cast class (class name)
        if ($this->isCustomCastClass($castType)) {
            return $castType;
        }

        // Handle datetime with format: 'datetime:Y-m-d'
        if (strpos($castType, ':') !== false) {
            return explode(':', $castType, 2)[0];
        }

        return trim(strtolower($castType));
    }

    /**
     * Check if a cast type is a custom cast class
     *
     * @param   string  $castType  The cast type to check
     * @return  bool
     */
    protected function isCustomCastClass($castType)
    {
        // Class names contain backslashes or start with uppercase
        if (strpos($castType, '\\') !== false) {
            return class_exists($castType) && is_subclass_of($castType, Casts\CastsAttributes::class);
        }

        return false;
    }

    /**
     * Get or create a custom caster instance
     *
     * @param   string  $castClass  The cast class name
     * @return  Casts\CastsAttributes
     */
    protected function getCustomCaster($castClass)
    {
        if (!isset(self::$customCasters[$castClass])) {
            self::$customCasters[$castClass] = new $castClass();
        }

        return self::$customCasters[$castClass];
    }

    /**
     * Get the format for a datetime cast
     *
     * @param   string  $key  The attribute name
     * @return  string|null
     */
    protected function getDateFormat($key)
    {
        if (!isset($this->casts[$key])) {
            return null;
        }

        $castType = $this->casts[$key];

        if (strpos($castType, ':') !== false) {
            return explode(':', $castType, 2)[1];
        }

        return null;
    }

    /**
     * Cast an attribute to a native PHP type
     *
     * @param   string  $key    The attribute name
     * @param   mixed   $value  The raw value from the database
     * @return  mixed
     */
    protected function castAttribute($key, $value)
    {
        if ($value === null) {
            return null;
        }

        $castType = $this->getCastType($key);

        // Some drivers (notably Informix TEXT in this runtime) can return empty
        // strings for NULL text values. Preserve nullable semantics for casts
        // that represent structured/JSON-like data.
        // NOTE: If caller must distinguish true NULL vs '', query an explicit
        // SQL-side flag (e.g. CASE WHEN col IS NULL THEN 1 ELSE 0 END).
        if ($value === '' && $this->shouldTreatEmptyStringAsNullForCast($castType)) {
            return null;
        }

        // Check cache first (for expensive casts like JSON/datetime)
        if (array_key_exists($key, $this->castCache)) {
            return $this->castCache[$key];
        }

        switch ($castType) {
            case 'int':
            case 'integer':
                $result = (int) $value;
                break;

            case 'real':
            case 'float':
            case 'double':
                $result = (float) $value;
                break;

            case 'decimal':
                $result = $this->castToDecimal($value, $key);
                break;

            case 'string':
                $result = (string) $value;
                break;

            case 'bool':
            case 'boolean':
                $result = $this->castToBoolean($value);
                break;

            case 'array':
                $result = $this->castToArray($value);
                // Cache array casts
                $this->castCache[$key] = $result;
                break;

            case 'object':
                $result = $this->castToObject($value);
                // Cache object casts
                $this->castCache[$key] = $result;
                break;

            case 'collection':
                $result = $this->castToCollection($value);
                // Cache collection casts
                $this->castCache[$key] = $result;
                break;

            case 'datetime':
                $result = $this->castToDateTime($value);
                // Cache datetime casts
                $this->castCache[$key] = $result;
                break;

            case 'date':
                $result = $this->castToDate($value);
                // Cache date casts
                $this->castCache[$key] = $result;
                break;

            case 'timestamp':
                $result = $this->castToTimestamp($value);
                break;

            default:
                // Check if it's a custom cast class
                if ($castType !== null && $this->isCustomCastClass($castType)) {
                    $caster = $this->getCustomCaster($castType);
                    $result = $caster->get($this, $key, $value, $this->attributes);
                    // Cache custom cast results
                    $this->castCache[$key] = $result;
                } else {
                    $result = $value;
                }
        }

        return $result;
    }

    /**
     * Determine if an empty string should be normalized to null for a cast.
     *
     * Informix TEXT can return '' for database NULL values in this runtime.
     * For structured/JSON-like casts, callers expect nullable semantics,
     * so normalize '' back to null before cast logic runs.
     *
     * @param   string|null  $castType
     * @return  bool
     */
    protected function shouldTreatEmptyStringAsNullForCast($castType)
    {
        if ($castType === null) {
            return false;
        }

        if (in_array($castType, ['array', 'object', 'collection'], true)) {
            return true;
        }

        return in_array($castType, [
            \Hubzero\Database\Casts\AsCollection::class,
            \Hubzero\Database\Casts\AsJson::class,
        ], true);
    }

    /**
     * Cast a value to boolean
     *
     * Handles various representations: 0/1, "0"/"1", "true"/"false", "yes"/"no"
     *
     * @param   mixed  $value  The value to cast
     * @return  bool
     */
    protected function castToBoolean($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $lower = strtolower($value);
            if (in_array($lower, ['true', 'yes', '1', 'on'], true)) {
                return true;
            }
            if (in_array($lower, ['false', 'no', '0', 'off', ''], true)) {
                return false;
            }
        }

        return (bool) $value;
    }

    /**
     * Cast a value to array (from JSON string)
     *
     * @param   mixed  $value  The value to cast
     * @return  array
     */
    protected function castToArray($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && strlen($value) > 0) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return (array) $decoded;
            }
        }

        return [];
    }

    /**
     * Cast a value to object (from JSON string)
     *
     * @param   mixed  $value  The value to cast
     * @return  object
     */
    protected function castToObject($value)
    {
        if (is_object($value)) {
            return $value;
        }

        if (is_string($value) && strlen($value) > 0) {
            $decoded = json_decode($value);
            if (json_last_error() === JSON_ERROR_NONE && is_object($decoded)) {
                return $decoded;
            }
        }

        return new \stdClass();
    }

    /**
     * Cast a value to a collection (from JSON string)
     *
     * Returns an ArrayObject for general-purpose collection use.
     * Note: Returns ArrayObject rather than Rows because Rows is specifically
     * designed for Relational model instances.
     *
     * @param   mixed  $value  The value to cast
     * @return  \ArrayObject
     */
    protected function castToCollection($value)
    {
        $array = $this->castToArray($value);
        return new \ArrayObject($array, \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Cast a value to decimal with precision
     *
     * Returns a string representation of the number with the specified
     * number of decimal places to avoid floating point precision issues.
     *
     * @param   mixed   $value  The value to cast
     * @param   string  $key    The attribute key (to get precision from cast definition)
     * @return  string
     */
    protected function castToDecimal($value, $key)
    {
        $precision = $this->getDecimalPrecision($key);
        return number_format((float) $value, $precision, '.', '');
    }

    /**
     * Get the decimal precision for an attribute
     *
     * Extracts the precision from cast definitions like 'decimal:2'
     *
     * @param   string  $key  The attribute key
     * @return  int     The precision (default 2)
     */
    protected function getDecimalPrecision($key)
    {
        $castDefinition = $this->casts[$key] ?? 'decimal:2';

        if (strpos($castDefinition, ':') !== false) {
            $parts = explode(':', $castDefinition, 2);
            if (isset($parts[1]) && is_numeric($parts[1])) {
                return (int) $parts[1];
            }
        }

        // Default precision
        return 2;
    }

    /**
     * Cast a value to DateTime
     *
     * @param   mixed  $value  The value to cast
     * @return  \DateTime|null
     */
    protected function castToDateTime($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return $value instanceof \DateTime ? $value : \DateTime::createFromInterface($value);
        }

        if (is_numeric($value)) {
            return (new \DateTime())->setTimestamp((int) $value);
        }

        if (is_string($value)) {
            $trimmed = trim($value);

            // Handle empty strings
            if ($trimmed === '') {
                return null;
            }

            // Handle common zero/invalid date values
            // These should not be parsed by DateTime
            $invalidDates = [
                '0000-00-00',
                '0000-00-00 00:00:00',
                '0000-00-00T00:00:00',
                '1970-01-01 00:00:00', // Unix epoch sometimes indicates "no date"
            ];

            if (in_array($trimmed, $invalidDates, true) || strpos($trimmed, '0000-00-00') === 0) {
                return null;
            }

            try {
                $dt = new \DateTime($trimmed);

                // Double-check: if the year is negative or 0, treat as invalid
                // (PHP parses 0000-00-00 as -0001-11-30)
                if ((int) $dt->format('Y') <= 0) {
                    return null;
                }

                return $dt;
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Cast a value to Date (DateTime at midnight)
     *
     * @param   mixed  $value  The value to cast
     * @return  \DateTime|null
     */
    protected function castToDate($value)
    {
        $datetime = $this->castToDateTime($value);

        if ($datetime) {
            $datetime->setTime(0, 0, 0);
        }

        return $datetime;
    }

    /**
     * Cast a value to Unix timestamp
     *
     * @param   mixed  $value  The value to cast
     * @return  int
     */
    protected function castToTimestamp($value)
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        $datetime = $this->castToDateTime($value);
        return $datetime ? $datetime->getTimestamp() : 0;
    }

    /**
     * Convert a value for database storage based on its cast type
     *
     * @param   string  $key    The attribute name
     * @param   mixed   $value  The value to convert
     * @return  mixed
     */
    protected function castAttributeForStorage($key, $value)
    {
        if ($value === null) {
            return null;
        }

        $castType = $this->getCastType($key);

        switch ($castType) {
            case 'int':
            case 'integer':
                return (int) $value;

            case 'real':
            case 'float':
            case 'double':
                return (float) $value;

            case 'decimal':
                return $this->castToDecimal($value, $key);

            case 'string':
                return (string) $value;

            case 'bool':
            case 'boolean':
                return $value ? 1 : 0;

            case 'array':
            case 'object':
            case 'collection':
                return $this->castJsonForStorage($value);

            case 'datetime':
                return $this->castDateTimeForStorage($value, 'Y-m-d H:i:s', $key);

            case 'date':
                return $this->castDateTimeForStorage($value, 'Y-m-d', $key);

            case 'timestamp':
                return $this->castTimestampForStorage($value);

            default:
                // Check if it's a custom cast class
                if ($castType !== null && $this->isCustomCastClass($castType)) {
                    $caster = $this->getCustomCaster($castType);
                    return $caster->set($this, $key, $value, $this->attributes);
                }
                return $value;
        }
    }

    /**
     * Convert a JSON-castable value for storage
     *
     * @param   mixed  $value  The value to convert
     * @return  string
     */
    protected function castJsonForStorage($value)
    {
        if (is_string($value)) {
            return $value;
        }

        if ($value instanceof Rows) {
            $value = $value->toArray();
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Convert a DateTime value for storage
     *
     * @param   mixed        $value   The value to convert
     * @param   string       $format  The date format
     * @param   string|null  $key     The attribute key (for custom format)
     * @return  string|null
     */
    protected function castDateTimeForStorage($value, $format, $key = null)
    {
        // Check for custom format defined in cast
        if ($key !== null) {
            $customFormat = $this->getDateFormat($key);
            if ($customFormat) {
                $format = $customFormat;
            }
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format($format);
        }

        if (is_numeric($value)) {
            return (new \DateTime())->setTimestamp((int) $value)->format($format);
        }

        if (is_string($value) && strlen($value) > 0) {
            try {
                return (new \DateTime($value))->format($format);
            } catch (\Exception $e) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Convert a timestamp value for storage
     *
     * @param   mixed  $value  The value to convert
     * @return  string|null
     */
    protected function castTimestampForStorage($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_numeric($value)) {
            return (new \DateTime())->setTimestamp((int) $value)->format('Y-m-d H:i:s');
        }

        return $value;
    }

    /**
     * Clear the cast cache for a specific attribute or all attributes
     *
     * @param   string|null  $key  The attribute to clear, or null for all
     * @return  $this
     */
    public function clearCastCache($key = null)
    {
        if ($key === null) {
            $this->castCache = [];
        } else {
            unset($this->castCache[$key]);
        }

        return $this;
    }

    /**
     * Get the raw (uncasted) value of an attribute
     *
     * @param   string  $key      The attribute name
     * @param   mixed   $default  Default value if not set
     * @return  mixed
     */
    public function getRaw($key, $default = null)
    {
        return $this->hasAttribute($key) ? $this->attributes[$key] : $default;
    }

    // =========================================================================
    // End Attribute Casting
    // =========================================================================

    /**
     * Checks to see if the current model has a helper by the given name
     *
     * @param   string  $name  The helper name to check for
     * @return  bool
     **/
    public function hasHelper($name)
    {
        return in_array('helper' . ucfirst($name), $this->methods);
    }

    /**
     * Calls the requested helper, passing the given arguments
     *
     * @param   string  $name       The helper name to call
     * @param   array   $arguments  Arguments to pass with the method call
     * @return  mixed
     **/
    public function callHelper($name, $arguments)
    {
        return call_user_func_array(array($this, 'helper' . ucfirst($name)), $arguments);
    }

    /**
     * Checks to see if the current model has a transformer by the given name
     *
     * @param   string  $name  The transformer name to check for
     * @return  bool
     **/
    public function hasTransformer($name)
    {
        return in_array('transform' . ucfirst($this->snakeToCamel($name)), $this->methods);
    }

    /**
     * Calls the requested transformer, passing the given arguments
     *
     * @param   string  $name       The transformer name to call
     * @param   array   $arguments  Arguments to pass with the method call
     * @return  mixed
     **/
    public function callTransformer($name, $arguments = [])
    {
        return call_user_func_array(array($this, 'transform' . ucfirst($this->snakeToCamel($name))), $arguments);
    }

    /**
     * Checks to see if the given field is one to be parsed
     *
     * @param   string  $field  The field to check
     * @return  bool
     **/
    public function isParsable($field)
    {
        return in_array($field, $this->parsed);
    }

    /**
     * Parses content string as directed
     *
     * @param   string  $field  The field to parse
     * @param   string  $as     The format to return state in
     * @return  string
     **/
    public function parse($field, $as = 'parsed')
    {
        switch (strtolower($as)) {
            case 'parsed':
                $property = "_{$field}Parsed";

                if (!isset($this->$property)) {
                    $this->$property = \Hubzero\Html\Builder\Content::prepare($this->get($field, ''));
                }

                return $this->$property;
            break;

            case 'raw':
            default:
                $content = stripslashes($this->get($field, '') ?? '');
                return preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
            break;
        }
    }

    /**
     * Takes a snake-cased string and camel cases it
     *
     * @param   string  $text  The string to camel case
     * @return  string
     **/
    public function snakeToCamel($text)
    {
        if (strpos($text, '_') !== false) {
            $bits = explode('_', $text);
            $bits = array_map('ucfirst', $bits);
            $text = lcfirst(implode('', $bits));
        }

        return $text;
    }

    /**
     * Resets the current model, likely for another query to be performed on it
     *
     * @return  self
     **/
    private function reset()
    {
        $this->clearAttributes();
        $this->newQuery();
        return $this;
    }

    /**
     * Gets a fresh query object
     *
     * @return  \Hubzero\Database\Query
     **/
    public function getQuery()
    {
        return new Query(self::$connection);
    }

    // =========================================================================
    // Query Debugging Methods
    // =========================================================================
    //
    // These methods help debug and inspect queries without executing them.
    //
    // | Method              | Returns | Description                              |
    // |---------------------|---------|------------------------------------------|
    // | toSql()             | string  | SQL with ? placeholders                  |
    // | getQueryBindings()  | array   | Bound parameter values                   |
    // | toRawSql()          | string  | SQL with values substituted (debug only) |
    // | dump()              | $this   | Output debug info and continue           |
    // | dd()                | never   | Output debug info and exit               |
    // | getDebugInfo()      | array   | Get all debug info as array              |
    //
    // Example:
    // ```php
    // $query = Article::all()->whereEquals('status', 'published');
    // echo $query->toSql();        // SELECT * FROM `articles` WHERE `status` = ?
    // print_r($query->getBindings()); // ['published']
    // echo $query->toRawSql();     // SELECT * FROM `articles` WHERE `status` = 'published'
    // ```
    // =========================================================================

    /**
     * Get the SQL representation of the current query with placeholders
     *
     * Example:
     * ```php
     * $sql = Article::all()->whereEquals('status', 'published')->toSql();
     * // "SELECT * FROM `#__articles` WHERE `status` = ?"
     * ```
     *
     * @return  string
     */
    public function toSql()
    {
        $this->bootIfNotBooted();
        if ($this->query === null) {
            $this->newQuery();
        }
        return $this->query->toSql();
    }

    /**
     * Get the current query bindings
     *
     * Example:
     * ```php
     * $bindings = Article::all()->whereEquals('status', 'published')->getQueryBindings();
     * // ['published']
     * ```
     *
     * @return  array
     */
    public function getQueryBindings()
    {
        $this->bootIfNotBooted();
        if ($this->query === null) {
            $this->newQuery();
        }
        return $this->query->getBindings();
    }

    /**
     * Get the raw SQL with bindings substituted
     *
     * This is for debugging only - never execute this string directly.
     *
     * Example:
     * ```php
     * $sql = Article::all()->whereEquals('status', 'published')->toRawSql();
     * // "SELECT * FROM `#__articles` WHERE `status` = 'published'"
     * ```
     *
     * @return  string
     */
    public function toRawSql()
    {
        $this->bootIfNotBooted();
        if ($this->query === null) {
            $this->newQuery();
        }
        return $this->query->toRawSql();
    }

    /**
     * Dump the query SQL and bindings for debugging
     *
     * Outputs the SQL and bindings to the screen and returns $this
     * for method chaining.
     *
     * Example:
     * ```php
     * Article::all()->whereEquals('status', 'published')->dump()->rows();
     * ```
     *
     * @return  $this
     */
    public function dump()
    {
        $this->bootIfNotBooted();
        if ($this->query === null) {
            $this->newQuery();
        }
        $this->query->dump();
        return $this;
    }

    /**
     * Dump the query SQL and bindings, then terminate execution
     *
     * Example:
     * ```php
     * Article::all()->whereEquals('status', 'published')->dd();
     * ```
     *
     * @return  never
     */
    public function dd()
    {
        $this->bootIfNotBooted();
        if ($this->query === null) {
            $this->newQuery();
        }
        $this->query->dd(); // @phpstan-ignore return.never (Query::dd() calls exit)
    }

    /**
     * Get debug information about the query as an array
     *
     * Example:
     * ```php
     * $debug = Article::all()->whereEquals('status', 'published')->getDebugInfo();
     * // ['sql' => '...', 'bindings' => [...], 'raw_sql' => '...']
     * ```
     *
     * @return  array
     */
    public function getDebugInfo()
    {
        $this->bootIfNotBooted();
        if ($this->query === null) {
            $this->newQuery();
        }
        return $this->query->getDebugInfo();
    }

    /**
     * Gets a fresh structure object
     *
     * @return  \Hubzero\Database\Structure
     **/
    public function getStructure()
    {
        return new Structure(self::$connection);
    }

    /**
     * Sets a fresh query object on the model, seeding it with helpful defaults
     *
     * This method also boots the model (if not already booted) and applies any
     * registered global scopes to the query.
     *
     * @return  self
     **/
    public function newQuery()
    {
        // Ensure the model is booted (registers global scopes)
        $this->bootIfNotBooted();

        $select = ($this->getTableAlias() ? $this->getTableAlias() . '.' : '') . '*';

        $this->query = $this->getQuery()->select($select)->from($this->getTableName(), $this->getTableAlias());

        // Apply global scopes (unless removed via withoutGlobalScopes)
        $this->applyGlobalScopes();

        return $this;
    }

    /**
     * Checks to see if the requested attribute is set on the model
     *
     * @return  bool
     **/
    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Grabs all of the model attributes
     *
     * @return  array
     **/
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Checks if the model or a specific attribute has been modified
     *
     * Compares current attributes against the original values loaded from the database.
     * For new models (not yet saved), always returns true if attributes are set.
     *
     * @param   string|array|null  $attributes  Attribute name(s) to check, or null for any change
     * @return  bool
     **/
    public function isDirty($attributes = null): bool
    {
        $dirty = $this->getDirty();

        // If no specific attribute requested, check if anything changed
        if ($attributes === null) {
            return count($dirty) > 0;
        }

        // Normalize to array
        $attributes = (array) $attributes;

        // Check if any of the specified attributes are dirty
        foreach ($attributes as $attribute) {
            if (array_key_exists($attribute, $dirty)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the attributes that have been modified since loading
     *
     * Returns an array of attribute names to their new values for all
     * attributes that differ from their original values.
     *
     * @return  array
     **/
    public function getDirty(): array
    {
        $dirty = [];

        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original)) {
                // New attribute that wasn't in original
                $dirty[$key] = $value;
            } elseif ($value !== $this->original[$key]) {
                // Value has changed from original
                // Use strict comparison but handle type coercion for DB values
                if (!$this->originalIsEquivalent($key, $value)) {
                    $dirty[$key] = $value;
                }
            }
        }

        return $dirty;
    }

    /**
     * Checks if the current value is equivalent to the original
     *
     * Handles type coercion issues common with database values (e.g., "1" vs 1).
     *
     * @param   string  $key    The attribute key
     * @param   mixed   $value  The current value
     * @return  bool
     **/
    protected function originalIsEquivalent(string $key, $value): bool
    {
        if (!array_key_exists($key, $this->original)) {
            return false;
        }

        $original = $this->original[$key];

        // Identical values
        if ($value === $original) {
            return true;
        }

        // Both null
        if ($value === null && $original === null) {
            return true;
        }

        // Handle numeric string comparison (common with DB results)
        if (is_numeric($original) && is_numeric($value)) {
            return (string) $original === (string) $value;
        }

        return false;
    }

    /**
     * Gets the original value(s) of the model's attributes
     *
     * Returns the values as they were when the model was loaded from the database.
     *
     * @param   string|null  $key      Specific attribute to get, or null for all
     * @param   mixed        $default  Default value if attribute doesn't exist
     * @return  mixed
     **/
    public function getOriginal(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->original;
        }

        return array_key_exists($key, $this->original) ? $this->original[$key] : $default;
    }

    /**
     * Checks if the model or a specific attribute was changed during the last save
     *
     * This is different from isDirty() - wasChanged() checks what was actually
     * persisted in the last save operation, while isDirty() checks current unsaved changes.
     *
     * @param   string|array|null  $attributes  Attribute name(s) to check, or null for any change
     * @return  bool
     **/
    public function wasChanged($attributes = null): bool
    {
        // If no specific attribute requested, check if anything changed
        if ($attributes === null) {
            return count($this->changes) > 0;
        }

        // Normalize to array
        $attributes = (array) $attributes;

        // Check if any of the specified attributes were changed
        foreach ($attributes as $attribute) {
            if (array_key_exists($attribute, $this->changes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the attributes that were changed during the last save operation
     *
     * @return  array
     **/
    public function getChanges(): array
    {
        return $this->changes;
    }

    /**
     * Syncs the original attributes with the current attributes
     *
     * Called after a successful save to reset the dirty tracking state.
     * Stores current dirty values in $changes before syncing.
     *
     * @return  self
     **/
    public function syncOriginal(): self
    {
        $this->original = $this->attributes;

        return $this;
    }

    /**
     * Syncs only a specific attribute to its original value
     *
     * @param   string  $attribute  The attribute to sync
     * @return  self
     **/
    public function syncOriginalAttribute(string $attribute): self
    {
        if (array_key_exists($attribute, $this->attributes)) {
            $this->original[$attribute] = $this->attributes[$attribute];
        }

        return $this;
    }

    /**
     * Discards changes and reverts to original values
     *
     * Useful for undoing unsaved modifications to a model.
     *
     * @param   string|array|null  $attributes  Specific attribute(s) to revert, or null for all
     * @return  self
     **/
    public function discardChanges($attributes = null): self
    {
        if ($attributes === null) {
            $this->attributes = $this->original;
        } else {
            $attributes = (array) $attributes;
            foreach ($attributes as $attribute) {
                if (array_key_exists($attribute, $this->original)) {
                    $this->attributes[$attribute] = $this->original[$attribute];
                } else {
                    unset($this->attributes[$attribute]);
                }
            }
        }

        return $this;
    }

    /**
     * Removes an attribute
     *
     * @param   string  $key  The attribute to remove
     * @return  self
     **/
    public function removeAttribute($key)
    {
        $this->offsetUnset($key);

        return $this;
    }

    /**
     * Clears data attributes set on the current model
     *
     * @return  void
     **/
    private function clearAttributes()
    {
        $this->attributes = array();
    }

    /**
     * Determines if the current model is new by looking for the presence of a primary key attribute
     *
     * @return  bool
     **/
    public function isNew()
    {
        return (!$this->hasAttribute($this->getPrimaryKey()) || !$this->{$this->getPrimaryKey()});
    }

    /**
     * Sets an interator parent on the model
     *
     * @param   \Hubzero\Database\Rows  $rows  The iterator to set
     * @return  self
     **/
    public function setIterator($rows)
    {
        $this->collection = $rows;

        return $this;
    }

    /**
     * Checks to see if the current item is the first in the list
     *
     * @return  bool
     **/
    public function isFirst()
    {
        if ($this->collection) {
            return $this->collection->isFirst($this->getPkValue());
        }

        return false;
    }

    /**
     * Checks to see if the current item is the last in the list
     *
     * @return  bool
     **/
    public function isLast()
    {
        if ($this->collection) {
            return $this->collection->isLast($this->getPkValue());
        }

        return false;
    }

    /**
     * Retrieves the current model's table name
     *
     * @return  string
     **/
    public function getTableName()
    {
        return $this->table;
    }

    /**
     * Retrieves the current model's table alias
     *
     * @return  string
     **/
    public function getTableAlias()
    {
        return $this->tableAlias;
    }

    /**
     * Sets the current model's table alias
     *
     * @param   string  $alias
     * @return  object
     **/
    public function setTableAlias($alias)
    {
        $this->tableAlias = (string) $alias;

        return $this;
    }

    /**
     * Retrieves the current model's primary key name
     *
     * @return  string
     **/
    public function getPrimaryKey()
    {
        return $this->pk;
    }

    /**
     * Gets the value of the primary key
     *
     * @return  mixed
     **/
    public function getPkValue()
    {
        return isset($this->attributes[$this->getPrimaryKey()]) ? $this->attributes[$this->getPrimaryKey()] : null;
    }

    /**
     * Creates the fully qualified field name by prepending the table name
     *
     * @return  string
     **/
    public function getQualifiedFieldName($field)
    {
        $tbl = ($this->getTableAlias() ? $this->getTableAlias() : $this->getTableName());
        return $tbl . '.' . $field;
    }

    /**
     * Retrieves the model's name
     *
     * @return  string
     **/
    public function getModelName()
    {
        return $this->modelName;
    }

    /**
     * Retrieves the model's name
     *
     * @return  string
     **/
    public function getModelNamespace()
    {
        return $this->modelNamespace;
    }

    /**
     * Retrieves the model's namespace
     *
     * @return  string
     **/
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Retrieves the model rules, filtered by the current scenario
     *
     * Rules are filtered as follows:
     * - String rules (BC): Always included
     * - Callable rules (BC): Always included
     * - Array rules with 'on' key: Included only if current scenario matches
     * - Array rules without 'on' key: Always included
     *
     * @param   bool  $raw  If true, return raw rules without scenario filtering
     * @return  array
     **/
    public function getRules($raw = false)
    {
        if ($raw) {
            return $this->rules;
        }

        return $this->filterRulesByScenario($this->rules);
    }

    /**
     * Filter rules array by the current scenario
     *
     * @param   array  $rules  The rules to filter
     * @return  array  Filtered rules ready for validation
     */
    protected function filterRulesByScenario(array $rules): array
    {
        $filtered = [];

        foreach ($rules as $field => $rule) {
            // BC: String rules always apply to all scenarios
            if (is_string($rule)) {
                $filtered[$field] = $rule;
                continue;
            }

            // BC: Callable rules always apply to all scenarios
            if (is_callable($rule)) {
                $filtered[$field] = $rule;
                continue;
            }

            // New format: Array with 'rule' key
            if (is_array($rule) && isset($rule['rule'])) {
                // Check if scenario restriction exists
                if (!isset($rule['on'])) {
                    // No scenario restriction - always apply
                    $filtered[$field] = $rule['rule'];
                    continue;
                }

                $scenarios = $rule['on'];

                // Single scenario as string
                if (is_string($scenarios) && $scenarios === $this->scenario) {
                    $filtered[$field] = $rule['rule'];
                    continue;
                }

                // Multiple scenarios as array
                if (is_array($scenarios) && in_array($this->scenario, $scenarios)) {
                    $filtered[$field] = $rule['rule'];
                    continue;
                }
            }
        }

        return $filtered;
    }

    /**
     * Sets the validation scenario
     *
     * The scenario determines which validation rules apply. Rules defined
     * with an 'on' key will only be validated when the scenario matches.
     *
     * Example:
     * ```php
     * $user = User::blank();
     * $user->setScenario('register');
     * $user->set($data);
     * $user->save();  // Only 'register' rules apply
     * ```
     *
     * @param   string  $scenario  The scenario name
     * @return  self
     */
    public function setScenario(string $scenario)
    {
        $this->scenario = $scenario;
        return $this;
    }

    /**
     * Gets the current validation scenario
     *
     * @return  string
     */
    public function getScenario(): string
    {
        return $this->scenario;
    }

    /**
     * Define which attributes are safe for mass assignment per scenario
     *
     * Override this method in child classes to restrict which attributes
     * can be mass-assigned in each scenario. If not overridden, all
     * attributes are considered safe (BC behavior).
     *
     * Example:
     * ```php
     * public function scenarios(): array
     * {
     *     return [
     *         'default' => ['*'],  // All attributes safe
     *         'register' => ['username', 'email', 'password'],
     *         'update' => ['email', 'bio', 'avatar'],
     *     ];
     * }
     * ```
     *
     * @return  array  Scenario name => list of safe attributes
     */
    public function scenarios(): array
    {
        // Default: all attributes are safe in all scenarios (BC)
        return [
            'default' => ['*'],
        ];
    }

    /**
     * Get the list of attributes that are safe for mass assignment
     * in the current scenario
     *
     * @return  array  List of safe attribute names, or ['*'] for all
     */
    public function getSafeAttributes(): array
    {
        $scenarios = $this->scenarios();

        if (isset($scenarios[$this->scenario])) {
            return $scenarios[$this->scenario];
        }

        // Fall back to default scenario
        if (isset($scenarios['default'])) {
            return $scenarios['default'];
        }

        // Ultimate fallback: all attributes are safe (BC)
        return ['*'];
    }

    /**
     * Check if an attribute is safe for mass assignment in the current scenario
     *
     * @param   string  $attribute  The attribute name to check
     * @return  bool
     */
    public function isAttributeSafe(string $attribute): bool
    {
        $safe = $this->getSafeAttributes();

        // Wildcard means all attributes are safe
        if (in_array('*', $safe)) {
            return true;
        }

        return in_array($attribute, $safe);
    }

    /**
     * Adds a new rule to the validation set
     *
     * @param   string  $key   The field to which the rule applies
     * @param   mixed   $rule  The rule to add
     * @return  self
     **/
    public function addRule($key, $rule)
    {
        $this->rules[$key] = $rule;

        return $this;
    }

    /**
     * Get total number of rows
     *
         * @param   boolean     $distinct       Count distinct rows (default false, counts all rows)
     * @return  int
     **/
    public function total($distinct = false)
    {
        $count = $distinct ? 'distinct' : true;

        // Note that we do not need to parse includes at this stage, as includes do not effect
        // the primary result set, and thus do not effect the count. whereRelated() could effect
        // the count, but that method is not currently in use.
        //
        // We also reset the 'select' clause to avoid pulling unnecessary records and reset
        // the 'order by' clause to avoid referenced fields in the aforementioned 'select' clauses
        // that mgiht have been removed. Neither of these should have any effect on a count.
        $first = $this->deselect()
                              ->select($this->getQualifiedFieldName($this->getPrimaryKey()), 'count', $count)
                      ->unordered()
                      ->rows(false)
                      ->first();
                      //->count;

        $total = $first ? (int)$first->count : 0;

        $this->reset();

        return $total;
    }

    /**
     * Counts rows, fetching them first
     *
     * The {@link \Hubzero\Database\Rows} class also has a count method, which is used
     * to count rows after they've already been fetched.
     *
     * If possible, you shouldn't use this method.  We have to make a clone of the current
     * query so that it won't be empty if you later try to fetch the results of the original
     * query.  It would be better to go ahead and fetch the results and call the count
     * method on the rows object, thus potentially saving a query if you later plan
     * to fetch the original rows that you were trying to count.
     *
     * @return  int
     **/
    #[\ReturnTypeWillChange]
    public function count()
    {
        return $this->copy()->rows()->count();
    }

    /**
     * Gets the results of the established query
     *
     * @param   bool  $parseIncludes  Whether or not to parse the includes
     * @return  \Hubzero\Database\Rows
     **/
    public function rows($parseIncludes = true)
    {
        // Fetch the results
        if ($this->query === null) {
            $this->newQuery();
        }
        $rows = $this->rowsFromRaw($this->query->fetch('rows', $this->noCache));

        if ($parseIncludes) {
            $rows = $this->parseIncluding($rows);
        }

        // Set a few things on the rows object that might be helpful
        $rows->pagination = $this->pagination;
        $rows->orderBy    = $this->orderBy;
        $rows->orderDir   = $this->orderDir;
        return $rows;
    }

    /**
     * Gets the first/only row from the established query
     *
     * Not quite the same as rows, in that we're assuming an intentional
     * call to only get one row wouldn't want any pagination info included.
     *
     * @return  \Hubzero\Database\Relational|static
     **/
    public function row()
    {
        if ($this->query === null) {
            $this->newQuery();
        }
        $row = $this->query->fetch('row');

        return ($row) ? self::newFromResults($row) : self::blank();
    }

    /**
     * Sets the results of the query on new models and returns a Rows collection
     *
     * @param   array  $data  The data to set on the model
     * @return  \Hubzero\Database\Rows
     **/
    public function rowsFromRaw($data)
    {
        $rows = new Rows();

        if ($data && count($data) > 0) {
            foreach ($data as $row) {
                $rows->push(self::newFromResults($row));
            }
        }

        return $rows;
    }

    // =========================================================================
    // Batch Processing & Memory-Efficient Iteration
    // =========================================================================
    //
    // These methods enable processing large datasets without loading everything
    // into memory at once. Choose the right method for your use case:
    //
    // | Method      | Returns        | Mutation-Safe | Use Case                        |
    // |-------------|----------------|---------------|----------------------------------|
    // | chunk()     | Rows batches   | No            | Batch processing, callbacks      |
    // | chunkById() | Rows batches   | Yes           | Batch processing with mutations  |
    // | cursor()    | Single models  | No            | Memory-efficient iteration       |
    // | lazy()      | Single models  | No            | Alias for cursor()               |
    // | lazyById()  | Single models  | Yes           | Iteration with mutations         |
    // | batch()     | Rows batches   | No            | Generator-based batch iteration  |
    //
    // "Mutation-Safe" means you can delete/update records during iteration
    // without skipping or duplicating rows.
    // =========================================================================

    /**
     * Process query results in chunks using ID-based pagination (mutation-safe)
     *
     * Unlike {@see chunk()}, this method uses `WHERE id > last_id` instead of
     * OFFSET pagination. This makes it safe to use when modifying records during
     * iteration - deleting or updating rows won't cause skipped or duplicated processing.
     *
     * Example usage:
     * ```php
     * // Safely delete inactive users in batches
     * User::all()->whereEquals('active', 0)->chunkById(100, function($users) {
     *     foreach ($users as $user) {
     *         $user->destroy(); // Safe! Won't skip any rows
     *     }
     * });
     *
     * // Archive old posts (modifying the records being queried)
     * Post::all()->where('created', '<', '2020-01-01')->chunkById(50, function($posts) {
     *     foreach ($posts as $post) {
     *         $post->set('archived', 1)->save(); // Safe!
     *     }
     * });
     * ```
     *
     * ## How It Works
     *
     * Instead of:
     * ```sql
     * SELECT * FROM users LIMIT 100 OFFSET 0
     * SELECT * FROM users LIMIT 100 OFFSET 100  -- Affected by deletions!
     * ```
     *
     * This method uses:
     * ```sql
     * SELECT * FROM users WHERE id > 0 ORDER BY id LIMIT 100
     * SELECT * FROM users WHERE id > 100 ORDER BY id LIMIT 100  -- Unaffected!
     * ```
     *
     * ## Requirements
     *
     * - The column must have unique, sequential-ish values (typically the primary key)
     * - Results are ordered by the specified column ascending
     *
     * @param   int       $count     Number of records per chunk
     * @param   callable  $callback  Function to process each chunk. Return false to stop.
     * @param   string    $column    Column to paginate by (default: primary key)
     * @param   string    $alias     Column alias if using joins (optional)
     * @return  bool      True if all chunks processed, false if stopped early
     */
    public function chunkById(int $count, callable $callback, ?string $column = null, ?string $alias = null): bool
    {
        if ($count < 1) {
            throw new \InvalidArgumentException('Chunk size must be at least 1');
        }

        $column = $column ?? $this->getPrimaryKey();
        $alias = $alias ?? $column;
        $lastId = null;

        do {
            // Clone to avoid modifying the original query
            $query = $this->copy();

            // Add the ID constraint if we've processed at least one chunk
            if ($lastId !== null) {
                $query->where($column, '>', $lastId);
            }

            // Order by the column and limit
            $rows = $query->order($column, 'asc')->limit($count)->rows();

            $countResults = count($rows);

            if ($countResults === 0) {
                break;
            }

            // Call the callback with the chunk
            if ($callback($rows) === false) {
                return false;
            }

            // Get the last ID for the next iteration
            $lastId = $rows->last()->{$alias};
        } while ($countResults === $count);

        return true;
    }

    /**
     * Lazily iterate over query results using ID-based pagination (mutation-safe)
     *
     * Like {@see cursor()}, but uses ID-based pagination instead of OFFSET,
     * making it safe to use when modifying records during iteration.
     *
     * Example usage:
     * ```php
     * // Safely iterate and potentially delete
     * foreach (User::all()->whereEquals('active', 0)->lazyById() as $user) {
     *     if ($user->isInactive()) {
     *         $user->destroy(); // Safe! Won't skip any rows
     *     }
     * }
     *
     * // Process large dataset with mutation
     * foreach (Post::all()->lazyById(500) as $post) {
     *     $post->set('processed', 1)->save(); // Safe to modify
     * }
     * ```
     *
     * ## How It Works
     *
     * Instead of using OFFSET (which shifts when rows are deleted):
     * ```sql
     * SELECT * FROM users LIMIT 100 OFFSET 0
     * SELECT * FROM users LIMIT 100 OFFSET 100  -- Rows shifted!
     * ```
     *
     * This method uses WHERE with the last ID:
     * ```sql
     * SELECT * FROM users WHERE id > 0 ORDER BY id LIMIT 100
     * SELECT * FROM users WHERE id > 100 ORDER BY id LIMIT 100  -- Stable!
     * ```
     *
     * @param   int     $chunkSize  Internal batch size for database fetches (default: 1000)
     * @param   string  $column     Column to paginate by (default: primary key)
     * @param   string  $alias      Column alias if using joins (optional)
     * @return  \Generator  Yields individual model instances
     */
    public function lazyById(int $chunkSize = 1000, ?string $column = null, ?string $alias = null): \Generator
    {
        if ($chunkSize < 1) {
            throw new \InvalidArgumentException('Chunk size must be at least 1');
        }

        $column = $column ?? $this->getPrimaryKey();
        $alias = $alias ?? $column;
        $lastId = null;

        do {
            $query = $this->copy();

            if ($lastId !== null) {
                $query->where($column, '>', $lastId);
            }

            $rows = $query->order($column, 'asc')->limit($chunkSize)->rows();

            $countResults = count($rows);

            foreach ($rows as $row) {
                yield $row;
            }

            if ($countResults > 0) {
                $lastId = $rows->last()->{$alias};
            }
        } while ($countResults === $chunkSize);
    }

    /**
     * Triggers when attempting to iterator over the object, so we know to fetch results
     *
     * We go ahead and use a copy, that way future calls to the same model will
     * continue to have the initial query elements set in place
     *
     * @return  \Hubzero\Database\Rows
     **/
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return $this->copy()->rows();
    }

    /**
     * Sets the atrributes key with value
     *
     * @param   mixed $offset    The key to set, or array of key/value pairs
     * @param   mixed $value  The value to set if key is string
     * @return  void
     **/
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (is_array($offset) || is_object($offset)) {
            foreach ($offset as $k => $v) {
                $this->attributes[$k] = $v;
            }
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    /**
     * Checks to see if the requested attribute is set on the model
     *
     * @param   mixed  $offset  The offset to check for
     * @return  bool
     **/
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $this->hasAttribute($offset);
    }

    /**
     * Unsets the requested attribute from the model
     *
     * @param   mixed $offset  The offset to remove
     * @return  void
     **/
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Gets an attribute by key
     *
     * @param   mixed $offset  The attribute key to get
     * @return  mixed
     **/
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Retrieves one row by primary key value provided
     *
     * @param   mixed  $id  The primary key field value to use to retrieve one row
     * @return  \Hubzero\Database\Relational|static
     **/
    public static function one($id)
    {
        $instance = self::blank();
        return $instance->whereEquals($instance->getPrimaryKey(), $id)->rows()->seek($id);
    }

    /**
     * Retrieves one row by primary key, throwing a new exception if not found
     *
     * @param   mixed  $id  The primary key field value to use to retrieve one row
     * @return  \Hubzero\Database\Relational|static
     * @throws  Hubzero\Database\Exception\RuntimeException
     **/
    public static function oneOrFail($id)
    {
        $row = self::one($id);

        // Make sure we have a valid row
        if ($row === false) {
            throw new RuntimeException("Failed to retrieve a model with a primary key of {$id}", 404);
        }

        return $row;
    }

    /**
     * Retrieves one row by primary key, returning an empty row if not found
     *
     * @param   mixed  $id  The primary key field value to use to retrieve one row
     * @return  \Hubzero\Database\Relational|static
     **/
    public static function oneOrNew($id)
    {
        $row = self::one($id);

        // See if we have a valid row
        if ($row === false) {
            $row = self::blank();
        }

        return $row;
    }

    /**
     * Retrieves one row loaded by an alias field
     *
     * @param   string  $alias  The alias to load by
     * @return  mixed
     **/
    public static function oneByAlias($alias)
    {
        $instance = self::blank();
        return $instance->whereEquals('alias', $alias)->row();
    }

    /**
     * Returns all rows (unless otherwise limited)
     *
     * @param   string|array  $columns  The columns to select
     * @return  \Hubzero\Database\Relational|static
     **/
    public static function all($columns = null)
    {
        return self::blank();
    }

    /**
     * Insert multiple rows in a single query
     *
     * This method is significantly faster than creating and saving models one at a time,
     * as it reduces network round-trips and query parsing overhead (typically 10-50x faster).
     *
     * IMPORTANT: This is a low-level insert that bypasses model validation, events,
     * and automatic timestamps. Use this for bulk data imports or migrations where
     * you've already validated the data.
     *
     * Example:
     * ```php
     * User::insertMany([
     *     ['name' => 'Alice', 'email' => 'alice@example.com', 'created' => date('Y-m-d H:i:s')],
     *     ['name' => 'Bob', 'email' => 'bob@example.com', 'created' => date('Y-m-d H:i:s')],
     * ]);
     * ```
     *
     * For very large datasets, automatic chunking prevents packet size limits:
     * ```php
     * User::insertMany($thousandsOfRecords, chunkSize: 500);
     * ```
     *
     * @param   array   $rows       Array of associative arrays (each row is column => value)
     * @param   bool    $ignore     Whether to use INSERT IGNORE (skip duplicates)
     * @param   int     $chunkSize  Rows per batch (0 = no chunking, default 1000)
     * @return  int     Number of rows inserted
     * @throws  \InvalidArgumentException  If rows have inconsistent structure
     **/
    public static function insertMany(array $rows, bool $ignore = false, int $chunkSize = 1000): int
    {
        if (empty($rows)) {
            return 0;
        }

        $instance = self::blank();
        $table = $instance->getTableName();

        return $instance->getQuery()->insertMany($table, $rows, $ignore, $chunkSize);
    }

    /**
     * Upserts multiple rows in a single query
     *
     * Low-level bulk upsert that bypasses model validation, events,
     * and automatic timestamps. Use for bulk data imports where
     * performance is critical.
     *
     * @param   array       $rows            Array of associative arrays
     * @param   array|null  $updateColumns   Columns to update on conflict
     * @param   array|null  $conflictColumns Columns defining conflict
     * @param   int         $chunkSize       Rows per batch (default 1000)
     * @return  int         Number of rows affected
     * @throws  \InvalidArgumentException  If rows have inconsistent structure
     **/
    public static function upsertMany(
        array $rows,
        ?array $updateColumns = null,
        ?array $conflictColumns = null,
        int $chunkSize = 1000
    ): int {
        if (empty($rows)) {
            return 0;
        }

        $instance = self::blank();
        $table = $instance->getTableName();

        return $instance->getQuery()->upsertMany(
            $table,
            $rows,
            $updateColumns,
            $conflictColumns,
            $chunkSize
        );
    }

    /**
     * Finds the first record matching attributes, or creates a new one
     *
     * Searches for a record matching the given attributes. If found,
     * returns the existing record. If not found, creates and saves a
     * new record with the attributes merged with additional values.
     *
     * Uses application-level SELECT + INSERT so model events,
     * validation, and automatics work correctly.
     *
     * @param   array  $attributes  Key-value pairs to search by
     * @param   array  $values      Additional values for creation only
     * @return  static
     **/
    public static function firstOrCreate(
        array $attributes,
        array $values = []
    ) {
        $instance = static::blank();

        foreach ($attributes as $key => $value) {
            $instance->whereEquals($key, $value);
        }

        $existing = $instance->rows()->first();

        if ($existing !== false) {
            return $existing;
        }

        // Create new record
        $newInstance = static::blank();
        foreach (array_merge($attributes, $values) as $key => $value) {
            $newInstance->set($key, $value);
        }

        $newInstance->save();
        return $newInstance;
    }

    /**
     * Finds a record matching attributes and updates it, or creates new
     *
     * Searches for a record matching the given attributes. If found,
     * updates it with the provided values. If not found, creates a new
     * record with attributes merged with values.
     *
     * Uses application-level SELECT + UPDATE/INSERT so model events,
     * validation, and automatics work correctly.
     *
     * @param   array  $attributes  Key-value pairs to search by
     * @param   array  $values      Key-value pairs to update/set
     * @return  static
     **/
    public static function updateOrCreate(
        array $attributes,
        array $values = []
    ) {
        $instance = static::blank();

        foreach ($attributes as $key => $value) {
            $instance->whereEquals($key, $value);
        }

        $existing = $instance->rows()->first();

        if ($existing !== false) {
            foreach ($values as $key => $value) {
                $existing->set($key, $value);
            }
            $existing->save();
            return $existing;
        }

        // Create new record
        $newInstance = static::blank();
        foreach (array_merge($attributes, $values) as $key => $value) {
            $newInstance->set($key, $value);
        }

        $newInstance->save();
        return $newInstance;
    }

    /**
     * Retrieves only the most recent applicable row
     *
     * This orders results by the limiter, and grabs the first one.
     * It by default assumes you want to order by created date.
     *
     * @param   string  $limiter  The column name to use to determine the latest row
     * @return  \Hubzero\Database\Relational|static
     **/
    public function latest($limiter = 'created')
    {
        return $this->order($limiter, 'desc')->limit(1)->rows()->first();
    }

    /**
     * Saves the current model to the database
     *
     * Fires lifecycle events in this order:
     * - saving (can cancel by returning false)
     * - creating/updating (can cancel by returning false)
     * - [database operation]
     * - created/updated
     * - saved
     * - system.onContentSave (existing HubZero event)
     *
     * @return  bool
     **/
    public function save()
    {
        // Ensure model is booted so trait callbacks (like HasUuid) are registered
        $this->bootIfNotBooted();

        // Validate
        if (!$this->validate()) {
            return false;
        }

        // Handle cases where the primary key might be an empty string
        // For auto-increment in strict-mode DBs, this needs to be NULL
        // instead.
        if (
            $this->hasAttribute($this->getPrimaryKey())
            && !$this->get($this->getPrimaryKey())
        ) {
            $this->set($this->getPrimaryKey(), null);
        }

        // See if we're creating or updating
        $isNew = $this->isNew();
        $method = $isNew ? 'create' : 'modify';

        // Fire "saving" event (can cancel)
        if ($this->fireModelEvent('saving') === false) {
            return false;
        }

        // Fire "creating" or "updating" event (can cancel)
        $event = $isNew ? 'creating' : 'updating';
        if ($this->fireModelEvent($event) === false) {
            return false;
        }

        // Perform DB writes atomically so parent and cascade operations
        // are committed or rolled back together.
        $result = $this->executeAtomically(function () use ($method, $isNew) {
            $result = $this->$method();

            if (!$result) {
                return $result;
            }

            // If creating, result is our new id, so set that back on the model
            // But only if the PK isn't already set (e.g., for UUID primary keys)
            if ($isNew) {
                $pk = $this->getPrimaryKey();
                $existingPk = $this->get($pk);

                // Only overwrite PK if not already set (for auto-increment columns)
                if (empty($existingPk)) {
                    $this->set($pk, $result);
                }
            }

            // Capture what changed (getDirty() compares current to original)
            // This must happen before syncOriginal() clears the dirty state
            $this->changes = $this->getDirty();

            // Purge cache
            $this->purgeCache();

            if ($isNew) {
                $pk = $this->getPrimaryKey();
                $existingPk = $this->get($pk);

                // Include the model PK in changes
                $this->changes[$pk] = empty($existingPk) ? $result : $existingPk;

                if (class_exists('Event')) {
                    \Event::trigger($this->getTableName() . '_new', ['model' => $this, 'changes' => $this->changes]);
                }
            }

            // Sync original state now that changes are saved
            $this->syncOriginal();

            // Fire "created" or "updated" event (no cancellation)
            $this->fireModelEvent($isNew ? 'created' : 'updated', false);

            // Fire "saved" event (no cancellation)
            $this->fireModelEvent('saved', false);

            // Existing HubZero system event (only if Event facade is available)
            if (class_exists('Event')) {
                \Event::trigger('system.onContentSave', array($this->getTableName(), $this, $this->changes));
            }

            // Handle cascade saves and orphan removal (opt-in)
            if ($this->cascadeRelationships) {
                if (!$this->performCascadeSaves()) {
                    return false;
                }

                if (!$this->performOrphanRemovals()) {
                    return false;
                }
            }

            return $result;
        });

        return $result;
    }

    /**
     * Execute callback within a transaction and rollback on explicit false.
     *
     * @param   callable  $callback
     * @return  mixed
     * @throws  \Throwable
     */
    protected function executeAtomically(callable $callback)
    {
        $connection = self::$connection;

        if (
            !$connection
            || !method_exists($connection, 'transactionStart')
            || !method_exists($connection, 'transactionCommit')
            || !method_exists($connection, 'transactionRollback')
        ) {
            return $callback();
        }

        $connection->transactionStart();

        try {
            $result = $callback();

            if ($result === false) {
                $connection->transactionRollback();
                return false;
            }

            $connection->transactionCommit();
            return $result;
        } catch (\Throwable $e) {
            $connection->transactionRollback();
            throw $e;
        }
    }

    /**
     * Update the model's timestamp without changing other attributes
     *
     * This is useful for "last activity" tracking or marking a record as
     * recently accessed without modifying its actual data.
     *
     * Example:
     *   $user->touch();                    // Updates 'modified' column
     *   $user->touch('last_login_at');     // Updates custom column
     *
     * @param   string|null  $column  The timestamp column to update (default: 'modified')
     * @return  bool         True on success, false if column doesn't exist or update fails
     **/
    public function touch($column = null)
    {
        $column = $column ?: 'modified';

        // Check if the model has been saved (has a primary key)
        if ($this->isNew()) {
            return false;
        }

        // Check if the column exists in the table
        $columns = $this->getTableColumns();
        if (!isset($columns[$column])) {
            return false;
        }

        $now = date('Y-m-d H:i:s');

        // Update the attribute locally
        $this->set($column, $now);

        // Perform direct update without full save cycle
        $result = $this->getQuery()
            ->update($this->getTableName())
            ->set([$column => $now])
            ->whereEquals($this->getPrimaryKey(), $this->getPkValue())
            ->execute();

        if ($result) {
            // Sync the original value so it's not marked as dirty
            $this->original[$column] = $now;
        }

        return (bool) $result;
    }

    /**
     * Reload the model from the database
     *
     * This refreshes the current instance with fresh data from the database,
     * discarding any unsaved changes. Cached relationships are also cleared.
     *
     * Use this when you need to ensure your model reflects the current database
     * state, such as after a background job might have modified the record.
     *
     * Example:
     *   $article->set('title', 'Draft');  // Local change
     *   $article->refresh();               // Reload from DB, discards change
     *   echo $article->get('title');       // Shows database value
     *
     * @return  $this|false  Returns $this on success, false if model has no primary key or not found
     **/
    public function refresh()
    {
        // Cannot refresh a model that hasn't been saved
        if ($this->isNew()) {
            return false;
        }

        $pk = $this->getPrimaryKey();
        $id = $this->getPkValue();

        // Fetch fresh data from database, bypassing query cache
        $fresh = static::blank()->disableCaching()
            ->whereEquals($pk, $id)->rows()->seek($id);

        if ($fresh === false) {
            return false;
        }

        // Replace attributes with fresh data
        $this->attributes = $fresh->getAttributes();

        // Sync original to match (no dirty state after refresh)
        $this->syncOriginal();

        // Clear cached relationships
        $this->relationships = [];

        return $this;
    }

    /**
     * Get a fresh instance of this model from the database
     *
     * Unlike refresh(), this returns a NEW model instance while leaving
     * the current instance unchanged. Useful when you need to compare
     * the current state with the database state.
     *
     * Example:
     *   $article->set('title', 'Draft');      // Local change
     *   $freshCopy = $article->fresh();       // New instance from DB
     *   echo $article->get('title');          // "Draft" (unchanged)
     *   echo $freshCopy->get('title');        // Database value
     *
     *   // With eager loading
     *   $freshWithRelations = $article->fresh(['comments', 'author']);
     *
     * @param   array|string|null  $with  Relationships to eager load
     * @return  static|false  New model instance, or false if not found
     **/
    public function fresh($with = null)
    {
        // Cannot get fresh copy of a model that hasn't been saved
        if ($this->isNew()) {
            return false;
        }

        $pk = $this->getPrimaryKey();
        $id = $this->getPkValue();

        // Always bypass query cache — fresh() must hit the database
        $instance = static::blank()->disableCaching();

        // If relationships specified, use eager loading
        if ($with !== null) {
            $with = is_array($with) ? $with : [$with];
            return $instance->with(...$with)->whereEquals($pk, $id)->row();
        }

        return $instance->whereEquals($pk, $id)->rows()->seek($id);
    }

    /**
     * Create a copy of the model without the primary key
     *
     * This method clones the model's attributes but removes the primary key
     * and optionally other attributes (like timestamps), so that when saved
     * it creates a new database record.
     *
     * Example:
     *   $original = Article::one(123);
     *   $copy = $original->replicate();
     *   $copy->set('title', $original->get('title') . ' (Copy)');
     *   $copy->save();  // Creates new record
     *
     *   // Exclude additional attributes
     *   $copy = $original->replicate(['slug', 'published_at']);
     *
     * @param   array|null  $except  Additional attributes to exclude from the copy
     * @return  static  New unsaved model instance
     **/
    public function replicate(?array $except = null)
    {
        // Default attributes to exclude (primary key + common timestamp/audit fields)
        $defaults = [
            $this->getPrimaryKey(),
            'created',
            'created_at',
            'modified',
            'modified_at',
            'updated_at',
        ];

        // Merge with user-specified exclusions
        $except = array_unique(array_merge(
            $defaults,
            $except ?? []
        ));

        // Get all attributes except the excluded ones
        $attributes = array_diff_key(
            $this->getAttributes(),
            array_flip($except)
        );

        // Create new instance and set the attributes
        $instance = new static();

        foreach ($attributes as $key => $value) {
            $instance->set($key, $value);
        }

        return $instance;
    }

    /**
     * Get database table columns
     *
     * @return  array
     **/
    public function getTableColumns()
    {
        $tableName = $this->getTableName();
        $connection = self::$connection;

        if ($connection instanceof Driver) {
            if (!$connection->hasCachedTableColumns($tableName)) {
                $columns = (array) $this->getStructure()->getTableColumns($tableName, false);

                if (empty($columns)) {
                    throw new \Exception(sprintf('Columns not found for table %s', $tableName));
                }

                $connection->setCachedTableColumns($tableName, $columns);
            }

            return $connection->getCachedTableColumns($tableName);
        }

        $columns = (array) $this->getStructure()->getTableColumns($tableName, false);

        if (empty($columns)) {
            throw new \Exception(sprintf('Columns not found for table %s', $tableName));
        }

        return $columns;
    }

    /**
     * Clear table column metadata cache.
     *
     * When both parameters are null, the entire cache is cleared.
     *
     * @param   string|null  $tableName     Optional table name to clear.
     * @param   string|null  $connectionId  Optional connection id to scope clear.
     * @return  void
     */
    public static function clearTableColumnsCache(?string $tableName = null, ?string $connectionId = null): void
    {
        Driver::flushAllTableColumnsCaches($tableName, $connectionId);
    }

    /**
     * Filters out fields that are not actually a table column
     *
     * @return  array
     **/
    public function getTableColumnsOnly()
    {
        return array_intersect_key($this->attributes, $this->getTableColumns());
    }

    /**
     * Get the defined default value for a database table column
     *
     * @param   string  $col  The name of the database table column
     * @return  mixed
     **/
    public function getTableColumnDefault($col)
    {
        $columns = $this->getTableColumns();

        if (isset($columns[$col])) {
            return $columns[$col]['default'];
        }

        return null;
    }

    /**
     * Inserts a new row into the database
     *
     * @return  bool
     **/
    private function create()
    {
        // Add any automatic fields
        $this->parseAutomatics('initiate');

        $data = $this->getTableColumnsOnly();

        if ($this->query === null) {
            $this->newQuery();
        }
        return $this->query->push($this->getTableName(), $data);
    }

    /**
     * Updates an existing item in the database
     *
     * @return  bool
     **/
    private function modify()
    {
        // Add any automatic fields (e.g., updated_at)
        $this->parseAutomatics('renew');

        // Only include changed attributes that exist as table columns
        $dirty = $this->getDirty();
        $data = array_intersect_key($dirty, $this->getTableColumns());

        // Remove the primary key from SET — it belongs in WHERE only
        $pk = $this->getPrimaryKey();
        unset($data[$pk]);

        // Nothing changed — skip the query
        if (empty($data)) {
            return true;
        }

        if ($this->query === null) {
            $this->newQuery();
        }
        return $this->query->alter(
            $this->getTableName(),
            $pk,
            $this->getPkValue(),
            $data
        );
    }

    /**
     * Parses for automatically fillable fields
     *
     * @param   string  $scope  The scope of rules to parse and run
     * @return  self
     **/
    private function parseAutomatics($scope = 'always')
    {
        $automatics = array_merge($this->$scope, $this->always);

        if (!empty($automatics)) {
            foreach ($automatics as $field) {
                if (strpos($field, '_')) {
                    $bits   = explode('_', $field);
                    $bits   = array_map('ucfirst', $bits);
                    $method = implode('', $bits);
                } else {
                    $method = ucfirst($field);
                }

                $method = 'automatic' . $method;
                // Pass the data to the method in case it needs to make use of another field's value
                $this->set($field, $this->$method($this->attributes));
            }
        }

        return $this;
    }

    /**
     * Saves the current model and any subsequent attached models
     *
     * @return  bool
     **/
    public function saveAndPropagate()
    {
        if (!$this->save()) {
            return false;
        }

        // Loop through the relationships and save
        // Both rows and models know how to save, so it doesn't matter
        // which of the two the particular relationship returned
        foreach ($this->getRelationships() as $relationship) {
            if (!$relationship->save()) {
                $this->setErrors($relationship->getErrors());
                return false;
            }
        }

        return true;
    }

    /**
     * Deletes the existing/current model
     *
     * Fires lifecycle events in this order:
     * - deleting (can cancel by returning false)
     * - [database operation]
     * - deleted
     * - system.onContentDestroy (existing HubZero event)
     *
     * @return  bool
     **/
    public function destroy()
    {
        // Fire "deleting" event (can cancel)
        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        // If it has an associated asset entry, try deleting that first
        if ($this->hasAttribute('asset_id')) {
            if (!Asset::destroy($this)) {
                return false;
            }
        }

        $result = $this->executeAtomically(function () {
            // Handle cascade deletes (opt-in)
            if ($this->cascadeRelationships && !$this->performCascadeDeletes()) {
                return false;
            }

            if ($this->query === null) {
                $this->newQuery();
            }

            return $this->query->remove(
                $this->getTableName(),
                $this->getPrimaryKey(),
                $this->getPkValue()
            );
        });

        if ($result) {
            // Fire "deleted" event (no cancellation)
            $this->fireModelEvent('deleted', false);

            // Existing HubZero system event (only if Event facade is available)
            if (class_exists('Event')) {
                \Event::trigger('system.onContentDestroy', array($this->getTableName(), $this));
            }
        }

        return $result;
    }

    /**
     * Checks out the current model to the provided user
     *
     * @param   int  $userId  Optional userId for whom the row should be checked out
     * @return  bool
     **/
    public function checkout($userId = null)
    {
        if (!$this->isNew()) {
            $columns = $this->getTableColumns();

            $data = [];

            if (isset($columns['checked_out_time'])) {
                $data['checked_out_time'] = date('Y-m-d H:i:s');
            }

            if (isset($columns['checked_out'])) {
                $userId = $userId ?: static::resolveCurrentUserId();
                $data['checked_out'] = $userId;
            }

            if (empty($data)) {
                // There is no 'checked_out_time' or 'checked_out' column
                return true;
            }

            $this->set($data);

            // We build a simple update query as calling save()
            // can have unintended consequences when all we want
            // is to update two columns
            $query = $this->getQuery()
                ->update($this->getTableName())
                ->set($data)
                ->whereEquals($this->getPrimaryKey(), $this->get($this->getPrimaryKey()));

            // @FIXME: Maybe unnecessary? Database may throw an exception on error
            //         so this might be pointless.
            if (!$query->execute()) {
                $this->addError(__CLASS__ . '::' . __METHOD__ . '() failed');
                return false;
            }
        }

        return true;
    }

    /**
     * Checks back in the current model
     *
     * @return  bool
     **/
    public function checkin()
    {
        if (!$this->isNew()) {
            $columns = $this->getTableColumns();

            $data = [];
            $orig = [];

            // We want to get the default values from the
            // table's schema, rather than assuming
            if (isset($columns['checked_out_time'])) {
                $orig['checked_out_time'] = $this->get('checked_out_time');
                $data['checked_out_time'] = $columns['checked_out_time']['default'];
            }

            if (isset($columns['checked_out'])) {
                $orig['checked_out'] = $this->get('checked_out');
                $data['checked_out'] = $columns['checked_out']['default'];
            }

            if (empty($data)) {
                // There is no 'checked_out_time' or 'checked_out' column
                return true;
            }

            $this->set($data);

            // We build a simple update query as calling save()
            // can have unintended consequences when all we want
            // is to update two columns
            $query = $this->getQuery()
                ->update($this->getTableName())
                ->set($data)
                ->whereEquals($this->getPrimaryKey(), $this->get($this->getPrimaryKey()));

            // @FIXME: Maybe unnecessary? Database may throw an exception on error
            //         so this might be pointless.
            if (!$query->execute()) {
                // Reset to original data
                $this->set($orig);

                $this->addError(__CLASS__ . '::' . __METHOD__ . '() failed');
                return false;
            }
        }

        return true;
    }

    /**
     * Checks to see if the current model is checked out by someone else
     *
     * @return  bool
     **/
    public function isCheckedOut()
    {
        return ($this->get('checked_out') && $this->get('checked_out') != static::resolveCurrentUserId());
    }

    /**
     * Selects applicable rows on the relation and limits current query accordingly
     *
     * NOTE: whereas other 'where' clauses can be called statically due to their
     * location in the query builder class, this method cannot be as it is attached
     * directly to the model itself.
     *
     * @param   string   $relationship  The relationship name
     * @param   Closure  $constraint    The constraint to apply to the related query
     * @param   int      $depth         The depth level of the clause, for sub clauses
     * @return  self
     **/
    public function whereRelatedHas($relationship, $constraint, $depth = 0)
    {
        $rel  = $this->$relationship();
        $keys = $rel->getConstrainedKeys($constraint);

        return $this->where($rel->getLocalKey(), 'IN', $keys, 'and', $depth);
    }

    /**
     * Selects applicable rows on the relation and limits current query accordingly
     *
     * NOTE: whereas other 'where' clauses can be called statically due to their
     * location in the query builder class, this method cannot be as it is attached
     * directly to the model itself.
     *
     * @param   string   $relationship  The relationship name
     * @param   Closure  $constraint    The constraint to apply to the related query
     * @param   int      $depth         The depth level of the clause, for sub clauses
     * @return  self
     **/
    public function orWhereRelatedHas($relationship, $constraint, $depth = 0)
    {
        $rel  = $this->$relationship();
        $keys = $rel->getConstrainedKeys($constraint);

        return $this->where($rel->getLocalKey(), 'IN', $keys, 'or', $depth);
    }

    /**
     * Selects rows where related table has at least x number of entries
     *
     * NOTE: whereas other 'where' clauses can be called statically due to their
     * location in the query builder class, this method cannot be as it is attached
     * directly to the model itself.
     *
     * @param   string  $relationship  The relationship name to constrain against
     * @param   int     $count         The minimum number of rows required
     * @param   int     $depth         The depth level of the clause, for sub clauses
     * @param   string  $operator      The comparison operator used between the column and the count
     * @return  self
     **/
    public function whereRelatedHasCount($relationship, $count = 1, $depth = 0, $operator = '>=')
    {
        $rel  = $this->$relationship();
        $keys = $rel->getConstrainedKeysByCount($count, $operator);

        return $this->where($rel->getLocalKey(), 'IN', $keys, 'and', $depth);
    }

    /**
     * Limits current model based on conditions of relationship
     *
     * @FIXME: decide whether or not to use this
     *
     * This is NOT currently used. The problem here has to do with relationship data.
     * If you constrain based on a relationship, and then later on end up wanting to access
     * properties of that relationship, it will currently do two queries.  Instead, we
     * could get the data with the original constraint and attach it to the models in a
     * similar fashion to the way that with() works.
     *
     * To make this work, data would need to be stored on the object, and then seeded
     * after the model rows are fetched (like parseIncludes() works now).
     *
     * @param \Hubzero\Database\Relationship\Relationship $relationsip
     * @param Closure $constraint
     * @return  self
     **/
    /*
    private function whereRelated($relationship, $constraint)
    {
        $this->data = [];
        $keys       = null;

        // Parse for nested relationships
        if (strpos($name, '.'))
        {
            // If we have a nested name, pull out the first one
            list($name, $subs)  = explode('.', $name, 2);
            $relationship       = $this->$name();
            $this->data[$name]  = $relationship->whereRelated($subs, $constraint);
        } else
        {
            $relationship       = $this->$name();
            $this->data[$name]  = $relationship->getConstrainedRows($constraint);
        }

        // Update keys to only include those in this and previous results
        $keys = is_null($keys) ? $relationship->getRelatedKeysFromRows($this->data[$name])
                               : array_intersect($keys, $relationship->getRelatedKeysFromRows($this->data[$name]));

        // Only keep unique keys
        $keys = array_unique($keys);

        // Set our where clause if needed
        if (!empty($keys))
        {
            $this->whereIn($relationship->getLocalKey(), $keys);
        }

        return $this;
    }
    */

    /**
     * Seeds the rows with any pre-fetched data
     *
     * @FIXME: decide whether or not to use this
     *
     * @param   \Hubzero\Database\Rows  $rows  The rows to seed
     * @return  \Hubzero\Database\Rows
     **/
    private function seed($rows)
    {
        // Set our constrained (pre-fetched data) back on the rows
        foreach ($this->data as $relationship => $data) {
            $rows = $this->$relationship()->seedWithData($rows, $data, $relationship);
        }

        return $rows;
    }

    /**
     * Applies a where clause comparing a field to the current user id
     *
     * NOTE: whereas other 'where' clauses can be called statically due to their
     * location in the query builder class, this method cannot be as it is attached
     * directly to the model itself.
     *
     * @param   string  $column  The field to use for ownership, defaulting to 'created_by'
     * @return  self
     **/
    public function whereIsMine($column = 'created_by')
    {
        $this->whereEquals($column, static::resolveCurrentUserId());
        return $this;
    }

    /**
     * Validates the set data attributes against the model rules
     *
     * @return  bool
     **/
    public function validate()
    {
        $validity = Rules::validate($this->attributes, $this->getRules());

        if ($validity === true) {
            return true;
        }

        $this->setErrors($validity);
        return false;
    }

    /**
     * Chunks the retrieved data based on a given chunk limit
     *
     * @param   int    $size  The chunk size
     * @return  self
     **/
    public function paginate($size)
    {
        // @FIXME: implement!
        return $this;
    }

    /**
     * Retrieves a chuck of data based on standard pagination parameters
     *
     * @param   string  $start  The request variable used to denote limit start
     * @param   string  $limit  The request variable used to denote limit of results to return
     * @return  self
     **/
    public function paginated($start = 'start', $limit = 'limit')
    {
        $this->pagination = Pagination::init($this->getModelName(), $this->copy()->total(), $start, $limit);

        // Set start and limit on query
        $this->start($this->pagination->start);
        $this->limit($this->pagination->limit);

        return $this;
    }

    /**
     * Sets the ordering based on the established request variables
     *
     * @param   string  $orderBy   The request variable used to denote ordering column
     * @param   string  $orderDir  The request variable used to denote ordering direction
     * @return  self
     **/
    public function ordered($orderBy = 'orderby', $orderDir = 'orderdir')
    {
        // Look for our request vars of interest
        $this->orderBy  = \Request::getCmd($orderBy, $this->getState('orderby', $this->orderBy));
        $this->orderDir = \Request::getCmd($orderDir, $this->getState('orderdir', $this->orderDir));

        $qualifiedOrderBy = $this->orderBy;

        // If we have a '.' we'll assume the prefix is a relationship name
        if (strpos($this->orderBy, '.') !== false) {
            list($relationship, $field) = explode('.', $this->orderBy);

            // We have to join to apply the order by clause
            $relationship     = $this->$relationship()->join();
            $qualifiedOrderBy = $relationship->getQualifiedFieldName($field);
        }

        // Apply order clause
        $this->order($qualifiedOrderBy, $this->orderDir);

        // Set state for future use
        $this->setState('orderby', $this->orderBy);
        $this->setState('orderdir', $this->orderDir);

        return $this;
    }

    /**
     * Unsets the ordering
     *
     * @return  self
     **/
    public function unordered()
    {
        $this->unorder();

        return $this;
    }

    /**
     * Retrieves state vars set in the model namespace
     *
     * @param   string  $public The public to attempt to retrieve
     * @param   mixed   $default  The default to return, should the public be unknown
     * @return  mixed
     **/
    public function getState($var, $default = null)
    {
        $key = str_replace('\\', '.', $this->getModelNamespace()) . '.' . $this->getModelName() . ".{$var}";
        return \User::getState($key, $default);
    }

    /**
     * Sets state vars on the model namespace
     *
     * @param   string  $key    The key under which the value will go
     * @param   mixed   $value  The value to assign to the key
     * @return  void
     **/
    public function setState($key, $value)
    {
        $key = str_replace('\\', '.', $this->getModelNamespace()) . '.' . $this->getModelName() . ".{$key}";
        \User::setState($key, $value);
    }

    /**
     * Checks whether or not the current user is the owner/creator of the row
     *
     * @param   string  $field  The field by which creation is determined
     * @return  bool
     * @throws  \Hubzero\Database\Exception\RuntimeException  If rows have not first been fetched
     **/
    public function isCreator($field = 'created_by')
    {
        // Make sure we have a valid row
        if (!$this->hasAttribute($field)) {
            throw new RuntimeException('Cannot determine creator of non-existent row(s)');
        }

        return $this->$field == static::resolveCurrentUserId();
    }

    /**
     * Finds the named class, checking a handful of scopes
     *
     * @param   string  $name  The name of the relationship to resolve
     * @return  object
     * @throws  \Hubzero\Database\Exception\RuntimeException  If a class of name cannot be found
     **/
    private function resolve($name)
    {
        if (!class_exists($name)) {
            // Get the scope of the current class and check there too
            $name = $this->getModelNamespace() . '\\' . $name;

            if (!class_exists($name)) {
                throw new RuntimeException("Relationship '{$name}' not found");
            }
        }

        return new $name();
    }

    /**
     * Retrieves a one to one model relationship
     *
     * @param   string       $model     The name of the primary model
     * @param   string|null  $childKey  The child key that point to the local key
     * @param   string|null  $thisKey   The local key on the model
     * @return  \Hubzero\Database\Relationship\OneToOne
     **/
    public function oneToOne($model, $childKey = null, $thisKey = null)
    {
        // Default the keys if not set
        $thisKey  = $thisKey  ?: $this->getPrimaryKey();
        $childKey = $childKey ?: strtolower($this->getModelName()) . '_id';

        return new OneToOne($this, $this->resolve($model), $thisKey, $childKey);
    }

    /**
     * Retrieves a one to many model relationship
     *
     * @param   string       $model       The name of the model to relate to the current one
     * @param   string|null  $foreignKey  The foreign key used to associate the many back to the model
     * @param   string|null  $thisKey     The local key used to associate the many back to the model
     * @return  \Hubzero\Database\Relationship\OneToMany
     **/
    public function oneToMany($model, $relatedKey = null, $thisKey = null)
    {
        // Default the keys if not set
        $thisKey    = $thisKey    ?: $this->getPrimaryKey();
        $relatedKey = $relatedKey ?: strtolower($this->getModelName()) . '_id';

        return new OneToMany($this, $this->resolve($model), $thisKey, $relatedKey);
    }

    /**
     * Retrieves a one shifts to many model relationship
     *
     * This is very similar to a one to many relationship, except that we also need to
     * constrain by a scope type.  Additionally, the related key is actually most likely
     * static (scope_id), rather than dynamic based on the model name.
     *
     * @param   string       $model       The name of the model to relate to the current one
     * @param   string|null  $relatedKey  The foreign key used to associate the many back to the model
     * @param   string|null  $shifter     The many side field used to differentiate/shift models
     * @param   string|null  $thisKey     The local key used to associate the many back to the model
     * @return  \Hubzero\Database\Relationship\OneShiftsToMany
     **/
    public function oneShiftsToMany($model, $relatedKey = 'scope_id', $shifter = 'scope', $thisKey = null)
    {
        // Default the keys if not set
        $thisKey = $thisKey ?: $this->getPrimaryKey();

        return new OneShiftsToMany($this, $this->resolve($model), $thisKey, $relatedKey, $shifter);
    }

    /**
     * Retrieves a many to many model relationship
     *
     * @param   string       $model             The name of the model to relate to the current one
     * @param   string       $associativeTable  The name of the intermediate table used to associate model->related
     * @param   string|null  $thisKey           The local key used on the associative table
     * @param   string|null  $relatedKey        The related key used on the associative table
     * @return  \Hubzero\Database\Relationship\ManyToMany
     **/
    public function manyToMany($model, $associativeTable = null, $thisKey = null, $relatedKey = null)
    {
        $related   = $this->resolve($model);
        $names     = [strtolower($this->getModelName()), strtolower($related->getModelName())];
        $namespace = (!$this->namespace ? '' : $this->namespace . '_');

        // Sort names alphabetically so both sides of manyToMany will resolve to the same table name
        sort($names);

        // Default the keys and table if not set
        $associativeTable = $associativeTable ?: '#__' . $namespace . implode('_', $names);
        $thisKey          = $thisKey          ?: strtolower($this->getModelName()) . '_id';
        $relatedKey       = $relatedKey       ?: strtolower($related->getModelName()) . '_id';

        return new ManyToMany($this, $related, $associativeTable, $thisKey, $relatedKey);
    }

    /**
     * Retrieves a many shifts to many model relationship
     *
     * @param   string       $model             The name of the model to relate to the current one
     * @param   string       $associativeTable  The name of the intermediate table used to associate model->related
     * @param   string|null  $thisKey           The local key used on the associative table
     * @param   string       $shifter           The many side field used to differentiate/shift models
     * @param   string       $relatedKey        The related key used on the associative table
     * @return  \Hubzero\Database\Relationship\ManyShiftsToMany
     **/
    public function manyShiftsToMany(
        $model,
        $associativeTable = null,
        $thisKey = 'scope_id',
        $shifter = 'scope',
        $relatedKey = null
    ) {
        $related = $this->resolve($model);

        // Default the keys and table if not set
        $associativeTable = $associativeTable ?: '#__' . strtolower($related->getModelName()) . '_object';
        $relatedKey       = $relatedKey       ?: strtolower($related->getModelName()) . '_id';

        return new ManyShiftsToMany($this, $related, $associativeTable, $thisKey, $relatedKey, $shifter);
    }

    /**
     * Retrieves a belongs to one model relationship
     *
     * @param   string       $model      The name of the model to relate to the current one
     * @param   string|null  $thisKey    The local key used to associate the many back to the model
     * @param   string|null  $parentKey  The parent key used to associate the model to its parent
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     **/
    public function belongsToOne($model, $thisKey = null, $parentKey = null)
    {
        $parent = $this->resolve($model);

        // Default the keys if not set
        $thisKey   = $thisKey   ?: strtolower($parent->getModelName()) . '_id';
        $parentKey = $parentKey ?: $this->getPrimaryKey();

        return new BelongsToOne($this, $parent, $thisKey, $parentKey);
    }

    /**
     * Retrieves a one to many through model relationship
     *
     * Note that here, versus the manyToMany relationship, we assume the 'through' item
     * actually has a formal model for it, rather than just an intermediate table name.
     *
     * @param   string       $model       The name of the related model to associate to the current one
     * @param   string       $through     The name of the intermediate model
     * @param   string|null  $relatedKey  The related key used to associate the model to its parent
     * @param   string|null  $localKey    The local key used to associate the many back to the model
     * @return  \Hubzero\Database\Relationship\OneToManyThrough
     **/
    public function oneToManyThrough($model, $through, $relatedKey = null, $localKey = null)
    {
        // Format the model name and instantiate new object
        $related = $this->resolve($model);
        $through = $this->resolve($through);

        // Keys
        $localKey   = $localKey   ?: strtolower($this->getModelName()) . '_id';
        $relatedKey = $relatedKey ?: strtolower($through->getModelName()) . '_id';

        return new OneToManyThrough($this, $related, $through->getTableName(), $localKey, $relatedKey);
    }

    /**
     * Retrieves a one to one through model relationship (hasOneThrough equivalent)
     *
     * Returns a single related model through an intermediate table/model.
     * This is similar to oneToManyThrough but returns a single model instead of a collection.
     *
     * Example: User -> Profile -> Country
     * "A user has one country through their profile"
     *
     * ```php
     * // In User model
     * public function country()
     * {
     *     return $this->oneToOneThrough(Country::class, Profile::class);
     * }
     *
     * // With explicit keys
     * public function country()
     * {
     *     return $this->oneToOneThrough(
     *         Country::class,
     *         Profile::class,
     *         'country_id',  // Key on Profile linking to Country
     *         'user_id'      // Key on Profile linking to User
     *     );
     * }
     * ```
     *
     * @param   string       $model       The name of the final related model
     * @param   string       $through     The name of the intermediate model
     * @param   string|null  $relatedKey  The key on intermediate table linking to related model
     * @param   string|null  $localKey    The key on intermediate table linking to local model
     * @return  \Hubzero\Database\Relationship\OneToOneThrough
     **/
    public function oneToOneThrough($model, $through, $relatedKey = null, $localKey = null)
    {
        // Format the model name and instantiate new object
        $related = $this->resolve($model);
        $through = $this->resolve($through);

        // Keys - defaults follow naming convention
        $localKey   = $localKey   ?: strtolower($this->getModelName()) . '_id';
        $relatedKey = $relatedKey ?: strtolower($related->getModelName()) . '_id';

        return new OneToOneThrough($this, $related, $through->getTableName(), $localKey, $relatedKey);
    }

    /**
     * Laravel-compatible alias for oneToOneThrough
     *
     * @param   string       $model       The name of the final related model
     * @param   string       $through     The name of the intermediate model
     * @param   string|null  $relatedKey  The key on intermediate table linking to related model
     * @param   string|null  $localKey    The key on intermediate table linking to local model
     * @return  \Hubzero\Database\Relationship\OneToOneThrough
     **/
    public function hasOneThrough($model, $through, $relatedKey = null, $localKey = null)
    {
        return $this->oneToOneThrough($model, $through, $relatedKey, $localKey);
    }

    /**
     * Retrieves a belongs to one model relationship as the inverse of a oneShiftsToMany
     *
     * @param   string  $shifter  The parent side field used to differentiate/shift models
     * @param   string  $thisKey  The local key used to associate the many back to the model
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     **/
    public function shifter($shifter = 'scope', $thisKey = 'scope_id')
    {
        $parent = $this->resolve($this->$shifter);

        return new BelongsToOne($this, $parent, $thisKey, 'id');
    }

    // =========================================================================
    // Polymorphic Relationships
    // =========================================================================
    //
    // Polymorphic relationships allow a model to belong to more than one type
    // of parent model using a single association. They use a type column to
    // identify which model class the relationship points to.
    //
    // | Method        | Description                                         |
    // |---------------|-----------------------------------------------------|
    // | morphTo()     | Inverse: child belongs to multiple parent types     |
    // | morphOne()    | Parent has one polymorphic child                    |
    // | morphMany()   | Parent has many polymorphic children                |
    // | morphToMany() | Many-to-many with polymorphic pivot                 |
    // | morphedByMany() | Inverse of morphToMany                            |
    //
    // ## Type Mapping
    //
    // By default, polymorphic relationships use the lowercase model name as the
    // type identifier. For cleaner, decoupled type strings, register a morph map:
    //
    // ```php
    // Relational::morphMap([
    //     'post'  => Post::class,
    //     'video' => Video::class,
    // ]);
    // ```
    //
    // =========================================================================

    /**
     * Register custom morph type to class mappings
     *
     * Use this to map simple type identifiers to their model classes.
     * This keeps the database decoupled from PHP class names.
     *
     * ```php
     * Relational::morphMap([
     *     'post'  => \Components\Blog\Models\Post::class,
     *     'video' => \Components\Media\Models\Video::class,
     *     'photo' => \Components\Gallery\Models\Photo::class,
     * ]);
     * ```
     *
     * @param  array|null  $map   The mappings to register (null to just retrieve)
     * @param  bool        $merge Whether to merge with existing mappings
     * @return array              The current morph map
     */
    public static function morphMap(?array $map = null, $merge = true)
    {
        if ($map !== null) {
            if ($merge) {
                static::$morphMap = array_merge(static::$morphMap, $map);
            } else {
                static::$morphMap = $map;
            }
        }

        return static::$morphMap;
    }

    /**
     * Get the current morph map
     *
     * @return array
     */
    public static function getMorphMap()
    {
        return static::$morphMap;
    }

    /**
     * Clear the morph map
     *
     * Useful for testing or resetting state.
     *
     * @return void
     */
    public static function clearMorphMap()
    {
        static::$morphMap = [];
    }

    /**
     * Define a polymorphic belongs-to relationship (morphTo)
     *
     * This is the inverse of morphOne/morphMany. It allows a child model
     * to belong to multiple parent types using type and id columns.
     *
     * ## Example
     *
     * ```php
     * // Comment model can belong to Post, Video, or Photo
     * class Comment extends Relational
     * {
     *     public function commentable()
     *     {
     *         return $this->morphTo('commentable');
     *         // Uses commentable_type and commentable_id columns
     *     }
     * }
     *
     * // Usage:
     * $comment = Comment::oneOrFail(1);
     * $parent = $comment->commentable;  // Returns Post, Video, or Photo
     * ```
     *
     * @param  string       $name       The relationship name (derives column names)
     * @param  string|null  $morphType  Custom type column name (default: {name}_type)
     * @param  string|null  $morphId    Custom id column name (default: {name}_id)
     * @return \Hubzero\Database\Relationship\MorphTo
     */
    public function morphTo($name = null, $morphType = null, $morphId = null)
    {
        // If name not provided, try to infer from calling method
        if ($name === null) {
            $name = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
        }

        return new MorphTo($this, $name, $morphType, $morphId);
    }

    /**
     * Define a polymorphic one-to-one relationship (morphOne)
     *
     * This allows different model types to share a one-to-one relationship
     * with a single related model type.
     *
     * ## Example
     *
     * ```php
     * // Both User and Post can have one Image
     * class User extends Relational
     * {
     *     public function image()
     *     {
     *         return $this->morphOne(Image::class, 'imageable');
     *         // Image table has: imageable_type, imageable_id columns
     *     }
     * }
     *
     * // Usage:
     * $user = User::oneOrFail(1);
     * $image = $user->image;
     * ```
     *
     * @param  string       $model      The related model class
     * @param  string       $name       The relationship name (derives column names)
     * @param  string|null  $morphType  Custom type column name
     * @param  string|null  $morphId    Custom id column name
     * @param  string|null  $localKey   Custom local key (default: primary key)
     * @return \Hubzero\Database\Relationship\MorphOne
     */
    public function morphOne($model, $name, $morphType = null, $morphId = null, $localKey = null)
    {
        $related = $this->resolve($model);

        return new MorphOne($this, $related, $name, $morphType, $morphId, $localKey);
    }

    /**
     * Define a polymorphic one-to-many relationship (morphMany)
     *
     * This allows different model types to share a one-to-many relationship
     * with a single related model type. The classic example is comments.
     *
     * ## Example
     *
     * ```php
     * // Both Post and Video can have many Comments
     * class Post extends Relational
     * {
     *     public function comments()
     *     {
     *         return $this->morphMany(Comment::class, 'commentable');
     *         // Comment table has: commentable_type, commentable_id columns
     *     }
     * }
     *
     * // Usage:
     * $post = Post::oneOrFail(1);
     * $comments = $post->comments;  // All comments for this post
     *
     * // Create a comment:
     * $post->comments()->create(['body' => 'Great post!']);
     * ```
     *
     * @param  string       $model      The related model class
     * @param  string       $name       The relationship name (derives column names)
     * @param  string|null  $morphType  Custom type column name
     * @param  string|null  $morphId    Custom id column name
     * @param  string|null  $localKey   Custom local key (default: primary key)
     * @return \Hubzero\Database\Relationship\MorphMany
     */
    public function morphMany($model, $name, $morphType = null, $morphId = null, $localKey = null)
    {
        $related = $this->resolve($model);

        return new MorphMany($this, $related, $name, $morphType, $morphId, $localKey);
    }

    /**
     * Define a polymorphic many-to-many relationship (morphToMany)
     *
     * This allows different model types to share a many-to-many relationship
     * through a polymorphic pivot table.
     *
     * ## Example
     *
     * ```php
     * // Both Post and Video can have many Tags
     * class Post extends Relational
     * {
     *     public function tags()
     *     {
     *         return $this->morphToMany(Tag::class, 'taggable');
     *         // Uses taggables table with: taggable_type, taggable_id, tag_id
     *     }
     * }
     *
     * // Usage:
     * $post = Post::oneOrFail(1);
     * $tags = $post->tags;
     *
     * // Sync tags:
     * $post->tags()->sync([1, 2, 3]);
     * ```
     *
     * @param  string       $model       The related model class
     * @param  string       $name        The relationship name (derives table/columns)
     * @param  string|null  $table       Custom pivot table name
     * @param  string|null  $morphType   Custom type column name
     * @param  string|null  $morphId     Custom id column name
     * @param  string|null  $relatedKey  Custom related key on pivot
     * @return \Hubzero\Database\Relationship\MorphToMany
     */
    public function morphToMany(
        $model,
        $name,
        $table = null,
        $morphType = null,
        $morphId = null,
        $relatedKey = null
    ) {
        $related = $this->resolve($model);

        return new MorphToMany(
            $this,
            $related,
            $name,
            $table,
            $morphType,
            $morphId,
            $relatedKey,
            false // not inverse
        );
    }

    /**
     * Define the inverse of a polymorphic many-to-many relationship
     *
     * Use this on the related side of morphToMany to get all parents
     * of a specific type.
     *
     * ## Example
     *
     * ```php
     * // Tag can be attached to Posts, Videos, Photos
     * class Tag extends Relational
     * {
     *     public function posts()
     *     {
     *         return $this->morphedByMany(Post::class, 'taggable');
     *     }
     *
     *     public function videos()
     *     {
     *         return $this->morphedByMany(Video::class, 'taggable');
     *     }
     * }
     *
     * // Usage:
     * $tag = Tag::oneOrFail(1);
     * $posts = $tag->posts;   // All posts with this tag
     * $videos = $tag->videos; // All videos with this tag
     * ```
     *
     * @param  string       $model       The related model class
     * @param  string       $name        The relationship name (from parent's morphToMany)
     * @param  string|null  $table       Custom pivot table name
     * @param  string|null  $morphType   Custom type column name
     * @param  string|null  $morphId     Custom id column name
     * @param  string|null  $relatedKey  Custom related key on pivot
     * @return \Hubzero\Database\Relationship\MorphToMany
     */
    public function morphedByMany(
        $model,
        $name,
        $table = null,
        $morphType = null,
        $morphId = null,
        $relatedKey = null
    ) {
        $related = $this->resolve($model);

        // For inverse, the related key defaults to the current model's name
        $relatedKey = $relatedKey ?: strtolower($this->getModelName()) . '_id';

        return new MorphToMany(
            $this,
            $related,
            $name,
            $table,
            $morphType,
            $morphId,
            $relatedKey,
            true // is inverse
        );
    }

    /**
     * Attaches the given model(s) to the current one via its relationship
     *
     * This is kind of like calling save on an individual relationship,
     * except that we're attaching the models back to the parent entity.
     * This is helpful if you're going to call saveAndPropagate and want
     * to pass the parent object back to a view in the event of a save error.
     *
     * @param   string        $relationship  The relationship to invoke
     * @param   array|object  $models        The model or models to attach
     * @return  self
     **/
    public function attach($relationship, $models)
    {
        // If we have an array, we'll put it into a rows object
        // (like we would if we were fetching the results from the db)
        if (is_array($models)) {
            $rows = new Rows();

            foreach ($models as $model) {
                $rows->push($model);
            }
        } else {
            // Otherwise it's just a single model
            $rows = $models;
        }

        // Get our rows associated according to their relationship type
        // This means we add related keys, etc to the passed in rows
        $rows = $this->$relationship()->associate($rows);
        $this->addRelationship($relationship, $rows);

        return $this;
    }

    // =========================================================================
    // Eager Loading (N+1 Prevention)
    // =========================================================================
    //
    // Eager loading fetches related models in bulk queries instead of one-at-a-time,
    // preventing the N+1 query problem. Use `with()` to specify which relationships
    // to load with your query. The `including()` method is an alias for BC.
    //
    // | Method       | Description                                            |
    // |--------------|--------------------------------------------------------|
    // | with()       | Primary eager loading method (Laravel-style)           |
    // | including()  | Alias for with() (backward compatibility)              |
    //
    // | Pattern             | Example                                              |
    // |---------------------|------------------------------------------------------|
    // | Simple names        | `$posts->with('comments', 'author')`                 |
    // | Array form          | `$posts->with(['comments', 'author'])`               |
    // | Nested relations    | `$posts->with('comments.author')`                    |
    // | Closure constraint  | `$posts->with(['comments', fn($q) => ...])`          |
    // | Array constraint    | `$posts->with(['comments' => ['scope' => 'approved']])` |
    //
    // ## Constraint Options (array format)
    //
    // | Key          | Type            | Description                              |
    // |--------------|-----------------|------------------------------------------|
    // | scope        | string|array    | Model scope(s) to apply                  |
    // | conditions   | array           | Field => value pairs for whereEquals     |
    // | order        | array           | Field => direction pairs                 |
    // | limit        | int             | Maximum related records to load          |
    //
    // ## How It Works
    //
    // 1. `with()` stores relationships to eager load
    // 2. When `rows()` executes, the parser processes each relationship
    // 3. `seedWithRelation()` fetches ALL related records in ONE query via whereIn()
    // 4. Results are attached to parent models
    //
    // This turns N+1 queries into just 2 queries (1 for parents + 1 per relationship).
    //
    // @see \Hubzero\Database\Relationship\Relationship::seedWithRelation()
    // =========================================================================

    /**
     * Sets an associated relationship to be retrieved with the current model
     *
     * Supports multiple formats for specifying relationships and constraints:
     *
     * **Simple relationship names:**
     * ```php
     * $posts->with('comments', 'author');
     * $posts->with(['comments', 'author']);
     * ```
     *
     * **Closure-based constraints:**
     * ```php
     * $posts->with(['comments', function($query) {
     *     $query->whereEquals('approved', 1);
     * }]);
     * ```
     *
     * **Array-based constraints (closure-free):**
     * ```php
     * $posts->with([
     *     'comments' => [
     *         'conditions' => ['approved' => 1, 'spam' => 0],
     *         'order' => ['created' => 'desc'],
     *         'limit' => 5
     *     ],
     *     'author'  // Simple relations can be mixed in
     * ]);
     * ```
     *
     * **Scope references:**
     * ```php
     * // Uses Comment::scopeApproved()
     * $posts->with([
     *     'comments' => ['scope' => 'approved']
     * ]);
     *
     * // Multiple scopes
     * $posts->with([
     *     'comments' => ['scope' => ['approved', 'recent']]
     * ]);
     *
     * // Scope with parameters
     * $posts->with([
     *     'comments' => ['scope' => ['byUser' => [42]]]
     * ]);
     * ```
     *
     * **Combined constraints:**
     * ```php
     * $posts->with([
     *     'comments' => [
     *         'scope' => 'approved',
     *         'conditions' => ['featured' => 1],
     *         'order' => ['votes' => 'desc'],
     *         'limit' => 3
     *     ]
     * ]);
     * ```
     *
     * @return  self
     **/
    public function with()
    {
        foreach (func_get_args() as $relationship) {
            $this->includes[] = $relationship;
        }

        return $this;
    }

    /**
     * Alias for with() - eager load relationships
     *
     * This is an alias for backward compatibility. New code should use with().
     *
     * @return  self
     * @see     with()
     **/
    public function including()
    {
        return call_user_func_array([$this, 'with'], func_get_args());
    }

    // =========================================================================
    // Lazy Eager Loading
    // =========================================================================
    //
    // Lazy eager loading allows you to load relationships on models AFTER they
    // have already been retrieved from the database. This is useful when:
    // - Models come from a cache or external source
    // - You need to conditionally load relationships based on runtime logic
    // - You receive a collection of models and need to efficiently load relations
    //
    // | Method         | Description                                           |
    // |----------------|-------------------------------------------------------|
    // | load()         | Load relationships on this model (post-retrieval)     |
    // | loadMissing()  | Load only relationships that aren't already loaded    |
    //
    // Unlike `with()` which must be called before the query executes, these
    // methods work on already-retrieved models and use the same efficient
    // bulk-loading mechanism to prevent N+1 queries.
    //
    // Example:
    // ```php
    // $users = User::all()->rows();  // No relationships loaded
    //
    // // Later, conditionally load relationships
    // if ($includeComments) {
    //     $users->load('posts.comments');  // Bulk load for all users
    // }
    //
    // // Or on a single model
    // $user = User::one(1);
    // $user->load('posts', 'profile');
    // $user->loadMissing('settings');  // Only if not already loaded
    // ```
    //
    // @see Rows::load() for loading on collections
    // =========================================================================

    /**
     * Lazy eager load relationships on this model
     *
     * Loads relationships AFTER the model has been retrieved from the database.
     * This is useful when you receive models from a cache, another method, or
     * need to conditionally load relationships based on runtime logic.
     *
     * Uses the same efficient bulk-loading mechanism as `with()`, but operates
     * on already-retrieved models.
     *
     * **Simple usage:**
     * ```php
     * $user = User::one(1);
     * $user->load('posts', 'profile');
     * // Now $user->posts and $user->profile are loaded
     * ```
     *
     * **Nested relationships:**
     * ```php
     * $user->load('posts.comments.author');
     * ```
     *
     * **With constraints (closure):**
     * ```php
     * $user->load(['posts', function($query) {
     *     $query->whereEquals('published', 1);
     * }]);
     * ```
     *
     * **With constraints (array - no closures):**
     * ```php
     * $user->load([
     *     'posts' => [
     *         'conditions' => ['published' => 1],
     *         'order' => ['created' => 'desc'],
     *         'limit' => 5
     *     ]
     * ]);
     * ```
     *
     * @param   mixed  ...$relations  Relationship names to load (same format as with())
     * @return  self
     **/
    public function load()
    {
        $relations = func_get_args();

        if (empty($relations)) {
            return $this;
        }

        // Create a Rows collection containing just this model
        // seedWithRelation() expects a Rows object
        $rows = new Rows([$this]);

        // Process each relationship
        foreach ($relations as $relation) {
            $rows = $this->loadRelationOnRows($rows, $relation);
        }

        return $this;
    }

    /**
     * Lazy eager load relationships that haven't been loaded yet
     *
     * Same as `load()` but only loads relationships that aren't already
     * present on the model. This is useful when you want to ensure a
     * relationship is available without re-loading it if it's already there.
     *
     * ```php
     * // Only loads profile if not already loaded
     * $user->loadMissing('profile');
     *
     * // Load multiple, skipping any already loaded
     * $user->loadMissing('posts', 'comments', 'profile');
     * ```
     *
     * @param   mixed  ...$relations  Relationship names to load if missing
     * @return  self
     **/
    public function loadMissing()
    {
        $relations = func_get_args();

        if (empty($relations)) {
            return $this;
        }

        // Filter to only relations that aren't already loaded
        $toLoad = [];
        foreach ($relations as $relation) {
            $relationName = $this->extractRelationName($relation);

            // Only add if not already loaded
            if ($this->getRelationship($relationName) === null) {
                $toLoad[] = $relation;
            }
        }

        if (!empty($toLoad)) {
            call_user_func_array([$this, 'load'], $toLoad);
        }

        return $this;
    }

    /**
     * Extract the base relationship name from various formats
     *
     * Handles:
     * - Simple string: 'posts' -> 'posts'
     * - Nested: 'posts.comments' -> 'posts'
     * - Array with closure: ['posts', fn() => ...] -> 'posts'
     * - Associative array: ['posts' => [...config...]] -> 'posts'
     *
     * @param   mixed   $relation  The relation in various formats
     * @return  string  The base relationship name
     **/
    private function extractRelationName($relation)
    {
        // Simple string
        if (is_string($relation)) {
            // Handle nested: 'posts.comments' -> 'posts'
            if (strpos($relation, '.') !== false) {
                return explode('.', $relation, 2)[0];
            }
            return $relation;
        }

        // Array format
        if (is_array($relation)) {
            // Associative array: ['posts' => [...]]
            if ($this->isAssociativeArray($relation)) {
                $keys = array_keys($relation);
                foreach ($keys as $key) {
                    if (is_string($key)) {
                        // Handle nested in key
                        if (strpos($key, '.') !== false) {
                            return explode('.', $key, 2)[0];
                        }
                        return $key;
                    }
                }
                // Fallback: first value might be a string relation name
                $first = reset($relation);
                if (is_string($first)) {
                    return strpos($first, '.') !== false
                        ? explode('.', $first, 2)[0]
                        : $first;
                }
            }

            // Indexed array: ['posts', fn() => ...]
            if (isset($relation[0]) && is_string($relation[0])) {
                $name = $relation[0];
                return strpos($name, '.') !== false
                    ? explode('.', $name, 2)[0]
                    : $name;
            }
        }

        // Fallback - shouldn't normally reach here
        return (string) $relation;
    }

    /**
     * Load a single relation definition on a Rows collection
     *
     * This is the internal method that processes different relation formats
     * and delegates to seedWithRelation().
     *
     * @param   Rows   $rows      The rows collection to load relations onto
     * @param   mixed  $relation  The relation definition (string, array, etc.)
     * @return  Rows
     **/
    private function loadRelationOnRows(Rows $rows, $relation)
    {
        $subs = null;
        $constraint = null;

        // Check if we have an associative array (new format: ['relation' => [...config...]])
        if (is_array($relation) && $this->isAssociativeArray($relation)) {
            return $this->loadAssociativeRelationOnRows($rows, $relation);
        }

        // Existing behavior: simple string or [name, closure] array
        $relationship = $relation;

        // Check for array, meaning we have relationship_name => constraint
        if (is_array($relationship)) {
            list($relationship, $constraint) = $relationship;
        }

        // Parse for nested relationships
        if (strpos($relationship, '.')) {
            list($relationship, $subs) = explode('.', $relationship, 2);
        }

        // If we have subs and a constraint, the constraint should apply to the subs
        if (isset($subs) && isset($constraint)) {
            $subs = [$subs, $constraint];
            $constraint = null;
        }

        // Load the relationship using seedWithRelation
        return $this->$relationship()->seedWithRelation($rows, $relationship, $constraint, $subs);
    }

    /**
     * Load associative relation definitions on a Rows collection
     *
     * Handles formats like:
     * - ['comments' => ['scope' => 'approved'], 'author']
     * - ['comments' => ['conditions' => ['approved' => 1]]]
     *
     * @param   Rows   $rows      The rows collection
     * @param   array  $includes  Associative array of relation => config
     * @return  Rows
     **/
    private function loadAssociativeRelationOnRows(Rows $rows, $includes)
    {
        foreach ($includes as $relationName => $config) {
            $subs = null;
            $constraint = null;

            // Numeric key with string value: simple relation name
            if (is_numeric($relationName)) {
                $relationName = $config;
                $config = null;
            }

            // Config is an array with constraints
            if (is_array($config)) {
                $constraint = $this->buildEagerConstraint($config);
            } elseif ($config instanceof \Closure) {
                $constraint = $config;
            }

            // Parse for nested relationships
            if (strpos($relationName, '.')) {
                list($relationName, $subs) = explode('.', $relationName, 2);
            }

            // If we have subs and a constraint, the constraint should apply to the subs
            if (isset($subs) && isset($constraint)) {
                $subs = [$subs, $constraint];
                $constraint = null;
            }

            // Load the relationship
            $rows = $this->$relationName()->seedWithRelation($rows, $relationName, $constraint, $subs);
        }

        return $rows;
    }

    /**
     * Join with a related model's table
     *
     * This method allows you to filter or sort by related model columns by adding
     * a JOIN clause. Unlike `with()` which loads relationships separately, `joinWith()`
     * uses SQL JOIN to combine tables in a single query.
     *
     * Use cases:
     * - Filter by related model attributes (e.g., posts where author.active = 1)
     * - Sort by related model columns (e.g., posts ordered by author.name)
     * - Efficiently check existence of related records
     *
     * Supported relationships:
     * - BelongsToOne: Joins related table using local foreign key
     * - OneToOne: Joins related table using related foreign key
     * - OneToMany: Joins related table (may duplicate parent rows)
     *
     * Note: ManyToMany relationships require manual join handling due to
     * intermediate table complexity.
     *
     * Example:
     * ```php
     * // Filter posts by active author
     * Post::joinWith('author')
     *     ->whereEquals('author.active', 1)
     *     ->rows();
     *
     * // Sort posts by author name
     * Post::joinWith('author')
     *     ->order('author.name', 'asc')
     *     ->rows();
     *
     * // Join with constraints via callback
     * Post::joinWith('comments', function($query) {
     *         $query->whereEquals('comments.approved', 1);
     *     })
     *     ->rows();
     *
     * // Join multiple relationships
     * Post::joinWith('author')
     *     ->joinWith('category')
     *     ->rows();
     * ```
     *
     * @param   string        $relation  The relationship method name
     * @param   callable|null $callback  Optional callback to add constraints to the join
     * @param   string        $type      Join type ('left', 'inner', 'right')
     * @return  self
     * @throws  \Hubzero\Database\Exception\RuntimeException  If relationship doesn't exist or isn't supported
     **/
    public function joinWith($relation, $callback = null, $type = 'left')
    {
        $this->bootIfNotBooted();

        // Ensure we have a query
        if ($this->query === null) {
            $this->newQuery();
        }

        // Check that the relationship method exists
        if (!method_exists($this, $relation)) {
            throw new RuntimeException("Relationship method '{$relation}' does not exist on " . get_class($this));
        }

        // Get the relationship object
        $relationship = $this->$relation();

        // Build the join based on relationship type
        $this->addJoinForRelationship($relationship, $relation, $callback, $type);

        return $this;
    }

    /**
     * Add an inner join with a related model's table
     *
     * This is a convenience method for `joinWith($relation, $callback, 'inner')`.
     * Inner joins only return rows where the related record exists.
     *
     * Example:
     * ```php
     * // Only get posts that have an author
     * Post::innerJoinWith('author')->rows();
     * ```
     *
     * @param   string        $relation  The relationship method name
     * @param   callable|null $callback  Optional callback to add constraints
     * @return  self
     **/
    public function innerJoinWith($relation, $callback = null)
    {
        return $this->joinWith($relation, $callback, 'inner');
    }

    /**
     * Add a left join with a related model's table
     *
     * This is a convenience method for `joinWith($relation, $callback, 'left')`.
     * Left joins return all parent rows, with NULL for missing related records.
     *
     * @param   string        $relation  The relationship method name
     * @param   callable|null $callback  Optional callback to add constraints
     * @return  self
     **/
    public function leftJoinWith($relation, $callback = null)
    {
        return $this->joinWith($relation, $callback, 'left');
    }

    /**
     * Add a right join with a related model's table
     *
     * @param   string        $relation  The relationship method name
     * @param   callable|null $callback  Optional callback to add constraints
     * @return  self
     **/
    public function rightJoinWith($relation, $callback = null)
    {
        return $this->joinWith($relation, $callback, 'right');
    }

    /**
     * Internal method to add a JOIN clause for a relationship
     *
     * @param   object        $relationship  The relationship object
     * @param   string        $alias         Alias for the joined table
     * @param   callable|null $callback      Optional callback for additional constraints
     * @param   string        $type          Join type
     * @return  void
     **/
    protected function addJoinForRelationship($relationship, $alias, $callback = null, $type = 'left')
    {
        $relationClass = get_class($relationship);
        $relatedModel = $relationship->getRelated();
        $relatedTable = $relatedModel->getTableName();

        // Determine join keys based on relationship type
        switch (true) {
            case $relationship instanceof BelongsToOne:
                // BelongsToOne: local key (e.g., user_id) -> related primary key (e.g., id)
                // Join: related_table AS alias ON this.local_key = alias.related_key
                $localKey = $relationship->getLocalKey();
                $relatedKey = $relationship->getRelatedKey();
                $thisTable = $this->getTableAlias() ?: $this->getTableName();
                $leftKey = $thisTable . '.' . $localKey;
                $rightKey = $alias . '.' . $relatedKey;
                break;

            case $relationship instanceof OneToOne:
            case $relationship instanceof OneToMany:
                // OneToOne/OneToMany: local primary key -> related foreign key
                // Join: related_table AS alias ON this.local_key = alias.related_key
                $localKey = $relationship->getLocalKey();
                $relatedKey = $relationship->getRelatedKey();
                $thisTable = $this->getTableAlias() ?: $this->getTableName();
                $leftKey = $thisTable . '.' . $localKey;
                $rightKey = $alias . '.' . $relatedKey;
                break;

            case $relationship instanceof ManyToMany:
                throw new RuntimeException(
                    "ManyToMany relationships require manual join handling. " .
                    "Use \$query->join() directly with the intermediate table."
                );

            case $relationship instanceof OneToManyThrough:
                throw new RuntimeException(
                    "OneToManyThrough relationships require manual join handling. " .
                    "Use \$query->join() directly with the intermediate model's table."
                );

            default:
                throw new RuntimeException(
                    "Unsupported relationship type for joinWith(): " . $relationClass
                );
        }

        // Add the join to the query
        $joinMethod = $type . 'Join';
        $this->query->$joinMethod($relatedTable . ' AS ' . $alias, $leftKey, $rightKey);

        // Apply optional callback constraints
        if ($callback !== null && is_callable($callback)) {
            $callback($this->query);
        }
    }

    /**
     * Retrieves an associated model in conjunction with the current one
     *
     * @param   \Hubzero\Database\Rows  $rows  The rows to parse and augment
     * @return  \Hubzero\Database\Rows
     **/
    private function parseIncluding($rows)
    {
        $subs       = null;
        $constraint = null;
        foreach ($this->includes as $include) {
            // Check if we have an associative array (new format: ['relation' => [...config...]])
            if (is_array($include) && $this->isAssociativeArray($include)) {
                $rows = $this->parseAssociativeInclude($rows, $include);
                continue;
            }

            // Existing behavior: simple string or [name, closure] array
            $relationship = $include;

            // Check for array, meaning we have relationship_name => constraint
            if (is_array($relationship)) {
                list($relationship, $constraint) = $relationship;
            }

            // Parse for nested relationships
            if (strpos($relationship, '.')) {
                list($relationship, $subs) = explode('.', $relationship, 2);
            }

            // If we have subs and a constraint, the constraint should apply to the subs, not the intermediate relation
            if (isset($subs) && isset($constraint)) {
                $subs       = [$subs, $constraint];
                $constraint = null;
            }

            // Get the actual rows
            $rows = $this->$relationship()->seedWithRelation($rows, $relationship, $constraint, $subs);

            // Reset some vars
            $subs       = null;
            $constraint = null;
        }

        return $rows;
    }

    /**
     * Parses an associative array of includes with optional constraints
     *
     * Handles formats like:
     * - ['comments' => ['scope' => 'approved'], 'author']
     * - ['comments' => ['conditions' => ['approved' => 1]], 'tags']
     *
     * @param   \Hubzero\Database\Rows  $rows     The rows to parse and augment
     * @param   array                   $includes Associative array of relation => config pairs
     * @return  \Hubzero\Database\Rows
     **/
    private function parseAssociativeInclude($rows, $includes)
    {
        foreach ($includes as $relationName => $config) {
            $subs = null;
            $constraint = null;

            // Numeric key with string value: simple relation name
            if (is_numeric($relationName)) {
                $relationName = $config;
                $config = null;
            }

            // Config is an array with constraints
            if (is_array($config)) {
                $constraint = $this->buildEagerConstraint($config);
            } elseif ($config instanceof Closure) {
                // Allow closures in the new format too for flexibility
                $constraint = $config;
            }

            // Parse for nested relationships
            if (strpos($relationName, '.')) {
                list($relationName, $subs) = explode('.', $relationName, 2);
            }

            // If we have subs and a constraint, the constraint should apply to the subs
            if (isset($subs) && isset($constraint)) {
                $subs = [$subs, $constraint];
                $constraint = null;
            }

            // Get the actual rows
            $rows = $this->$relationName()->seedWithRelation($rows, $relationName, $constraint, $subs);
        }

        return $rows;
    }

    /**
     * Checks if an array is associative (has string keys)
     *
     * @param   array  $array  The array to check
     * @return  bool
     **/
    private function isAssociativeArray($array)
    {
        if (!is_array($array) || empty($array)) {
            return false;
        }

        // Check if any key is a string (not numeric)
        foreach (array_keys($array) as $key) {
            if (is_string($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Builds a constraint closure from an array configuration
     *
     * Supports the following configuration keys:
     * - 'scope': Single scope name, array of scope names, or associative array of scope => params
     * - 'conditions': Associative array of field => value pairs for whereEquals
     * - 'order': Associative array of field => direction pairs
     * - 'limit': Integer limit value
     *
     * This method is public to allow Rows::load() to build constraints when
     * loading relationships on collections.
     *
     * @param   array  $config  The constraint configuration
     * @return  Closure
     **/
    public function buildEagerConstraint($config)
    {
        return function ($query) use ($config) {
            // Apply scopes first
            if (isset($config['scope'])) {
                $scopes = $config['scope'];

                // Normalize to array format
                if (is_string($scopes)) {
                    $scopes = [$scopes];
                }

                foreach ($scopes as $scopeName => $scopeParams) {
                    // If numeric key, the value is the scope name with no params
                    if (is_numeric($scopeName)) {
                        $scopeName = $scopeParams;
                        $scopeParams = [];
                    }

                    // Ensure params is an array
                    if (!is_array($scopeParams)) {
                        $scopeParams = [$scopeParams];
                    }

                    // Call the scope method on the query
                    // Scopes are called as scopeName on the query (magic method handles it)
                    call_user_func_array([$query, $scopeName], $scopeParams);
                }
            }

            // Apply conditions (whereEquals for each key => value pair)
            if (isset($config['conditions']) && is_array($config['conditions'])) {
                foreach ($config['conditions'] as $field => $value) {
                    if (is_null($value)) {
                        $query->whereIsNull($field);
                    } else {
                        $query->whereEquals($field, $value);
                    }
                }
            }

            // Apply ordering
            if (isset($config['order']) && is_array($config['order'])) {
                foreach ($config['order'] as $field => $direction) {
                    $query->order($field, $direction);
                }
            }

            // Apply limit
            if (isset($config['limit']) && is_numeric($config['limit'])) {
                $query->limit((int) $config['limit']);
            }
        };
    }

    /**
     * Adds alternate locations to look for model properties
     *
     * This method merely adds them to the list. See the __get
     * method above for the code that actually checks for a
     * valid attribute on the forwarding model.
     *
     * @return  self
     **/
    public function forwardTo()
    {
        foreach (func_get_args() as $relationship) {
            $this->forwards[] = $relationship;
        }

        return $this;
    }

    /**
     * Adds a new relationship to the current model
     *
     * @param   string  $name   The name of the relationship
     * @param   object  $model  The model or rows to add
     * @return  self
     **/
    public function addRelationship($name, $model)
    {
        $this->relationships[$name] = $model;

        return $this;
    }

    /**
     * Gets all relationships
     *
     * @return  array
     **/
    public function getRelationships()
    {
        return $this->relationships;
    }

    /**
     * Gets the defined relationship
     *
     * @param   string  $name  The relationship to return
     * @return  \Hubzero\Database\Rows|\Hubzero\Database\Relational|static
     **/
    public function getRelationship($name)
    {
        return isset($this->relationships[$name]) ? $this->relationships[$name] : null;
    }

    /**
     * Checks if a relationship has been loaded
     *
     * @param   string  $name  The relationship name to check
     * @return  bool
     **/
    public function hasRelationship($name)
    {
        return isset($this->relationships[$name]);
    }

    // =========================================================================
    // Cascade Operations
    // =========================================================================
    //
    // These methods support automatic cascade delete and save operations on
    // relationships that have opted-in via cascadeOnDelete(), cascadeOnSave(),
    // or orphanRemoval() fluent methods.
    //
    // Cascade operations are backwards-compatible: existing code is unaffected.
    // You must explicitly enable cascade behavior per-relationship.
    //
    // =========================================================================

    /**
     * Perform cascade deletes on relationships that have cascadeOnDelete enabled
     *
     * This method is called during destroy() to automatically delete related
     * models before the parent is deleted.
     *
     * @return  bool  True on success, false if any cascade delete failed
     **/
    protected function performCascadeDeletes()
    {
        $cascadeRelationships = $this->getCascadeDeleteRelationships();

        foreach ($cascadeRelationships as $relationName) {
            $relationship = $this->$relationName();

            // Check if bulk mode is enabled for better performance
            if ($relationship->shouldBulkCascadeDelete()) {
                // Bulk delete using a single query - faster but no model events
                if (!$this->performBulkCascadeDelete($relationship)) {
                    return false;
                }
                continue;
            }

            // Individual deletes - slower but fires model events on each
            $related = $relationship->rows();

            // Handle both single models and collections
            if ($related instanceof Rows) {
                foreach ($related as $model) {
                    if (!$model->destroy()) {
                        $this->setErrors($model->getErrors());
                        return false;
                    }
                }
            } elseif ($related instanceof self && !$related->isNew()) {
                if (!$related->destroy()) {
                    $this->setErrors($related->getErrors());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Perform bulk cascade delete using a single DELETE query
     *
     * This is much faster than individual deletes but does NOT fire
     * model lifecycle events (deleting/deleted) on the related models.
     *
     * Use this for large datasets where performance matters more than
     * triggering events on each related model.
     *
     * @param   Relationship\Relationship  $relationship  The relationship to cascade delete
     * @return  bool  True on success
     **/
    protected function performBulkCascadeDelete($relationship)
    {
        $relatedModel = $relationship->getRelated();
        $relatedKey = $relationship->getRelatedKey();
        $localKey = $relationship->getLocalKey();
        $localValue = $this->get($localKey);

        // Skip if no local value (model not saved)
        if ($localValue === null) {
            return true;
        }

        // Build and execute bulk DELETE query
        $query = $relatedModel->getQuery();
        $query->delete($relatedModel->getTableName())
              ->whereEquals($relatedKey, $localValue);

        $query->execute();

        return true;
    }

    /**
     * Perform cascade saves on relationships that have cascadeOnSave enabled
     *
     * This method is called during save() to automatically save dirty related
     * models after the parent is saved.
     *
     * @return  bool  True on success, false if any cascade save failed
     **/
    protected function performCascadeSaves()
    {
        $cascadeRelationships = $this->getCascadeSaveRelationships();

        foreach ($cascadeRelationships as $relationName) {
            // Only process if the relationship has been loaded
            $cachedRelation = $this->getRelationship($relationName);
            if ($cachedRelation === null) {
                continue;
            }

            $relationship = $this->$relationName();

            // Handle both single models and collections
            if ($cachedRelation instanceof Rows) {
                foreach ($cachedRelation as $model) {
                    // Associate and save
                    $relationship->associate($model);
                    if ($model->isDirty() && !$model->save()) {
                        $this->setErrors($model->getErrors());
                        return false;
                    }
                }
            } elseif ($cachedRelation instanceof self) {
                $relationship->associate($cachedRelation);
                if ($cachedRelation->isDirty() && !$cachedRelation->save()) {
                    $this->setErrors($cachedRelation->getErrors());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get relationship names that have cascade delete enabled
     *
     * This method uses reflection to find all relationship methods on the model
     * and returns those that have cascadeOnDelete() configured.
     *
     * @return  array  Array of relationship method names
     **/
    protected function getCascadeDeleteRelationships()
    {
        return $this->getCascadeRelationships('shouldCascadeOnDelete');
    }

    /**
     * Get relationship names that have cascade save enabled
     *
     * @return  array  Array of relationship method names
     **/
    protected function getCascadeSaveRelationships()
    {
        return $this->getCascadeRelationships('shouldCascadeOnSave');
    }

    /**
     * Get relationship names that have orphan removal enabled
     *
     * @return  array  Array of relationship method names
     **/
    protected function getOrphanRemovalRelationships()
    {
        return $this->getCascadeRelationships('shouldRemoveOrphans');
    }

    /**
     * Get relationships matching a specific cascade check method
     *
     * @param   string  $checkMethod  The method to call on the relationship (e.g., 'shouldCascadeOnDelete')
     * @return  array   Array of relationship method names
     **/
    protected function getCascadeRelationships($checkMethod)
    {
        $cascadeRelationships = [];
        $relationshipNames = static::introspectRelationships();

        foreach ($relationshipNames as $name) {
            try {
                $relationship = $this->$name();
                if ($relationship instanceof Relationship\Relationship && $relationship->$checkMethod()) {
                    $cascadeRelationships[] = $name;
                }
            } catch (\Exception $e) {
                // Skip methods that throw exceptions
                continue;
            }
        }

        return $cascadeRelationships;
    }

    /**
     * Track original relationship IDs for orphan removal
     *
     * Call this method when you want to enable orphan tracking for a relationship.
     * Any models that were in the original set but not in the updated set will
     * be deleted when save() is called.
     *
     * Example:
     * ```php
     * $post = Post::one(1);
     * $post->trackOrphansFor('comments');  // Start tracking
     *
     * // Remove some comments from the collection
     * $comments = $post->comments->reject(fn($c) => $c->spam);
     * $post->addRelationship('comments', $comments);
     *
     * $post->save(); // Spam comments are deleted
     * ```
     *
     * @param   string  $relationName  The relationship name to track
     * @return  $this
     **/
    public function trackOrphansFor($relationName)
    {
        $relationship = $this->$relationName();
        $related = $relationship->rows();

        if ($related instanceof Rows) {
            $pk = $relationship->getRelated()->getPrimaryKey();
            $this->originalRelationshipIds[$relationName] = $related->fieldsByKey($pk);
        } elseif ($related instanceof self && !$related->isNew()) {
            $this->originalRelationshipIds[$relationName] = [$related->getPkValue()];
        } else {
            $this->originalRelationshipIds[$relationName] = [];
        }

        return $this;
    }

    /**
     * Perform orphan removal for relationships that have it enabled
     *
     * Compares current relationship state to original tracked state and
     * deletes any models that are no longer associated.
     *
     * @return  bool  True on success, false if any delete failed
     **/
    protected function performOrphanRemovals()
    {
        $orphanRelationships = $this->getOrphanRemovalRelationships();

        foreach ($orphanRelationships as $relationName) {
            // Skip if we haven't tracked originals for this relationship
            if (!isset($this->originalRelationshipIds[$relationName])) {
                continue;
            }

            $originalIds = $this->originalRelationshipIds[$relationName];

            // Get current IDs
            $cachedRelation = $this->getRelationship($relationName);
            if ($cachedRelation === null) {
                continue;
            }

            $relationship = $this->$relationName();
            $currentIds = [];

            if ($cachedRelation instanceof Rows) {
                $pk = $relationship->getRelated()->getPrimaryKey();
                $currentIds = $cachedRelation->fieldsByKey($pk);
            } elseif ($cachedRelation instanceof self && !$cachedRelation->isNew()) {
                $currentIds = [$cachedRelation->getPkValue()];
            }

            // Find orphaned IDs (were in original but not in current)
            $orphanedIds = array_diff($originalIds, $currentIds);

            if (!empty($orphanedIds)) {
                // Delete orphaned models
                $relatedClass = get_class($relationship->getRelated());
                foreach ($orphanedIds as $orphanId) {
                    $orphan = $relatedClass::one($orphanId);
                    if ($orphan && !$orphan->isNew() && !$orphan->destroy()) {
                        $this->setErrors($orphan->getErrors());
                        return false;
                    }
                }
            }

            // Clear the tracking for this relationship
            unset($this->originalRelationshipIds[$relationName]);
        }

        return true;
    }

    /**
     * Establishes a relationship, fetching the rows as needed
     *
     * @param   string  $name  The name of the relationship
     * @return  self
     **/
    public function makeRelationship($name)
    {
        // See if the relationship already exists
        if (!$this->getRelationship($name)) {
            // Get the child rows/row and set them back on the model as a relationship for future use
            $rows = call_user_func_array(array($this, $name), array())->rows();
            $this->addRelationship($name, $rows);
        }

        return $this;
    }

    /**
     * Establishes a relationship, based on the acquaintances, fetching the rows as needed
     *
     * @param   string  $name  The name of the relationship
     * @return  self
     **/
    public function makeAcquaintance($name)
    {
        // See if the relationship already exists
        if (!$this->getRelationship($name)) {
            $resolver = static::getRelationshipRegistry()->get(static::class, $name);
            if ($resolver === null) {
                throw new BadMethodCallException("'{$name}' relationship does not exist.", 500);
            }

            // Get the child rows/row and set them back on the model as a relationship for future use
            $rows = call_user_func_array($resolver, [$this])->rows();
            $this->addRelationship($name, $rows);
        }

        return $this;
    }

    /**
     * Registers a new relationship at runtime, rather than explicitly in model
     *
     * @param   string   $name      The relationship name
     * @param   Closure  $response  The relationship response function
     * @return  void
     **/
    public static function registerRelationship($name, $response)
    {
        static::getRelationshipRegistry()->register(static::class, (string) $name, $response);
    }

    /**
     * Identifies known relationships on the model
     *
     * @return  array
     **/
    public static function introspectRelationships()
    {
        $class = static::class;

        if (isset(self::$introspectedRelationships[$class])) {
            $acquaintances = array_keys(static::getRelationshipRegistry()->all($class));
            return array_merge(self::$introspectedRelationships[$class], $acquaintances);
        }

        $instance     = self::blank();
        $methods      = [];
        $reflection   = new \ReflectionClass($instance);
        $relationship = __NAMESPACE__ . '\\Relationship\\Relationship';

        // Build a set of method names from framework base classes.
        // We skip by name (not declaring class) so that overridden methods
        // like save() or destroy() are still excluded from introspection.
        $skipClasses = [__CLASS__, __NAMESPACE__ . '\\Nested'];
        $skipNames   = [];

        foreach ($skipClasses as $skipClass) {
            if (!class_exists($skipClass)) {
                continue;
            }
            foreach ((new \ReflectionClass($skipClass))->getMethods(\ReflectionMethod::IS_PUBLIC) as $m) {
                $skipNames[$m->name] = true;
            }
        }

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            // Skip methods defined in framework base classes (even if overridden)
            if (isset($skipNames[$method->name])) {
                continue;
            }

            // Skip methods that require parameters - they can't be relationship definitions
            if ($method->getNumberOfRequiredParameters() > 0) {
                continue;
            }

            $result = null;

            try {
                // Invoke method and get the result
                $result = $method->invoke(new $reflection->name());
            } catch (\Throwable $e) {
                // Ignore all errors - we'll assume that means we don't care about the method
            }

            // If the method returned a relationship, we'll keep track of it
            if ($result instanceof $relationship) {
                $methods[] = $method->name;
            }
        }

        self::$introspectedRelationships[$class] = $methods;

        $acquaintances = array_keys(static::getRelationshipRegistry()->all($class));

        return array_merge($methods, $acquaintances);
    }

    /**
     * Set the runtime relationship registry implementation.
     *
     * @param   RelationshipRegistry  $registry
     * @return  void
     */
    public static function setRelationshipRegistry(RelationshipRegistry $registry): void
    {
        static::$relationshipRegistry = $registry;
    }

    /**
     * Get the runtime relationship registry.
     *
     * @return  RelationshipRegistry
     */
    protected static function getRelationshipRegistry(): RelationshipRegistry
    {
        if (!static::$relationshipRegistry instanceof RelationshipRegistry) {
            static::$relationshipRegistry = new RelationshipRegistry();
        }

        return static::$relationshipRegistry;
    }

    /**
     * Generates automatic created field value
     *
     * @param   array   $data  The data being saved
     * @return  string
     **/
    public function automaticCreated($data)
    {
        if (!isset($data['created']) || !$data['created']) {
            $data['created'] = date('Y-m-d H:i:s');
        }
        return $data['created'];
    }

    /**
     * Generates automatic created by field value
     *
     * @param   array  $data  The data being saved
     * @return  int
     **/
    public function automaticCreatedBy($data)
    {
        return (isset($data['created_by']) && $data['created_by']
            ? (int)$data['created_by']
            : static::resolveCurrentUserId());
    }

    /**
     * Generates automatic asset id field
     *
     * @return  int
     **/
    public function automaticAssetId()
    {
        return Asset::resolve($this);
    }

    /**
     * Sets limit and offset for a given page of results
     *
     * @param   int  $page     The page number (1-based)
     * @param   int  $perPage  The number of results per page
     * @return  self
     **/
    public function forPage(int $page, int $perPage): self
    {
        return $this->start(($page - 1) * $perPage)->limit($perPage);
    }

    /**
     * Process results in batches to reduce memory usage
     *
     * Retrieves results in chunks using OFFSET pagination, calling the callback
     * for each batch. Useful for processing large datasets without loading
     * everything into memory at once.
     *
     * **Warning:** This method uses OFFSET-based pagination which is NOT safe
     * for mutations (deletes/updates) during iteration. If you need to modify
     * records while processing, use {@see chunkById()} instead.
     *
     * Example usage:
     * ```php
     * // Send emails to all users in batches of 100
     * User::all()->whereEquals('newsletter', 1)->chunk(100, function($users) {
     *     foreach ($users as $user) {
     *         Mail::send($user->email, 'Newsletter', $content);
     *     }
     * });
     *
     * // Export articles to CSV in batches
     * Article::all()->chunk(500, function($articles, $page) use ($file) {
     *     foreach ($articles as $article) {
     *         fputcsv($file, $article->toArray());
     *     }
     *     echo "Processed page $page\n";
     * });
     *
     * // Stop early by returning false
     * Article::all()->chunk(100, function($articles) use (&$count) {
     *     $count += count($articles);
     *     if ($count >= 1000) {
     *         return false; // Stop after 1000 records
     *     }
     * });
     * ```
     *
     * @param   int       $size      Batch size (number of records per batch)
     * @param   callable  $callback  Function to process each batch: function(Rows $batch, int $page): bool|void
     * @return  bool  True if all chunks processed, false if callback returned false
     * @see     chunkById()  For mutation-safe chunk processing
     * @see     batch()      For generator-based batch iteration
     **/
    public function chunk(int $size, callable $callback): bool
    {
        if ($size <= 0) {
            throw new \InvalidArgumentException('Chunk size must be greater than 0');
        }

        $page = 1;

        do {
            $results = (clone $this)->forPage($page, $size)->rows();
            $count = $results->count();

            if ($count === 0) {
                break;
            }

            if ($callback($results, $page) === false) {
                return false;
            }

            $page++;
        } while ($count === $size);

        return true;
    }

    /**
     * Iterate through results one at a time using a generator
     *
     * Returns a Generator that yields individual model instances, internally
     * fetching records in batches for efficiency. This is the most memory-efficient
     * way to iterate over large result sets.
     *
     * **Warning:** Uses OFFSET-based pagination internally, which is NOT safe
     * for mutations during iteration. Use {@see lazyById()} if you need to
     * delete or update records while iterating.
     *
     * Example usage:
     * ```php
     * // Memory-efficient iteration over millions of records
     * foreach (User::all()->cursor() as $user) {
     *     $this->processUser($user);
     * }
     *
     * // With custom batch size (larger = fewer queries, more memory)
     * foreach (Article::all()->whereEquals('status', 'published')->cursor(500) as $article) {
     *     $this->indexArticle($article);
     * }
     *
     * // Use with array functions via iterator_to_array (careful with large sets!)
     * $emails = [];
     * foreach (User::all()->whereEquals('active', 1)->cursor() as $user) {
     *     $emails[] = $user->email;
     * }
     * ```
     *
     * ## Memory Comparison
     *
     * ```php
     * // BAD: Loads all 1M users into memory at once
     * foreach (User::all()->rows() as $user) { ... }
     *
     * // GOOD: Only keeps ~100 users in memory at a time
     * foreach (User::all()->cursor(100) as $user) { ... }
     * ```
     *
     * @param   int  $chunkSize  Internal batch size for database fetches (default: 100)
     * @return  \Generator  Yields individual Relational model instances
     * @see     lazyById()  For mutation-safe lazy iteration
     * @see     lazy()      Alias with larger default batch size
     **/
    public function cursor(int $chunkSize = 100): \Generator
    {
        if ($chunkSize <= 0) {
            throw new \InvalidArgumentException('Chunk size must be greater than 0');
        }

        $page = 1;

        do {
            $results = (clone $this)->forPage($page, $chunkSize)->rows();
            $count = $results->count();

            foreach ($results as $model) {
                yield $model;
            }

            $page++;
        } while ($count === $chunkSize);
    }

    /**
     * Iterate through results one at a time using a generator (alias for cursor)
     *
     * Retrieves results in internal batches but yields one model at a time.
     * Memory-efficient for iterating over large result sets.
     *
     * **Note:** This method uses OFFSET-based pagination internally, which is NOT
     * safe for mutations during iteration. If you need to delete or update records
     * while iterating, use `lazyById()` instead.
     *
     * ```php
     * // Memory-efficient iteration (read-only)
     * foreach (Article::all()->lazy(100) as $article) {
     *     $this->process($article);
     * }
     *
     * // For mutations during iteration, use lazyById():
     * foreach (Article::all()->lazyById(100) as $article) {
     *     $article->delete(); // Safe!
     * }
     * ```
     *
     * @param   int  $chunkSize  Internal batch size for efficiency (default: 1000)
     * @return  \Generator  Yields individual Relational model instances
     * @see     lazyById()  For mutation-safe lazy iteration
     **/
    public function lazy(int $chunkSize = 1000): \Generator
    {
        return $this->cursor($chunkSize);
    }

    /**
     * Iterate through results in batches using a generator
     *
     * Similar to {@see chunk()} but uses a generator instead of callbacks,
     * giving you more control over the iteration flow. Each yield returns
     * a Rows collection containing the batch results.
     *
     * **Warning:** Uses OFFSET-based pagination internally, which is NOT safe
     * for mutations during iteration.
     *
     * Example usage:
     * ```php
     * // Process batches with a foreach loop
     * foreach (User::all()->batch(100) as $userBatch) {
     *     $this->sendBulkEmail($userBatch);
     * }
     *
     * // Process with manual control (can break/continue)
     * $generator = Article::all()->batch(500);
     * foreach ($generator as $articles) {
     *     if ($this->shouldStop()) {
     *         break; // Exit early
     *     }
     *     $this->processArticles($articles);
     * }
     *
     * // Get batch count
     * $batchNumber = 0;
     * foreach (Order::all()->batch(50) as $orders) {
     *     $batchNumber++;
     *     echo "Processing batch $batchNumber with " . count($orders) . " orders\n";
     * }
     * ```
     *
     * @param   int  $size  Batch size (number of records per batch, default: 100)
     * @return  \Generator  Yields Rows collections
     * @see     chunk()     For callback-based batch processing
     * @see     cursor()    For single-record iteration
     **/
    public function batch(int $size = 100): \Generator
    {
        if ($size <= 0) {
            throw new \InvalidArgumentException('Batch size must be greater than 0');
        }

        $page = 1;

        do {
            $results = (clone $this)->forPage($page, $size)->rows();
            $count = $results->count();

            if ($count === 0) {
                break;
            }

            yield $results;

            $page++;
        } while ($count === $size);
    }
}
