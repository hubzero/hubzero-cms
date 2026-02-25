<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * User facade
 *
 * @method static mixed    get(string $key, mixed $default = null)
 * @method static object   set(string|array $key, mixed $value = null)
 * @method static bool     isGuest()
 * @method static bool     authorise(string $action, string $assetname = null)
 * @method static string   picture(int $anonymous = 0, bool $thumbnail = true, bool $serveFile = true)
 * @method static string   link(string $type = '')
 * @method static array    groups(string $role = 'all')
 * @method static array    getAuthorisedViewLevels()
 * @method static array    getAuthorisedGroups()
 * @method static array    getAuthorisedCategories(string $component, string $action)
 * @method static bool     save()
 * @method static bool     destroy()
 * @method static object   reputation()
 * @method static object   tokens()
 * @method static mixed    getParam(string $key, mixed $default = null)
 * @method static mixed    setParam(string $key, mixed $value)
 * @method static bool     setLastVisit(string $timestamp = 'now')
 *
 * @codeCoverageIgnore
 */
class User extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getAccessor()
    {
        return 'user';
    }

    /**
     * Gets a user state.
     *
     * @param   string  $key      The path of the state.
     * @param   mixed   $default  Optional default value, returned if the internal value is null.
     * @return  mixed   The user state or null.
     */
    public static function getState($key, $default = null)
    {
        $session  = self::$app->get('session');
        $registry = $session->get('registry');

        if (!is_null($registry)) {
            return $registry->get($key, $default);
        }

        return $default;
    }

    /**
     * Sets the value of a user state variable.
     *
     * @param   string  $key    The path of the state.
     * @param   string  $value  The value of the variable.
     * @return  mixed   The previous state, if one existed.
     */
    public static function setState($key, $value)
    {
        $session  = self::$app->get('session');
        $registry = $session->get('registry');

        if (!is_null($registry)) {
            return $registry->set($key, $value);
        }

        return null;
    }
}
