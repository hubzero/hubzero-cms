<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Api;

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

        $vars['controller'] = 'sessions';

        // Storage endpoints live on their own controller: /api/tools/storage/{task}
        if (isset($segments[0]) && !is_numeric($segments[0]) && $segments[0] == 'storage') {
            $vars['controller'] = 'storage';
            $vars['task'] = isset($segments[1]) ? $segments[1] : 'usage';
            return $vars;
        }

        if (isset($segments[0])) {
            if (is_numeric($segments[0])) {
                $vars['sessionid'] = $segments[0];
                if (App::get('request')->method() == 'GET') {
                    $vars['task'] = 'read';
                }
            } else {
                $vars['task'] = $segments[0];
            }

            if (isset($segments[1])) {
                // /tools/{tool}/{task}: keep the tool name for the task
                // (rappturexml read it from Apache's SCRIPT_URL)
                if (!is_numeric($segments[0])) {
                    $vars['tool'] = $segments[0];
                }
                $vars['task'] = $segments[1];
            }
        }

        return $vars;
    }
}
