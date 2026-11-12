<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Api;

use Hubzero\Component\Router\Base;
use Hubzero\Facades\App;

/**
 * Routing class for the component
 */
class Router extends Base
{
    /**
     * Build the route for the component.
     *
     * @param   array<string, mixed>  &$query  An array of URL arguments
     * @return  array<int, mixed>  The URL arguments to use to assemble the subsequent URL.
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
     * Parse the segments of a URL.
     *
     * @param   array<int, string>  &$segments  The segments of the URL to parse.
     * @return  array<string, mixed>  The URL attributes to be used by the application.
     */
    public function parse(&$segments)
    {
        $vars = [];

        $vars['controller'] = 'collections';

        if (isset($segments[0])) {
            //  /collections
            //  /collections/list
            //  /collections/##
            //  /collections/##/posts
            //  /collections/##/posts/list
            //  /collections/##/posts/##
            if (is_numeric($segments[0])) {
                $vars['id'] = $segments[0];
                if (App::get('request')->method() == 'GET') {
                    $vars['task'] = 'read';
                }
            } else {
                if ($segments[0] == 'posts') {
                    $vars['controller'] = $segments[0];
                } else {
                    $vars['task'] = $segments[0];
                }
            }

            if (isset($segments[1])) {
                if (is_numeric($segments[1])) {
                    $vars['id'] = $segments[1];

                    if (App::get('request')->method() == 'GET') {
                        $vars['task'] = 'read';
                    }
                } else {
                    $vars['task'] = $segments[1];
                }

                if (isset($segments[2])) {
                    $vars['task'] = $segments[2];
                }
            }
        }

        return $vars;
    }
}
