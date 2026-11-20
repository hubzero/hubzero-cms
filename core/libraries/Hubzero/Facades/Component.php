<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

use Hubzero\Component\Router\RouterInterface;

/**
 * Component loader facade
 *
 * @method static bool   isEnabled(string $option, bool $strict = false)
 * @method static \Hubzero\Config\Registry params(string $option, bool $strict = false)
 * @method static string path(string $option)
 * @method static string canonical(string $option)
 * @method static string render(string $option, array<mixed> $params = [])
 * @method static RouterInterface router(string $option, string $client = null, string $version = null)
 * @method static \stdClass load(string $option, bool $strict = false)
 *
 * @codeCoverageIgnore
 */
class Component extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'component';
    }
}
