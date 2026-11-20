<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Abstract face class
 *
 * Heavily influenced by Laravel
 * http://laravel.com
 */
abstract class Facade
{
    /**
     * The application instance being facaded.
     *
     * @var  \Hubzero\Container\Container  The application (a bare container in tests)
     */
    protected static $app;

    /**
     * The application aliases being loaded
     *
     * @var  array<mixed>
     */
    protected static $aliases;

    /**
     * Whether the alias autoloader has been registered
     *
     * @var  bool
     */
    protected static $aliasLoaderRegistered = false;

    /**
     * Get the application instance behind the facade.
     *
     * @return  \Hubzero\Container\Container|null  Null until an application is set
     */
    public static function getApplication()
    {
        return static::$app;
    }

    /**
     * Set the application instance.
     *
     * @param   \Hubzero\Container\Container  $app  The application (a bare container in tests)
     * @return  void
     */
    public static function setApplication($app)
    {
        static::$app = $app;
    }

    /**
     * Hotswap the underlying instance behind the facade.
     *
     * @param   mixed  $instance
     * @return  void
     */
    public static function swap($instance)
    {
        static::$app->offsetSet(static::getAccessor(), $instance);
    }

    /**
     * Get the root object behind the facade.
     *
     * @return  mixed
     */
    public static function getRoot()
    {
        return static::$app->get(static::getAccessor());
    }

    /**
     * Get the registered name of the component.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        throw new \RuntimeException('Facade does not implement getAccessor method.');
    }

    /**
     * Create aliases
     *
     * @param   array<mixed>  $aliases
     * @return  void
     */
    public static function createAliases(array $aliases)
    {
        // Merge rather than replace. Callers that register a partial map (test
        // bootstraps, for instance) would otherwise drop every alias registered
        // before them, and the facades those aliases covered stop resolving.
        static::$aliases = array_merge((array) static::$aliases, $aliases);

        // Create autoloader that creates class aliases during runtime.
        // Registering it again would only add a duplicate callback.
        if (!static::$aliasLoaderRegistered) {
            spl_autoload_register(__NAMESPACE__ . '\Facade::loadAliases');

            static::$aliasLoaderRegistered = true;
        }
    }

    /**
     * Load aliases (the autoloader the facades register; an autoloader returns nothing)
     *
     * @param   string  $class  The class being loaded
     * @return  void
     */
    public static function loadAliases($class)
    {
        $aliases = static::$aliases;

        if (array_key_exists($class, $aliases)) {
            class_alias($aliases[$class], $class);
            return;
        }

        // Allow calling facade in namespaced class
        // without resetting to the root namespace
        $classPieces = explode('\\', $class);
        $classAlt    = array_pop($classPieces);
        if (array_key_exists($classAlt, $aliases)) {
            class_alias($aliases[$classAlt], $class);
        }
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param   string  $method
     * @param   array<mixed>   $args
     * @return  mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getRoot();

        // Seems counter-intuitive but the switch here can
        // actually be faster than calling call_user_func_array
        // every time.
        switch (count($args)) {
            case 0:
                return $instance->$method();

            case 1:
                return $instance->$method($args[0]);

            case 2:
                return $instance->$method($args[0], $args[1]);

            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);

            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return $instance->$method(...$args);
        }
    }
}
