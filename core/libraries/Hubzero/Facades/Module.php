<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Module loader facade
 *
 * @method static int    count(string $condition)
 * @method static \stdClass|null byName(string $name, string $title = null)
 * @method static array<mixed>  byPosition(string $position)
 * @method static bool   isEnabled(string $module)
 * @method static string position(string $position, string|array<mixed> $style = 'none')
 * @method static string name(string $name, string $style = 'none')
 * @method static string render(object $module, array<mixed> $attribs = [])
 * @method static string getLayoutPath(string $module, string $layout = 'default')
 * @method static array<mixed>  all()
 * @method static \Hubzero\Config\Registry params(mixed $id)
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
