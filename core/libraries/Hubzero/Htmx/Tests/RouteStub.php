<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Htmx\Tests;

class RouteStub
{
    public static function url(string $url, bool $xhtml = true): string
    {
        return '/' . ltrim($url, '/');
    }
}
