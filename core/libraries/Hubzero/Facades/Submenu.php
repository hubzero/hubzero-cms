<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Submenu facade
 *
 * @method static void   addEntry(string $name, string $link = '', bool $active = false)
 * @method static array<mixed>  getItems()
 * @method static string render()
 *
 * @codeCoverageIgnore
 */
class Submenu extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'submenu';
    }
}
