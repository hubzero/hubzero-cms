<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Api;

use Hubzero\Component\Router\Base;

/**
 * Routing class for the component
 */
class Router extends Base
{
    /**
     * Build the route for the component
     *
     * @param   array<string, mixed>  &$query  An array of URL arguments
     * @return  array<int, mixed>  The URL arguments to use to assemble the subsequent URL
     */
    public function build(&$query)
    {
        $segments = [];

        if (!empty($query['controller'])) {
            $segments[] = $query['controller'];
            unset($query['controller']);
        }

        if (!empty($query['task'])) {
            $segments[] = $query['task'];
            unset($query['task']);
        }

        return $segments;
    }

    /**
     * Parse the segments of a URL
     *
     * @param   array<int, string>  &$segments  The segments of the URL to parse
     * @return  array<string, mixed>  The URL attributes to be used by the application
     */
    public function parse(&$segments)
    {
        $vars = [];

        if (isset($segments[0])) {
            if ($segments[0] == 'unityscoresave') {
                $vars['controller'] = 'unity';
                $vars['task']       = 'save';
            } elseif ($segments[0] == 'passport') {
                $vars['controller'] = 'passport';
                $vars['task']       = 'badge';
            } else {
                if (in_array($segments[0], ['list', 'read', 'update', 'create', 'delete'])) {
                    $vars['controller'] = 'courses';
                    $vars['task']       = $segments[0];
                } else {
                    $vars['controller'] = $segments[0];
                    if (isset($segments[1])) {
                        $vars['task'] = $segments[1];
                    }
                }
            }
        }

        return $vars;
    }
}
