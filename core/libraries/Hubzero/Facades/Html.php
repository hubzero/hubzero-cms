<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * HTML Helper facade
 *
 * Uses dynamic __call delegation to Builder sub-classes (Asset, Batch, Select, etc.):
 * the first argument names the method of that class, and the rest are its own.
 *
 * @method static bool  register(string $key, callable $callable)
 * @method static bool  forget(string $key)
 * @method static bool  has(string $key)
 * @method static array addIncludePath(string $path = '')
 * @method static mixed access(string $function, mixed ...$args)
 * @method static mixed asset(string $function, mixed ...$args)
 * @method static mixed batch(string $function, mixed ...$args)
 * @method static mixed behavior(string $function, mixed ...$args)
 * @method static mixed category(string $function, mixed ...$args)
 * @method static mixed content(string $function, mixed ...$args)
 * @method static mixed contentlanguage(string $function, mixed ...$args)
 * @method static mixed grid(string $function, mixed ...$args)
 * @method static mixed input(string $function, mixed ...$args)
 * @method static mixed menu(string $function, mixed ...$args)
 * @method static mixed select(string $function, mixed ...$args)
 * @method static mixed sliders(string $function, mixed ...$args)
 * @method static mixed tabs(string $function, mixed ...$args)
 *
 * @codeCoverageIgnore
 */
class Html extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'html.builder';
    }
}
