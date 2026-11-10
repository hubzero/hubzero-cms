<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
