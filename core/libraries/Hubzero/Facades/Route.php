<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Router facade
 *
 * @method static string url(string $url, bool $xhtml = true, int|bool $ssl = null)
 * @method static object build(string $uri)
 * @method static object parse(string $url)
 * @method static string urlForClient(string $client, string $url, bool $xhtml = true, int $ssl = null)
 * @method static object client(string $client = null)
 * @method static object rules(string $type)
 * @method static void   bind(array<mixed> $vars = [], bool $merge = true)
 * @method static void   flush()
 *
 * @codeCoverageIgnore
 */
class Route extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getAccessor()
    {
        return 'router';
    }
}
