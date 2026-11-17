<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

use Hubzero\Config\Registry;
use Hubzero\Database\Drivers\Base\BaseSqlDriver;

/**
 * App facade
 *
 * @method static ($key is 'db' ? BaseSqlDriver : ($key is 'menu.params' ? Registry : mixed)) get(string $key)
 * @method static void   set(string $key, mixed $value)
 * @method static bool   has(string $key)
 * @method static void   forget(string $key)
 * @method static bool   isSite()
 * @method static bool   isAdmin()
 * @method static bool   isApi()
 * @method static bool   isCli()
 * @method static string version()
 * @method static string hash(string $seed)
 * @method static object register(mixed $provider, array<mixed> $options = [])
 * @method static never  redirect(string $url, ?string $message = null, array<mixed>|string|null $type = 'success')
 * @method static never  abort(int $code, string $message = '')
 * @method static never  close()
 *
 * @codeCoverageIgnore
 */
class App extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'app';
    }
}
