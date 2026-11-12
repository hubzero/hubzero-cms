<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site;

use Hubzero\Component\Router\Base;

/**
 * Routing class for the component
 */
class Router extends Base
{
    /**
     * Turn querystring parameters into an SEF route
     *
     * @param  array<string, mixed> &$query Querystring bits
     * @return array<int, mixed>
     */
    public function build(&$query)
    {
        $segments = [];

        return $segments;
    }

    /**
     * Parse a SEF route
     *
     * @param  array<int, string> $segments Exploded SEF URL
     * @return array<string, mixed>
     */
    public function parse(&$segments)
    {
        $vars = [];

        if (empty($segments)) {
            return $vars;
        }

        $vars['task'] = isset($segments[0]) ? $segments[0] : 'view';
        $vars['db']   = isset($segments[1]) ? $segments[1] : false;
        $vars['dv']   = isset($segments[2]) ? $segments[2] : false;

        return $vars;
    }
}
