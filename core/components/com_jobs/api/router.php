<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Api;

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
     * @return  array<int, mixed>  The URL arguments to use to assemble the subsequent URL.
     */
    public function build(&$query)
    {
        $segments = [];

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
        $vars['controller'] = 'jobs';

        if (isset($segments[0])) {
            // /jobs/{jobcode} (documented, and what jobTask reads) went to a
            // task named after the code, which never exists
            if (ctype_digit((string) $segments[0])) {
                $vars['task']    = 'job';
                $vars['jobcode'] = $segments[0];
            } else {
                $vars['task'] = $segments[0];
            }
        }

        return $vars;
    }
}
