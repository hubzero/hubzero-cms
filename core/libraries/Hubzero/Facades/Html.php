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
 * Uses dynamic __call delegation to Builder sub-classes (Asset, Batch, Select, etc.)
 *
 * @method static bool register(string $key, callable $callable)
 * @method static bool has(string $key)
 * @method static void addPath(string $path)
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
