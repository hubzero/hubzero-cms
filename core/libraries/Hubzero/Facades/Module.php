<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Module loader facade
 *
 * @method static int    count(string $condition)
 * @method static object byName(string $name, string $title = null)
 * @method static array  byPosition(string $position)
 * @method static bool   isEnabled(string $module)
 * @method static string position(string $position, string $style = 'none')
 * @method static string name(string $name, string $style = 'none')
 * @method static string render(object $module, array $attribs = [])
 * @method static string getLayoutPath(string $module, string $layout = 'default')
 * @method static array  all()
 * @method static object params(mixed $id)
 * @method static string canonical(string $module)
 * @method static string path(string $module)
 */
class Module extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'module';
    }
}
